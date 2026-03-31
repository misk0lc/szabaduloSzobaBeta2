# Szabadulószoba – Frontend Dokumentáció

## Tartalomjegyzék

1. [Áttekintés](#1-áttekintés)
2. [Technológiai stack](#2-technológiai-stack)
3. [Telepítés és beállítás](#3-telepítés-és-beállítás)
4. [Projektstruktúra](#4-projektstruktúra)
5. [Bootstrap és alkalmazás konfiguráció](#5-bootstrap-és-alkalmazás-konfiguráció)
6. [Routing és navigáció](#6-routing-és-navigáció)
7. [Guardok és interceptor](#7-guardok-és-interceptor)
8. [Modellek és interfészek](#8-modellek-és-interfészek)
9. [Szolgáltatások (Services)](#9-szolgáltatások-services)
10. [Komponensek](#10-komponensek)
11. [Stílusrendszer](#11-stílusrendszer)

---

## 1. Áttekintés

A Szabadulószoba frontend egy **Angular 19** alapú single-page alkalmazás (SPA), amely egy interaktív szabadulószoba játékot valósít meg. A felhasználók szobákat választanak, kérdésekre válaszolnak, számjegyeket gyűjtenek, és kódokat adnak be a szobák teljesítéséhez.

A rendszer támogatja az **egyjátékos** és **többjátékos** módot, **ranglistát**, **admin panelt** és **hibajelentő rendszert**.

A felület nyelve **magyar**.

---

## 2. Technológiai stack

| Komponens        | Technológia                             |
| ---------------- | --------------------------------------- |
| Framework        | Angular 19.2.0                          |
| Nyelv            | TypeScript 5.7.2                        |
| Build rendszer   | @angular-devkit/build-angular           |
| Csomagkezelő     | npm                                     |
| HTTP kliens      | Angular HttpClient + interceptor        |
| Routing          | Angular Router (lazy loading)           |
| Formok           | Angular FormsModule (template-driven)   |
| Reaktív könyvtár | RxJS ~7.8.0                             |

### 2.1 Függőségek

**Futtatási:**
- `@angular/common`, `@angular/core`, `@angular/forms`, `@angular/router` — ^19.2.0
- `rxjs ~7.8.0`
- `zone.js ~0.15.0`

**Fejlesztői:**
- `@angular-devkit/build-angular ^19.2.19`
- `@angular/cli ^19.2.19`
- `typescript ~5.7.2`
- Karma + Jasmine tesztelés

---

## 3. Telepítés és beállítás

### 3.1 Előfeltételek

- Node.js (LTS ajánlott)
- npm

### 3.2 Telepítési lépések

```bash
# 1. Navigálás a frontend mappába
cd frontend

# 2. Függőségek telepítése
npm install

# 3. Fejlesztői szerver indítása
npm start

# 4. Produkciós build
npm run build
```

### 3.3 Elérhető scriptek

| Script  | Parancs                                        | Leírás                     |
| ------- | ---------------------------------------------- | -------------------------- |
| `start` | `ng serve`                                     | Fejlesztői szerver          |
| `build` | `ng build`                                     | Produkciós build            |
| `watch` | `ng build --watch --configuration development` | Fejlesztői build figyeléssel |
| `test`  | `ng test`                                      | Unit tesztek (Karma)        |

### 3.4 Build konfiguráció

| Beállítás              | Érték                |
| ---------------------- | -------------------- |
| Output mappa           | `dist/frontend`      |
| Globális stílusok      | `src/styles.css`     |
| Statikus eszközök      | `public/`            |
| Kezdeti csomag limit   | 500 kB (warn), 1 MB (error) |
| Komponens CSS limit    | 20 kB (warn), 40 kB (error) |

### 3.5 API kapcsolat

Az alkalmazás dinamikusan építi fel az API URL-t:

```typescript
http://${window.location.hostname}:8001/api
```

Ez biztosítja, hogy bármely hostról elérhető legyen anélkül, hogy environment beállítást kellene módosítani.

---

## 4. Projektstruktúra

```
src/
├── index.html                              # Gyökér HTML (<app-root>)
├── main.ts                                 # Bootstrap
├── styles.css                              # Globális stílusok
├── environments/
│   └── environment.ts                      # API URL konfiguráció
└── app/
    ├── app.component.ts                    # Gyökér komponens (<router-outlet>)
    ├── app.component.html                  # Csak <router-outlet>
    ├── app.config.ts                       # Alkalmazás providerek
    ├── app.routes.ts                       # Útvonal definíciók
    ├── auth/                               # Autentikációs komponensek
    │   ├── auth.css                        # Közös auth stílusok
    │   ├── login/                          # Bejelentkezés + nyilvános hibajelentés
    │   └── register/                       # Regisztráció
    ├── guards/
    │   └── auth.guard.ts                   # authGuard, guestGuard, adminGuard
    ├── interceptors/
    │   └── auth.interceptor.ts             # Bearer token + 401 kezelés
    ├── models/                             # TypeScript interfészek
    │   ├── hint.model.ts
    │   ├── leaderboard.model.ts
    │   ├── level.model.ts
    │   ├── progress.model.ts
    │   ├── question.model.ts
    │   └── user.model.ts
    ├── pages/                              # Fő oldal komponensek
    │   ├── admin/                          # Admin panel (5 tab)
    │   ├── game/                           # Szoba választó (főoldal)
    │   ├── leaderboard/                    # Ranglista
    │   └── room/                           # Játékszoba
    └── services/                           # API kommunikáció
        ├── admin.service.ts
        ├── auth.service.ts
        ├── hint.service.ts
        ├── level.service.ts
        ├── multiplayer.service.ts
        ├── progress.service.ts
        ├── question.service.ts
        └── report.service.ts
```

**Statikus eszközök:**
```
public/rooms/room1/background.png ... room15/background.png
```

---

## 5. Bootstrap és alkalmazás konfiguráció

### 5.1 main.ts

```typescript
bootstrapApplication(AppComponent, appConfig);
```

### 5.2 app.config.ts

Az alkalmazás 3 providert regisztrál:

1. **`provideZoneChangeDetection({ eventCoalescing: true })`** — Optimalizált change detection
2. **`provideRouter(routes)`** — Routing lazy loading támogatással
3. **`provideHttpClient(withInterceptors([authInterceptor]))`** — HTTP kliens auth interceptorral

### 5.3 Komponens architektúra

Az alkalmazás Angular 17+ **standalone komponenseket** használ (nincs NgModule). Minden komponens saját maga deklarálja az importjait:

```typescript
@Component({
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
})
```

A template szintaxis az Angular 17+ **új kontrollfolyam direktívákat** használja:
- `@if / @else` — feltételes renderelés
- `@for` — iteráció `track` kifejezéssel

---

## 6. Routing és navigáció

### 6.1 Útvonal tábla

Fájl: `src/app/app.routes.ts`

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

Minden komponens **lazy-loaded** a `loadComponent()` segítségével.

### 6.2 Navigációs folyamat

```
Nem bejelentkezett felhasználó:
  /game → /login (authGuard átirányít)
  /login → OK (guestGuard engedélyez)

Bejelentkezett normál felhasználó:
  /login → /game (guestGuard átirányít)
  /game → OK
  /room/1 → OK
  /admin → /game (adminGuard átirányít)

Bejelentkezett admin:
  /login → /admin (guestGuard átirányít)
  /admin → OK
  /game → OK
```

### 6.3 Szoba útvonalak

A `RoomComponent` két módban indulhat:

- **Szóló mód:** `/room/:id` — `isMultiplayer = false`
- **Többjátékos mód:** `/room/:id/multi/:sessionId` — `isMultiplayer = true`, polling indul

---

## 7. Guardok és interceptor

### 7.1 HTTP Interceptor

**Fájl:** `src/app/interceptors/auth.interceptor.ts`

Funkcionális HTTP interceptor (`HttpInterceptorFn`):

1. Beállítja: `Accept: application/json`, `Content-Type: application/json`
2. Ha van token a `localStorage`-ban → `Authorization: Bearer <token>` header csatolása
3. **401 válasznál:** `token` és `user` törlése localStorage-ból → átirányítás `/login`-ra

### 7.2 Guardok

**Fájl:** `src/app/guards/auth.guard.ts`

Három funkcionális guard (`CanActivateFn`):

| Guard        | Logika                                                              | Használat                 |
| ------------ | ------------------------------------------------------------------- | ------------------------- |
| `authGuard`  | Ha bejelentkezve → OK; egyébként → `/login`                         | `/game`, `/room`, `/leaderboard` |
| `guestGuard` | Ha nincs bejelentkezve → OK; admin → `/admin`; normál → `/game`     | `/login`, `/register`     |
| `adminGuard` | Ha bejelentkezve ÉS admin → OK; egyébként → `/game`                | `/admin`                  |

### 7.3 Autentikációs folyamat

```
1. Bejelentkezés/Regisztráció → token + user JSON tárolása localStorage-ban
2. Minden HTTP kérés → interceptor csatolja a Bearer tokent
3. 401 válasz → automatikus kijelentkezés → /login átirányítás
4. Kijelentkezés gomb → POST /api/logout → localStorage törlés → /login
```

---

## 8. Modellek és interfészek

### 8.1 User (`src/app/models/user.model.ts`)

```typescript
interface User {
  UserID: number;
  Username: string;
  Email: string;
  IsAdmin: boolean;
  IsActive: boolean;
  CreatedAt?: string;
  Balance?: number;
}

interface AuthResponse {
  message: string;
  user: User;
  token: string;
}

interface LoginRequest {
  Email: string;
  Password: string;
}

interface RegisterRequest {
  Username: string;
  Email: string;
  Password: string;
  Password_confirmation: string;
}
```

### 8.2 Level (`src/app/models/level.model.ts`)

```typescript
interface Level {
  LevelID: number;
  Name: string;
  Description: string;
  Category: string;       // 'Könnyed' | 'Közepes' | 'Nehéz'
  OrderNumber: number;
  IsUnlocked: boolean;
  IsCompleted: boolean;
  IsActive: boolean;
  BackgroundUrl?: string | null;
}

interface LevelDetail extends Level {
  TimeSpent: number;      // Eddigi eltelt idő másodpercben
  CompletedAt?: string;
}
```

### 8.3 Question (`src/app/models/question.model.ts`)

```typescript
interface Question {
  QuestionID: number;
  LevelID: number;
  QuestionText: string;
  PositionX: number;      // 1-20
  PositionY: number;      // 1-4
  MoneyReward: number;
  Solved?: boolean;
  RewardDigit?: number;   // 0-9, csak megoldottaknál
  Options?: {
    OptionID: number;
    OptionText: string;
    IsCorrect: boolean;
  }[];
}

interface CheckAnswerRequest { answer: string; }

interface CheckAnswerResponse {
  correct: boolean;
  message: string;
  RewardDigit?: number;
  MoneyReward?: number;
  NewBalance?: number;
  MoneyPenalty?: number;
  TimePenalty?: number;
  WrongCount?: number;
  Penalty?: number;
}
```

### 8.4 Hint (`src/app/models/hint.model.ts`)

```typescript
interface Hint {
  HintID: number;
  HintOrder: number;
  Cost: number;
  HintText?: string;      // Csak megvásárlás után
}

interface BuyHintResponse {
  message: string;
  HintID: number;
  HintOrder: number;
  HintText: string;
  Cost: number;
  NewBalance: number;
  HintsUsed: number;
}
```

### 8.5 Progress (`src/app/models/progress.model.ts`)

```typescript
interface SubmitCodeRequest {
  code: string;
  timeSpent: number;
}

interface SubmitCodeResponse {
  correct: boolean;
  message: string;
  Score?: number;
  TimeSpent?: number;
  CompletedAt?: string;
  TotalScore?: number;
  LevelsCompleted?: number;
  NextLevel?: {
    LevelID: number;
    Name: string;
    OrderNumber: number;
  };
}
```

### 8.6 Leaderboard (`src/app/models/leaderboard.model.ts`)

```typescript
interface LeaderboardEntry {
  UserID: number;
  Username?: string;
  Score: number;
  LevelsCompleted: number;
  TimeTotal: number;
  HintsUsed: number;
}
```

### 8.7 Multiplayer (inline, `multiplayer.service.ts`)

```typescript
interface MultiplayerPlayer {
  UserID: number;
  Username: string;
  IsReady: boolean;
}

interface MultiplayerSolvedQuestion {
  id: number;
  digit: number;
}

interface MultiplayerState {
  id: number;
  LevelID: number;
  Status: 'waiting' | 'playing' | 'finished' | 'abandoned';
  SolvedQuestions: MultiplayerSolvedQuestion[];
  Players: MultiplayerPlayer[];
  MyUserID: number;
}
```

### 8.8 Admin (inline, `admin.service.ts`)

```typescript
interface AdminUser {
  UserID: number; Username: string; Email: string;
  IsAdmin: boolean; IsActive: boolean; CreatedAt?: string;
  Balance: number; Score: number;
}

interface AdminLevel {
  LevelID: number; Name: string; Description: string;
  Category: string; OrderNumber: number; IsActive: boolean;
  BackgroundUrl?: string;
}

interface AdminQuestion {
  QuestionID: number; LevelID: number; QuestionText: string;
  CorrectAnswer: string; RewardDigit: number; MoneyReward: number;
  PositionX: number; PositionY: number;
  level?: { Name: string };
  options?: AdminQuestionOption[];
}

interface AdminQuestionOption {
  OptionID?: number; OptionText: string; IsCorrect: boolean;
}

interface AdminStats {
  totalUsers: number; activeUsers: number; totalLevels: number;
  totalQuestions: number; totalAnswers: number; correctAnswers: number;
  completedRooms: number; newReports: number;
}

interface AdminReport {
  ReportID: number; UserID: number | null; Title: string;
  Category: string; ContactEmail?: string; Message: string;
  Page?: string; Status: 'new' | 'seen' | 'resolved';
  created_at: string; user?: { Username: string };
}
```

---

## 9. Szolgáltatások (Services)

Minden szolgáltatás az alap URL-t dinamikusan építi: `http://${window.location.hostname}:8001/api`

### 9.1 AuthService (`src/app/services/auth.service.ts`)

| Metódus                     | HTTP   | Endpoint           | Leírás                                       |
| --------------------------- | ------ | ------------------ | -------------------------------------------- |
| `login(data)`               | POST   | `/api/login`       | Bejelentkezés → token/user localStorage-ba   |
| `register(data)`            | POST   | `/api/register`    | Regisztráció → token/user localStorage-ba    |
| `logout()`                  | POST   | `/api/logout`      | Kijelentkezés → localStorage törlés → /login |
| `getMe()`                   | GET    | `/api/me`          | Aktuális felhasználó a szerverről             |
| `changePassword(cur, new)`  | PUT    | `/api/me/password` | Jelszóváltoztatás                            |
| `getUser()`                 | —      | —                  | User lekérése localStorage-ból               |
| `getToken()`                | —      | —                  | Token lekérése localStorage-ból              |
| `isLoggedIn()`              | —      | —                  | Token + user létezik-e                       |
| `isAdmin()`                 | —      | —                  | `user.IsAdmin` ellenőrzés                    |

### 9.2 LevelService (`src/app/services/level.service.ts`)

| Metódus         | HTTP | Endpoint           | Visszatérés            |
| --------------- | ---- | ------------------ | ---------------------- |
| `getLevels()`   | GET  | `/api/levels`      | `Observable<Level[]>`  |
| `getLevel(id)`  | GET  | `/api/levels/{id}` | `Observable<LevelDetail>` |

### 9.3 QuestionService (`src/app/services/question.service.ts`)

| Metódus                       | HTTP | Endpoint                            | Visszatérés                      |
| ----------------------------- | ---- | ----------------------------------- | -------------------------------- |
| `getQuestions(levelId)`       | GET  | `/api/levels/{levelId}/questions`   | `Observable<Question[]>`         |
| `checkAnswer(questionId, body)` | POST | `/api/questions/{qId}/check-answer` | `Observable<CheckAnswerResponse>` |

### 9.4 HintService (`src/app/services/hint.service.ts`)

| Metódus             | HTTP | Endpoint                        | Visszatérés                |
| ------------------- | ---- | ------------------------------- | -------------------------- |
| `getHints(questionId)` | GET | `/api/questions/{qId}/hints`   | `Observable<Hint[]>`       |
| `buyHint(hintId)`   | POST | `/api/hints/{hintId}/buy`      | `Observable<BuyHintResponse>` |

### 9.5 ProgressService (`src/app/services/progress.service.ts`)

| Metódus                    | HTTP   | Endpoint                          | Visszatérés                     |
| -------------------------- | ------ | --------------------------------- | ------------------------------- |
| `submitCode(levelId, body)` | POST  | `/api/levels/{levelId}/submit-code` | `Observable<SubmitCodeResponse>` |
| `resetMyProgress()`        | DELETE | `/api/me/reset-progress`          | `Observable<{message}>`        |

### 9.6 MultiplayerService (`src/app/services/multiplayer.service.ts`)

| Metódus                               | HTTP   | Endpoint                              | Visszatérés               |
| ------------------------------------- | ------ | ------------------------------------- | ------------------------- |
| `join(levelId)`                       | POST   | `/api/multiplayer/join`               | `Observable<MultiplayerState>` |
| `getState(sessionId)`                 | GET    | `/api/multiplayer/{sId}/state`        | `Observable<MultiplayerState>` |
| `solve(sessionId, questionId, digit)` | POST   | `/api/multiplayer/{sId}/solve`        | `Observable<MultiplayerState>` |
| `finish(sessionId)`                   | POST   | `/api/multiplayer/{sId}/finish`       | `Observable<void>`        |
| `leave(sessionId)`                    | DELETE | `/api/multiplayer/{sId}/leave`        | `Observable<void>`        |

### 9.7 ReportService (`src/app/services/report.service.ts`)

| Metódus                  | HTTP | Endpoint              | Leírás                        |
| ------------------------ | ---- | --------------------- | ----------------------------- |
| `createReport(data)`     | POST | `/api/reports`        | Hitelesített hibajelentés     |
| `createPublicReport(data)` | POST | `/api/reports/public` | Nyilvános hibajelentés        |

### 9.8 AdminService (`src/app/services/admin.service.ts`)

Alap URL: `/api/admin`

| Metódus                    | HTTP   | Endpoint                           | Leírás                    |
| -------------------------- | ------ | ---------------------------------- | ------------------------- |
| `getStats()`               | GET    | `/admin/stats`                     | Dashboard statisztikák    |
| `getUsers(q?)`             | GET    | `/admin/users?q=`                  | Felhasználók keresővel    |
| `updateUser(id, data)`     | PUT    | `/admin/users/{id}`                | Felhasználó módosítás     |
| `deleteUser(id)`           | DELETE | `/admin/users/{id}`                | Felhasználó törlés        |
| `resetUserProgress(id)`    | DELETE | `/admin/users/{id}/reset-progress` | Progress reset            |
| `getLevels()`              | GET    | `/admin/levels`                    | Szobák listázása          |
| `createLevel(data)`        | POST   | `/admin/levels`                    | Szoba létrehozás          |
| `updateLevel(id, data)`    | PUT    | `/admin/levels/{id}`               | Szoba módosítás           |
| `deleteLevel(id)`          | DELETE | `/admin/levels/{id}`               | Szoba törlés              |
| `getQuestions(levelId?)`   | GET    | `/admin/questions?level_id=`       | Kérdések szűréssel        |
| `createQuestion(data)`     | POST   | `/admin/questions`                 | Kérdés létrehozás         |
| `updateQuestion(id, data)` | PUT    | `/admin/questions/{id}`            | Kérdés módosítás          |
| `deleteQuestion(id)`       | DELETE | `/admin/questions/{id}`            | Kérdés törlés             |
| `getReports(status?)`      | GET    | `/admin/reports?status=`           | Jelentések szűréssel      |
| `updateReport(id, status)` | PUT    | `/admin/reports/{id}`              | Jelentés státusz módosítás |
| `deleteReport(id)`         | DELETE | `/admin/reports/{id}`              | Jelentés törlés           |

---

## 10. Komponensek

### 10.1 LoginComponent

**Mappa:** `src/app/auth/login/` | **Útvonal:** `/login` | **Guard:** `guestGuard`

**Cél:** Bejelentkezési űrlap és nyilvános hibajelentés nem bejelentkezett felhasználók számára.

**Főbb tulajdonságok:**

| Tulajdonság       | Típus       | Leírás                         |
| ----------------- | ----------- | ------------------------------ |
| `form`            | `FormGroup` | Email (required, email) + Password (required, minLength:6) |
| `hiba`            | `string`    | Hibaüzenet                     |
| `toltes`          | `boolean`   | Betöltés állapot               |
| `showReport`      | `boolean`   | Hibajelentés modál láthatóság  |
| `reportCategory`  | `string`    | Kiválasztott kategória         |
| `reportSent`      | `boolean`   | Sikeres küldés jelző           |

**Főbb metódusok:**

| Metódus          | Leírás                                                              |
| ---------------- | ------------------------------------------------------------------- |
| `onSubmit()`     | Validáció → bejelentkezés → átirányítás (admin→/admin, normál→/game)|
| `submitReport()` | Nyilvános hibajelentés küldése (opcionális email)                   |

**Report kategóriák:** `forgotten-password`, `bug`, `account`, `question`, `other`

**Stílusok:** `auth.css` (közös) + `login.component.css`

---

### 10.2 RegisterComponent

**Mappa:** `src/app/auth/register/` | **Útvonal:** `/register` | **Guard:** `guestGuard`

**Cél:** Felhasználó regisztrációs űrlap.

**Validáció:**

| Mező                    | Szabály                              |
| ----------------------- | ------------------------------------ |
| `Username`              | Kötelező, 3–50 karakter             |
| `Email`                 | Kötelező, email formátum            |
| `Password`              | Kötelező, min 6 karakter            |
| `Password_confirmation` | Egyeznie kell a jelszóval           |

Egyedi cross-field validator (`jelszóEgyezés`): ha a két jelszó nem egyezik, `{ nemEgyezik: true }` hiba.

---

### 10.3 GameComponent

**Mappa:** `src/app/pages/game/` | **Útvonal:** `/game` | **Guard:** `authGuard`

**Cél:** Fő játékoldal — szobák böngészése, szóló/multiplayer indítás, jelszóváltoztatás, progress reset, hibajelentés.

**Főbb tulajdonságok:**

| Tulajdonság          | Típus            | Leírás                                         |
| -------------------- | ---------------- | ---------------------------------------------- |
| `user`               | `User`           | Bejelentkezett felhasználó                     |
| `levels`             | `Level[]`        | Összes szoba                                   |
| `multiJoining`       | `number \| null` | Melyik szobához csatlakozik multi-ban           |
| `categoryOrder`      | `string[]`       | `['Könnyed', 'Közepes', 'Nehéz']`             |
| `showReport`         | `boolean`        | Report modál láthatóság                        |
| `showPasswordChange` | `boolean`        | Jelszó modál láthatóság                        |

**Főbb metódusok:**

| Metódus                    | Leírás                                                   |
| -------------------------- | -------------------------------------------------------- |
| `ngOnInit()`               | Szobák betöltése `levelService.getLevels()`               |
| `szobaValaszt(level)`      | Navigáció `/room/{id}` (szóló mód)                       |
| `szobaMulti(level, event)` | `multiSvc.join()` → navigáció `/room/{id}/multi/{sid}`    |
| `getAllapot(level)`         | Visszatérés: `'completed'` / `'active'` / `'locked'`    |
| `submitPasswordChange()`   | Jelszóváltoztatás API hívás                              |
| `resetProgress()`          | Előrehaladás reset (megerősítéssel)                      |

**Számított tulajdonságok:**

| Getter               | Leírás                                                     |
| -------------------- | ---------------------------------------------------------- |
| `groupedLevels`      | Szobák kategóriánként csoportosítva                        |
| `allLevelsCompleted` | `true` ha minden szoba teljesítve → reset banner megjelenik|

**Template jellemzők:**
- Sticky fejléc: logó, felhasználónév, navigációs gombok, kijelentkezés
- Szobák nehézségi kategóriánként csoportosítva (Könnyed, Közepes, Nehéz)
- Szoba kártyák háttérképpel, állapot ikonokkal (✅ teljesített / 🚪 elérhető / 🔒 zárolt)
- Aktív szobáknál: Egyjátékos + Többjátékos gombok
- Teljesített szobák: zöld keret és jelvény
- Zárolt szobák: halvány overlay blur-rel
- Reset banner minden szoba teljesítése után
- Lebegő hibajelentés FAB (? gomb, jobb alsó sarok)

---

### 10.4 RoomComponent

**Mappa:** `src/app/pages/room/` | **Útvonal:** `/room/:id` vagy `/room/:id/multi/:sessionId` | **Guard:** `authGuard`

**Cél:** Az aktív játék komponens — interaktív szoba kérdésekkel, időzítővel, segítségekkel, számjegy-gyűjtéssel, kód beküldéssel és multiplayer támogatással.

**Főbb tulajdonságok:**

| Tulajdonság          | Típus                     | Leírás                                    |
| -------------------- | ------------------------- | ----------------------------------------- |
| `levelId`            | `number`                  | Szoba azonosító                           |
| `level`              | `LevelDetail \| null`     | Szoba adatok                              |
| `questions`          | `QuestionState[]`         | Kérdések állapottal (solved, digit, justSolved) |
| `balance`            | `number`                  | Játékos egyenlege                         |
| `isMultiplayer`      | `boolean`                 | Többjátékos mód jelző                     |
| `multiSessionId`     | `number \| null`          | Munkamenet azonosító                      |
| `multiState`         | `MultiplayerState`        | Munkamenet állapot                        |
| `timeSpent`          | `number`                  | Eltelt idő (másodperc)                    |
| `activeQuestion`     | `QuestionState \| null`   | Nyitott kérdés                            |
| `selectedOption`     | `string \| null`          | Kiválasztott opció                        |
| `codeInput`          | `string`                  | Beírt szobakód                            |
| `manualDigits`       | `string[]`                | Manuálisan beírt számjegyek               |

**Számított tulajdonságok:**

| Getter              | Leírás                                               |
| ------------------- | ---------------------------------------------------- |
| `timerDisplay`      | `MM:SS` formátumú időkijelzés                        |
| `collectedDigits`   | Összegyűjtött számjegyek (multi session-ből is)      |
| `mergedDigits`      | Megoldott + manuálisan beírt kombinálva               |
| `solvedCount`       | Megoldott kérdések száma                             |
| `progressPercent`   | Előrehaladás százalékban                             |
| `allSolved`         | Minden kérdés megoldva-e                             |
| `canSubmitCode`     | Minden jegy kitöltve-e                               |
| `roomTheme`         | `{icon, bg, accent}` a szoba neve alapján            |

**Főbb metódusok:**

| Metódus                       | Leírás                                                 |
| ----------------------------- | ------------------------------------------------------ |
| `loadRoom()`                  | `forkJoin`: szoba + kérdések + egyenleg betöltése      |
| `openQuestion(qs)`            | Kérdés modál megnyitása                                |
| `checkAnswer()`               | Válasz ellenőrzés API hívás + animációk                |
| `use5050()`                   | 50/50 segítség: 2 rossz opció eltávolítása, -25 pénz  |
| `submitCode()`                | Kód beküldés (szóló: API, multi: lokális ellenőrzés)   |
| `startMultiplayerPolling()`   | 2 másodperces állapot-polling indítása                 |
| `leaveMulti()`                | Multiplayer elhagyás + navigáció /game-re              |
| `copyMultiLink()`             | Megosztó link vágólapra másolása                       |

**Kérdés-válasz folyamat:**

1. Kérdés csomópontra kattintás → modál megnyitása
2. ABCD opció kiválasztása → `checkAnswer()` API hívás
3. **Helyes:** számjegy megjelenik + pénz animáció + 700ms után modál bezáródik
4. **Helytelen:** pénzbüntetés / időbüntetés animáció + opció visszaállítása újrapróbálkozáshoz

**50/50 segítség:**
- 25 pénzbe kerül
- Megtartja a helyes opciót + 1 véletlenszerű helytelent
- Eltávolítja a többi rossz opciót

**Multiplayer működés:**
1. `waiting` állapot → megosztó link másolása a társnak
2. 2 játékos csatlakozik → automatikus `playing` státusz, időzítő indul
3. 2 másodperces polling szinkronizálja a megoldásokat
4. `abandoned` (társ kilépett) → automatikus átirányítás `/game`-re
5. `finished` → sikeres üzenet → 3.5s után átirányítás
6. `ngOnDestroy()` → automatikus kilépés (nincs árva session)

**Szoba témák** (CSS custom properties alapján):

| Téma       | Kulcsszó  | Akcentus  | Háttér    |
| ---------- | --------- | --------- | --------- |
| Könyvtár   | könyvtár  | `#c19a6b` | `#1a1410` |
| Labor      | labor     | `#34d399` | `#0a1a14` |
| Pince      | pince     | `#fb923c` | `#1a0e09` |
| Hajó       | kapitány  | `#38bdf8` | `#0a1220` |
| Űr         | űr        | `#c19a6b` | `#1a1410` |

---

### 10.5 LeaderboardComponent

**Mappa:** `src/app/pages/leaderboard/` | **Útvonal:** `/leaderboard` | **Guard:** `authGuard`

**Cél:** Globális játékos ranglista rendezési lehetőségekkel, statisztikákkal és dobogóval.

**Főbb tulajdonságok:**

| Tulajdonság  | Típus                | Leírás                        |
| ------------ | -------------------- | ----------------------------- |
| `entries`    | `LeaderboardEntry[]` | Ranglista bejegyzések         |
| `sortBy`     | `string`             | Rendezési szempont            |
| `animatedIn` | `boolean`            | Belépő animáció               |

**Rendezési szempontok:**

| Érték    | Szempont                  | Sorrend   |
| -------- | ------------------------- | --------- |
| `score`  | Pontszám                  | Csökkenő  |
| `levels` | Teljesített szobák        | Csökkenő  |
| `time`   | Összidő                   | Növekvő   |
| `hints`  | Használt segítségek       | Növekvő   |

**Template jellemzők:**
- Fejléc navigációs gombokkal (Játék, Admin)
- Statisztika sor: összjátékosok, átlag pontszám, átlag idő, legtöbb szoba
- 4 rendezési gomb
- Top 3 dobogó (arany/ezüst/bronz kártyák pontszám barral)
- Teljes rangsor táblázat érem ikonokkal, „Te" jelvénnyel az aktuális felhasználónál
- Üres/betöltés/hiba állapotok

---

### 10.6 AdminComponent

**Mappa:** `src/app/pages/admin/` | **Útvonal:** `/admin` | **Guard:** `adminGuard`

**Cél:** Teljes admin felület 5 fülsorral.

**Elrendezés:** Bal oldali navigáció + fő tartalom terület sticky fejléccel.

#### 1. Irányítópult (Dashboard)

Statisztika rácsozat: összes felhasználó, aktív felhasználók száma, szobák, kérdések, teljesített szobák, új hibajelentések (kattintható → Hibajelentések fülre navigál), válasz helyességi arány sáv.

#### 2. Felhasználók

- Keresés név/email alapján
- Adattábla: ID, Név, Email, Admin jelvény, Aktív jelvény, Egyenleg, Pontszám
- Műveletek: Szerkesztés (modál), Aktiválás/Inaktiválás toggle, Progress reset (megerősítéssel), Törlés (megerősítéssel)

#### 3. Szobák

- „Új szoba" gomb
- Adattábla: ID, Sorrend, Név, Kategória jelvény, Leírás, Aktív
- Szerkesztő modál: Név, Leírás, Kategória (Könnyed/Közepes/Nehéz), Sorrend, Aktív, Háttérkép URL élő előnézettel

#### 4. Kérdések

- Szoba szűrő legördülő + „Új kérdés" gomb
- Adattábla: ID, Szoba, Kérdés szöveg, Helyes válasz, Számjegy, Jutalom, Pozíció
- Szerkesztő modál: Szoba választó, Kérdés, RewardDigit (0–9), MoneyReward, PositionX (1–20), PositionY (1–4), ABCD opciók rádiógombbal a helyes válaszhoz

#### 5. Hibajelentések

- Státusz szűrő: Összes / Új / Megtekintett / Megoldott
- Adattábla: ID, Kategória, Felhasználó/Email, Cím és Leírás, Oldal, Státusz jelvény, Dátum
- Műveletek: Megtekintettnek jelölés, Megoldottnak jelölés, Visszaállítás újra, Törlés

**Toast értesítések:** Sikeres (zöld) / Hiba (piros) — 3 másodperces auto-dismiss.

---

## 11. Stílusrendszer

### 11.1 Szín paletta

| Szín                | Kód       | Használat                              |
| ------------------- | --------- | -------------------------------------- |
| Elsődleges háttér   | `#1a1410` | Body, kártyák, fejléc                  |
| Elsődleges szöveg   | `#e8ddd0` | Body szöveg, címek                     |
| Arany akcentus      | `#c19a6b` | Gombok, keretek, kiemelések            |
| Halvány szöveg      | `#8a7a6a` | Másodlagos szöveg, címkék              |
| Kártya háttér       | `#2a1f15` | Szoba kártyák, modálok                 |
| Hiba / piros        | `#fc8181` | Validációs hibák, figyelmeztetések     |
| Siker / zöld        | `#34d399` | Teljesített állapotok                  |
| Multiplayer / kék   | `#7dd3fc` | Multi gombok, jelvények                |
| Admin / narancs     | `#fb923c` | Admin jelvény                          |

### 11.2 Stílus architektúra

| Fájl                          | Hatókör           | Leírás                                       |
| ----------------------------- | ----------------- | -------------------------------------------- |
| `src/styles.css`              | Globális          | CSS reset, body, megosztott report rendszer   |
| `src/app/auth/auth.css`       | Login + Register  | Közös kártya, űrlap, gomb stílusok           |
| `login.component.css`         | Login             | Report gomb stílus                           |
| `register.component.css`      | Register          | Minimális felülírások                        |
| `game.component.css`          | Game              | Fejléc, szoba kártyák, kategóriák, gombok    |
| `room.component.css`          | Room              | Szoba témák, kérdés csomópontok, modálok     |
| `leaderboard.component.css`   | Leaderboard       | Dobogó, statisztikák, rangsor táblázat       |
| `admin.component.css`         | Admin             | Sidebar, adattáblák, szerkesztő modálok      |

### 11.3 Megosztott report rendszer

A `styles.css` tartalmazza a report rendszer stílusait, amelyeket a Game, Room és Login komponensek közösen használnak:

- `.report-fab` — Lebegő akció gomb (52px kör, jobb alsó sarok)
- `.report-overlay` — Modál háttér overlay
- `.report-modal` — Modál konténer
- `.report-category-grid` — Kategória gombok rácsozata
- `.report-submit` — Beküldés gomb

### 11.4 Közös auth stílusok (`auth.css`)

| Osztály        | Leírás                                         |
| -------------- | ---------------------------------------------- |
| `.oldal`       | Teljes viewport központosított elrendezés      |
| `.kartya`      | Kártya konténer (max-width: 400px)             |
| `.mezo`        | Űrlap mező wrapper (címke + input)             |
| `.hiba-szoveg` | Validációs hiba szöveg (piros)                 |
| `.hiba-doboz`  | Hiba doboz konténer                            |
| `button`       | Teljes szélességű arany akció gomb             |
| `.link`        | Alul elhelyezett navigációs link               |

### 11.5 Szoba témák

A `RoomComponent` CSS egyedi tulajdonságokat (`custom properties`) használ:

```css
:host {
  --room-accent: #c19a6b;
  --room-bg: #1a1410;
  --room-glow: rgba(193, 154, 107, 0.4);
}
```

A téma a szoba `Name` mezőjéből határozódik meg kulcsszó-egyezéssel (könyvtár, labor, pince, kapitány, űr).

### 11.6 Reszponzív design

| Breakpoint    | Változás                                  |
| ------------- | ----------------------------------------- |
| ≤ 900px       | Game: kártyák oszlopváltás                |
| ≤ 768px       | Leaderboard: dobogó vertikális            |
| ≤ 700px       | Admin: sidebar összecsukás                |
| ≤ 600px       | Game: fejléc stackelés                    |
| ≤ 480px       | Room: modál teljes szélességű             |
