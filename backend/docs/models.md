# Modellek és kapcsolatok

## Tartalomjegyzék

- [User](#user)
- [Level](#level)
- [Question](#question)
- [QuestionOption](#questionoption)
- [Hint](#hint)
- [UserProgress](#userprogress)
- [UserMoney](#usermoney)
- [UserAnswer](#useranswer)
- [LeaderboardEntry](#leaderboardentry)
- [Report](#report)
- [MultiplayerSession](#multiplayersession)
- [Kapcsolati diagram](#kapcsolati-diagram)

---

## User

**Fájl:** `app/Models/User.php`  
**Tábla:** `users`  
**Elsődleges kulcs:** `UserID`  
**Időbélyeg:** nincs (csak `CreatedAt` manuálisan)

### Mezők (fillable)

`Username`, `Email`, `PasswordHash`, `IsAdmin`, `IsActive`

### Rejtett mezők

`PasswordHash`, `remember_token`

### Típuskonverziók (casts)

| Mező           | Típus     |
| -------------- | --------- |
| `PasswordHash` | `hashed`  |
| `IsAdmin`      | `boolean` |
| `IsActive`     | `boolean` |

### Egyedi metódusok

- `getAuthPassword()` — Sanctum kompatibilitáshoz a `PasswordHash` mezőt adja vissza jelszóként

### Kapcsolatok

| Kapcsolat       | Típus      | Cél modell       |
| --------------- | ---------- | ---------------- |
| `money()`       | `hasOne`   | `UserMoney`      |
| `progress()`    | `hasMany`  | `UserProgress`   |
| `answers()`     | `hasMany`  | `UserAnswer`     |
| `leaderboard()` | `hasOne`   | `LeaderboardEntry` |

### Trait-ek

`HasApiTokens`, `HasFactory`, `Notifiable`

---

## Level

**Fájl:** `app/Models/Level.php`  
**Tábla:** `levels`  
**Elsődleges kulcs:** `LevelID`  
**Időbélyeg:** nincs

### Mezők (fillable)

`Name`, `Description`, `Category`, `OrderNumber`, `IsActive`, `BackgroundUrl`

### Típuskonverziók

| Mező        | Típus      |
| ----------- | ---------- |
| `IsActive`  | `boolean`  |
| `CreatedAt` | `datetime` |

### Kapcsolatok

| Kapcsolat         | Típus     | Cél modell      |
| ----------------- | --------- | --------------- |
| `questions()`     | `hasMany` | `Question`      |
| `userProgress()`  | `hasMany` | `UserProgress`  |

---

## Question

**Fájl:** `app/Models/Question.php`  
**Tábla:** `questions`  
**Elsődleges kulcs:** `QuestionID`  
**Időbélyeg:** nincs

### Mezők (fillable)

`LevelID`, `QuestionText`, `CorrectAnswer`, `RewardDigit`, `MoneyReward`, `PositionX`, `PositionY`

### Típuskonverziók

| Mező          | Típus     |
| ------------- | --------- |
| `RewardDigit` | `integer` |
| `MoneyReward` | `integer` |
| `PositionX`   | `integer` |
| `PositionY`   | `integer` |

### Kapcsolatok

| Kapcsolat       | Típus      | Cél modell       |
| --------------- | ---------- | ---------------- |
| `level()`       | `belongsTo`| `Level`          |
| `hints()`       | `hasMany`  | `Hint`           |
| `options()`     | `hasMany`  | `QuestionOption` |
| `userAnswers()` | `hasMany`  | `UserAnswer`     |

---

## QuestionOption

**Fájl:** `app/Models/QuestionOption.php`  
**Tábla:** `question_options`  
**Elsődleges kulcs:** `OptionID`  
**Időbélyeg:** nincs

### Mezők (fillable)

`QuestionID`, `OptionText`, `IsCorrect`

### Típuskonverziók

| Mező        | Típus     |
| ----------- | --------- |
| `IsCorrect` | `boolean` |

### Kapcsolatok

| Kapcsolat    | Típus       | Cél modell  |
| ------------ | ----------- | ----------- |
| `question()` | `belongsTo` | `Question`  |

---

## Hint

**Fájl:** `app/Models/Hint.php`  
**Tábla:** `hints`  
**Elsődleges kulcs:** `HintID`  
**Időbélyeg:** nincs

### Mezők (fillable)

`QuestionID`, `HintText`, `Cost`, `HintOrder`

### Típuskonverziók

| Mező        | Típus     |
| ----------- | --------- |
| `Cost`      | `integer` |
| `HintOrder` | `integer` |

### Kapcsolatok

| Kapcsolat    | Típus       | Cél modell  |
| ------------ | ----------- | ----------- |
| `question()` | `belongsTo` | `Question`  |

---

## UserProgress

**Fájl:** `app/Models/UserProgress.php`  
**Tábla:** `user_progress`  
**Elsődleges kulcs:** `ProgressID`  
**Időbélyeg:** nincs

### Mezők (fillable)

`UserID`, `LevelID`, `Completed`, `TimeSpent`, `CompletedAt`

### Típuskonverziók

| Mező          | Típus      |
| ------------- | ---------- |
| `Completed`   | `boolean`  |
| `TimeSpent`   | `integer`  |
| `CompletedAt` | `datetime` |

### Kapcsolatok

| Kapcsolat  | Típus       | Cél modell |
| ---------- | ----------- | ---------- |
| `user()`   | `belongsTo` | `User`     |
| `level()`  | `belongsTo` | `Level`    |

---

## UserMoney

**Fájl:** `app/Models/UserMoney.php`  
**Tábla:** `user_money`  
**Elsődleges kulcs:** `UserID` (nem auto-increment)  
**Időbélyeg:** nincs

### Mezők (fillable)

`UserID`, `Amount`

### Kapcsolatok

| Kapcsolat | Típus       | Cél modell |
| --------- | ----------- | ---------- |
| `user()`  | `belongsTo` | `User`     |

---

## UserAnswer

**Fájl:** `app/Models/UserAnswer.php`  
**Tábla:** `user_answers`  
**Elsődleges kulcs:** `AnswerID`  
**Időbélyeg:** nincs

### Mezők (fillable)

`UserID`, `QuestionID`, `GivenAnswer`, `IsCorrect`, `AnsweredAt`

### Típuskonverziók

| Mező         | Típus      |
| ------------ | ---------- |
| `IsCorrect`  | `boolean`  |
| `AnsweredAt` | `datetime` |

### Kapcsolatok

| Kapcsolat    | Típus       | Cél modell  |
| ------------ | ----------- | ----------- |
| `user()`     | `belongsTo` | `User`      |
| `question()` | `belongsTo` | `Question`  |

---

## LeaderboardEntry

**Fájl:** `app/Models/LeaderboardEntry.php`  
**Tábla:** `leaderboard`  
**Elsődleges kulcs:** `UserID` (nem auto-increment)  
**Időbélyeg:** nincs

### Mezők (fillable)

`UserID`, `Score`, `LevelsCompleted`, `TimeTotal`, `HintsUsed`

### Típuskonverziók

Minden mező `integer`.

### Kapcsolatok

| Kapcsolat | Típus       | Cél modell |
| --------- | ----------- | ---------- |
| `user()`  | `belongsTo` | `User`     |

---

## Report

**Fájl:** `app/Models/Report.php`  
**Tábla:** `reports`  
**Elsődleges kulcs:** `ReportID`  
**Időbélyeg:** igen (Laravel `created_at`, `updated_at`)

### Mezők (fillable)

`UserID`, `Title`, `Category`, `ContactEmail`, `Message`, `Page`, `Status`

### Kapcsolatok

| Kapcsolat | Típus       | Cél modell |
| --------- | ----------- | ---------- |
| `user()`  | `belongsTo` | `User`     |

---

## MultiplayerSession

**Fájl:** `app/Models/MultiplayerSession.php`  
**Tábla:** `multiplayer_sessions`  
**Elsődleges kulcs:** `id` (alapértelmezett)  
**Időbélyeg:** igen

### Mezők (fillable)

`LevelID`, `Status`, `SolvedQuestions`

### Típuskonverziók

| Mező              | Típus   |
| ----------------- | ------- |
| `SolvedQuestions`  | `array` |

### Kapcsolatok

| Kapcsolat  | Típus           | Cél modell | Megjegyzés                                               |
| ---------- | --------------- | ---------- | -------------------------------------------------------- |
| `users()`  | `belongsToMany` | `User`     | Pivot: `multiplayer_session_users` (`IsReady`, timestamps) |
| `level()`  | `belongsTo`     | `Level`    |                                                          |

---

## Kapcsolati diagram

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
