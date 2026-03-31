# Projektstruktúra

## Teljes fájlfa

```
src/
├── index.html                              # Gyökér HTML, <app-root>
├── main.ts                                 # Bootstrap appConfig-gal
├── styles.css                              # Globális stílusok (reset, report modálok)
├── environments/
│   └── environment.ts                      # API URL konfiguráció
└── app/
    ├── app.component.ts                    # Gyökér komponens (<router-outlet>)
    ├── app.component.html                  # Csak <router-outlet>
    ├── app.config.ts                       # Alkalmazás providerek (router, HTTP, interceptor)
    ├── app.routes.ts                       # Összes útvonal definíció
    │
    ├── auth/                               # Autentikációs komponensek
    │   ├── auth.css                        # Közös auth stílusok (login + register)
    │   ├── login/
    │   │   ├── login.component.ts          # Bejelentkezés + nyilvános hibajelentés
    │   │   ├── login.component.html
    │   │   └── login.component.css
    │   └── register/
    │       ├── register.component.ts       # Regisztráció
    │       ├── register.component.html
    │       └── register.component.css
    │
    ├── guards/
    │   └── auth.guard.ts                   # authGuard, guestGuard, adminGuard
    │
    ├── interceptors/
    │   └── auth.interceptor.ts             # Bearer token + 401 kezelés
    │
    ├── models/                             # TypeScript interfészek
    │   ├── hint.model.ts                   # Hint, BuyHintResponse
    │   ├── leaderboard.model.ts            # LeaderboardEntry
    │   ├── level.model.ts                  # Level, LevelDetail
    │   ├── progress.model.ts               # SubmitCodeRequest/Response
    │   ├── question.model.ts               # Question, CheckAnswerRequest/Response
    │   └── user.model.ts                   # User, AuthResponse, LoginRequest, RegisterRequest
    │
    ├── pages/                              # Fő oldal komponensek
    │   ├── admin/
    │   │   ├── admin.component.ts          # Admin panel (5 tab)
    │   │   ├── admin.component.html
    │   │   └── admin.component.css
    │   ├── game/
    │   │   ├── game.component.ts           # Szoba választó / főoldal
    │   │   ├── game.component.html
    │   │   └── game.component.css
    │   ├── leaderboard/
    │   │   ├── leaderboard.component.ts    # Ranglista
    │   │   ├── leaderboard.component.html
    │   │   └── leaderboard.component.css
    │   └── room/
    │       ├── room.component.ts           # Játékszoba (kérdések, időzítő, multiplayer)
    │       ├── room.component.html
    │       └── room.component.css
    │
    └── services/                           # API kommunikáció
        ├── admin.service.ts                # Admin CRUD műveletek
        ├── auth.service.ts                 # Bejelentkezés, regisztráció, token kezelés
        ├── hint.service.ts                 # Segítségek lekérése/vásárlása
        ├── level.service.ts                # Szobák lekérése
        ├── multiplayer.service.ts          # Többjátékos: csatlakozás, polling, megoldás
        ├── progress.service.ts             # Kód beküldés, progress reset
        ├── question.service.ts             # Kérdések lekérése, válasz ellenőrzés
        └── report.service.ts              # Hibajelentés (hitelesített + nyilvános)
```

## Statikus eszközök

```
public/
└── rooms/
    ├── room1/background.png
    ├── room2/background.png
    ├── ...
    └── room15/background.png
```

Minden szobának van háttérképe, amelyet a szoba `BackgroundUrl` mezője hivatkozik.

---

## Alkalmazás indítás (Bootstrap)

### main.ts

```typescript
bootstrapApplication(AppComponent, appConfig);
```

### app.config.ts

Az alkalmazás konfigurációja 3 providert tartalmaz:

1. **`provideZoneChangeDetection({ eventCoalescing: true })`** — Optimalizált change detection
2. **`provideRouter(routes)`** — Routing lazy loading-gal
3. **`provideHttpClient(withInterceptors([authInterceptor]))`** — HTTP kliens auth interceptorral

### app.component.ts

Minimális gyökér komponens — csak a `<router-outlet>`-et rendereli.

---

## Komponens architektúra

Az alkalmazás **standalone Angular komponenseket** használ (nincs NgModule). Minden komponens saját maga deklarálja az importjait:

```typescript
@Component({
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  // ...
})
```

A template szintaxis az Angular 17+ **új kontrollfolyam direktívákat** használja:
- `@if / @else` — feltételes renderelés
- `@for` — iteráció `track` kifejezéssel
