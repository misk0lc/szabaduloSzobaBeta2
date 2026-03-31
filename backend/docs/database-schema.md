# Adatbázis séma

## Tartalomjegyzék

- [users](#users)
- [levels](#levels)
- [questions](#questions)
- [question_options](#question_options)
- [hints](#hints)
- [user_progress](#user_progress)
- [user_money](#user_money)
- [user_answers](#user_answers)
- [leaderboard](#leaderboard)
- [reports](#reports)
- [multiplayer_sessions](#multiplayer_sessions)
- [multiplayer_session_users](#multiplayer_session_users)
- [Infrastruktúra táblák](#infrastruktúra-táblák)
- [ER-diagram](#er-diagram)

---

## users

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

---

## levels

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

---

## questions

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

A helyes megoldásért kapott `RewardDigit` alkotja a szoba kódját (`PositionX` szerint rendezve).

---

## question_options

Többválasztós kérdések opciói (mindig 4 db).

| Oszlop       | Típus              | Megkötések                                   |
| ------------ | ------------------ | -------------------------------------------- |
| `OptionID`   | BIGINT UNSIGNED PK | Auto increment                               |
| `QuestionID` | BIGINT UNSIGNED    | FK → `questions.QuestionID` CASCADE          |
| `OptionText` | VARCHAR(255)       |                                              |
| `IsCorrect`  | BOOLEAN            | Alapértelmezett: `false`                     |

---

## hints

Kérdésekhez tartozó segítségek.

| Oszlop       | Típus              | Megkötések                                   |
| ------------ | ------------------ | -------------------------------------------- |
| `HintID`     | BIGINT UNSIGNED PK | Auto increment                               |
| `QuestionID` | BIGINT UNSIGNED    | FK → `questions.QuestionID` CASCADE          |
| `HintText`   | TEXT               |                                              |
| `Cost`       | INTEGER            | Ár (játékpénzben)                            |
| `HintOrder`  | INTEGER            | Megjelenítési sorrend                        |

---

## user_progress

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

---

## user_money

Felhasználók játékpénz egyenlege (1:1 a users táblával).

| Oszlop   | Típus              | Megkötések                                   |
| -------- | ------------------ | -------------------------------------------- |
| `UserID` | BIGINT UNSIGNED PK | FK → `users.UserID` CASCADE                 |
| `Amount` | INTEGER            | Alapértelmezett: `0`                         |

---

## user_answers

Minden megválaszolt kérdés naplózása.

| Oszlop       | Típus              | Megkötések                                   |
| ------------ | ------------------ | -------------------------------------------- |
| `AnswerID`   | BIGINT UNSIGNED PK | Auto increment                               |
| `UserID`     | BIGINT UNSIGNED    | FK → `users.UserID` CASCADE                 |
| `QuestionID` | BIGINT UNSIGNED    | FK → `questions.QuestionID` CASCADE          |
| `GivenAnswer`| VARCHAR(255)       |                                              |
| `IsCorrect`  | BOOLEAN            |                                              |
| `AnsweredAt` | DATETIME           | Alapértelmezett: `NOW()`                     |

---

## leaderboard

Ranglista rekordok (1:1 a users táblával).

| Oszlop            | Típus              | Megkötések                                   |
| ----------------- | ------------------ | -------------------------------------------- |
| `UserID`          | BIGINT UNSIGNED PK | FK → `users.UserID` CASCADE                 |
| `Score`           | INTEGER            | Alapértelmezett: `0`                         |
| `LevelsCompleted` | INTEGER            | Alapértelmezett: `0`                         |
| `TimeTotal`       | INTEGER            | Alapértelmezett: `0` (másodpercben)          |
| `HintsUsed`       | INTEGER            | Alapértelmezett: `0`                         |

---

## reports

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

---

## multiplayer_sessions

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

---

## multiplayer_session_users

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

---

## Infrastruktúra táblák

| Tábla                    | Cél                                    |
| ------------------------ | -------------------------------------- |
| `password_reset_tokens`  | Jelszó-visszaállító tokenek            |
| `sessions`               | Laravel session tábla                  |
| `personal_access_tokens` | Sanctum bearer tokenek                 |
| `cache` / `cache_locks`  | Laravel cache driver                   |
| `jobs` / `job_batches` / `failed_jobs` | Laravel queue rendszer   |

---

## ER-diagram

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

**Kapcsolatok összefoglalása:**
- Egy felhasználó (user) több szobához (level) rendelkezhet előrehaladással
- Egy szobának több kérdése van, minden kérdésnek 4 opciója és opcionálisan segítségei
- Minden válasz naplózásra kerül (user_answers)
- A ranglista és pénzegyenleg 1:1 kapcsolatban áll a felhasználóval
- A multiplayer session max. 2 játékost köt össze egy szobával
