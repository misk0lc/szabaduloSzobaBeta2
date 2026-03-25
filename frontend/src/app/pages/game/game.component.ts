import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AuthService } from '../../services/auth.service';
import { LevelService } from '../../services/level.service';
import { ReportService } from '../../services/report.service';
import { Level } from '../../models/level.model';

export const REPORT_CATEGORIES = [
  { value: 'bug',      label: '🐛 Bug / Hiba' },
  { value: 'question', label: '❓ Kérdés' },
  { value: 'other',    label: '📝 Egyéb' },
];

@Component({
  selector: 'app-game',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './game.component.html',
  styleUrl: './game.component.css'
})
export class GameComponent implements OnInit {

  user;
  levels: Level[] = [];
  toltes = true;
  hiba = '';

  // Report panel
  showReport = false;
  reportCategories = REPORT_CATEGORIES;
  reportCategory = 'bug';
  reportTitle = '';
  reportMessage = '';
  reportLoading = false;
  reportSent = false;
  reportError = '';

  // Jelszóváltó panel
  showPasswordChange = false;
  pwCurrent = '';
  pwNew = '';
  pwConfirm = '';
  pwLoading = false;
  pwSuccess = '';
  pwError = '';

  constructor(
    private auth: AuthService,
    private levelService: LevelService,
    private reportService: ReportService,
    private router: Router
  ) {
    this.user = this.auth.getUser();
  }

  ngOnInit(): void {
    this.levelService.getLevels().subscribe({
      next: (levels) => {
        this.levels = levels;
        this.toltes = false;
      },
      error: () => {
        this.hiba = 'Nem sikerült betölteni a pályákat.';
        this.toltes = false;
      }
    });
  }

  szobaValaszt(level: Level): void {
    if (!level.IsUnlocked) return;
    this.router.navigate(['/room', level.LevelID]);
  }

  goLeaderboard(): void {
    this.router.navigate(['/leaderboard']);
  }

  goAdmin(): void {
    this.router.navigate(['/admin']);
  }

  logout(): void {
    this.auth.logout().subscribe();
  }

  openReport(): void {
    this.showReport = true;
    this.reportCategory = 'bug';
    this.reportTitle = '';
    this.reportMessage = '';
    this.reportSent = false;
    this.reportError = '';
  }

  closeReport(): void {
    this.showReport = false;
  }

  openPasswordChange(): void {
    this.showPasswordChange = true;
    this.pwCurrent = '';
    this.pwNew = '';
    this.pwConfirm = '';
    this.pwSuccess = '';
    this.pwError = '';
  }

  closePasswordChange(): void {
    this.showPasswordChange = false;
  }

  submitPasswordChange(): void {
    this.pwError = '';
    this.pwSuccess = '';

    if (!this.pwCurrent || !this.pwNew || !this.pwConfirm) {
      this.pwError = 'Minden mezőt ki kell tölteni!';
      return;
    }
    if (this.pwNew.length < 6) {
      this.pwError = 'Az új jelszónak legalább 6 karakter kell legyen!';
      return;
    }
    if (this.pwNew !== this.pwConfirm) {
      this.pwError = 'A két új jelszó nem egyezik!';
      return;
    }

    this.pwLoading = true;
    this.auth.changePassword(this.pwCurrent, this.pwNew).subscribe({
      next: (res) => {
        this.pwLoading = false;
        this.pwSuccess = res.message;
        setTimeout(() => this.closePasswordChange(), 2000);
      },
      error: (err) => {
        this.pwLoading = false;
        this.pwError = err.error?.message ?? 'Hiba történt a jelszóváltás során.';
      }
    });
  }

  submitReport(): void {
    if (!this.reportTitle.trim() || !this.reportMessage.trim()) return;
    this.reportLoading = true;
    this.reportError = '';
    this.reportService.createReport({
      Title:    this.reportTitle.trim(),
      Category: this.reportCategory,
      Message:  this.reportMessage.trim(),
      Page:     window.location.pathname,
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

  getAllapot(level: Level): 'completed' | 'active' | 'locked' {
    if (level.IsCompleted) return 'completed';
    if (level.IsUnlocked) return 'active';
    return 'locked';
  }

  getBgStyle(level: Level): string {
    if (level.BackgroundUrl) {
      return `url(${level.BackgroundUrl})`;
    }
    const n = level.OrderNumber;
    if (n >= 1 && n <= 5) {
      return `url(/rooms/room${n}/background.png)`;
    }
    return 'none';
  }
}