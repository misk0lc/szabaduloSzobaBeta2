import { Routes } from '@angular/router';
import { authGuard, guestGuard, adminGuard } from './guards/auth.guard';

export const routes: Routes = [
  {
    path: 'login',
    canActivate: [guestGuard],
    loadComponent: () => import('./auth/login/login.component').then(m => m.LoginComponent)
  },
  {
    path: 'register',
    canActivate: [guestGuard],
    loadComponent: () => import('./auth/register/register.component').then(m => m.RegisterComponent)
  },
  {
    path: 'game',
    canActivate: [authGuard],
    loadComponent: () => import('./pages/game/game.component').then(m => m.GameComponent)
  },
  {
    path: 'room/:id',
    canActivate: [authGuard],
    loadComponent: () => import('./pages/room/room.component').then(m => m.RoomComponent)
  },
  {
    path: 'room/:id/multi/:sessionId',
    canActivate: [authGuard],
    loadComponent: () => import('./pages/room/room.component').then(m => m.RoomComponent)
  },
  {
    path: 'leaderboard',
    canActivate: [authGuard],
    loadComponent: () => import('./pages/leaderboard/leaderboard.component').then(m => m.LeaderboardComponent)
  },
  {
    path: 'admin',
    canActivate: [adminGuard],
    loadComponent: () => import('./pages/admin/admin.component').then(m => m.AdminComponent)
  },
  { path: '', redirectTo: '/login', pathMatch: 'full' } as any,
  { path: '**', redirectTo: '/login' } as any
] as Routes;
