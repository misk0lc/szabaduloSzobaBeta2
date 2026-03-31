import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { Observable, tap, map } from 'rxjs';
import { User, AuthResponse, LoginRequest, RegisterRequest } from '../models/user.model';

@Injectable({ providedIn: 'root' })
export class AuthService {

  private api = `http://${window.location.hostname}:8001/api`;

  constructor(private http: HttpClient, private router: Router) {}

  login(data: LoginRequest): Observable<User> {
    return this.http.post<AuthResponse>(`${this.api}/login`, data).pipe(
      tap(res => {
        localStorage.setItem('token', res.token);
        localStorage.setItem('user', JSON.stringify(res.user));
      }),
      map(res => res.user)
    );
  }

  register(data: RegisterRequest): Observable<User> {
    const { Username, Email, Password } = data;
    return this.http.post<AuthResponse>(`${this.api}/register`, { Username, Email, Password }).pipe(
      tap(res => {
        localStorage.setItem('token', res.token);
        localStorage.setItem('user', JSON.stringify(res.user));
      }),
      map(res => res.user)
    );
  }

  logout(): Observable<void> {
    return this.http.post<void>(`${this.api}/logout`, {}).pipe(
      tap(() => {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        this.router.navigate(['/login']);
      })
    );
  }

  getUser(): User | null {
    const raw = localStorage.getItem('user');
    return raw ? JSON.parse(raw) : null;
  }

  getToken(): string | null {
    return localStorage.getItem('token');
  }

  isLoggedIn(): boolean {
    return !!this.getToken() && !!this.getUser();
  }

  isAdmin(): boolean {
    return this.getUser()?.IsAdmin === true;
  }

  getMe(): Observable<User> {
    return this.http.get<User>(`${this.api}/me`);
  }

  changePassword(currentPassword: string, newPassword: string): Observable<{ message: string }> {
    return this.http.put<{ message: string }>(`${this.api}/me/password`, {
      current_password: currentPassword,
      new_password: newPassword,
    });
  }

}
