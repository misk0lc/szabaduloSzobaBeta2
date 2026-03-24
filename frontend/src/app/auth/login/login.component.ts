import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { CommonModule } from '@angular/common';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent {

  form: FormGroup;
  hiba = '';
  toltes = false;

  constructor(
    private fb: FormBuilder,
    private auth: AuthService,
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

}
