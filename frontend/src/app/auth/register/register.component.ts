import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule, AbstractControl } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { CommonModule } from '@angular/common';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  templateUrl: './register.component.html',
  styleUrls: ['../auth.css', './register.component.css']
})
export class RegisterComponent {

  form: FormGroup;
  hiba = '';
  toltes = false;

  constructor(
    private fb: FormBuilder,
    private auth: AuthService,
    private router: Router
  ) {
    this.form = this.fb.group({
      Username:             ['', [Validators.required, Validators.minLength(3), Validators.maxLength(50)]],
      Email:                ['', [Validators.required, Validators.email]],
      Password:             ['', [Validators.required, Validators.minLength(6)]],
      Password_confirmation: ['', Validators.required]
    }, { validators: this.jelszóEgyezés });
  }

  jelszóEgyezés(form: AbstractControl) {
    const jelszo  = form.get('Password')?.value;
    const megerősít = form.get('Password_confirmation')?.value;
    return jelszo === megerősít ? null : { nemEgyezik: true };
  }

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    this.toltes = true;
    this.hiba = '';

    this.auth.register(this.form.value).subscribe({
      next: (user) => {
        this.toltes = false;
        this.router.navigate([user.IsAdmin ? '/admin' : '/game']);
      },
      error: (err) => {
        this.toltes = false;
        this.hiba = err.error?.message ?? 'Regisztráció sikertelen!';
      }
    });
  }

}
