import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { AuthService } from '../../services/auth.service';
import { LeaderboardEntry } from '../../models/leaderboard.model';

@Component({
  selector: 'app-leaderboard',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './leaderboard.component.html',
  styleUrls: ['./leaderboard.component.css']
})
export class LeaderboardComponent implements OnInit {
  entries: LeaderboardEntry[] = [];
  loading = true;
  error = '';
  sortBy: 'score' | 'levels' | 'time' | 'hints' = 'score';
  animatedIn = false;
  private api = `http://${window.location.hostname}:8001/api`;

  constructor(
    private http: HttpClient,
    private auth: AuthService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.loading = true;
    this.error = '';
    this.http.get<LeaderboardEntry[]>(`${this.api}/leaderboard`).subscribe({
      next: (data) => {
        this.entries = data;
        this.loading = false;
        setTimeout(() => this.animatedIn = true, 50);
      },
      error: () => {
        this.error = 'Nem sikerült betölteni a rangsort.';
        this.loading = false;
      }
    });
  }

  setSortBy(s: 'score' | 'levels' | 'time' | 'hints'): void {
    this.sortBy = s;
  }

  get sorted(): LeaderboardEntry[] {
    return [...this.entries].sort((a, b) => {
      if (this.sortBy === 'score')  return b.Score - a.Score;
      if (this.sortBy === 'levels') return b.LevelsCompleted - a.LevelsCompleted;
      if (this.sortBy === 'time')   return a.TimeTotal - b.TimeTotal;
      if (this.sortBy === 'hints')  return a.HintsUsed - b.HintsUsed;
      return 0;
    });
  }

  get topScore(): number {
    return this.entries.length ? Math.max(...this.entries.map(e => e.Score)) : 0;
  }

  getScorePercent(score: number): number {
    return this.topScore ? Math.round((score / this.topScore) * 100) : 0;
  }

  get totalPlayers(): number { return this.entries.length; }

  get totalScore(): number {
    return this.entries.reduce((s, e) => s + e.Score, 0);
  }

  get avgScore(): number {
    if (!this.entries.length) return 0;
    return Math.round(this.totalScore / this.entries.length);
  }

  get avgTime(): string {
    if (!this.entries.length) return '–';
    const avg = this.entries.reduce((s, e) => s + e.TimeTotal, 0) / this.entries.length;
    return this.formatIdo(Math.round(avg));
  }

  get fewestHints(): number {
    const completed = this.entries.filter(e => e.LevelsCompleted > 0);
    if (!completed.length) return 0;
    return Math.min(...completed.map(e => e.HintsUsed));
  }

  get topLevels(): number {
    return this.entries.length ? Math.max(...this.entries.map(e => e.LevelsCompleted)) : 0;
  }

  getSajat(): number {
    const user = this.auth.getUser();
    return this.sorted.findIndex(e => e.UserID === user?.UserID) + 1;
  }

  visszaMegyek(): void {
    this.router.navigate(['/game']);
  }

  kilepes(): void {
    this.auth.logout().subscribe();
  }

  isAdmin(): boolean {
    return this.auth.isAdmin();
  }

  adminPanel(): void {
    this.router.navigate(['/admin']);
  }

  formatIdo(seconds: number): string {
    if (!seconds) return '–';
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return `${m}p ${s < 10 ? '0' + s : s}mp`;
  }
}
