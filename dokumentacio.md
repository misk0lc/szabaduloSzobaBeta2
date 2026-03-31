# Szabadulószoba – Teljes Programozói Dokumentáció

---

## Tartalomjegyzék

### I. rész – Projektáttekintés
1. [Az alkalmazásról](#1-az-alkalmazásról)
2. [Rendszerarchitektúra](#2-rendszerarchitektúra)
3. [Telepítés és indítás](#3-telepítés-és-indítás)

### II. rész – Backend
4. [Backend technológiai stack](#4-backend-technológiai-stack)
5. [Backend mappastruktúra](#5-backend-mappastruktúra)
6. [Adatbázis séma](#6-adatbázis-séma)
7. [Modellek és kapcsolatok](#7-modellek-és-kapcsolatok)
8. [API végpontok](#8-api-végpontok)
9. [Kontrollerek](#9-kontrollerek)
10. [Middleware és autentikáció](#10-middleware-és-autentikáció)
11. [Játéklogika](#11-játéklogika)
12. [Seederek](#12-seederek)

### III. rész – Frontend
13. [Frontend technológiai stack](#13-frontend-technológiai-stack)
14. [Frontend projektstruktúra](#14-frontend-projektstruktúra)
15. [Bootstrap és alkalmazás konfiguráció](#15-bootstrap-és-alkalmazás-konfiguráció)
16. [Routing és navigáció](#16-routing-és-navigáció)
17. [Guardok és interceptor](#17-guardok-és-interceptor)
18. [Modellek és interfészek](#18-modellek-és-interfészek)
19. [Szolgáltatások (Services)](#19-szolgáltatások-services)
20. [Komponensek](#20-komponensek)
21. [Stílusrendszer](#21-stílusrendszer)

---

# I. RÉSZ – PROJEKTÁTTEKINTÉS

---

## 1. Az alkalmazásról

A **Szabadulószoba** egy interaktív webes szabadulószoba-játék, amelyben a felhasználók különböző tematikájú szobákat oldanak meg kérdés-válasz formában. A játékosok szobákat választanak, kérdésekre válaszolnak, számjegyeket gyűjtenek, és az összegyűjtött számjegyekből álló kódot adják be a szoba teljesítéséhez.

### Fő funkciók

- **15 szabadulószoba** 3 nehézségi kategóriában (Könnyed, Közepes, Nehéz)
- **Egyjátékos és többjátékos mód** (max. 2 játékos)
- **Kérdés-válasz rendszer** 4 opciós feleletválasztós kérdésekkel
- **Büntetési rendszer** hibás válaszokra (pénz- és időbüntetés)
- **Ranglistarendszer** pontszámítással
- **Admin panel** teljes CRUD felhasználókra, szobákra, kérdésekre és hibajelentésekre
- **Hibajelentő rendszer** bejelentkezett és nem bejelentkezett felhasználóknak

A felület nyelve **magyar**.

---

## 2. Rendszerarchitektúra

Az alkalmazás kliens-szerver architektúrát alkalmaz:

```
┌─────────────────────┐         HTTP/JSON         ┌─────────────────────┐
│                     │  ◄──────────────────────►  │                     │
│   Angular 19 SPA    │    REST API (port 8001)    │  Laravel 12.x API   │
│   (Frontend)        │   Bearer Token Auth        │   (Backend)         │
│                     │                            │                     │
│   - TypeScript      │                            │   - PHP 8.2+        │
│   - Standalone      │                            │   - Sanctum Auth    │
│     Components      │                            │   - Eloquent ORM    │
│   - Lazy Loading    │                            │                     │
└─────────────────────┘                            └──────────┬──────────┘
                                                              │
                                                              │ Eloquent
                                                              ▼
                                                   ┌─────────────────────┐
                                                   │      MySQL          │
                                                   │   Adatbázis         │
                                                   │   (15+ tábla)       │
                                                   └─────────────────────┘
```

| Réteg    | Technológia           | Port  |
| -------- | --------------------- | ----- |
| Frontend | Angular 19.2.0        | 4200  |
| Backend  | Laravel 12.x (PHP)    | 8001  |
| Adatbázis| MySQL                 | 3306  |

**Kommunikáció:** RESTful JSON API, Sanctum Bearer token autentikáció (opaque token, nem JWT).

---

## 3. Telepítés és indítás

### 3.1 Előfeltételek

- PHP >= 8.2
- Composer
- Node.js (LTS)
- npm
- MySQL szerver

### 3.2 Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate

# .env fájlban: DB_DATABASE=szabaduloszoba-db

php artisan migrate
php artisan db:seed
php artisan serve --port=8001
```

### 3.3 Frontend

```bash
cd frontend
npm install
npm start
```

### 3.4 Gyors indítás (batch scriptek)

A projekt gyökerében elérhetők:
- `setup-project.bat` — teljes telepítés
- `start-backend.bat` — backend szerver indítás
- `start-frontend.bat` — frontend szerver indítás

### 3.5 Környezeti változók (.env)

| Változó          | Alapértelmezett      | Leírás              |
| ---------------- | -------------------- | ------------------- |
| `DB_CONNECTION`  | `mysql`              | Adatbázis driver    |
| `DB_HOST`        | `127.0.0.1`          | Adatbázis host      |
| `DB_PORT`        | `3306`               | Adatbázis port      |
| `DB_DATABASE`    | `szabaduloszoba-db`  | Adatbázis neve      |
| `DB_USERNAME`    | `root`               | Felhasználó         |
| `DB_PASSWORD`    | (üres)               | Jelszó              |
| `BCRYPT_ROUNDS`  | `12`                 | Jelszó hash erősség |

### 3.6 API kapcsolat

A frontend dinamikusan építi az API URL-t:

```typescript
http://${window.location.hostname}:8001/api
```

### 3.7 CORS konfiguráció

- Engedélyezett útvonal: `api/*`, `sanctum/csrf-cookie`
- Minden origin, metódus és header engedélyezett

---

# II. RÉSZ – BACKEND

---

## 4. Backend technológiai stack

| Komponens      | Technológia                          |
| -------------- | ------------------------------------ |
| Framework      | Laravel 12.x (PHP ^8.2)             |
| Autentikáció   | Laravel Sanctum 4.3 (Bearer token)  |
| Adatbázis      | MySQL                                |
| API stílus     | RESTful JSON                         |
| Multiplayer    | Polling-alapú (nincs WebSocket)      |

### Függőségek

**Produkciós:**
- `laravel/framework ^12.0`
- `laravel/sanctum ^4.3`
- `laravel/tinker ^2.10.1`

**Fejlesztői:**
- `fakerphp/faker` — teszt adatok generálása
- `phpunit/phpunit ^11.5.3` — unit tesztek
- `laravel/pint` — kód formázás
- `mockery/mockery` — mock objektumok teszteléshez
- `nunomaduro/collision` — szebb hiba kimenetek

---

## 5. Backend mappastruktúra

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php        # Admin CRUD műveletek
│   │   │   ├── AuthController.php         # Regisztráció, bejelentkezés, jelszóváltoztatás
│   │   │   ├── HintController.php         # Segítségek
│   │   │   ├── LeaderboardController.php  # Ranglista
│   │   │   ├── LevelController.php        # Szobák listázása
│   │   │   ├── MultiplayerController.php  # Többjátékos munkamenetek
│   │   │   ├── ProgressController.php     # Kód beküldés, progress reset
│   │   │   ├── QuestionController.php     # Kérdések, válasz ellenőrzés
│   │   │   └── ReportController.php       # Hibajelentések
│   │   ├── Middleware/
│   │   │   ├── IsActive.php               # Aktív fiók ellenőrzés
│   │   │   └── IsAdmin.php                # Admin jogosultság ellenőrzés
│   │   └── Traits/
│   │       └── ChecksLevelUnlock.php      # Szint feloldási logika
│   ├── Models/
│   │   ├── Hint.php
│   │   ├── LeaderboardEntry.php
│   │   ├── Level.php
│   │   ├── MultiplayerSession.php
│   │   ├── Question.php
│   │   ├── QuestionOption.php
│   │   ├── Report.php
│   │   ├── User.php
│   │   ├── UserAnswer.php
│   │   ├── UserMoney.php
│   │   └── UserProgress.php
│   └── Providers/
│       └── AppServiceProvider.php
├── bootstrap/
│   └── app.php                            # Middleware alias regisztráció
├── config/
│   ├── auth.php, cors.php, database.php, sanctum.php
├── database/
│   ├── migrations/                        # Adatbázis migrációk
│   └── seeders/                           # Teszt adatok
├── routes/
│   └── api.php                            # API útvonalak
└── .env.example                           # Környezeti változók sablon
```

---

## 6. Adatbázis séma

### 6.1 ER-diagram

```
users 1──* user_progress *──1 levels
  │                              │
  ├──1 user_money                ├──* questions
  │                              │       │
  ├──1 leaderboard               │       ├──* question_options
  │                              │       │
  ├──* user_answers ─────────────┼───────┘
  │                              │       ├──* hints
  ├──* reports                   │
  │                              │
  └──*─ multiplayer_session_users ─*──1 multiplayer_sessions ──1 levels
```

### 6.2 users

| Oszlop           | Típus              | Megkötések               |
| ---------------- | ------------------ | ------------------------ |
| `UserID`         | BIGINT UNSIGNED PK | Auto increment           |
| `Username`       | VARCHAR(50)        | UNIQUE                   |
| `Email`          | VARCHAR(100)       | UNIQUE                   |
| `PasswordHash`   | VARCHAR(255)       |                          |
| `IsAdmin`        | BOOLEAN            | Alapértelmezett: `false` |
| `CreatedAt`      | DATETIME           | Alapértelmezett: `NOW()` |
| `IsActive`       | BOOLEAN            | Alapértelmezett: `true`  |
| `remember_token` | VARCHAR(100)       | Nullable                 |

### 6.3 levels

| Oszlop          | Típus              | Megkötések                     |
| --------------- | ------------------ | ------------------------------ |
| `LevelID`       | BIGINT UNSIGNED PK | Auto increment                 |
| `Name`          | VARCHAR(100)       |                                |
| `Description`   | TEXT               | Nullable                       |
| `Category`      | VARCHAR(50)        | Alapértelmezett: `'Könnyed'`   |
| `OrderNumber`   | INTEGER            | UNIQUE                         |
| `IsActive`      | BOOLEAN            | Alapértelmezett: `true`        |
| `BackgroundUrl`  | VARCHAR(500)       | Nullable                       |
| `CreatedAt`     | DATETIME           | Alapértelmezett: `NOW()`       |

**Kategóriák:** `Könnyed`, `Közepes`, `Nehéz`

### 6.4 questions

| Oszlop          | Típus              | Megkötések                                   |
| --------------- | ------------------ | -------------------------------------------- |
| `QuestionID`    | BIGINT UNSIGNED PK | Auto increment                               |
| `LevelID`       | BIGINT UNSIGNED    | FK → `levels.LevelID` CASCADE               |
| `QuestionText`  | TEXT               |                                              |
| `CorrectAnswer` | VARCHAR(255)       |                                              |
| `RewardDigit`   | INTEGER            | 0–9, a szoba kódjának egy számjegye          |
| `MoneyReward`   | INTEGER            | Alapértelmezett: `0`                         |
| `PositionX`     | INTEGER            | Rácspozíció (1–20)                           |
| `PositionY`     | INTEGER            | Rácspozíció (1–4)                            |

### 6.5 question_options

| Oszlop       | Típus              | Megkötések                                   |
| ------------ | ------------------ | -------------------------------------------- |
| `OptionID`   | BIGINT UNSIGNED PK | Auto increment                               |
| `QuestionID` | BIGINT UNSIGNED    | FK → `questions.QuestionID` CASCADE          |
| `OptionText` | VARCHAR(255)       |                                              |
| `IsCorrect`  | BOOLEAN            | Alapértelmezett: `false`                     |

### 6.6 hints

| Oszlop       | Típus              | Megkötések                                   |
| ------------ | ------------------ | -------------------------------------------- |
| `HintID`     | BIGINT UNSIGNED PK | Auto increment                               |
| `QuestionID` | BIGINT UNSIGNED    | FK → `questions.QuestionID` CASCADE          |
| `HintText`   | TEXT               |                                              |
| `Cost`       | INTEGER            | Ár játékpénzben                              |
| `HintOrder`  | INTEGER            | Megjelenítési sorrend                        |

### 6.7 user_progress

| Oszlop        | Típus              | Megkötések                                   |
| ------------- | ------------------ | -------------------------------------------- |
| `ProgressID`  | BIGINT UNSIGNED PK | Auto increment                               |
| `UserID`      | BIGINT UNSIGNED    | FK → `users.UserID` CASCADE                 |
| `LevelID`     | BIGINT UNSIGNED    | FK → `levels.LevelID` CASCADE               |
| `Completed`   | BOOLEAN            | Alapértelmezett: `false`                     |
| `TimeSpent`   | INTEGER            | Alapértelmezett: `0` (másodpercben)          |
| `CompletedAt` | DATETIME           | Nullable                                     |

**Egyedi index:** `UNIQUE(UserID, LevelID)`

### 6.8 user_money

| Oszlop   | Típus              | Megkötések                                   |
| -------- | ------------------ | -------------------------------------------- |
| `UserID` | BIGINT UNSIGNED PK | FK → `users.UserID` CASCADE                 |
| `Amount` | INTEGER            | Alapértelmezett: `0`                         |

### 6.9 user_answers

| Oszlop       | Típus              | Megkötések                                   |
| ------------ | ------------------ | -------------------------------------------- |
| `AnswerID`   | BIGINT UNSIGNED PK | Auto increment                               |
| `UserID`     | BIGINT UNSIGNED    | FK → `users.UserID` CASCADE                 |
| `QuestionID` | BIGINT UNSIGNED    | FK → `questions.QuestionID` CASCADE          |
| `GivenAnswer`| VARCHAR(255)       |                                              |
| `IsCorrect`  | BOOLEAN            |                                              |
| `AnsweredAt` | DATETIME           | Alapértelmezett: `NOW()`                     |

### 6.10 leaderboard

| Oszlop            | Típus              | Megkötések                                   |
| ----------------- | ------------------ | -------------------------------------------- |
| `UserID`          | BIGINT UNSIGNED PK | FK → `users.UserID` CASCADE                 |
| `Score`           | INTEGER            | Alapértelmezett: `0`                         |
| `LevelsCompleted` | INTEGER            | Alapértelmezett: `0`                         |
| `TimeTotal`       | INTEGER            | Alapértelmezett: `0` (másodpercben)          |
| `HintsUsed`       | INTEGER            | Alapértelmezett: `0`                         |

### 6.11 reports

| Oszlop         | Típus                                 | Megkötések                                   |
| -------------- | ------------------------------------- | -------------------------------------------- |
| `ReportID`     | BIGINT UNSIGNED PK                    | Auto increment                               |
| `UserID`       | BIGINT UNSIGNED                       | Nullable, FK → `users.UserID` SET NULL       |
| `Title`        | VARCHAR(100)                          |                                              |
| `Category`     | VARCHAR(50)                           | Alapértelmezett: `'bug'`                     |
| `ContactEmail` | VARCHAR(100)                          | Nullable                                     |
| `Message`      | TEXT                                  |                                              |
| `Page`         | VARCHAR(100)                          | Nullable                                     |
| `Status`       | ENUM('new','seen','resolved')         | Alapértelmezett: `'new'`                     |
| `created_at`   | TIMESTAMP                             |                                              |
| `updated_at`   | TIMESTAMP                             |                                              |

### 6.12 multiplayer_sessions

| Oszlop            | Típus                                              | Megkötések               |
| ----------------- | -------------------------------------------------- | ------------------------ |
| `id`              | BIGINT UNSIGNED PK                                 | Auto increment           |
| `LevelID`         | BIGINT UNSIGNED                                    | FK → `levels.LevelID` CASCADE |
| `Status`          | ENUM('waiting','playing','finished','abandoned')    | Alapértelmezett: `'waiting'` |
| `SolvedQuestions`  | JSON                                               | Alapértelmezett: `'[]'`  |
| `created_at`      | TIMESTAMP                                          |                          |
| `updated_at`      | TIMESTAMP                                          |                          |

### 6.13 multiplayer_session_users

| Oszlop       | Típus              | Megkötések                                   |
| ------------ | ------------------ | -------------------------------------------- |
| `id`         | BIGINT UNSIGNED PK | Auto increment                               |
| `SessionID`  | BIGINT UNSIGNED    | FK → `multiplayer_sessions.id` CASCADE       |
| `UserID`     | BIGINT UNSIGNED    | FK → `users.UserID` CASCADE                 |
| `IsReady`    | BOOLEAN            | Alapértelmezett: `false`                     |

**Egyedi index:** `UNIQUE(SessionID, UserID)`

### 6.14 Infrastruktúra táblák

| Tábla                    | Cél                                    |
| ------------------------ | -------------------------------------- |
| `personal_access_tokens` | Sanctum bearer tokenek                 |
| `sessions`               | Laravel session tábla                  |
| `password_reset_tokens`  | Jelszó-visszaállító tokenek            |
| `cache` / `cache_locks`  | Laravel cache driver                   |
| `jobs` / `job_batches` / `failed_jobs` | Laravel queue rendszer   |

---

## 7. Modellek és kapcsolatok

### 7.1 Kapcsolati diagram

```
User
 ├── hasOne    → UserMoney
 ├── hasOne    → LeaderboardEntry
 ├── hasMany   → UserProgress    → belongsTo Level
 ├── hasMany   → UserAnswer      → belongsTo Question
 ├── hasMany   → Report
 └── belongsToMany → MultiplayerSession (pivot: multiplayer_session_users)

Level
 ├── hasMany   → Question
 │               ├── hasMany → QuestionOption
 │               ├── hasMany → Hint
 │               └── hasMany → UserAnswer
 ├── hasMany   → UserProgress
 └── hasMany   → MultiplayerSession
```

### 7.2 User

**Tábla:** `users` | **PK:** `UserID`

**Fillable:** `Username`, `Email`, `PasswordHash`, `IsAdmin`, `IsActive`
**Hidden:** `PasswordHash`, `remember_token`
**Cast:** `PasswordHash → hashed`, `IsAdmin → boolean`, `IsActive → boolean`
**Trait-ek:** `HasApiTokens`, `HasFactory`, `Notifiable`

### 7.3 Level

**Tábla:** `levels` | **PK:** `LevelID`

**Fillable:** `Name`, `Description`, `Category`, `OrderNumber`, `IsActive`, `BackgroundUrl`
**Kapcsolatok:** `questions() → hasMany`, `userProgress() → hasMany`

### 7.4 Question

**Tábla:** `questions` | **PK:** `QuestionID`

**Fillable:** `LevelID`, `QuestionText`, `CorrectAnswer`, `RewardDigit`, `MoneyReward`, `PositionX`, `PositionY`
**Kapcsolatok:** `level() → belongsTo`, `hints() → hasMany`, `options() → hasMany`, `userAnswers() → hasMany`

### 7.5 QuestionOption

**Tábla:** `question_options` | **PK:** `OptionID`

**Fillable:** `QuestionID`, `OptionText`, `IsCorrect`

### 7.6 Hint

**Tábla:** `hints` | **PK:** `HintID`

**Fillable:** `QuestionID`, `HintText`, `Cost`, `HintOrder`

### 7.7 UserProgress

**Tábla:** `user_progress` | **PK:** `ProgressID`

**Fillable:** `UserID`, `LevelID`, `Completed`, `TimeSpent`, `CompletedAt`

### 7.8 UserMoney

**Tábla:** `user_money` | **PK:** `UserID`

**Fillable:** `UserID`, `Amount`

### 7.9 UserAnswer

**Tábla:** `user_answers` | **PK:** `AnswerID`

**Fillable:** `UserID`, `QuestionID`, `GivenAnswer`, `IsCorrect`, `AnsweredAt`

### 7.10 LeaderboardEntry

**Tábla:** `leaderboard` | **PK:** `UserID`

**Fillable:** `UserID`, `Score`, `LevelsCompleted`, `TimeTotal`, `HintsUsed`

### 7.11 Report

**Tábla:** `reports` | **PK:** `ReportID` | **Timestamps:** igen

**Fillable:** `UserID`, `Title`, `Category`, `ContactEmail`, `Message`, `Page`, `Status`

### 7.12 MultiplayerSession

**Tábla:** `multiplayer_sessions` | **PK:** `id` | **Timestamps:** igen

**Fillable:** `LevelID`, `Status`, `SolvedQuestions`
**Cast:** `SolvedQuestions → array`
**Kapcsolat:** `users() → belongsToMany` (pivot: `multiplayer_session_users`, `IsReady`)

---

## 8. API végpontok

**Alap URL:** `http://<host>:8001/api`

Minden válasz JSON formátumú. Hitelesített végpontokhoz `Authorization: Bearer <token>` header szükséges.

### 8.1 Publikus végpontok

| Metódus | URL                   | Controller@Method              | Leírás                       |
| ------- | --------------------- | ------------------------------ | ---------------------------- |
| `POST`  | `/api/register`       | `AuthController@register`      | Új felhasználó regisztrálása |
| `POST`  | `/api/login`          | `AuthController@login`         | Bejelentkezés, token kiadása |
| `POST`  | `/api/reports/public`  | `ReportController@storePublic` | Nyilvános hibajelentés       |

#### POST /api/register

**Kérés:**
```json
{
  "Username": "felhasznalo",
  "Email": "email@example.com",
  "Password": "Jelszo123",
  "Password_confirmation": "Jelszo123"
}
```

**Validáció:** Username: kötelező, max 50, egyedi | Email: kötelező, max 100, egyedi | Password: kötelező, min 6, betű+szám

**Válasz (201):**
```json
{
  "message": "Sikeres regisztráció!",
  "user": { "UserID": 1, "Username": "felhasznalo", "Email": "email@example.com", "IsAdmin": false },
  "token": "1|abc123..."
}
```

**Mellékhatások:** `user_money` (Amount=0) és `leaderboard` rekord létrehozás.

#### POST /api/login

**Kérés:** `{ "Email": "email@example.com", "Password": "Jelszo123" }`

**Válasz (200):**
```json
{
  "message": "Sikeres bejelentkezés!",
  "user": { "UserID": 1, "Username": "...", "Email": "...", "IsAdmin": false, "IsActive": true, "Balance": 500 },
  "token": "2|xyz789..."
}
```

**Hibák:** 401 — Hibás email/jelszó | 403 — Inaktív fiók

### 8.2 Hitelesített végpontok

**Middleware:** `auth:sanctum` + `is_active`

| Metódus  | URL                                    | Controller@Method                | Leírás                          |
| -------- | -------------------------------------- | -------------------------------- | ------------------------------- |
| `POST`   | `/api/logout`                          | `AuthController@logout`          | Kijelentkezés                   |
| `GET`    | `/api/me`                              | `AuthController@me`              | Aktuális felhasználó adatai     |
| `PUT`    | `/api/me/password`                     | `AuthController@changePassword`  | Jelszó módosítás                |
| `GET`    | `/api/levels`                          | `LevelController@index`          | Szobák listázása                |
| `GET`    | `/api/levels/{id}`                     | `LevelController@show`           | Szoba részletei                 |
| `GET`    | `/api/levels/{levelId}/questions`      | `QuestionController@index`       | Szoba kérdései                  |
| `POST`   | `/api/questions/{id}/check-answer`     | `QuestionController@checkAnswer` | Válasz ellenőrzés               |
| `POST`   | `/api/levels/{levelId}/submit-code`    | `ProgressController@submitCode`  | Szobakód beküldés               |
| `DELETE` | `/api/me/reset-progress`               | `ProgressController@resetProgress`| Saját progress reset           |
| `GET`    | `/api/leaderboard`                     | `LeaderboardController@index`    | Top 10 ranglista                |
| `POST`   | `/api/reports`                         | `ReportController@store`         | Hitelesített hibajelentés       |
| `POST`   | `/api/multiplayer/join`                | `MultiplayerController@join`     | Multi csatlakozás/létrehozás    |
| `GET`    | `/api/multiplayer/{sessionId}/state`   | `MultiplayerController@state`    | Multi állapot polling           |
| `POST`   | `/api/multiplayer/{sessionId}/solve`   | `MultiplayerController@solve`    | Megoldott kérdés jelentése      |
| `POST`   | `/api/multiplayer/{sessionId}/finish`  | `MultiplayerController@finish`   | Multi befejezés                 |
| `DELETE` | `/api/multiplayer/{sessionId}/leave`   | `MultiplayerController@leave`    | Multi elhagyás                  |

#### Példa válaszok

**GET /api/levels:**
```json
[
  {
    "LevelID": 1, "Name": "A Könyvtárszoba", "Description": "...", "Category": "Nehéz",
    "OrderNumber": 1, "IsActive": true, "IsUnlocked": true, "IsCompleted": false,
    "BackgroundUrl": "rooms/room1/background.png"
  }
]
```

**POST /api/questions/{id}/check-answer — helyes:**
```json
{ "correct": true, "message": "Helyes válasz!", "RewardDigit": 7, "MoneyReward": 30, "NewBalance": 530 }
```

**POST /api/questions/{id}/check-answer — helytelen:**
```json
{ "correct": false, "message": "Helytelen válasz!", "WrongCount": 2, "Penalty": 30, "TimePenalty": 30, "NewBalance": 480 }
```

**POST /api/levels/{id}/submit-code — helyes:**
```json
{
  "correct": true, "message": "Gratulálunk!", "Score": 755, "TimeSpent": 245,
  "CompletedAt": "2025-06-12 14:30:00", "TotalScore": 1500, "LevelsCompleted": 3,
  "NextLevel": { "LevelID": 4, "Name": "A Kapitány Kabinja", "OrderNumber": 4 }
}
```

### 8.3 Admin végpontok

**Middleware:** `auth:sanctum` + `is_active` + `is_admin` | **Prefix:** `/api/admin`

| Metódus  | URL                                    | Leírás                        |
| -------- | -------------------------------------- | ----------------------------- |
| `GET`    | `/api/admin/stats`                     | Dashboard statisztikák        |
| `GET`    | `/api/admin/users`                     | Felhasználók (`?q=` kereső)   |
| `PUT`    | `/api/admin/users/{id}`                | Felhasználó módosítás         |
| `DELETE` | `/api/admin/users/{id}`                | Felhasználó törlés            |
| `DELETE` | `/api/admin/users/{id}/reset-progress` | Progress reset                |
| `GET`    | `/api/admin/levels`                    | Szobák listázása              |
| `POST`   | `/api/admin/levels`                    | Szoba létrehozás              |
| `PUT`    | `/api/admin/levels/{id}`               | Szoba módosítás               |
| `DELETE` | `/api/admin/levels/{id}`               | Szoba törlés                  |
| `GET`    | `/api/admin/questions`                 | Kérdések (`?level_id=` szűrő) |
| `POST`   | `/api/admin/questions`                 | Kérdés létrehozás             |
| `PUT`    | `/api/admin/questions/{id}`            | Kérdés módosítás              |
| `DELETE` | `/api/admin/questions/{id}`            | Kérdés törlés                 |
| `GET`    | `/api/admin/reports`                   | Jelentések (`?status=` szűrő) |
| `PUT`    | `/api/admin/reports/{id}`              | Jelentés státusz módosítás     |
| `DELETE` | `/api/admin/reports/{id}`              | Jelentés törlés               |

#### GET /api/admin/stats válasz

```json
{
  "totalUsers": 50, "activeUsers": 45, "totalLevels": 15, "totalQuestions": 300,
  "totalAnswers": 1200, "correctAnswers": 800, "completedRooms": 150, "newReports": 3
}
```

### 8.4 Hibaválaszok

| HTTP kód | Jelentés                                      |
| -------- | --------------------------------------------- |
| 400      | Hibás kérés / validációs hiba                 |
| 401      | Nem hitelesített (hiányzó/érvénytelen token)  |
| 403      | Hozzáférés megtagadva (inaktív/nem admin)     |
| 404      | Erőforrás nem található                       |
| 422      | Validációs hiba (Laravel formátum)            |

**Validációs hiba formátum (422):**
```json
{
  "message": "Validation failed",
  "errors": { "Email": ["The email field is required."], "Password": ["The password must be at least 6 characters."] }
}
```

---

## 9. Kontrollerek

### 9.1 AuthController

| Metódus              | Leírás                                                                         |
| -------------------- | ------------------------------------------------------------------------------ |
| `register(Request)`  | Validáció → user + money + leaderboard létrehozás → token (201)                |
| `login(Request)`     | Validáció → IsActive ellenőrzés → korábbi tokenek törlése → új token           |
| `logout(Request)`    | Aktuális access token törlése                                                  |
| `me(Request)`        | Felhasználó adatai Balance-szal                                                |
| `changePassword(Request)` | Régi jelszó ellenőrzés → új jelszó beállítás                              |

### 9.2 LevelController

**Trait:** `ChecksLevelUnlock`

| Metódus               | Leírás                                                          |
| --------------------- | --------------------------------------------------------------- |
| `index(Request)`      | Aktív szobák + `IsUnlocked`, `IsCompleted` kiszámított mezőkkel |
| `show(Request, $id)`  | Szoba részletei, feloldási jogosultság ellenőrzéssel             |

### 9.3 QuestionController

**Trait:** `ChecksLevelUnlock`

| Metódus                    | Leírás                                                                |
| -------------------------- | --------------------------------------------------------------------- |
| `index(Request, $levelId)` | Kérdések kevert opciókkal; `RewardDigit` csak megoldottaknál          |
| `checkAnswer(Request, $id)`| Válasz ellenőrzés + büntetési rendszer + `user_answers` naplózás      |

### 9.4 ProgressController

**Trait:** `ChecksLevelUnlock`

| Metódus                         | Leírás                                                    |
| ------------------------------- | --------------------------------------------------------- |
| `submitCode(Request, $levelId)` | Kód ellenőrzés → pontszámítás → ranglista frissítés       |
| `resetProgress(Request)`        | Csak ha minden szoba kész → teljes reset                  |

### 9.5 MultiplayerController

| Metódus                       | Leírás                                                                    |
| ----------------------------- | ------------------------------------------------------------------------- |
| `join(Request)`               | Matchmaking: meglévő session → várakozó → új létrehozás (DB lock-kal)     |
| `state(Request, $sessionId)`  | Polling: állapot lekérés; `abandoned` → cleanup                           |
| `solve(Request, $sessionId)`  | Megoldott kérdés hozzáfűzése a `SolvedQuestions` JSON-höz                  |
| `finish(Request, $sessionId)` | Befejezés: mindkét játékos `Completed=true`; nincs ranglista pont         |
| `leave(Request, $sessionId)`  | Kilépés: elhagyott session → `abandoned` státusz                          |

### 9.6 AdminController

Teljes CRUD felhasználókra, szobákra, kérdésekre és hibajelentésekre. Kérdés létrehozásnál/módosításnál pontosan 4 opció kötelező.

### 9.7 ReportController

| Metódus             | Leírás                                                     |
| ------------------- | ---------------------------------------------------------- |
| `store(Request)`    | Hitelesített jelentés (UserID automatikusan hozzárendelődik)|
| `storePublic(Request)` | Nyilvános jelentés (opcionális ContactEmail, UserID=null) |

---

## 10. Middleware és autentikáció

### 10.1 Autentikációs folyamat

```
Regisztráció:  POST /api/register → User + Money + Leaderboard → Sanctum token
Bejelentkezés: POST /api/login    → Credential check → IsActive check → Korábbi tokenek törlése → Új token
Kérések:       Authorization: Bearer <token> header
Kijelentkezés: POST /api/logout   → Aktuális token törlése
```

**A token Sanctum Bearer token** (opaque, adatbázisban tárolt) — **nem JWT**. A `personal_access_tokens` táblában hash-elve tárolódik, bármikor visszavonható.

**Egyetlen aktív token policy:** Bejelentkezéskor minden korábbi token törlődik.

### 10.2 Middleware-ek

| Middleware   | Alias       | Ellenőrzés                       | Hiba válasz                         |
| ------------ | ----------- | -------------------------------- | ----------------------------------- |
| `IsActive`   | `is_active` | `$user->IsActive === true`       | 403 — „A fiók inaktív."            |
| `IsAdmin`    | `is_admin`  | `$user->IsAdmin === true`        | 403 — „Hozzáférés megtagadva."     |

### 10.3 Middleware csoportok

| Útvonal csoport | Middleware lánc                           |
| --------------- | ---------------------------------------- |
| Publikus        | Nincs middleware                          |
| Hitelesített    | `auth:sanctum` → `is_active`             |
| Admin           | `auth:sanctum` → `is_active` → `is_admin`|

### 10.4 ChecksLevelUnlock Trait

**`isLevelUnlocked(int $userId, int $levelId): bool`**

1. Szint lekérése (aktívnak kell lennie)
2. Összes aktív szint a kategóriában, `OrderNumber` szerint
3. Ha az **első** a kategóriájában → mindig feloldott
4. Egyébként: az **előző szint** teljesítve kell legyen

---

## 11. Játéklogika

### 11.1 Szoba felépítés

Minden szoba:
- **20 kérdés** egy 20×4-es rácson pozicionálva
- Kérdésenként **4 opció** (ABCD), 1 helyes
- Minden kérdés ad egy **RewardDigit** (0–9) — a szoba kódjának egy számjegye
- Opcionálisan **segítségek** vásárolhatók

### 11.2 Büntetési rendszer

| Hibás válasz # | Típus          | Érték               |
| -------------- | -------------- | ------------------- |
| 1.             | Pénzbüntetés   | **-50** játékpénz   |
| 2.             | Időbüntetés    | **+30** másodperc   |
| 3+             | Időbüntetés    | **+120** másodperc  |

### 11.3 Szobakód

A szoba kódja = összes kérdés `RewardDigit` értéke, `PositionX` szerint növekvő sorrendben összefűzve.

### 11.4 Pontszámítás

```
Pontszám = max(100, 1000 - eltelt_idő_másodpercben)
```

| Eltelt idő | Pontszám |
| ---------- | -------- |
| < 1 perc   | 940+     |
| 5 perc     | 700      |
| 10 perc    | 400      |
| 15+ perc   | 100      |

**Multiplayer szobák NEM adnak ranglistapontot.**

### 11.5 Szint feloldási logika

3 független nehézségi kategória:

| Kategória | Szobák       |
| --------- | ------------ |
| Nehéz     | Room 1–5     |
| Könnyed   | Room 6–10    |
| Közepes   | Room 11–15   |

- Kategória **első szobája** mindig elérhető
- A következő szoba az **előző teljesítése után** nyílik meg
- A 3 kategória teljesen **független**

### 11.6 Multiplayer rendszer

- **Polling** (2s) — nincs WebSocket
- **Max 2 játékos** munkamenetenként
- **DB zárolás** (`lockForUpdate`) a versenyhelyzetek elkerülésére

**Életciklus:** `waiting → playing → finished / abandoned`

### 11.7 Pénzrendszer

| Esemény          | Hatás            |
| ---------------- | ---------------- |
| Regisztráció     | 0 pénz (seeder: 500) |
| Helyes válasz    | +20–70           |
| 1. hibás válasz  | -50              |
| 50/50 segítség   | -25 (kliensen)   |

### 11.8 Progress reset

| Típus         | Feltétel                       | Endpoint                              |
| ------------- | ------------------------------ | ------------------------------------- |
| Önkiszolgáló  | Minden szoba teljesítve        | `DELETE /api/me/reset-progress`       |
| Admin         | Nincs feltétel                 | `DELETE /api/admin/users/{id}/reset-progress` |

---

## 12. Seederek

Futtatás: `php artisan db:seed` | Sorrend: User → Level → Question → Report

### 12.1 Teszt felhasználók

| Felhasználónév | Email                   | Jelszó        | Admin |
| -------------- | ----------------------- | ------------- | ----- |
| `admin`        | `admin@szabadulo.hu`    | `Admin1234`   | Igen  |
| `jatekos1`     | `jatekos1@szabadulo.hu` | `Jatekos1234` | Nem   |
| `jatekos2`     | `jatekos2@szabadulo.hu` | `Jatekos1234` | Nem   |

### 12.2 Szobák (15 db)

| Sorrend | Név                    | Kategória |
| ------- | ---------------------- | --------- |
| 1–5     | Könyvtárszoba, Laboratorium, Kastély Pincéje, Kapitány Kabinja, Űrállomás | Nehéz |
| 6–10    | Játékszoba, Kávézó, Osztályterem, Kert, Cukrászda | Könnyed |
| 11–15   | Detektív Irodája, Múzeum, Téli Kunyhó, Hajógyár, Varázslatos Könyvtár | Közepes |

### 12.3 Kérdések

Szobánként **20 kérdés**, mindegyikhez: kérdés szöveg, helyes válasz, RewardDigit (0–9), MoneyReward (20–70), pozíció, 4 opció, 1 segítség.

### 12.4 Hibajelentések

7 minta hibajelentés különböző státuszokkal (`new`, `seen`, `resolved`) és kategóriákkal.

---

# III. RÉSZ – FRONTEND

---

## 13. Frontend technológiai stack

| Komponens        | Technológia                             |
| ---------------- | --------------------------------------- |
| Framework        | Angular 19.2.0                          |
| Nyelv            | TypeScript 5.7.2                        |
| Build rendszer   | @angular-devkit/build-angular           |
| HTTP kliens      | Angular HttpClient + interceptor        |
| Routing          | Angular Router (lazy loading)           |
| Formok           | Angular FormsModule (template-driven)   |
| Reaktív könyvtár | RxJS ~7.8.0                             |

### Függőségek

**Futtatási:** `@angular/common`, `@angular/core`, `@angular/forms`, `@angular/router` ^19.2.0, `rxjs ~7.8.0`, `zone.js ~0.15.0`

**Fejlesztői:** `@angular-devkit/build-angular ^19.2.19`, `@angular/cli ^19.2.19`, `typescript ~5.7.2`, Karma + Jasmine

### Elérhető scriptek

| Script  | Parancs    | Leírás                     |
| ------- | ---------- | -------------------------- |
| `start` | `ng serve` | Fejlesztői szerver          |
| `build` | `ng build` | Produkciós build            |
| `test`  | `ng test`  | Unit tesztek (Karma)        |

---

## 14. Frontend projektstruktúra

```
src/
├── index.html                     # Gyökér HTML (<app-root>)
├── main.ts                        # Bootstrap
├── styles.css                     # Globális stílusok
├── environments/
│   └── environment.ts             # API URL konfiguráció
└── app/
    ├── app.component.ts           # Gyökér komponens (<router-outlet>)
    ├── app.config.ts              # Alkalmazás providerek
    ├── app.routes.ts              # Útvonal definíciók
    ├── auth/
    │   ├── auth.css               # Közös auth stílusok
    │   ├── login/                 # Bejelentkezés + nyilvános hibajelentés
    │   └── register/              # Regisztráció
    ├── guards/
    │   └── auth.guard.ts          # authGuard, guestGuard, adminGuard
    ├── interceptors/
    │   └── auth.interceptor.ts    # Bearer token + 401 kezelés
    ├── models/                    # TypeScript interfészek (6 fájl)
    ├── pages/
    │   ├── admin/                 # Admin panel (5 tab)
    │   ├── game/                  # Szoba választó (főoldal)
    │   ├── leaderboard/           # Ranglista
    │   └── room/                  # Játékszoba
    └── services/                  # API kommunikáció (8 service)
```

**Statikus eszközök:** `public/rooms/room1/background.png ... room15/background.png`

---

## 15. Bootstrap és alkalmazás konfiguráció

### 15.1 Alkalmazás indítás

```typescript
bootstrapApplication(AppComponent, appConfig);
```

### 15.2 Providerek (`app.config.ts`)

1. **`provideZoneChangeDetection({ eventCoalescing: true })`** — Optimalizált change detection
2. **`provideRouter(routes)`** — Routing lazy loading támogatással
3. **`provideHttpClient(withInterceptors([authInterceptor]))`** — HTTP kliens auth interceptorral

### 15.3 Komponens architektúra

**Standalone komponensek** (nincs NgModule):

```typescript
@Component({
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
})
```

**Új kontrollfolyam direktívák:** `@if / @else`, `@for` (track kifejezéssel)

---

## 16. Routing és navigáció

### 16.1 Útvonal tábla

| Útvonal                        | Komponens              | Guard        | Lazy loaded |
| ------------------------------ | ---------------------- | ------------ | ----------- |
| `/login`                       | `LoginComponent`       | `guestGuard` | Igen        |
| `/register`                    | `RegisterComponent`    | `guestGuard` | Igen        |
| `/game`                        | `GameComponent`        | `authGuard`  | Igen        |
| `/room/:id`                    | `RoomComponent`        | `authGuard`  | Igen        |
| `/room/:id/multi/:sessionId`   | `RoomComponent`        | `authGuard`  | Igen        |
| `/leaderboard`                 | `LeaderboardComponent` | `authGuard`  | Igen        |
| `/admin`                       | `AdminComponent`       | `adminGuard` | Igen        |
| `` / `**`                      | → átirányítás `/login` | —            | —           |

### 16.2 Navigációs folyamat

```
Nem bejelentkezett    → /login engedélyezve, minden más → /login
Normál felhasználó    → /game, /room, /leaderboard; /login → /game; /admin → /game
Admin felhasználó     → /admin, /game, /room; /login → /admin
```

### 16.3 Szoba útvonalak

- **Szóló:** `/room/:id` → `isMultiplayer = false`
- **Multi:** `/room/:id/multi/:sessionId` → `isMultiplayer = true`, polling indul

---

## 17. Guardok és interceptor

### 17.1 HTTP Interceptor

Funkcionális interceptor (`HttpInterceptorFn`):

1. Header-ek: `Accept: application/json`, `Content-Type: application/json`
2. Token csatolás: `Authorization: Bearer <token>` (localStorage-ból)
3. **401 kezelés:** token + user törlése → átirányítás `/login`

### 17.2 Guardok

| Guard        | Logika                                                          | Használat              |
| ------------ | --------------------------------------------------------------- | ---------------------- |
| `authGuard`  | Bejelentkezve → OK; egyébként → `/login`                        | game, room, leaderboard|
| `guestGuard` | Nincs bejelentkezve → OK; admin → `/admin`; normál → `/game`    | login, register        |
| `adminGuard` | Bejelentkezve ÉS admin → OK; egyébként → `/game`               | admin                  |

### 17.3 Teljes autentikációs folyamat

```
1. Bejelentkezés/Regisztráció → token + user JSON localStorage-ba
2. Minden HTTP kérés → interceptor Bearer tokent csatol
3. 401 válasz → automatikus kijelentkezés → /login
4. Kijelentkezés gomb → POST /api/logout → localStorage törlés → /login
```

---

## 18. Modellek és interfészek

### 18.1 User

```typescript
interface User {
  UserID: number; Username: string; Email: string;
  IsAdmin: boolean; IsActive: boolean; CreatedAt?: string; Balance?: number;
}

interface AuthResponse { message: string; user: User; token: string; }
interface LoginRequest { Email: string; Password: string; }
interface RegisterRequest { Username: string; Email: string; Password: string; Password_confirmation: string; }
```

### 18.2 Level

```typescript
interface Level {
  LevelID: number; Name: string; Description: string;
  Category: string; OrderNumber: number;
  IsUnlocked: boolean; IsCompleted: boolean; IsActive: boolean;
  BackgroundUrl?: string | null;
}

interface LevelDetail extends Level { TimeSpent: number; CompletedAt?: string; }
```

### 18.3 Question

```typescript
interface Question {
  QuestionID: number; LevelID: number; QuestionText: string;
  PositionX: number; PositionY: number; MoneyReward: number;
  Solved?: boolean; RewardDigit?: number;
  Options?: { OptionID: number; OptionText: string; IsCorrect: boolean; }[];
}

interface CheckAnswerResponse {
  correct: boolean; message: string;
  RewardDigit?: number; MoneyReward?: number; NewBalance?: number;
  MoneyPenalty?: number; TimePenalty?: number; WrongCount?: number; Penalty?: number;
}
```

### 18.4 Hint

```typescript
interface Hint { HintID: number; HintOrder: number; Cost: number; HintText?: string; }

interface BuyHintResponse {
  message: string; HintID: number; HintOrder: number;
  HintText: string; Cost: number; NewBalance: number; HintsUsed: number;
}
```

### 18.5 Progress

```typescript
interface SubmitCodeRequest { code: string; timeSpent: number; }

interface SubmitCodeResponse {
  correct: boolean; message: string; Score?: number; TimeSpent?: number;
  CompletedAt?: string; TotalScore?: number; LevelsCompleted?: number;
  NextLevel?: { LevelID: number; Name: string; OrderNumber: number; };
}
```

### 18.6 Leaderboard

```typescript
interface LeaderboardEntry {
  UserID: number; Username?: string; Score: number;
  LevelsCompleted: number; TimeTotal: number; HintsUsed: number;
}
```

### 18.7 Multiplayer

```typescript
interface MultiplayerState {
  id: number; LevelID: number;
  Status: 'waiting' | 'playing' | 'finished' | 'abandoned';
  SolvedQuestions: { id: number; digit: number; }[];
  Players: { UserID: number; Username: string; IsReady: boolean; }[];
  MyUserID: number;
}
```

### 18.8 Admin interfészek

```typescript
interface AdminUser { UserID: number; Username: string; Email: string; IsAdmin: boolean; IsActive: boolean; Balance: number; Score: number; }
interface AdminLevel { LevelID: number; Name: string; Description: string; Category: string; OrderNumber: number; IsActive: boolean; }
interface AdminQuestion { QuestionID: number; LevelID: number; QuestionText: string; CorrectAnswer: string; RewardDigit: number; MoneyReward: number; PositionX: number; PositionY: number; options?: { OptionID?: number; OptionText: string; IsCorrect: boolean; }[]; }
interface AdminStats { totalUsers: number; activeUsers: number; totalLevels: number; totalQuestions: number; totalAnswers: number; correctAnswers: number; completedRooms: number; newReports: number; }
interface AdminReport { ReportID: number; UserID: number | null; Title: string; Category: string; ContactEmail?: string; Message: string; Page?: string; Status: 'new' | 'seen' | 'resolved'; created_at: string; }
```

---

## 19. Szolgáltatások (Services)

Minden szolgáltatás alap URL-je: `http://${window.location.hostname}:8001/api`

### 19.1 AuthService

| Metódus                     | HTTP | Endpoint           | Leírás                                    |
| --------------------------- | ---- | ------------------ | ----------------------------------------- |
| `login(data)`               | POST | `/api/login`       | Bejelentkezés → localStorage              |
| `register(data)`            | POST | `/api/register`    | Regisztráció → localStorage               |
| `logout()`                  | POST | `/api/logout`      | Kijelentkezés → törlés → /login           |
| `getMe()`                   | GET  | `/api/me`          | Aktuális felhasználó                      |
| `changePassword(cur, new)`  | PUT  | `/api/me/password` | Jelszóváltoztatás                         |
| `getUser()`, `getToken()`, `isLoggedIn()`, `isAdmin()` | — | — | Lokális állapot lekérdezések |

### 19.2 LevelService

| Metódus        | HTTP | Endpoint           |
| -------------- | ---- | ------------------ |
| `getLevels()`  | GET  | `/api/levels`      |
| `getLevel(id)` | GET  | `/api/levels/{id}` |

### 19.3 QuestionService

| Metódus                          | HTTP | Endpoint                            |
| -------------------------------- | ---- | ----------------------------------- |
| `getQuestions(levelId)`          | GET  | `/api/levels/{levelId}/questions`   |
| `checkAnswer(questionId, body)`  | POST | `/api/questions/{qId}/check-answer` |

### 19.4 HintService

| Metódus              | HTTP | Endpoint                      |
| -------------------- | ---- | ----------------------------- |
| `getHints(questionId)` | GET | `/api/questions/{qId}/hints` |
| `buyHint(hintId)`    | POST | `/api/hints/{hintId}/buy`    |

### 19.5 ProgressService

| Metódus                     | HTTP   | Endpoint                          |
| --------------------------- | ------ | --------------------------------- |
| `submitCode(levelId, body)` | POST   | `/api/levels/{levelId}/submit-code` |
| `resetMyProgress()`         | DELETE | `/api/me/reset-progress`          |

### 19.6 MultiplayerService

| Metódus                                | HTTP   | Endpoint                        |
| -------------------------------------- | ------ | ------------------------------- |
| `join(levelId)`                        | POST   | `/api/multiplayer/join`         |
| `getState(sessionId)`                  | GET    | `/api/multiplayer/{sId}/state`  |
| `solve(sessionId, questionId, digit)`  | POST   | `/api/multiplayer/{sId}/solve`  |
| `finish(sessionId)`                    | POST   | `/api/multiplayer/{sId}/finish` |
| `leave(sessionId)`                     | DELETE | `/api/multiplayer/{sId}/leave`  |

### 19.7 ReportService

| Metódus                   | HTTP | Endpoint              |
| ------------------------- | ---- | --------------------- |
| `createReport(data)`      | POST | `/api/reports`        |
| `createPublicReport(data)` | POST | `/api/reports/public` |

### 19.8 AdminService

Alap URL: `/api/admin` — Tartalmaz: `getStats()`, `getUsers()`, `updateUser()`, `deleteUser()`, `resetUserProgress()`, `getLevels()`, `createLevel()`, `updateLevel()`, `deleteLevel()`, `getQuestions()`, `createQuestion()`, `updateQuestion()`, `deleteQuestion()`, `getReports()`, `updateReport()`, `deleteReport()`

---

## 20. Komponensek

### 20.1 LoginComponent

**Útvonal:** `/login` | **Guard:** `guestGuard`

Bejelentkezési űrlap és nyilvános hibajelentés. Sikeres bejelentkezés után admin → `/admin`, normál → `/game`.

**Report kategóriák:** `forgotten-password`, `bug`, `account`, `question`, `other`

### 20.2 RegisterComponent

**Útvonal:** `/register` | **Guard:** `guestGuard`

Regisztrációs űrlap: Username (3–50), Email, Password (min 6), Password confirmation. Egyedi cross-field validator a jelszó-egyezésre.

### 20.3 GameComponent

**Útvonal:** `/game` | **Guard:** `authGuard`

Fő játékoldal — szobák böngészése, szóló/multiplayer indítás, jelszóváltoztatás, progress reset, hibajelentés.

**Főbb jellemzők:**
- Sticky fejléc: logó, felhasználónév, Ranglista/Admin gombok, kijelentkezés
- Szobák kategóriánként csoportosítva (Könnyed, Közepes, Nehéz)
- Szoba kártyák háttérképpel, állapot ikonok (teljesített / elérhető / zárolt)
- Egyjátékos + Többjátékos gombok aktív szobáknál
- Zárolt szobák halvány overlay-jel
- Reset banner minden szoba teljesítése után
- Lebegő hibajelentés FAB (? gomb)

**Állapotok:** `'completed'` (zöld keret) | `'active'` (kattintható) | `'locked'` (blúros, nem kattintható)

### 20.4 RoomComponent

**Útvonal:** `/room/:id` vagy `/room/:id/multi/:sessionId` | **Guard:** `authGuard`

Az aktív játék komponens — interaktív szoba kérdésekkel, időzítővel, számjegy-gyűjtéssel, kód beküldéssel.

**Játékmenet:**
1. Kérdés csomópontra kattintás → modál megnyitás
2. ABCD opció választás → API válasz-ellenőrzés
3. Helyes → számjegy + pénz animáció + 700ms után bezáródik
4. Helytelen → büntetés animáció + újrapróbálkozás
5. Összes kérdés megoldva → kód beírás → beküldés

**50/50 segítség:** 25 pénz, megtart 1 helyes + 1 rossz opciót.

**Multiplayer működés:**
- `waiting` → megosztó link; 2 játékos → `playing`
- 2 másodperces polling szinkronizálja a megoldásokat
- `abandoned` → automatikus átirányítás
- `ngOnDestroy()` → automatikus kilépés

**Szoba témák:**

| Téma     | Akcentus  | Háttér    |
| -------- | --------- | --------- |
| Könyvtár | `#c19a6b` | `#1a1410` |
| Labor    | `#34d399` | `#0a1a14` |
| Pince    | `#fb923c` | `#1a0e09` |
| Hajó     | `#38bdf8` | `#0a1220` |
| Űr       | `#c19a6b` | `#1a1410` |

### 20.5 LeaderboardComponent

**Útvonal:** `/leaderboard` | **Guard:** `authGuard`

Globális játékos ranglista.

**Rendezési szempontok:** Pontszám (csökkenő) | Teljesített szobák (csökkenő) | Összidő (növekvő) | Segítségek (növekvő)

**Jellemzők:** Statisztika sor, top 3 dobogó (arany/ezüst/bronz), rangsor táblázat „Te" jelvénnyel.

### 20.6 AdminComponent

**Útvonal:** `/admin` | **Guard:** `adminGuard`

Teljes admin felület 5 tab-bal, bal oldali navigációval.

**1. Irányítópult:** Statisztika rácsozat (felhasználók, szobák, kérdések, teljesítések, hibajelentések, válasz arány).

**2. Felhasználók:** Keresés, adattábla, szerkesztés modállal, aktiválás/inaktiválás, progress reset, törlés.

**3. Szobák:** CRUD, szerkesztő modál (név, leírás, kategória, sorrend, aktív, háttérkép URL előnézettel).

**4. Kérdések:** Szoba szűrő, CRUD, szerkesztő modál (szoba, kérdés, RewardDigit 0–9, MoneyReward, pozíció, ABCD opciók rádiógombbal).

**5. Hibajelentések:** Státusz szűrő (Összes/Új/Megtekintett/Megoldott), státusz módosítás, törlés.

**Toast értesítések:** Sikeres (zöld) / Hiba (piros) — 3 másodperces auto-dismiss.

---

## 21. Stílusrendszer

### 21.1 Szín paletta

| Szín              | Kód       | Használat                          |
| ----------------- | --------- | ---------------------------------- |
| Elsődleges háttér | `#1a1410` | Body, kártyák, fejléc              |
| Elsődleges szöveg | `#e8ddd0` | Szöveg, címek                      |
| Arany akcentus    | `#c19a6b` | Gombok, keretek, kiemelések        |
| Halvány szöveg    | `#8a7a6a` | Másodlagos szöveg                  |
| Kártya háttér     | `#2a1f15` | Szoba kártyák, modálok             |
| Hiba / piros      | `#fc8181` | Validációs hibák                   |
| Siker / zöld      | `#34d399` | Teljesített állapotok              |
| Multi / kék       | `#7dd3fc` | Multiplayer gombok                 |
| Admin / narancs   | `#fb923c` | Admin jelvény                      |

### 21.2 Stílus fájlok

| Fájl                        | Hatókör         | Leírás                               |
| --------------------------- | --------------- | ------------------------------------ |
| `src/styles.css`            | Globális        | CSS reset, body, megosztott report   |
| `auth/auth.css`             | Login+Register  | Közös kártya, űrlap, gomb stílusok  |
| `game.component.css`        | Game            | Fejléc, szoba kártyák, kategóriák   |
| `room.component.css`        | Room            | Szoba témák, kérdés csomópontok     |
| `leaderboard.component.css` | Leaderboard     | Dobogó, statisztikák, rangsor       |
| `admin.component.css`       | Admin           | Sidebar, táblák, szerkesztő modálok |

### 21.3 Megosztott report rendszer (globális)

`.report-fab` (52px kör FAB), `.report-overlay`, `.report-modal`, `.report-category-grid`, `.report-submit`

### 21.4 Reszponzív breakpointok

| Breakpoint | Változás                          |
| ---------- | --------------------------------- |
| ≤ 900px    | Game: kártyák oszlopváltás        |
| ≤ 768px    | Leaderboard: dobogó vertikális    |
| ≤ 700px    | Admin: sidebar összecsukás        |
| ≤ 600px    | Game: fejléc stackelés            |
| ≤ 480px    | Room: modál teljes szélességű     |
