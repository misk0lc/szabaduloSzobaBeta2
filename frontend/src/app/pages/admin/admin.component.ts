import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import {
  AdminService,
  AdminUser, AdminLevel, AdminQuestion, AdminQuestionOption, AdminStats, AdminReport
} from '../../services/admin.service';

type Tab = 'dashboard' | 'users' | 'levels' | 'questions' | 'reports';

@Component({
  selector: 'app-admin',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './admin.component.html',
  styleUrls: ['./admin.component.css']
})
export class AdminComponent implements OnInit {

  activeTab: Tab = 'dashboard';
  user: any = null;

  // Dashboard
  stats: AdminStats | null = null;

  // Users
  users: AdminUser[] = [];
  userSearch = '';
  editingUser: Partial<AdminUser & { Password?: string }> | null = null;

  // Levels
  levels: AdminLevel[] = [];
  editingLevel: Partial<AdminLevel> | null = null;
  isNewLevel = false;

  // Questions
  questions: AdminQuestion[] = [];
  questionLevelFilter = 0;
  editingQuestion: Partial<AdminQuestion> | null = null;
  isNewQuestion = false;

  // Reports
  reports: AdminReport[] = [];
  reportsFilter: '' | 'new' | 'seen' | 'resolved' = '';

  // Toast
  toast = '';
  toastType: 'ok' | 'err' = 'ok';

  constructor(
    private auth: AuthService,
    private admin: AdminService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.user = this.auth.getUser();
    this.loadStats();
  }

  switchTab(tab: Tab): void {
    this.activeTab = tab;
    this.closeAllEditors();
    if (tab === 'dashboard') this.loadStats();
    if (tab === 'users') this.loadUsers();
    if (tab === 'levels') this.loadLevels();
    if (tab === 'questions') this.loadQuestions();
    if (tab === 'reports') this.loadReports();
  }

  closeAllEditors(): void {
    this.editingUser = null;
    this.editingLevel = null;
    this.editingQuestion = null;
    this.isNewLevel = false;
    this.isNewQuestion = false;
  }

  showToast(msg: string, type: 'ok' | 'err' = 'ok'): void {
    this.toast = msg;
    this.toastType = type;
    setTimeout(() => this.toast = '', 3000);
  }

  // ─── Dashboard ──────────────────────────────────
  loadStats(): void {
    this.admin.getStats().subscribe({
      next: s => this.stats = s,
      error: () => this.showToast('Nem sikerült betölteni a statisztikákat.', 'err')
    });
  }

  get correctRate(): number {
    if (!this.stats || !this.stats.totalAnswers) return 0;
    return Math.round((this.stats.correctAnswers / this.stats.totalAnswers) * 100);
  }

  // ─── Users ──────────────────────────────────────
  loadUsers(): void {
    this.admin.getUsers(this.userSearch).subscribe({
      next: u => this.users = u,
      error: () => this.showToast('Felhasználók betöltése sikertelen.', 'err')
    });
  }

  searchUsers(): void { this.loadUsers(); }

  editUser(u: AdminUser): void {
    this.editingUser = { ...u };
  }

  saveUser(): void {
    if (!this.editingUser?.UserID) return;
    this.admin.updateUser(this.editingUser.UserID, this.editingUser).subscribe({
      next: () => { this.showToast('Felhasználó mentve.'); this.editingUser = null; this.loadUsers(); },
      error: () => this.showToast('Mentés sikertelen.', 'err')
    });
  }

  toggleUserActive(u: AdminUser): void {
    this.admin.updateUser(u.UserID, { IsActive: !u.IsActive } as any).subscribe({
      next: () => { this.showToast(u.IsActive ? 'Felhasználó inaktiválva.' : 'Felhasználó aktiválva.'); this.loadUsers(); },
      error: () => this.showToast('Művelet sikertelen.', 'err')
    });
  }

  deleteUser(u: AdminUser): void {
    if (!confirm(`Biztosan törlöd "${u.Username}" felhasználót?`)) return;
    this.admin.deleteUser(u.UserID).subscribe({
      next: () => { this.showToast('Felhasználó törölve.'); this.loadUsers(); },
      error: () => this.showToast('Törlés sikertelen.', 'err')
    });
  }

  resetUserProgress(u: AdminUser): void {
    if (!confirm(`Biztosan törlöd "${u.Username}" haladását? (progress, válaszok, leaderboard, egyenleg nullázódik)`)) return;
    this.admin.resetUserProgress(u.UserID).subscribe({
      next: () => { this.showToast(`${u.Username} haladása törölve.`); this.loadUsers(); },
      error: () => this.showToast('Reset sikertelen.', 'err')
    });
  }

  // ─── Levels ──────────────────────────────────────
  loadLevels(): void {
    this.admin.getLevels().subscribe({
      next: l => this.levels = l,
      error: () => this.showToast('Pályák betöltése sikertelen.', 'err')
    });
  }

  newLevel(): void {
    this.editingLevel = { Name: '', Description: '', OrderNumber: this.levels.length + 1, IsActive: true, BackgroundUrl: '' };
    this.isNewLevel = true;
  }

  editLevel(l: AdminLevel): void {
    this.editingLevel = { ...l };
    this.isNewLevel = false;
  }

  saveLevel(): void {
    if (!this.editingLevel) return;
    const isNew = this.isNewLevel;
    const obs = isNew
      ? this.admin.createLevel(this.editingLevel)
      : this.admin.updateLevel(this.editingLevel.LevelID!, this.editingLevel);

    obs.subscribe({
      next: () => {
        this.showToast(isNew ? 'Szoba létrehozva.' : 'Szoba mentve.');
        this.editingLevel = null;
        this.loadLevels();
      },
      error: (err: any) => {
        const errors = err?.error?.errors;
        if (errors) {
          const first = Object.values(errors)[0] as string[];
          this.showToast(first?.[0] ?? 'Mentés sikertelen.', 'err');
        } else {
          this.showToast(err?.error?.message ?? 'Mentés sikertelen.', 'err');
        }
      }
    });
  }

  deleteLevel(l: AdminLevel): void {
    if (!confirm(`Biztosan törlöd "${l.Name}" pályát?`)) return;
    this.admin.deleteLevel(l.LevelID).subscribe({
      next: () => { this.showToast('Szoba törölve.'); this.loadLevels(); },
      error: () => this.showToast('Törlés sikertelen.', 'err')
    });
  }

  // ─── Questions ───────────────────────────────────
  loadQuestions(): void {
    const filter = this.questionLevelFilter || undefined;
    this.admin.getQuestions(filter).subscribe({
      next: q => this.questions = q,
      error: () => this.showToast('Kérdések betöltése sikertelen.', 'err')
    });
    if (!this.levels.length) this.admin.getLevels().subscribe(l => this.levels = l);
  }

  newQuestion(): void {
    this.editingQuestion = {
      LevelID: this.questionLevelFilter || (this.levels[0]?.LevelID ?? 1),
      QuestionText: '', CorrectAnswer: '', RewardDigit: 0, MoneyReward: 50,
      PositionX: 1, PositionY: 1,
      options: [
        { OptionText: '', IsCorrect: true },
        { OptionText: '', IsCorrect: false },
        { OptionText: '', IsCorrect: false },
        { OptionText: '', IsCorrect: false },
      ]
    };
    this.isNewQuestion = true;
  }

  editQuestion(q: AdminQuestion): void {
    this.editingQuestion = {
      ...q,
      options: q.options?.length
        ? q.options.map(o => ({ OptionText: o.OptionText, IsCorrect: o.IsCorrect }))
        : [
            { OptionText: '', IsCorrect: true },
            { OptionText: '', IsCorrect: false },
            { OptionText: '', IsCorrect: false },
            { OptionText: '', IsCorrect: false },
          ]
    };
    this.isNewQuestion = false;
  }

  setCorrectOption(index: number): void {
    if (!this.editingQuestion?.options) return;
    this.editingQuestion.options = this.editingQuestion.options.map((o, i) => ({
      ...o,
      IsCorrect: i === index
    }));
    // sync CorrectAnswer from the correct option
    this.editingQuestion.CorrectAnswer = this.editingQuestion.options[index].OptionText;
  }

  onOptionTextChange(index: number): void {
    if (!this.editingQuestion?.options) return;
    if (this.editingQuestion.options[index].IsCorrect) {
      this.editingQuestion.CorrectAnswer = this.editingQuestion.options[index].OptionText;
    }
  }

  saveQuestion(): void {
    if (!this.editingQuestion) return;
    const obs = this.isNewQuestion
      ? this.admin.createQuestion(this.editingQuestion)
      : this.admin.updateQuestion(this.editingQuestion.QuestionID!, this.editingQuestion);

    obs.subscribe({
      next: () => { this.showToast(this.isNewQuestion ? 'Kérdés létrehozva.' : 'Kérdés mentve.'); this.editingQuestion = null; this.loadQuestions(); },
      error: () => this.showToast('Mentés sikertelen.', 'err')
    });
  }

  deleteQuestion(q: AdminQuestion): void {
    if (!confirm('Biztosan törlöd ezt a kérdést?')) return;
    this.admin.deleteQuestion(q.QuestionID).subscribe({
      next: () => { this.showToast('Kérdés törölve.'); this.loadQuestions(); },
      error: () => this.showToast('Törlés sikertelen.', 'err')
    });
  }

  // ─── Reports ─────────────────────────────────────
  loadReports(): void {
    this.admin.getReports(this.reportsFilter || undefined).subscribe({
      next: r => this.reports = r,
      error: () => this.showToast('Bejelentések betöltése sikertelen.', 'err')
    });
  }

  filterReports(f: '' | 'new' | 'seen' | 'resolved'): void {
    this.reportsFilter = f;
    this.loadReports();
  }

  updateReportStatus(r: AdminReport, status: 'new' | 'seen' | 'resolved'): void {
    this.admin.updateReport(r.ReportID, status).subscribe({
      next: () => { this.showToast('Státusz frissítve.'); this.loadReports(); },
      error: () => this.showToast('Frissítés sikertelen.', 'err')
    });
  }

  deleteReport(r: AdminReport): void {
    if (!confirm('Biztosan törlöd ezt a bejelentést?')) return;
    this.admin.deleteReport(r.ReportID).subscribe({
      next: () => { this.showToast('Bejelentés törölve.'); this.loadReports(); },
      error: () => this.showToast('Törlés sikertelen.', 'err')
    });
  }

  // ─── Navigation ──────────────────────────────────
  goGame(): void { this.router.navigate(['/game']); }
  goLeaderboard(): void { this.router.navigate(['/leaderboard']); }

  logout(): void {
    this.auth.logout().subscribe();
  }
}
