# Szolgáltatások (Services)

Minden szolgáltatás az API-val kommunikál a következő bázis URL-en:

```
http://${window.location.hostname}:8001/api
```

---

## AuthService

**Fájl:** `src/app/services/auth.service.ts`

Felhasználó-kezelés és token menedzsment.

### API metódusok

| Metódus                        | HTTP   | Végpont             | Kérés                               | Válasz             |
| ------------------------------ | ------ | ------------------- | ------------------------------------ | ------------------ |
| `login(data)`                  | POST   | `/api/login`        | `LoginRequest`                       | `Observable<User>` |
| `register(data)`               | POST   | `/api/register`     | `{Username, Email, Password}`        | `Observable<User>` |
| `logout()`                     | POST   | `/api/logout`       | `{}`                                 | `Observable<void>` |
| `getMe()`                      | GET    | `/api/me`           | —                                    | `Observable<User>` |
| `changePassword(current, new)` | PUT    | `/api/me/password`  | `{current_password, new_password}`   | `Observable<{message}>` |

### Lokális metódusok

| Metódus        | Visszatérés     | Leírás                                         |
| -------------- | --------------- | ---------------------------------------------- |
| `getUser()`    | `User \| null`  | Felhasználó a localStorage-ból                 |
| `getToken()`   | `string \| null`| Token a localStorage-ból                       |
| `isLoggedIn()` | `boolean`       | Token és felhasználó létezik-e                 |
| `isAdmin()`    | `boolean`       | `user.IsAdmin` értéke                          |

### Token kezelés

- Bejelentkezés/regisztráció után a `token` és `user` JSON mentésre kerül a `localStorage`-ba
- Kijelentkezéskor a localStorage törlődik és navigáció `/login`-ra
- A token az `authInterceptor` által automatikusan hozzáadódik minden kéréshez

---

## LevelService

**Fájl:** `src/app/services/level.service.ts`

| Metódus         | HTTP | Végpont            | Válasz                   |
| --------------- | ---- | ------------------ | ------------------------ |
| `getLevels()`   | GET  | `/api/levels`      | `Observable<Level[]>`    |
| `getLevel(id)`  | GET  | `/api/levels/{id}` | `Observable<LevelDetail>`|

---

## QuestionService

**Fájl:** `src/app/services/question.service.ts`

| Metódus                     | HTTP | Végpont                               | Kérés                | Válasz                          |
| --------------------------- | ---- | ------------------------------------- | -------------------- | ------------------------------- |
| `getQuestions(levelId)`     | GET  | `/api/levels/{levelId}/questions`     | —                    | `Observable<Question[]>`        |
| `checkAnswer(questionId, body)` | POST | `/api/questions/{id}/check-answer` | `CheckAnswerRequest` | `Observable<CheckAnswerResponse>` |

---

## HintService

**Fájl:** `src/app/services/hint.service.ts`

| Metódus              | HTTP | Végpont                       | Válasz                       |
| -------------------- | ---- | ----------------------------- | ---------------------------- |
| `getHints(questionId)` | GET  | `/api/questions/{id}/hints` | `Observable<Hint[]>`         |
| `buyHint(hintId)`    | POST | `/api/hints/{id}/buy`         | `Observable<BuyHintResponse>`|

---

## ProgressService

**Fájl:** `src/app/services/progress.service.ts`

| Metódus                    | HTTP   | Végpont                              | Kérés                | Válasz                           |
| -------------------------- | ------ | ------------------------------------ | -------------------- | -------------------------------- |
| `submitCode(levelId, body)`| POST   | `/api/levels/{levelId}/submit-code`  | `SubmitCodeRequest`  | `Observable<SubmitCodeResponse>` |
| `resetMyProgress()`        | DELETE | `/api/me/reset-progress`             | —                    | `Observable<{message}>`          |

---

## MultiplayerService

**Fájl:** `src/app/services/multiplayer.service.ts`

| Metódus                           | HTTP   | Végpont                               | Kérés                          | Válasz                          |
| --------------------------------- | ------ | ------------------------------------- | ------------------------------ | ------------------------------- |
| `join(levelId)`                   | POST   | `/api/multiplayer/join`               | `{level_id}`                   | `Observable<MultiplayerState>`  |
| `getState(sessionId)`             | GET    | `/api/multiplayer/{id}/state`         | —                              | `Observable<MultiplayerState>`  |
| `solve(sid, questionId, digit)`   | POST   | `/api/multiplayer/{id}/solve`         | `{question_id, reward_digit}`  | `Observable<MultiplayerState>`  |
| `finish(sessionId)`               | POST   | `/api/multiplayer/{id}/finish`        | `{}`                           | `Observable<void>`              |
| `leave(sessionId)`                | DELETE | `/api/multiplayer/{id}/leave`         | —                              | `Observable<void>`              |

### Inline interfészek

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

---

## ReportService

**Fájl:** `src/app/services/report.service.ts`

| Metódus                  | HTTP | Végpont               | Kérés                | Válasz           |
| ------------------------ | ---- | --------------------- | -------------------- | ---------------- |
| `createReport(data)`     | POST | `/api/reports`        | `CreateReportDto`    | `Observable<any>`|
| `createPublicReport(data)` | POST | `/api/reports/public` | `CreatePublicReportDto` | `Observable<any>` |

### Interfészek

```typescript
interface CreateReportDto {
  Title: string;
  Category?: string;
  Message: string;
  Page?: string;
}

interface CreatePublicReportDto {
  Title: string;
  Category: string;
  ContactEmail?: string;
  Message: string;
  Page?: string;
}
```

---

## AdminService

**Fájl:** `src/app/services/admin.service.ts`

Bázis URL: `/api/admin`

### Statisztika

| Metódus      | HTTP | Végpont        | Válasz                   |
| ------------ | ---- | -------------- | ------------------------ |
| `getStats()` | GET  | `/admin/stats` | `Observable<AdminStats>` |

### Felhasználó-kezelés

| Metódus                   | HTTP   | Végpont                         | Leírás                       |
| ------------------------- | ------ | ------------------------------- | ---------------------------- |
| `getUsers(q?)`            | GET    | `/admin/users?q=`              | Keresés név/email alapján    |
| `updateUser(id, data)`    | PUT    | `/admin/users/{id}`            | Részleges frissítés + jelszó |
| `deleteUser(id)`          | DELETE | `/admin/users/{id}`            | Felhasználó törlése          |
| `resetUserProgress(id)`   | DELETE | `/admin/users/{id}/reset-progress` | Progress visszaállítás   |

### Szoba-kezelés

| Metódus                   | HTTP   | Végpont              | Leírás           |
| ------------------------- | ------ | -------------------- | ---------------- |
| `getLevels()`             | GET    | `/admin/levels`      | Összes szoba     |
| `createLevel(data)`       | POST   | `/admin/levels`      | Szoba létrehozás |
| `updateLevel(id, data)`   | PUT    | `/admin/levels/{id}` | Szoba módosítás  |
| `deleteLevel(id)`         | DELETE | `/admin/levels/{id}` | Szoba törlés     |

### Kérdés-kezelés

| Metódus                      | HTTP   | Végpont                  | Leírás               |
| ---------------------------- | ------ | ------------------------ | -------------------- |
| `getQuestions(levelId?)`     | GET    | `/admin/questions?level_id=` | Szűrhető listázás |
| `createQuestion(data)`       | POST   | `/admin/questions`       | Létrehozás 4 opcióval|
| `updateQuestion(id, data)`   | PUT    | `/admin/questions/{id}`  | Módosítás            |
| `deleteQuestion(id)`         | DELETE | `/admin/questions/{id}`  | Törlés               |

### Hibajelentés-kezelés

| Metódus                   | HTTP   | Végpont                  | Leírás              |
| ------------------------- | ------ | ------------------------ | ------------------- |
| `getReports(status?)`     | GET    | `/admin/reports?status=` | Szűrhető listázás   |
| `updateReport(id, status)`| PUT    | `/admin/reports/{id}`    | Státusz módosítás   |
| `deleteReport(id)`        | DELETE | `/admin/reports/{id}`    | Törlés              |
