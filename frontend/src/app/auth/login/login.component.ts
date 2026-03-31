import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { CommonModule } from '@angular/common';
import { AuthService } from '../../services/auth.service';
import { ReportService } from '../../services/report.service';

export const REPORT_CATEGORIES = [
  { value: 'forgotten-password', label: '🔑 Elfelejtett jelszó' },
  { value: 'bug',                label: '🐛 Bug / Hiba bejelentés' },
  { value: 'account',            label: '👤 Fiókkal kapcsolatos' },
  { value: 'question',           label: '❓ Kérdés / Visszajelzés' },
  { value: 'other',              label: '📝 Egyéb' },
];

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, FormsModule, RouterLink],
  templateUrl: './login.component.html',
  styleUrls: ['../auth.css', './login.component.css']
})
export class LoginComponent {

  form: FormGroup;
  hiba = '';
  toltes = false;

  // Report panel
  showReport = false;
  reportCategories = REPORT_CATEGORIES;
  reportCategory = 'bug';
  reportContactEmail = '';
  reportTitle = '';
  reportMessage = '';
  reportLoading = false;
  reportSent = false;
  reportError = '';

  constructor(
    private fb: FormBuilder,
    private auth: AuthService,
    private reportService: ReportService,
    private router: Router
  ) {
    this.form = this.fb.group({
      Email:    ['', [Validators.required, Validators.email]],
      Password: ['', [Validators.required, Validators.minLength(6)]]
    });
  }

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    this.toltes = true;
    this.hiba = '';

    this.auth.login(this.form.value).subscribe({
      next: (user) => {
        this.toltes = false;
        this.router.navigate([user.IsAdmin ? '/admin' : '/game']);
      },
      error: (err) => {
        this.toltes = false;
        this.hiba = err.error?.message ?? 'Hibás email vagy jelszó!';
      }
    });
  }

  openReport(): void {
    this.showReport = true;
    this.reportCategory = 'bug';
    this.reportContactEmail = '';
    this.reportTitle = '';
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
    this.reportService.createPublicReport({
      Title:        this.reportTitle.trim(),
      Category:     this.reportCategory,
      ContactEmail: this.reportContactEmail.trim() || undefined,
      Message:      this.reportMessage.trim(),
      Page:         'login',
    }).subscribe({
      next: () => {
        this.reportLoading = false;
        this.reportSent = true;
        setTimeout(() => this.closeReport(), 2500);
      },
      error: () => {
        this.reportLoading = false;
        this.reportError = 'Hiba történt a beküldés során. Kérjük próbáld újra.';
      }
    });
  }
}