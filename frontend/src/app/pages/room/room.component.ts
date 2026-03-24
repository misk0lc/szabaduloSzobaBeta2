import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { interval, Subscription, forkJoin } from 'rxjs';

import { AuthService } from '../../services/auth.service';
import { QuestionService } from '../../services/question.service';
import { HintService } from '../../services/hint.service';
import { ProgressService } from '../../services/progress.service';
import { LevelService } from '../../services/level.service';

import { Question, CheckAnswerResponse } from '../../models/question.model';
import { Hint } from '../../models/hint.model';
import { LevelDetail } from '../../models/level.model';

export interface QuestionState {
  question: Question;
  solved: boolean;
  digit: number | null;
  justSolved: boolean;   // animáció triggerhez
}

// Szoba téma szobánév alapján
export interface RoomTheme {
  icon: string;
  bgClass: string;
  objects: RoomObject[];
}

export interface RoomObject {
  emoji: string;
  label: string;
  col: number;   // PositionX tartomány (1-5, 6-10, stb.)
  row: number;   // PositionY (1-4)
}

@Component({
  selector: 'app-room',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './room.component.html',
  styleUrls: ['./room.component.css']
})
export class RoomComponent implements OnInit, OnDestroy {

  // ─── Alap állapot ──────────────────────────────────────────────
  levelId = 0;
  level: LevelDetail | null = null;
  loading = true;
  error = '';

  questions: QuestionState[] = [];
  balance = 0;

  // ─── Timer ─────────────────────────────────────────────────────
  timeSpent = 0;
  timerWarning = false;   // 10 perc felett pirosra vált
  private timerSub?: Subscription;

  get timerDisplay(): string {
    const m = Math.floor(this.timeSpent / 60);
    const s = this.timeSpent % 60;
    return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
  }

  get modalTimerDisplay(): string {
    const m = Math.floor(this.modalTimeSpent / 60);
    const s = this.modalTimeSpent % 60;
    return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
  }

  // ─── Kérdés modal ──────────────────────────────────────────────
  activeQuestion: QuestionState | null = null;
  answerInput = '';
  selectedOption: string | null = null;
  answerResult: CheckAnswerResponse | null = null;
  answerLoading = false;
  modalVisible = false;    // CSS animáció
  answerAnim: 'correct' | 'wrong' | null = null;  // answer animation trigger

  // ─── Modal badge animációk ─────────────────────────────────────
  modalBalanceAnim: 'gain' | 'loss' | null = null;   // pénz animáció a modalban
  modalTimerAnim: 'penalty' | null = null;            // idő animáció a modalban
  modalBalance = 0;        // modal saját pénzegyenleg (szinkronban balance-szal)
  modalTimeSpent = 0;      // modal saját időkijelző
  lastTimePenalty = 0;     // utolsó időbüntetés értéke (mp-ben)

  // ─── Hint panel ────────────────────────────────────────────────
  showHints = false;
  hints: Hint[] = [];
  hintsLoading = false;
  hintError = '';

  // ─── Digit gyűjtés ─────────────────────────────────────────────
  newDigitIndex: number | null = null;   // villog animáció
  manualDigits: (string | undefined)[] = [];  // kézzel beírt számjegyek

  get collectedDigits(): (number | null)[] {
    return [...this.questions]
      .sort((a, b) => a.question.PositionX - b.question.PositionX)
      .map(q => q.digit);
  }

  // Megoldott + kézzel beírt digit összefésülve (kód beküldéshez)
  get mergedDigits(): string[] {
    return this.collectedDigits.map((d, i) =>
      d !== null ? String(d) : (this.manualDigits[i] ?? '')
    );
  }

  onManualDigitInput(i: number, event: Event): void {
    const input = event.target as HTMLInputElement;
    // Csak 0-9 engedélyezett, 1 karakter
    const val = input.value.replace(/[^0-9]/g, '').slice(-1);
    input.value = val;
    if (!this.manualDigits) this.manualDigits = [];
    this.manualDigits[i] = val;
    // Következő üres mezőre ugrás
    if (val) {
      const next = document.querySelector<HTMLInputElement>(`.digit-manual-input[data-idx="${i + 1}"]`);
      next?.focus();
    }
  }

  get solvedCount(): number {
    return this.questions.filter(q => q.solved).length;
  }

  get progressPercent(): number {
    if (!this.questions.length) return 0;
    return Math.round((this.solvedCount / this.questions.length) * 100);
  }

  get allSolved(): boolean {
    return this.questions.length > 0 && this.questions.every(q => q.solved);
  }

  get canSubmitCode(): boolean {
    return this.mergedDigits.length > 0 && this.mergedDigits.every(d => d !== '');
  }

  // ─── Kód beküldés ──────────────────────────────────────────────
  showCodeSubmit = false;
  codeInput = '';
  submitResult: { correct: boolean; message: string; score?: number } | null = null;
  submitLoading = false;
  submitSuccess = false;

  // ─── Szoba téma ────────────────────────────────────────────────
  get roomTheme(): { icon: string; bg: string; accent: string } {
    const name = (this.level?.Name ?? '').toLowerCase();
    if (name.includes('könyvtár'))  return { icon: '📚', bg: 'theme-library',   accent: '#c19a6b' };
    if (name.includes('labor'))     return { icon: '🧪', bg: 'theme-lab',       accent: '#34d399' };
    if (name.includes('pince'))     return { icon: '🏰', bg: 'theme-dungeon',   accent: '#fb923c' };
    if (name.includes('kapitány'))  return { icon: '⚓', bg: 'theme-ship',      accent: '#38bdf8' };
    if (name.includes('űr'))        return { icon: '🚀', bg: 'theme-space',     accent: '#c19a6b' };
    return { icon: '🔐', bg: 'theme-default', accent: '#c19a6b' };
  }

  get roomBgStyle(): string {
    // BackgroundUrl (admin által beállított) elsőbbséget élvez
    if (this.level?.BackgroundUrl) {
      return `url(${this.level.BackgroundUrl})`;
    }
    // Fallback: csak 1-5 OrderNumber esetén van helyi kép
    const n = this.level?.OrderNumber ?? 0;
    if (n >= 1 && n <= 5) {
      return `url(/rooms/room${n}/background.png)`;
    }
    return 'none';
  }

  // Objektum ikonok a PositionX/Y alapján (dekoráció)
  get roomDecorations(): { emoji: string; x: number; y: number }[] {
    const name = (this.level?.Name ?? '').toLowerCase();
    if (name.includes('könyvtár')) return [
      { emoji: '🗄️', x: 5, y: 10 }, { emoji: '🕯️', x: 85, y: 8 },
      { emoji: '🦉', x: 50, y: 5 }, { emoji: '🖋️', x: 30, y: 85 },
      { emoji: '📜', x: 70, y: 80 }
    ];
    if (name.includes('labor')) return [
      { emoji: '⚗️', x: 10, y: 15 }, { emoji: '🔬', x: 75, y: 10 },
      { emoji: '💊', x: 45, y: 75 }, { emoji: '🧫', x: 20, y: 70 },
      { emoji: '☢️', x: 80, y: 80 }
    ];
    if (name.includes('pince')) return [
      { emoji: '🕸️', x: 5, y: 5 }, { emoji: '🕸️', x: 90, y: 8 },
      { emoji: '🪨', x: 40, y: 85 }, { emoji: '🔦', x: 65, y: 20 },
      { emoji: '🐀', x: 80, y: 80 }
    ];
    if (name.includes('kapitány')) return [
      { emoji: '🗺️', x: 10, y: 10 }, { emoji: '⚓', x: 85, y: 15 },
      { emoji: '🦜', x: 50, y: 8 }, { emoji: '🧭', x: 25, y: 80 },
      { emoji: '💎', x: 75, y: 75 }
    ];
    if (name.includes('űr')) return [
      { emoji: '🌌', x: 5, y: 5 }, { emoji: '🛸', x: 80, y: 10 },
      { emoji: '⭐', x: 45, y: 8 }, { emoji: '🌙', x: 20, y: 78 },
      { emoji: '🤖', x: 78, y: 80 }
    ];
    return [];
  }

  // Kérdés-pont pozíció: PositionX (1-20) → %, PositionY (1-4) → %
  nodeLeft(q: Question): number {
    // 1-20 → 5%-93% (margókkal)
    return 5 + ((q.PositionX - 1) / 19) * 88;
  }

  nodeTop(q: Question): number {
    // 1-4 → 15%-78% (header és digit-bar helye miatt)
    return 15 + ((q.PositionY - 1) / 3) * 63;
  }

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private auth: AuthService,
    private levelSvc: LevelService,
    private questionSvc: QuestionService,
    private hintSvc: HintService,
    private progressSvc: ProgressService
  ) {}

  ngOnInit(): void {
    this.levelId = Number(this.route.snapshot.paramMap.get('id'));
    this.loadRoom();
  }

  ngOnDestroy(): void {
    this.timerSub?.unsubscribe();
  }

  private startTimer(): void {
    this.timerSub = interval(1000).subscribe(() => {
      this.timeSpent++;
      this.timerWarning = this.timeSpent > 600; // 10 perc után figyelmeztetés
      // Modal timer szinkronban, ha nincs büntetés animáció aktív
      if (this.modalTimerAnim === null) {
        this.modalTimeSpent = this.timeSpent;
      }
    });
  }

  loadRoom(): void {
    this.loading = true;
    forkJoin({
      level: this.levelSvc.getLevel(this.levelId),
      questions: this.questionSvc.getQuestions(this.levelId),
      me: this.auth.getMe()
    }).subscribe({
      next: ({ level, questions, me }) => {
        this.level = level;
        this.balance = me.Balance ?? 0;
        this.questions = questions.map(q => ({
          question: q,
          solved: q.Solved ?? false,
          digit: q.RewardDigit ?? null,
          justSolved: false
        }));
        this.loading = false;
        this.startTimer();
      },
      error: (err) => {
        this.error = err.status === 403
          ? 'Ez a szoba még nem érhető el.'
          : 'Nem sikerült betölteni a szobát.';
        this.loading = false;
      }
    });
  }

  // ─── Kérdés modal ──────────────────────────────────────────────
  openQuestion(qs: QuestionState): void {
    if (qs.solved) return;
    this.activeQuestion = qs;
    this.answerInput = '';
    this.selectedOption = null;
    this.answerResult = null;
    this.showHints = false;
    this.hints = [];
    this.hintError = '';
    this.modalBalance = this.balance;
    this.modalTimeSpent = this.timeSpent;
    this.modalBalanceAnim = null;
    this.modalTimerAnim = null;
    // Animáció delay
    setTimeout(() => this.modalVisible = true, 10);
  }

  closeQuestion(): void {
    this.modalVisible = false;
    setTimeout(() => {
      this.activeQuestion = null;
      this.answerResult = null;
      this.selectedOption = null;
      this.modalBalanceAnim = null;
      this.modalTimerAnim = null;
    }, 250);
  }

  // időbüntetés villogás trigger
  timePenaltyAnim = false;

  checkAnswer(): void {
    if (!this.activeQuestion || !this.selectedOption) return;
    this.answerLoading = true;
    this.answerResult = null;
    this.answerAnim = null;

    this.questionSvc.checkAnswer(this.activeQuestion.question.QuestionID, {
      answer: this.selectedOption
    }).subscribe({
      next: (res) => {
        this.answerResult = res;
        this.answerLoading = false;

        // Trigger animation
        this.answerAnim = res.correct ? 'correct' : 'wrong';
        setTimeout(() => {
          this.answerAnim = null;
          if (!res.correct) this.selectedOption = null;
        }, 800);

        if (!res.correct) {
          // Pénzlevonás frissítése + modal animáció
          if (res.NewBalance !== undefined) {
            this.balance = res.NewBalance;
            this.modalBalance = res.NewBalance;
            if (res.MoneyPenalty && res.MoneyPenalty > 0) {
              this.modalBalanceAnim = 'loss';
              setTimeout(() => this.modalBalanceAnim = null, 1200);
            }
          }
          // Időbüntetés hozzáadása a timerhez + modal animáció
          if (res.TimePenalty && res.TimePenalty > 0) {
            this.timeSpent += res.TimePenalty;
            this.modalTimeSpent = this.timeSpent;
            this.lastTimePenalty = res.TimePenalty;
            this.timePenaltyAnim = true;
            this.modalTimerAnim = 'penalty';
            setTimeout(() => { this.timePenaltyAnim = false; this.modalTimerAnim = null; }, 1500);
          }
        }

        if (res.correct && this.activeQuestion) {
          this.activeQuestion.solved = true;
          this.activeQuestion.justSolved = true;

          const idx = this.questions.findIndex(
            q => q.question.QuestionID === this.activeQuestion!.question.QuestionID
          );
          if (res.RewardDigit !== undefined) {
            this.activeQuestion.digit = res.RewardDigit;
            this.newDigitIndex = idx;
            setTimeout(() => this.newDigitIndex = null, 1500);
          }
          if (res.NewBalance !== undefined) {
            this.balance = res.NewBalance;
            this.modalBalance = res.NewBalance;
            this.modalBalanceAnim = 'gain';
            setTimeout(() => this.modalBalanceAnim = null, 1200);
          }

          setTimeout(() => this.closeQuestion(), 1800);
        }
      },
      error: () => {
        this.answerResult = { correct: false, message: 'Szerver hiba. Próbáld újra.' };
        this.answerLoading = false;
      }
    });
  }

  // ─── Hint ──────────────────────────────────────────────────────
  toggleHints(): void {
    this.showHints = !this.showHints;
    if (this.showHints && this.hints.length === 0 && this.activeQuestion) {
      this.hintsLoading = true;
      this.hintSvc.getHints(this.activeQuestion.question.QuestionID).subscribe({
        next: (data) => { this.hints = data; this.hintsLoading = false; },
        error: () => { this.hintError = 'Nem sikerült betölteni a tippeket.'; this.hintsLoading = false; }
      });
    }
  }

  buyHint(hint: Hint): void {
    this.hintError = '';
    this.hintSvc.buyHint(hint.HintID).subscribe({
      next: (res) => {
        this.balance = res.NewBalance;
        this.modalBalance = res.NewBalance;
        // Tipp megvásárolva: jelöljük megvettnek
        const idx = this.hints.findIndex(h => h.HintID === hint.HintID);
        if (idx !== -1) this.hints[idx] = { ...hint, HintText: res.HintText };

        // 50/50: eltávolítjuk a 2 rossz opciót, csak a helyes + 1 hamis marad
        if (this.activeQuestion?.question.Options) {
          const opts = this.activeQuestion.question.Options;
          const correct = opts.filter(o => o.IsCorrect);
          const wrong   = opts.filter(o => !o.IsCorrect);
          // Véletlenszerűen megtartunk 1 rosszat
          const keepWrong = wrong.length > 0
            ? [wrong[Math.floor(Math.random() * wrong.length)]]
            : [];
          this.activeQuestion.question.Options = [...correct, ...keepWrong]
            .sort(() => Math.random() - 0.5);
        }
      },
      error: () => { this.hintError = 'Nincs elegendő egyenleged, vagy már megvetted.'; }
    });
  }

  isHintBought(hint: Hint): boolean {
    return hint.HintText !== undefined && hint.HintText !== null;
  }

  // ─── Kód beküldés ──────────────────────────────────────────────
  openCodeSubmit(): void {
    this.showCodeSubmit = true;
    this.codeInput = this.mergedDigits.join('');
    this.submitResult = null;
    this.submitSuccess = false;
  }

  closeCodeSubmit(): void {
    if (this.submitSuccess) return;
    this.showCodeSubmit = false;
  }

  submitCode(): void {
    if (!this.codeInput.trim()) return;
    this.submitLoading = true;
    this.submitResult = null;

    this.progressSvc.submitCode(this.levelId, {
      code: this.codeInput.trim(),
      timeSpent: this.timeSpent
    }).subscribe({
      next: (res) => {
        this.submitLoading = false;
        this.submitResult = { correct: res.correct, message: res.message, score: res.Score };
        if (res.correct) {
          this.submitSuccess = true;
          this.timerSub?.unsubscribe();
          setTimeout(() => this.router.navigate(['/game']), 3500);
        }
      },
      error: (err) => {
        this.submitLoading = false;
        console.error('submitCode error:', err);
        this.submitResult = { correct: false, message: `Szerver hiba (${err.status}): ${err.error?.message ?? 'Próbáld újra.'}` };
      }
    });
  }

  // ─── Navigáció ─────────────────────────────────────────────────
  visszaMegyek(): void {
    this.router.navigate(['/game']);
  }

  kilepes(): void {
    this.auth.logout().subscribe();
  }

  getUsername(): string {
    return this.auth.getUser()?.Username ?? '';
  }
}
