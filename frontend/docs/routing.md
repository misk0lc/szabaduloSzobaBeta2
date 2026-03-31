# Routing és navigáció

## Útvonal tábla

**Fájl:** `src/app/app.routes.ts`

| Útvonal                        | Komponens              | Guard        | Lazy loaded |
| ------------------------------ | ---------------------- | ------------ | ----------- |
| `/login`                       | `LoginComponent`       | `guestGuard` | Igen        |
| `/register`                    | `RegisterComponent`    | `guestGuard` | Igen        |
| `/game`                        | `GameComponent`        | `authGuard`  | Igen        |
| `/room/:id`                    | `RoomComponent`        | `authGuard`  | Igen        |
| `/room/:id/multi/:sessionId`   | `RoomComponent`        | `authGuard`  | Igen        |
| `/leaderboard`                 | `LeaderboardComponent` | `authGuard`  | Igen        |
| `/admin`                       | `AdminComponent`       | `adminGuard` | Igen        |
| `` (üres)                      | → átirányítás `/login` | —            | —           |
| `**` (wildcard)                | → átirányítás `/login` | —            | —           |

Minden komponens **lazy-loaded** a `loadComponent()` segítségével, ami csökkenti a kezdeti betöltési időt.

---

## Guardok

**Fájl:** `src/app/guards/auth.guard.ts`

Három funkcionális guard (`CanActivateFn`):

### authGuard

Hitelesített felhasználók számára engedélyezi az útvonalat.

- Ha be van jelentkezve → engedélyez
- Ha nincs → átirányítás `/login`-ra

**Használat:** `/game`, `/room/:id`, `/leaderboard`

### guestGuard

Csak nem bejelentkezett felhasználók számára.

- Ha nincs bejelentkezve → engedélyez
- Ha be van jelentkezve:
  - Admin → átirányítás `/admin`-ra
  - Normál felhasználó → átirányítás `/game`-re

**Használat:** `/login`, `/register`

### adminGuard

Admin jogosultságot igénylő útvonalakhoz.

- Ha be van jelentkezve ÉS admin → engedélyez
- Egyébként → átirányítás `/game`-re

**Használat:** `/admin`

---

## Navigációs folyamat

```
Nem bejelentkezett felhasználó:
  / → /login
  /game → /login (authGuard)
  /admin → /login (authGuard)
  /login → OK (guestGuard)
  /register → OK (guestGuard)

Bejelentkezett (normál) felhasználó:
  / → /login → /game (guestGuard átirányít)
  /game → OK
  /room/1 → OK
  /leaderboard → OK
  /admin → /game (adminGuard)
  /login → /game (guestGuard átirányít)

Bejelentkezett admin:
  /login → /admin (guestGuard átirányít)
  /admin → OK
  /game → OK
  /room/1 → OK
```

---

## Szoba útvonalak

A `RoomComponent` kétféle módban indulhat:

### Szóló mód
```
/room/:id
```
Az `:id` paraméter a szoba `LevelID`-ja. Az `isMultiplayer` flag `false`.

### Többjátékos mód
```
/room/:id/multi/:sessionId
```
Az `:id` és `:sessionId` is URL paraméterből olvasódik. Az `isMultiplayer` flag `true`, és a polling elindul.
