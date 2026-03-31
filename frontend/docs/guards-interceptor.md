# Guardok és interceptor

## HTTP Interceptor

**Fájl:** `src/app/interceptors/auth.interceptor.ts`

Funkcionális HTTP interceptor (`HttpInterceptorFn`).

### Működés

1. **Headerek beállítása:**
   - `Accept: application/json`
   - `Content-Type: application/json`

2. **Token csatolása:**
   - Ha van `token` a `localStorage`-ban → `Authorization: Bearer <token>` header hozzáadása

3. **401 hiba kezelése:**
   - Ha a szerver 401-es választ ad → `token` és `user` törlése a `localStorage`-ból → átirányítás `/login`-ra

### Regisztráció

Az interceptor az `app.config.ts`-ben van regisztrálva:

```typescript
provideHttpClient(withInterceptors([authInterceptor]))
```

---

## Guardok

**Fájl:** `src/app/guards/auth.guard.ts`

Három funkcionális guard (`CanActivateFn`), mindegyik az `AuthService`-t injektálja.

### authGuard

```
Ha isLoggedIn() → engedélyez
Egyébként → átirányítás /login
```

**Használat:** `/game`, `/room/:id`, `/leaderboard`

### guestGuard

```
Ha NEM isLoggedIn() → engedélyez
Ha isLoggedIn() és isAdmin() → átirányítás /admin
Ha isLoggedIn() és NEM admin → átirányítás /game
```

**Használat:** `/login`, `/register`

### adminGuard

```
Ha isLoggedIn() ÉS isAdmin() → engedélyez
Egyébként → átirányítás /game
```

**Használat:** `/admin`

---

## Autentikációs folyamat teljes képe

### Bejelentkezés

```
1. Felhasználó kitölti az email/jelszó űrlapot
2. LoginComponent.onSubmit() → AuthService.login()
3. POST /api/login → szerver válasz: { token, user }
4. AuthService tárolja: localStorage.setItem('token', token)
                         localStorage.setItem('user', JSON.stringify(user))
5. guestGuard átirányít: admin → /admin, normál → /game
```

### Hitelesített kérés

```
1. Szolgáltatás HTTP kérést indít (pl. LevelService.getLevels())
2. authInterceptor elfogja a kérést
3. localStorage-ból kinyeri a tokent
4. Hozzáadja: Authorization: Bearer <token>
5. Kérés elküldése a szervernek
6. Ha 401 válasz → automatikus kijelentkezés + /login átirányítás
```

### Kijelentkezés

```
1. Felhasználó rákattint a kijelentkezés gombra
2. AuthService.logout() → POST /api/logout
3. localStorage.removeItem('token')
   localStorage.removeItem('user')
4. Router.navigate(['/login'])
```

---

## Biztonsági megfontolások

| Szempont                 | Megvalósítás                                        |
| ------------------------ | --------------------------------------------------- |
| Token tárolás            | `localStorage` (böngésző bezárásával is megmarad)   |
| Token lejárat            | Nincs automatikus lejárat (szerver oldalon sem)     |
| Egyetlen aktív token     | Bejelentkezéskor minden korábbi token törlődik      |
| 401 kezelés              | Automatikus kijelentkezés interceptorból             |
| Útvonal védelem          | Guard-ok minden védett útvonalat lefednek           |
| Admin hozzáférés         | Kétszintű védelem: frontenden (adminGuard) + backend (is_admin middleware) |
