# Szabadulószoba – Backend Dokumentáció

## Tartalomjegyzék

1. [Áttekintés](#1-áttekintés)
2. [Technológiai stack](#2-technológiai-stack)
3. [Telepítés és beállítás](#3-telepítés-és-beállítás)
4. [Mappastruktúra](#4-mappastruktúra)
5. [Adatbázis séma](#5-adatbázis-séma)
6. [Modellek és kapcsolatok](#6-modellek-és-kapcsolatok)
7. [API végpontok](#7-api-végpontok)
8. [Kontrollerek](#8-kontrollerek)
9. [Middleware és autentikáció](#9-middleware-és-autentikáció)
10. [Játéklogika](#10-játéklogika)
11. [Seederek](#11-seederek)

---

## 1. Áttekintés

A Szabadulószoba backend egy **Laravel 12.x** alapú RESTful JSON API, amely a játék összes szerver-oldali logikáját kezeli: felhasználó-kezelés, szoba/szint menedzsment, kérdés-válasz rendszer, pontszámítás, multiplayer munkamenetek és hibajelentések.

Az API a **Laravel Sanctum** tokenalapú autentikációt használja Bearer tokenekkel.

---

## 2. Technológiai stack

| Komponens      | Technológia                          |
| -------------- | ------------------------------------ |
| Framework      | Laravel 12.x (PHP ^8.2)             |
| Autentikáció   | Laravel Sanctum 4.3 (Bearer token)  |
| Adatbázis      | MySQL (alapértelmezett), SQLite is   |
| API stílus     | RESTful JSON                         |
| Multiplayer    | Polling-alapú (nincs WebSocket)      |

### 2.1 Függőségek (composer.json)

**Produkciós:**
- `laravel/framework ^12.0`
- `laravel/sanctum ^4.3`
- `laravel/tinker ^2.10.1`

**Fejlesztői:**
- `fakerphp/faker` — teszt adatok generálása
- `laravel/pail` — log figyelés
- `laravel/pint` — kód formázás
- `laravel/sail` — Docker fejlesztési környezet
- `mockery/mockery` — mock objektumok teszteléshez
- `nunomaduro/collision` — szebb hiba kimenetek
- `phpunit/phpunit ^11.5.3` — unit tesztek

---

## 3. Telepítés és beállítás

### 3.1 Előfeltételek

- PHP >= 8.2
- Composer
- MySQL szerver (vagy SQLite)

### 3.2 Telepítési lépések

```bash
# 1. Függőségek telepítése
composer install

# 2. Környezeti fájl létrehozása
cp .env.example .env

# 3. Alkalmazás kulcs generálása
php artisan key:generate

# 4. Adatbázis beállítása (.env fájlban)
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=szabaduloszoba-db
#    DB_USERNAME=root
#    DB_PASSWORD=

# 5. Migrációk futtatása
php artisan migrate

# 6. Seederek futtatása (teszt adatok)
php artisan db:seed

# 7. Szerver indítása
php artisan serve --port=8001
```

### 3.3 Környezeti változók (.env)

| Változó          | Alapértelmezett érték  | Leírás                    |
| ---------------- | ---------------------- | ------------------------- |
| `DB_CONNECTION`  | `mysql`                | Adatbázis driver          |
| `DB_HOST`        | `127.0.0.1`            | Adatbázis host            |
| `DB_PORT`        | `3306`                 | Adatbázis port            |
| `DB_DATABASE`    | `szabaduloszoba-db`    | Adatbázis neve            |
| `DB_USERNAME`    | `root`                 | Felhasználó               |
| `DB_PASSWORD`    | (üres)                 | Jelszó                    |
| `BCRYPT_ROUNDS`  | `12`                   | Jelszó hash erősség       |
| `SESSION_DRIVER` | `database`             | Session driver            |
| `CACHE_STORE`    | `database`             | Cache driver              |

### 3.4 CORS konfiguráció

Fájl: `config/cors.php`

- Engedélyezett útvonal: `api/*`, `sanctum/csrf-cookie`
- Minden origin, metódus és header engedélyezett (`*`)
- Credentials: `false`

### 3.5 Sanctum konfiguráció

Fájl: `config/sanctum.php`

- Stateful domének: `localhost`, `localhost:3000`, `127.0.0.1`, `127.0.0.1:8000`, `::1`
- Token lejárat: nincs (`null`)

---

## 4. Mappastruktúra

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php        # Admin CRUD műveletek
│   │   │   ├── AuthController.php         # Regisztráció, bejelentkezés, jelszóváltoztatás
│   │   │   ├── HintController.php         # Segítségek (nincs regisztrálva az útvonalakban)
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
│   ├── auth.php                           # Autentikáció konfig
│   ├── cors.php                           # CORS beállítások
│   ├── database.php                       # Adatbázis konfig
│   └── sanctum.php                        # Sanctum konfig
├── database/
│   ├── migrations/                        # Adatbázis migrációk
│   └── seeders/                           # Teszt adatok
├── routes/
│   └── api.php                            # API útvonalak
└── .env.example                           # Környezeti változók sablon
```

---

## 5. Adatbázis séma

### 5.1 users

Felhasználói fiókok tárolása.

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

### 5.2 levels

Szabadulószobák (pályák) definíciója.

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

**Kategóriák:** `Könnyed` (Easy), `Közepes` (Medium), `Nehéz` (Hard)

### 5.3 questions

Kérdések, amelyek a szobákban találhatók.

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

### 5.4 question_options

Többválasztós kérdések opciói (mindig 4 db).

| Oszlop       | Típus              | Megkötések                                   |
| ------------ | ------------------ | -------------------------------------------- |
| `OptionID`   | BIGINT UNSIGNED PK | Auto increment                               |
| `QuestionID` | BIGINT UNSIGNED    | FK → `questions.QuestionID` CASCADE          |
| `OptionText` | VARCHAR(255)       |                                              |
| `IsCorrect`  | BOOLEAN            | Alapértelmezett: `false`                     |

### 5.5 hints

Kérdésekhez tartozó segítségek.

| Oszlop       | Típus              | Megkötések                                   |
| ------------ | ------------------ | -------------------------------------------- |
| `HintID`     | BIGINT UNSIGNED PK | Auto increment                               |
| `QuestionID` | BIGINT UNSIGNED    | FK → `questions.QuestionID` CASCADE          |
| `HintText`   | TEXT               |                                              |
| `Cost`       | INTEGER            | Ár (játékpénzben)                            |
| `HintOrder`  | INTEGER            | Megjelenítési sorrend                        |

### 5.6 user_progress

Felhasználók szobánkénti előrehaladása.

| Oszlop        | Típus              | Megkötések                                   |
| ------------- | ------------------ | -------------------------------------------- |
| `ProgressID`  | BIGINT UNSIGNED PK | Auto increment                               |
| `UserID`      | BIGINT UNSIGNED    | FK → `users.UserID` CASCADE                 |
| `LevelID`     | BIGINT UNSIGNED    | FK → `levels.LevelID` CASCADE               |
| `Completed`   | BOOLEAN            | Alapértelmezett: `false`                     |
| `TimeSpent`   | INTEGER            | Alapértelmezett: `0` (másodpercben)          |
| `CompletedAt` | DATETIME           | Nullable                                     |

**Egyedi index:** `UNIQUE(UserID, LevelID)`

### 5.7 user_money

Felhasználók játékpénz egyenlege (1:1 a users táblával).

| Oszlop   | Típus              | Megkötések                                   |
| -------- | ------------------ | -------------------------------------------- |
| `UserID` | BIGINT UNSIGNED PK | FK → `users.UserID` CASCADE                 |
| `Amount` | INTEGER            | Alapértelmezett: `0`                         |

### 5.8 user_answers

Minden megválaszolt kérdés naplózása.

| Oszlop       | Típus              | Megkötések                                   |
| ------------ | ------------------ | -------------------------------------------- |
| `AnswerID`   | BIGINT UNSIGNED PK | Auto increment                               |
| `UserID`     | BIGINT UNSIGNED    | FK → `users.UserID` CASCADE                 |
| `QuestionID` | BIGINT UNSIGNED    | FK → `questions.QuestionID` CASCADE          |
| `GivenAnswer`| VARCHAR(255)       |                                              |
| `IsCorrect`  | BOOLEAN            |                                              |
| `AnsweredAt` | DATETIME           | Alapértelmezett: `NOW()`                     |

### 5.9 leaderboard

Ranglista rekordok (1:1 a users táblával).

| Oszlop            | Típus              | Megkötések                                   |
| ----------------- | ------------------ | -------------------------------------------- |
| `UserID`          | BIGINT UNSIGNED PK | FK → `users.UserID` CASCADE                 |
| `Score`           | INTEGER            | Alapértelmezett: `0`                         |
| `LevelsCompleted` | INTEGER            | Alapértelmezett: `0`                         |
| `TimeTotal`       | INTEGER            | Alapértelmezett: `0` (másodpercben)          |
| `HintsUsed`       | INTEGER            | Alapértelmezett: `0`                         |

### 5.10 reports

Hibajelentések és visszajelzések.

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
| `created_at`   | TIMESTAMP                             | Laravel timestamp                            |
| `updated_at`   | TIMESTAMP                             | Laravel timestamp                            |

**Kategóriák:** `bug`, `forgotten-password`, `question`, `account`, `other`

### 5.11 multiplayer_sessions

Többjátékos munkamenetek.

| Oszlop            | Típus                                              | Megkötések               |
| ----------------- | -------------------------------------------------- | ------------------------ |
| `id`              | BIGINT UNSIGNED PK                                 | Auto increment           |
| `LevelID`         | BIGINT UNSIGNED                                    | FK → `levels.LevelID` CASCADE |
| `Status`          | ENUM('waiting','playing','finished','abandoned')    | Alapértelmezett: `'waiting'` |
| `SolvedQuestions`  | JSON                                               | Alapértelmezett: `'[]'`  |
| `created_at`      | TIMESTAMP                                          | Laravel timestamp        |
| `updated_at`      | TIMESTAMP                                          | Laravel timestamp        |

**SolvedQuestions formátum:** `[{"id": 42, "digit": 7}, ...]`

### 5.12 multiplayer_session_users

Pivot tábla – játékosok és munkamenetek összekapcsolása.

| Oszlop       | Típus              | Megkötések                                            |
| ------------ | ------------------ | ----------------------------------------------------- |
| `id`         | BIGINT UNSIGNED PK | Auto increment                                        |
| `SessionID`  | BIGINT UNSIGNED    | FK → `multiplayer_sessions.id` CASCADE                |
| `UserID`     | BIGINT UNSIGNED    | FK → `users.UserID` CASCADE                          |
| `IsReady`    | BOOLEAN            | Alapértelmezett: `false`                              |
| `created_at` | TIMESTAMP          | Laravel timestamp                                     |
| `updated_at` | TIMESTAMP          | Laravel timestamp                                     |

**Egyedi index:** `UNIQUE(SessionID, UserID)`

### 5.13 Infrastruktúra táblák

| Tábla                    | Cél                                    |
| ------------------------ | -------------------------------------- |
| `password_reset_tokens`  | Jelszó-visszaállító tokenek            |
| `sessions`               | Laravel session tábla                  |
| `personal_access_tokens` | Sanctum bearer tokenek                 |
| `cache` / `cache_locks`  | Laravel cache driver                   |
| `jobs` / `job_batches` / `failed_jobs` | Laravel queue rendszer   |

### 5.14 ER-diagram

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

---

## 6. Modellek és kapcsolatok

### 6.1 User

**Fájl:** `app/Models/User.php` | **Tábla:** `users` | **PK:** `UserID` | **Timestamps:** nincs

**Fillable:** `Username`, `Email`, `PasswordHash`, `IsAdmin`, `IsActive`  
**Hidden:** `PasswordHash`, `remember_token`

| Cast           | Típus     |
| -------------- | --------- |
| `PasswordHash` | `hashed`  |
| `IsAdmin`      | `boolean` |
| `IsActive`     | `boolean` |

| Kapcsolat       | Típus      | Cél modell         |
| --------------- | ---------- | ------------------ |
| `money()`       | `hasOne`   | `UserMoney`        |
| `progress()`    | `hasMany`  | `UserProgress`     |
| `answers()`     | `hasMany`  | `UserAnswer`       |
| `leaderboard()` | `hasOne`   | `LeaderboardEntry` |

Egyedi metódus: `getAuthPassword()` — a `PasswordHash` mezőt adja vissza jelszóként (Sanctum kompatibilitás).

Trait-ek: `HasApiTokens`, `HasFactory`, `Notifiable`

### 6.2 Level

**Fájl:** `app/Models/Level.php` | **Tábla:** `levels` | **PK:** `LevelID` | **Timestamps:** nincs

**Fillable:** `Name`, `Description`, `Category`, `OrderNumber`, `IsActive`, `BackgroundUrl`

| Kapcsolat         | Típus     | Cél modell      |
| ----------------- | --------- | --------------- |
| `questions()`     | `hasMany` | `Question`      |
| `userProgress()`  | `hasMany` | `UserProgress`  |

### 6.3 Question

**Fájl:** `app/Models/Question.php` | **Tábla:** `questions` | **PK:** `QuestionID` | **Timestamps:** nincs

**Fillable:** `LevelID`, `QuestionText`, `CorrectAnswer`, `RewardDigit`, `MoneyReward`, `PositionX`, `PositionY`

| Kapcsolat       | Típus       | Cél modell       |
| --------------- | ----------- | ---------------- |
| `level()`       | `belongsTo` | `Level`          |
| `hints()`       | `hasMany`   | `Hint`           |
| `options()`     | `hasMany`   | `QuestionOption` |
| `userAnswers()` | `hasMany`   | `UserAnswer`     |

### 6.4 QuestionOption

**Fájl:** `app/Models/QuestionOption.php` | **Tábla:** `question_options` | **PK:** `OptionID`

**Fillable:** `QuestionID`, `OptionText`, `IsCorrect`

| Kapcsolat    | Típus       | Cél modell  |
| ------------ | ----------- | ----------- |
| `question()` | `belongsTo` | `Question`  |

### 6.5 Hint

**Fájl:** `app/Models/Hint.php` | **Tábla:** `hints` | **PK:** `HintID`

**Fillable:** `QuestionID`, `HintText`, `Cost`, `HintOrder`

| Kapcsolat    | Típus       | Cél modell  |
| ------------ | ----------- | ----------- |
| `question()` | `belongsTo` | `Question`  |

### 6.6 UserProgress

**Fájl:** `app/Models/UserProgress.php` | **Tábla:** `user_progress` | **PK:** `ProgressID`

**Fillable:** `UserID`, `LevelID`, `Completed`, `TimeSpent`, `CompletedAt`

| Kapcsolat  | Típus       | Cél modell |
| ---------- | ----------- | ---------- |
| `user()`   | `belongsTo` | `User`     |
| `level()`  | `belongsTo` | `Level`    |

### 6.7 UserMoney

**Fájl:** `app/Models/UserMoney.php` | **Tábla:** `user_money` | **PK:** `UserID` (nem auto-increment)

**Fillable:** `UserID`, `Amount`

| Kapcsolat | Típus       | Cél modell |
| --------- | ----------- | ---------- |
| `user()`  | `belongsTo` | `User`     |

### 6.8 UserAnswer

**Fájl:** `app/Models/UserAnswer.php` | **Tábla:** `user_answers` | **PK:** `AnswerID`

**Fillable:** `UserID`, `QuestionID`, `GivenAnswer`, `IsCorrect`, `AnsweredAt`

| Kapcsolat    | Típus       | Cél modell  |
| ------------ | ----------- | ----------- |
| `user()`     | `belongsTo` | `User`      |
| `question()` | `belongsTo` | `Question`  |

### 6.9 LeaderboardEntry

**Fájl:** `app/Models/LeaderboardEntry.php` | **Tábla:** `leaderboard` | **PK:** `UserID` (nem auto-increment)

**Fillable:** `UserID`, `Score`, `LevelsCompleted`, `TimeTotal`, `HintsUsed`

| Kapcsolat | Típus       | Cél modell |
| --------- | ----------- | ---------- |
| `user()`  | `belongsTo` | `User`     |

### 6.10 Report

**Fájl:** `app/Models/Report.php` | **Tábla:** `reports` | **PK:** `ReportID` | **Timestamps:** igen

**Fillable:** `UserID`, `Title`, `Category`, `ContactEmail`, `Message`, `Page`, `Status`

| Kapcsolat | Típus       | Cél modell |
| --------- | ----------- | ---------- |
| `user()`  | `belongsTo` | `User`     |

### 6.11 MultiplayerSession

**Fájl:** `app/Models/MultiplayerSession.php` | **Tábla:** `multiplayer_sessions` | **PK:** `id` | **Timestamps:** igen

**Fillable:** `LevelID`, `Status`, `SolvedQuestions`  
**Cast:** `SolvedQuestions → array`

| Kapcsolat  | Típus           | Cél modell | Megjegyzés                                               |
| ---------- | --------------- | ---------- | -------------------------------------------------------- |
| `users()`  | `belongsToMany` | `User`     | Pivot: `multiplayer_session_users` (`IsReady`, timestamps) |
| `level()`  | `belongsTo`     | `Level`    |                                                          |

### 6.12 Kapcsolati diagram

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
 └── hasMany   → MultiplayerSession (belongsTo)
```

---

## 7. API végpontok

### Alap URL

```
http://<host>:8001/api
```

Minden válasz JSON formátumú. Hitelesített végpontokhoz `Authorization: Bearer <token>` header szükséges.

### 7.1 Publikus végpontok (nincs autentikáció)

| Metódus | URL                  | Controller@Method              | Leírás                       |
| ------- | -------------------- | ------------------------------ | ---------------------------- |
| `POST`  | `/api/register`      | `AuthController@register`      | Új felhasználó regisztrálása |
| `POST`  | `/api/login`         | `AuthController@login`         | Bejelentkezés, token kiadása |
| `POST`  | `/api/reports/public` | `ReportController@storePublic` | Nyilvános hibajelentés       |

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

**Validáció:**
- `Username`: kötelező, max 50 karakter, egyedi
- `Email`: kötelező, max 100 karakter, érvényes email, egyedi
- `Password`: kötelező, min 6 karakter, tartalmaznia kell betűt és számot

**Válasz (201):**
```json
{
  "message": "Sikeres regisztráció!",
  "user": { "UserID": 1, "Username": "felhasznalo", "Email": "email@example.com", "IsAdmin": false, "IsActive": true },
  "token": "1|abc123..."
}
```

**Mellékhatások:** Létrehozza a `user_money` (Amount=0) és `leaderboard` rekordot.

#### POST /api/login

**Kérés:**
```json
{ "Email": "email@example.com", "Password": "Jelszo123" }
```

**Válasz (200):**
```json
{
  "message": "Sikeres bejelentkezés!",
  "user": { "UserID": 1, "Username": "...", "Email": "...", "IsAdmin": false, "IsActive": true, "Balance": 500 },
  "token": "2|xyz789..."
}
```

**Hibák:** 401 — Hibás email/jelszó | 403 — Inaktív fiók

#### POST /api/reports/public

**Kérés:**
```json
{
  "Title": "Nem tudok bejelentkezni",
  "Category": "forgotten-password",
  "ContactEmail": "email@example.com",
  "Message": "Elfelejtettem a jelszavam...",
  "Page": "login"
}
```

### 7.2 Hitelesített végpontok

Middleware: `auth:sanctum` + `is_active`

| Metódus  | URL                               | Controller@Method                | Leírás                          |
| -------- | --------------------------------- | -------------------------------- | ------------------------------- |
| `POST`   | `/api/logout`                     | `AuthController@logout`          | Kijelentkezés                   |
| `GET`    | `/api/me`                         | `AuthController@me`              | Aktuális felhasználó adatai     |
| `PUT`    | `/api/me/password`                | `AuthController@changePassword`  | Jelszó módosítás                |
| `GET`    | `/api/levels`                     | `LevelController@index`          | Szobák listázása                |
| `GET`    | `/api/levels/{id}`                | `LevelController@show`           | Szoba részletei                 |
| `GET`    | `/api/levels/{levelId}/questions` | `QuestionController@index`       | Szoba kérdései                  |
| `POST`   | `/api/questions/{id}/check-answer`| `QuestionController@checkAnswer`  | Válasz ellenőrzés              |
| `POST`   | `/api/levels/{levelId}/submit-code`| `ProgressController@submitCode`  | Szobakód beküldés              |
| `DELETE` | `/api/me/reset-progress`          | `ProgressController@resetProgress`| Saját progress reset           |
| `GET`    | `/api/leaderboard`                | `LeaderboardController@index`    | Top 10 ranglista                |
| `POST`   | `/api/reports`                    | `ReportController@store`         | Hitelesített hibajelentés       |
| `POST`   | `/api/multiplayer/join`           | `MultiplayerController@join`     | Multi csatlakozás/létrehozás    |
| `GET`    | `/api/multiplayer/{sessionId}/state`| `MultiplayerController@state`  | Multi állapot polling           |
| `POST`   | `/api/multiplayer/{sessionId}/solve`| `MultiplayerController@solve`  | Megoldott kérdés jelentése      |
| `POST`   | `/api/multiplayer/{sessionId}/finish`| `MultiplayerController@finish`| Multi befejezés                 |
| `DELETE` | `/api/multiplayer/{sessionId}/leave`| `MultiplayerController@leave`  | Multi elhagyás                  |

#### GET /api/levels

**Válasz:** Összes aktív szoba feloldási/teljesítési állapottal.
```json
[
  {
    "LevelID": 1, "Name": "A Könyvtárszoba", "Description": "...", "Category": "Nehéz",
    "OrderNumber": 1, "IsActive": true, "IsUnlocked": true, "IsCompleted": false,
    "BackgroundUrl": "rooms/room1/background.png"
  }
]
```

#### POST /api/questions/{id}/check-answer

**Kérés:** `{ "answer": "Válasz B" }`

**Válasz (helyes):**
```json
{ "correct": true, "message": "Helyes válasz!", "RewardDigit": 7, "MoneyReward": 30, "NewBalance": 530 }
```

**Válasz (helytelen):**
```json
{ "correct": false, "message": "Helytelen válasz!", "WrongCount": 2, "Penalty": 30, "TimePenalty": 30, "NewBalance": 480 }
```

#### POST /api/levels/{levelId}/submit-code

**Kérés:** `{ "code": "31472859605131", "timeSpent": 245 }`

**Válasz (helyes):**
```json
{
  "correct": true, "message": "Gratulálunk!", "Score": 755, "TimeSpent": 245,
  "CompletedAt": "2025-06-12 14:30:00", "TotalScore": 1500, "LevelsCompleted": 3,
  "NextLevel": { "LevelID": 4, "Name": "A Kapitány Kabinja", "OrderNumber": 4 }
}
```

#### POST /api/multiplayer/join

**Kérés:** `{ "level_id": 1 }`

**Válasz:**
```json
{
  "id": 1, "LevelID": 1, "Status": "waiting", "SolvedQuestions": [],
  "Players": [{ "UserID": 1, "Username": "jatekos1", "IsReady": false }],
  "MyUserID": 1
}
```

### 7.3 Admin végpontok

Middleware: `auth:sanctum` + `is_active` + `is_admin` | Prefix: `/api/admin`

| Metódus  | URL                                    | Controller@Method                    | Leírás                        |
| -------- | -------------------------------------- | ------------------------------------ | ----------------------------- |
| `GET`    | `/api/admin/stats`                     | `AdminController@stats`              | Statisztikák                  |
| `GET`    | `/api/admin/users`                     | `AdminController@users`              | Felhasználók (`?q=` kereső)   |
| `PUT`    | `/api/admin/users/{id}`                | `AdminController@updateUser`         | Felhasználó módosítás         |
| `DELETE` | `/api/admin/users/{id}`                | `AdminController@deleteUser`         | Felhasználó törlés            |
| `DELETE` | `/api/admin/users/{id}/reset-progress` | `AdminController@resetUserProgress`  | Progress reset                |
| `GET`    | `/api/admin/levels`                    | `AdminController@levels`             | Szobák listázása              |
| `POST`   | `/api/admin/levels`                    | `AdminController@createLevel`        | Szoba létrehozás              |
| `PUT`    | `/api/admin/levels/{id}`               | `AdminController@updateLevel`        | Szoba módosítás               |
| `DELETE` | `/api/admin/levels/{id}`               | `AdminController@deleteLevel`        | Szoba törlés                  |
| `GET`    | `/api/admin/questions`                 | `AdminController@questions`          | Kérdések (`?level_id=` szűrő) |
| `POST`   | `/api/admin/questions`                 | `AdminController@createQuestion`     | Kérdés létrehozás             |
| `PUT`    | `/api/admin/questions/{id}`            | `AdminController@updateQuestion`     | Kérdés módosítás              |
| `DELETE` | `/api/admin/questions/{id}`            | `AdminController@deleteQuestion`     | Kérdés törlés                 |
| `GET`    | `/api/admin/reports`                   | `AdminController@reports`            | Jelentések (`?status=` szűrő) |
| `PUT`    | `/api/admin/reports/{id}`              | `AdminController@updateReport`       | Jelentés státusz módosítás     |
| `DELETE` | `/api/admin/reports/{id}`              | `AdminController@deleteReport`       | Jelentés törlés               |

#### GET /api/admin/stats

```json
{
  "totalUsers": 50, "activeUsers": 45, "totalLevels": 15, "totalQuestions": 300,
  "totalAnswers": 1200, "correctAnswers": 800, "completedRooms": 150, "newReports": 3
}
```

### 7.4 Hibaválaszok

| HTTP kód | Jelentés                                      |
| -------- | --------------------------------------------- |
| 400      | Hibás kérés / validációs hiba                 |
| 401      | Nem hitelesített (hiányzó/érvénytelen token)  |
| 403      | Hozzáférés megtagadva (inaktív/nem admin)     |
| 404      | Erőforrás nem található                       |
| 422      | Validációs hiba (Laravel formátum)            |
| 500      | Szerver hiba                                  |

**Validációs hiba formátum (422):**
```json
{
  "message": "Validation failed",
  "errors": { "Email": ["The email field is required."], "Password": ["The password must be at least 6 characters."] }
}
```

---

## 8. Kontrollerek

### 8.1 AuthController

**Fájl:** `app/Http/Controllers/AuthController.php`

| Metódus              | Leírás                                                                         |
| -------------------- | ------------------------------------------------------------------------------ |
| `register(Request)`  | Regisztráció: validáció → user + money + leaderboard létrehozás → token (201)  |
| `login(Request)`     | Bejelentkezés: validáció → IsActive ellenőrzés → korábbi tokenek törlése → új token |
| `logout(Request)`    | Aktuális access token törlése                                                  |
| `me(Request)`        | Felhasználó adatai Balance-szal                                                |
| `changePassword(Request)` | Régi jelszó ellenőrzés → új jelszó beállítás                              |

### 8.2 LevelController

**Fájl:** `app/Http/Controllers/LevelController.php` | **Trait:** `ChecksLevelUnlock`

| Metódus               | Leírás                                                          |
| --------------------- | --------------------------------------------------------------- |
| `index(Request)`      | Aktív szobák + `IsUnlocked`, `IsCompleted` kiszámított mezőkkel |
| `show(Request, $id)`  | Szoba részletei, feloldási jogosultság ellenőrzéssel             |

### 8.3 QuestionController

**Fájl:** `app/Http/Controllers/QuestionController.php` | **Trait:** `ChecksLevelUnlock`

| Metódus                    | Leírás                                                                |
| -------------------------- | --------------------------------------------------------------------- |
| `index(Request, $levelId)` | Kérdések kevert opciókkal; `RewardDigit` csak megoldottaknál          |
| `checkAnswer(Request, $id)`| Válasz ellenőrzés + büntetési rendszer + `user_answers` naplózás      |

### 8.4 ProgressController

**Fájl:** `app/Http/Controllers/ProgressController.php` | **Trait:** `ChecksLevelUnlock`

| Metódus                                  | Leírás                                                         |
| ---------------------------------------- | -------------------------------------------------------------- |
| `submitCode(Request, $levelId)`          | Kód ellenőrzés → pontszámítás → ranglista frissítés            |
| `resetProgress(Request)`                 | Csak ha minden szoba kész → teljes reset                       |
| `doResetProgress($userId)` (statikus)    | UserProgress, UserAnswer, Leaderboard törlés, Money nullázás   |
| `doResetLevelProgress($userId, $levelId)` (statikus) | Egyetlen szoba progressének törlése              |

### 8.5 LeaderboardController

**Fájl:** `app/Http/Controllers/LeaderboardController.php`

| Metódus   | Leírás                                            |
| --------- | ------------------------------------------------- |
| `index()` | Top 10 ranglista Score szerint csökkenő sorrendben |

### 8.6 MultiplayerController

**Fájl:** `app/Http/Controllers/MultiplayerController.php`

| Metódus                      | Leírás                                                                    |
| ---------------------------- | ------------------------------------------------------------------------- |
| `join(Request)`              | Matchmaking: meglévő session → várakozó → új létrehozás (DB lock-kal)     |
| `state(Request, $sessionId)` | Polling: állapot lekérés; `abandoned` → cleanup                           |
| `solve(Request, $sessionId)` | Megoldott kérdés hozzáfűzése a `SolvedQuestions` JSON-höz                  |
| `finish(Request, $sessionId)`| Befejezés: mindkét játékos `Completed=true`; **nincs ranglista pont**     |
| `leave(Request, $sessionId)` | Kilépés: résztvevők progressének törlése; üres → delete, van → `abandoned` |

### 8.7 ReportController

**Fájl:** `app/Http/Controllers/ReportController.php`

| Metódus             | Leírás                                                 |
| ------------------- | ------------------------------------------------------ |
| `store(Request)`    | Hitelesített jelentés (UserID automatikusan hozzárendelődik) |
| `storePublic(Request)` | Nyilvános jelentés (opcionális ContactEmail, UserID=null)  |

### 8.8 AdminController

**Fájl:** `app/Http/Controllers/AdminController.php`

Teljes CRUD felhasználókra, szobákra, kérdésekre és hibajelentésekre. Statisztika végpont a dashboardhoz.

Kérdés létrehozás/módosítás: pontosan 4 opció kötelező (`size:4`). Módosításnál a régi opciók törlődnek.

### 8.9 HintController (nem regisztrált)

**Fájl:** `app/Http/Controllers/HintController.php`

> Ez a kontroller **nincs regisztrálva** a `routes/api.php`-ban.

| Metódus               | Leírás                                                |
| --------------------- | ----------------------------------------------------- |
| `index($questionId)`  | Segítségek listázása (`HintText` nélkül)              |
| `buy(Request, $id)`   | Vásárlás: Cost levonás → HintsUsed növelés → HintText |

---

## 9. Middleware és autentikáció

### 9.1 Autentikációs folyamat

```
Regisztráció:  POST /api/register → User + Money + Leaderboard → Sanctum token
Bejelentkezés: POST /api/login    → Credential check → IsActive check → Korábbi tokenek törlése → Új token
Kérések:       Authorization: Bearer <token> header
Kijelentkezés: POST /api/logout   → Aktuális token törlése
```

**Egyetlen aktív token policy:** Bejelentkezéskor minden korábbi token törlődik.

### 9.2 Middleware-ek

Regisztráció helye: `bootstrap/app.php`

#### IsActive (`is_active`)

**Fájl:** `app/Http/Middleware/IsActive.php`

Ellenőrzi: `$request->user()->IsActive === true`  
Hibás: 403 — `"A fiók inaktív."`

#### IsAdmin (`is_admin`)

**Fájl:** `app/Http/Middleware/IsAdmin.php`

Ellenőrzi: `$request->user()->IsAdmin === true`  
Hibás: 403 — `"Hozzáférés megtagadva. Admin jogosultság szükséges."`

### 9.3 Middleware csoportok

| Útvonal csoport  | Middleware lánc                           |
| ----------------- | ---------------------------------------- |
| Publikus          | Nincs middleware                          |
| Hitelesített      | `auth:sanctum` → `is_active`             |
| Admin             | `auth:sanctum` → `is_active` → `is_admin`|

### 9.4 ChecksLevelUnlock Trait

**Fájl:** `app/Http/Traits/ChecksLevelUnlock.php`

Használja: `LevelController`, `QuestionController`, `ProgressController`

**`isLevelUnlocked(int $userId, int $levelId): bool`**

1. Megkeresi a szintet (aktívnak kell lennie)
2. Lekéri az összes aktív szintet ugyanabban a `Category`-ban, `OrderNumber` szerint rendezve
3. Ha ez az **első** szint a kategóriájában → mindig feloldott
4. Egyébként: az **előző szint** teljesítve kell legyen a `user_progress`-ben

---

## 10. Játéklogika

### 10.1 Szoba felépítés

Minden szoba (level) a következőkből áll:
- **20 kérdés** egy 20×4-es rácson pozicionálva
- Minden kérdéshez **4 opció** (ABCD), amelyből 1 helyes
- Minden kérdés ad egy **RewardDigit** (0–9), amely a szoba kódjának egy számjegye
- Opcionálisan **segítségek** (hints) vásárolhatók

### 10.2 Büntetési rendszer

| Hibás válasz # | Büntetés típusa       | Értéke              |
| -------------- | --------------------- | ------------------- |
| 1.             | Pénzbüntetés          | **-50** játékpénz   |
| 2.             | Időbüntetés           | **+30** másodperc   |
| 3+             | Időbüntetés           | **+120** másodperc  |

### 10.3 Szobakód mechanika

A szoba kódja = összes kérdés `RewardDigit` értéke, `PositionX` szerint növekvő sorrendben összefűzve.

### 10.4 Pontszámítás

```
Pontszám = max(100, 1000 - eltelt_idő_másodpercben)
```

| Eltelt idő   | Pontszám |
| ------------ | -------- |
| < 1 perc     | 940+     |
| 5 perc       | 700      |
| 10 perc      | 400      |
| 15+ perc     | 100      |

**Multiplayer szobák NEM adnak ranglistapontot.**

### 10.5 Szint feloldási logika

3 független kategória:

| Kategória | Szobák     | Sorrend  |
| --------- | ---------- | -------- |
| Nehéz     | Room 1–5   | 1→2→3→4→5  |
| Könnyed   | Room 6–10  | 6→7→8→9→10 |
| Közepes   | Room 11–15 | 11→12→13→14→15 |

- Minden kategória **első szobája mindig elérhető**
- A következő szoba az **előző teljesítése után** válik elérhetővé
- A 3 kategória teljesen **független**

### 10.6 Multiplayer rendszer

- **Polling alapú** — 2 másodperces lekérdezés (nem WebSocket)
- **Max 2 játékos** per munkamenet
- **Matchmaking:** meglévő `waiting` session → csatlakozás; nincs → létrehozás
- **DB zárolás:** `lockForUpdate` a versenyhelyzetek elkerülésére

**Munkamenet életciklus:**
```
waiting → playing → finished
                  ↘ abandoned
```

### 10.7 Pénzrendszer

- Regisztrációkor: **0 pénz** (seeder: 500)
- Helyes válasz: **+MoneyReward** (20–70)
- 1. hibás válasz: **-50** pénz
- 50/50 segítség: **-25** pénz (kliens oldalon)
- Az egyenleg nem mehet 0 alá

### 10.8 Előrehaladás visszaállítása

| Típus             | Feltétel                           | Endpoint                              |
| ----------------- | ---------------------------------- | ------------------------------------- |
| Önkiszolgáló      | Minden aktív szoba teljesítve      | `DELETE /api/me/reset-progress`       |
| Admin             | Nincs feltétel                     | `DELETE /api/admin/users/{id}/reset-progress` |

**Törlődik:** UserProgress, UserAnswer, LeaderboardEntry. **Nullázódik:** UserMoney. A felhasználói fiók megmarad.

---

## 11. Seederek

Futtatás: `php artisan db:seed`

Sorrend: `UserSeeder` → `LevelSeeder` → `QuestionSeeder` → `ReportSeeder`

### 11.1 UserSeeder

| Felhasználónév | Email                   | Jelszó        | Admin |
| -------------- | ----------------------- | ------------- | ----- |
| `admin`        | `admin@szabadulo.hu`    | `Admin1234`   | Igen  |
| `jatekos1`     | `jatekos1@szabadulo.hu` | `Jatekos1234` | Nem   |
| `jatekos2`     | `jatekos2@szabadulo.hu` | `Jatekos1234` | Nem   |

Mindegyikhez 500 induló pénz és üres ranglista bejegyzés.

### 11.2 LevelSeeder

15 szoba 3 nehézségi kategóriában:

| Sorrend | Név                    | Kategória |
| ------- | ---------------------- | --------- |
| 1       | A Könyvtárszoba        | Nehéz     |
| 2       | A Laboratorium         | Nehéz     |
| 3       | A Kastély Pincéje      | Nehéz     |
| 4       | A Kapitány Kabinja     | Nehéz     |
| 5       | Az Űrállomás           | Nehéz     |
| 6       | A Játékszoba           | Könnyed   |
| 7       | A Kávézó               | Könnyed   |
| 8       | Az Osztályterem        | Könnyed   |
| 9       | A Kert                 | Könnyed   |
| 10      | A Cukrászda            | Könnyed   |
| 11      | A Detektív Irodája     | Közepes   |
| 12      | A Múzeum               | Közepes   |
| 13      | A Téli Kunyhó          | Közepes   |
| 14      | A Hajógyár             | Közepes   |
| 15      | A Varázslatos Könyvtár | Közepes   |

### 11.3 QuestionSeeder

Szobánként **20 kérdés**, mindegyikhez:
- Kérdés szöveg, helyes válasz, RewardDigit (0–9), MoneyReward (20–70)
- Pozíció (PositionX: 1–20, PositionY: 1–4)
- 1 segítség (hint) költséggel (10–45)
- 4 opció (1 helyes + 3 helytelen)

### 11.4 ReportSeeder

7 minta hibajelentés különböző státuszokkal (`new`, `seen`, `resolved`) és kategóriákkal.
