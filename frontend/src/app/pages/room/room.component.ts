import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { interval, Subscription, forkJoin } from 'rxjs';

import { AuthService } from '../../services/auth.service';
import { QuestionService } from '../../services/question.service';
import { ProgressService } from '../../services/progress.service';
import { LevelService } from '../../services/level.service';
import { ReportService } from '../../services/report.service';
import { MultiplayerService, MultiplayerState } from '../../services/multiplayer.service';
import { HintService } from '../../services/hint.service';

import { Question, CheckAnswerResponse } from '../../models/question.model';
import { LevelDetail } from '../../models/level.model';

export interface QuestionState {
  question: Question;
  solved: boolean;
  digit: number | null;
  justSolved: boolean;   // animáció triggerhez
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

  // ─── Multiplayer ───────────────────────────────────────────────
  isMultiplayer = false;
  multiSessionId = 0;
  multiState: MultiplayerState | null = null;
  multiLinkCopied = false;
  private pollSub?: Subscription;
  private leaveCalled = false;  // dupla leave hívás megakadályozása

  get multiWaiting(): boolean {
    return this.isMultiplayer && this.multiState?.Status === 'waiting';
  }

  get multiPlayers(): { UserID: number; Username: string }[] {
    return this.multiState?.Players ?? [];
  }

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
  hintUsed = false;
  hintError = '';

  // ─── Report panel ───────────────────────────────────────────────
  showReport = false;
  reportCategories = [
    { value: 'bug',      label: 'Bug / Hiba' },
    { value: 'question', label: 'Kérdés' },
    { value: 'other',    label: 'Egyéb' },
  ];
  reportCategory = 'bug';
  reportTitle = '';
  reportMessage = '';
  reportLoading = false;
  reportSent = false;
  reportError = '';

  // ─── Digit gyűjtés ─────────────────────────────────────────────
  newDigitIndex: number | null = null;   // villog animáció
  manualDigits: (string | undefined)[] = [];  // kézzel beírt számjegyek

  get collectedDigits(): (number | null)[] {
    const sortedQuestions = [...this.questions]
      .sort((a, b) => a.question.PositionX - b.question.PositionX);

    if (this.isMultiplayer) {
      const sessionSolved = this.multiState?.SolvedQuestions ?? [];
      return sortedQuestions.map(qs => {
        const sessionItem = sessionSolved.find(s => s.id === qs.question.QuestionID);
        if (sessionItem) return sessionItem.digit;
        return qs.digit;
      });
    }

    return sortedQuestions.map(q => q.digit);
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
    if (this.isMultiplayer) {
      return this.collectedDigits.filter(d => d !== null).length;
    }
    return this.questions.filter(q => q.solved).length;
  }

  get progressPercent(): number {
    if (!this.questions.length) return 0;
    return Math.round((this.solvedCount / this.questions.length) * 100);
  }

  get allSolved(): boolean {
    if (this.isMultiplayer) {
      return this.questions.length > 0 &&
             this.collectedDigits.every(d => d !== null);
    }
    return this.questions.length > 0 && this.questions.every(q => q.solved);
  }

  get canSubmitCode(): boolean {
    if (this.isMultiplayer) {
      const code = this.buildMultiCode();
      // Minden kérdéshez kell digit: a kód hossza egyezzen a kérdések számával
      // és egyetlen slot sem lehet üres
      return code.length === this.questions.length &&
             code.split('').every(ch => ch !== '');
    }
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
    if (this.level?.BackgroundUrl) {
      return `url(${this.level.BackgroundUrl})`;
    }
    return 'none';
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
    private progressSvc: ProgressService,
    private reportService: ReportService,
    private multiSvc: MultiplayerService,
    private hintSvc: HintService
  ) {}

  ngOnInit(): void {
    this.levelId = Number(this.route.snapshot.paramMap.get('id'));
    const sessionParam = this.route.snapshot.paramMap.get('sessionId');
    if (sessionParam) {
      this.isMultiplayer = true;
      this.multiSessionId = Number(sessionParam);
    }
    this.loadRoom();
  }

  ngOnDestroy(): void {
    this.timerSub?.unsubscribe();
    this.pollSub?.unsubscribe();
    // Kilépés a sessionből ha multiplayer és még aktív (waiting vagy playing)
    // és nem sikeres beküldéssel fejezte be (mert akkor a progress maradjon meg)
    // leaveCalled flag védi a dupla hívástól (leaveMulti után ngOnDestroy is fut)
    if (this.isMultiplayer && this.multiSessionId && !this.submitSuccess &&
        !this.leaveCalled &&
        (this.multiState?.Status === 'waiting' || this.multiState?.Status === 'playing')) {
      this.leaveCalled = true;
      this.multiSvc.leave(this.multiSessionId).subscribe({ error: () => {} });
    }
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

    const base$ = {
      level: this.levelSvc.getLevel(this.levelId),
      questions: this.questionSvc.getQuestions(this.levelId),
      me: this.auth.getMe()
    };

    forkJoin(base$).subscribe({
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

        if (this.isMultiplayer) {
          // Multiplayer: először lekérjük a session state-et (multiState feltöltése),
          // majd indítjuk a pollingot
          this.multiSvc.getState(this.multiSessionId).subscribe({
            next: (state) => {
              this.multiState = state;
              if (state.Status === 'playing' && !this.timerSub) {
                this.startTimer();
              }
              this.startMultiplayerPolling();
            },
            error: () => this.startMultiplayerPolling()
          });
        } else {
          this.startTimer();
        }
      },
      error: (err) => {
        this.error = err.status === 403
          ? 'Ez a szoba még nem érhető el.'
          : 'Nem sikerült betölteni a szobát.';
        this.loading = false;
      }
    });
  }

  // ─── Multiplayer polling ────────────────────────────────────────
  private startMultiplayerPolling(): void {
    // Azonnal lekérjük az állapotot
    this.fetchMultiState();
    // Majd 2 másodpercenként
    this.pollSub = interval(2000).subscribe(() => this.fetchMultiState());
  }

  private fetchMultiState(): void {
    this.multiSvc.getState(this.multiSessionId).subscribe({
      next: (state) => {
        const wasWaiting = this.multiState?.Status === 'waiting';
        this.multiState = state;

        // Ha éppen playing lett (valaki csatlakozott) → indítjuk a timert
        if (wasWaiting && state.Status === 'playing' && !this.timerSub) {
          this.startTimer();
        }

        // Ha a partner kilépett → 'abandoned' státusz → azonnal kilépünk, üzenet nélkül
        if (state.Status === 'abandoned') {
          this.pollSub?.unsubscribe();
          this.timerSub?.unsubscribe();
          if (this.activeQuestion) this.closeQuestion();
          if (this.showCodeSubmit) this.showCodeSubmit = false;
          this.router.navigate(['/game']);
          return;
        }

        // Szinkronizáljuk a megoldott kérdéseket a partner alapján is
        if (state.Status === 'playing') {
          state.SolvedQuestions.forEach(item => {
            const qs = this.questions.find(q => q.question.QuestionID === item.id);
            if (qs && !qs.solved) {
              qs.solved = true;
              // A digit a session-ből jön (partner is megosztotta)
              qs.digit = item.digit;

              // Ha éppen ez a kérdés van nyitva, automatikusan bezárjuk
              if (this.activeQuestion?.question.QuestionID === item.id) {
                this.closeQuestion();
              }
            }
          });
        }

        if (state.Status === 'finished') {
          this.pollSub?.unsubscribe();
          this.timerSub?.unsubscribe();
          // Modalt bezárjuk ha nyitva van
          if (this.activeQuestion) {
            this.closeQuestion();
          }
          if (this.showCodeSubmit) {
            this.showCodeSubmit = false;
          }
          // Ha nem mi küldtük be (submitSuccess === false) → partner oldotta meg
          if (!this.submitSuccess) {
            this.submitSuccess = true;
            this.submitResult = { correct: true, message: '🎉 A partner beküldte a helyes kódot! Pálya teljesítve!' };
            this.showCodeSubmit = true;
            setTimeout(() => this.router.navigate(['/game']), 3500);
          }
        }
      },
      error: (err) => {
        // 404 = session törölve (a másik játékos kilépett várakozás közben)
        if (err.status === 404) {
          this.pollSub?.unsubscribe();
          this.timerSub?.unsubscribe();
          this.router.navigate(['/game']);
        }
        // egyéb hiba: silent, újra próbál 2mp múlva
      }
    });
  }

  leaveMulti(): void {
    this.pollSub?.unsubscribe();
    this.timerSub?.unsubscribe();
    if (!this.leaveCalled) {
      this.leaveCalled = true;
      this.multiSvc.leave(this.multiSessionId).subscribe({ error: () => {} });
    }
    this.router.navigate(['/game']);
  }

  copyMultiLink(): void {
    const link = `${window.location.origin}/room/${this.levelId}/multi/${this.multiSessionId}`;
    navigator.clipboard.writeText(link).then(() => {
      this.multiLinkCopied = true;
      setTimeout(() => this.multiLinkCopied = false, 2000);
    });
  }

  // ─── Kérdés modal ──────────────────────────────────────────────
  openQuestion(qs: QuestionState): void {
    // Multiplayerben a session-szintű megoldottságot is ellenőrizzük
    if (this.isMultiplayer) {
      const sessionSolved = this.multiState?.SolvedQuestions ?? [];
      const alreadySolved = qs.solved ||
        sessionSolved.some(s => s.id === qs.question.QuestionID);
      if (alreadySolved) return;
    } else {
      if (qs.solved) return;
    }
    this.activeQuestion = qs;
    this.answerInput = '';
    this.selectedOption = null;
    this.answerResult = null;
    this.hintUsed = false;
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
          // Időbüntetés: multiplayerben NEM adjuk hozzá a timerhez
          if (!this.isMultiplayer && res.TimePenalty && res.TimePenalty > 0) {
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

          // Multiplayer: bejelentjük a partnernek is (digit-tel együtt), majd bezárjuk a modalt
          if (this.isMultiplayer && this.multiSessionId) {
            const digit = res.RewardDigit ?? 0;
            this.multiSvc.solve(this.multiSessionId, this.activeQuestion.question.QuestionID, digit).subscribe({
              next: (state) => { this.multiState = state; },
              error: () => {}
            });
            setTimeout(() => this.closeQuestion(), 700);
          } else {
            setTimeout(() => this.closeQuestion(), 700);
          }
        }
      },
      error: () => {
        this.answerResult = { correct: false, message: 'Szerver hiba. Próbáld újra.' };
        this.answerLoading = false;
      }
    });
  }

  // ─── 50/50 Hint ────────────────────────────────────────────────
  use5050(): void {
    this.hintError = '';
    if (this.balance < 25) {
      this.hintError = 'Nincs elegendő egyenleged a 50/50 használatához.';
      return;
    }
    if (!this.activeQuestion?.question.Options) return;

    this.hintSvc.use5050().subscribe({
      next: (res) => {
        // Egyenleg frissítése a szerver válasza alapján
        this.balance = res.NewBalance;
        this.modalBalance = res.NewBalance;
        this.modalBalanceAnim = 'loss';
        setTimeout(() => this.modalBalanceAnim = null, 1200);

        // 50/50: megtartjuk a helyes + 1 véletlenszerű rosszat
        const opts = this.activeQuestion!.question.Options!;
        const correct = opts.filter(o => o.IsCorrect);
        const wrong   = opts.filter(o => !o.IsCorrect);
        const keepWrong = wrong.length > 0
          ? [wrong[Math.floor(Math.random() * wrong.length)]]
          : [];
        this.activeQuestion!.question.Options = [...correct, ...keepWrong]
          .sort(() => Math.random() - 0.5);

        this.hintUsed = true;
      },
      error: (err) => {
        this.hintError = err.error?.message ?? 'Nem sikerült a 50/50 használata.';
      }
    });
  }

  // ─── Kód beküldés ──────────────────────────────────────────────
  openCodeSubmit(): void {
    this.showCodeSubmit = true;
    if (this.isMultiplayer) {
      this.codeInput = this.buildMultiCode();
    } else {
      this.codeInput = this.mergedDigits.join('');
    }
    this.submitResult = null;
    this.submitSuccess = false;
  }

  /** Multiplayer kód összerakása: session SolvedQuestions + lokális digitek kombinációja */
  private buildMultiCode(): string {
    const sortedSolved = this.multiState?.SolvedQuestions ?? [];
    return [...this.questions]
      .sort((a, b) => a.question.PositionX - b.question.PositionX)
      .map(qs => {
        const sessionItem = sortedSolved.find(s => s.id === qs.question.QuestionID);
        if (sessionItem) return String(sessionItem.digit);
        if (qs.digit !== null) return String(qs.digit);
        return '';
      })
      .join('');
  }

  closeCodeSubmit(): void {
    if (this.submitSuccess) return;
    this.showCodeSubmit = false;
  }

  submitCode(): void {
    if (!this.codeInput.trim()) return;
    this.submitLoading = true;
    this.submitResult = null;

    if (this.isMultiplayer) {
      // Multiplayer: a kódot a session SolvedQuestions + lokális digitek alapján rakjuk össze
      const multiCode = this.buildMultiCode();

      const given = this.codeInput.trim();

      if (given === multiCode) {
        this.submitLoading = false;
        this.submitResult = { correct: true, message: '🎉 Gratulálok! A kód helyes! (Multiplayer – ranglista nélkül)' };
        this.submitSuccess = true;
        this.timerSub?.unsubscribe();
        this.pollSub?.unsubscribe();
        // finish → backend Status = 'finished', a polling a másik félnél is észleli és navigál
        this.multiSvc.finish(this.multiSessionId).subscribe();
        setTimeout(() => this.router.navigate(['/game']), 3500);
      } else {
        this.submitLoading = false;
        this.submitResult = { correct: false, message: 'Hibás kód. Próbáld újra!' };
      }
      return;
    }

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
    if (this.isMultiplayer && this.multiSessionId &&
        (this.multiState?.Status === 'waiting' || this.multiState?.Status === 'playing')) {
      this.leaveMulti();
    } else {
      this.router.navigate(['/game']);
    }
  }

  kilepes(): void {
    this.auth.logout().subscribe();
  }

  getUsername(): string {
    return this.auth.getUser()?.Username ?? '';
  }

  // ─── Report panel ───────────────────────────────────────────────
  openReport(): void {
    this.showReport = true;
    this.reportTitle = '';
    this.reportCategory = 'bug';
    this.reportMessage = '';
    this.reportSent = false;
    this.reportError = '';
  }

  closeReport(): void {
    this.showReport = false;
  }

  submitReport(): void {
    if (!this.reportTitle.trim() || !this.reportMessage.trim()) return;
    this.reportLoading = true;
    this.reportError = '';
    this.reportService.createReport({
      Title:    this.reportTitle.trim(),
      Category: this.reportCategory,
      Message: this.reportMessage.trim(),
      Page: window.location.pathname,
    }).subscribe({
      next: () => {
        this.reportLoading = false;
        this.reportSent = true;
        setTimeout(() => this.closeReport(), 2000);
      },
      error: () => {
        this.reportLoading = false;
        this.reportError = 'Hiba történt a beküldés során.';
      }
    });
  }
}
