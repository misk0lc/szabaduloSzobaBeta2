import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

export const authGuard: CanActivateFn = () => {
  const auth = inject(AuthService);
  const router: Router = inject(Router);

  if (auth.isLoggedIn()) return true;
  return router.createUrlTree(['/login']);
};

export const guestGuard: CanActivateFn = () => {
  const auth = inject(AuthService);
  const router: Router = inject(Router);

  if (!auth.isLoggedIn()) return true;
  return router.createUrlTree([auth.isAdmin() ? '/admin' : '/game']);
};

export const adminGuard: CanActivateFn = () => {
  const auth = inject(AuthService);
  const router: Router = inject(Router);

  if (auth.isLoggedIn() && auth.isAdmin()) return true;
  return router.createUrlTree(['/game']);
};
