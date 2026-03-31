# API végpontok

## Tartalomjegyzék

- [Autentikáció](#autentikáció)
- [Publikus végpontok](#publikus-végpontok)
- [Hitelesített végpontok](#hitelesített-végpontok)
- [Admin végpontok](#admin-végpontok)
- [Hibaválaszok](#hibaválaszok)

---

## Alap URL

```
http://<host>:8001/api
```

Minden válasz JSON formátumú. Hitelesített végpontokhoz `Authorization: Bearer <token>` header szükséges.

---

## Autentikáció

A rendszer **Laravel Sanctum** tokenalapú autentikációt használ.

- Regisztrációkor és bejelentkezéskor a szerver Bearer tokent ad vissza
- A token a `personal_access_tokens` táblában tárolódik
- Bejelentkezéskor minden korábbi token törlődik (egyetlen aktív token)
- A token nem jár le automatikusan

---

## Publikus végpontok

Ezekhez nem szükséges autentikáció.

### POST /api/register

Új felhasználó regisztrálása.

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
  "user": {
    "UserID": 1,
    "Username": "felhasznalo",
    "Email": "email@example.com",
    "IsAdmin": false,
    "IsActive": true
  },
  "token": "1|abc123..."
}
```

**Mellékhatások:** Létrehozza a `user_money` (Amount=0) és `leaderboard` rekordot.

---

### POST /api/login

Bejelentkezés.

**Kérés:**
```json
{
  "Email": "email@example.com",
  "Password": "Jelszo123"
}
```

**Validáció:**
- `Email`: kötelező, érvényes email
- `Password`: kötelező

**Válasz (200):**
```json
{
  "message": "Sikeres bejelentkezés!",
  "user": { "UserID": 1, "Username": "...", "Email": "...", "IsAdmin": false, "IsActive": true, "Balance": 500 },
  "token": "2|xyz789..."
}
```

**Hibák:**
- 401: Hibás email/jelszó
- 403: Inaktív fiók

---

### POST /api/reports/public

Nyilvános hibajelentés (bejelentkezés nélkül).

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

**Validáció:**
- `Title`: kötelező, max 100 karakter
- `Category`: kötelező
- `ContactEmail`: opcionális, érvényes email, max 100 karakter
- `Message`: kötelező
- `Page`: opcionális, max 100 karakter

---

## Hitelesített végpontok

Middleware: `auth:sanctum` + `is_active`

### POST /api/logout

Kijelentkezés (aktuális token törlése).

**Válasz:** `{ "message": "Sikeres kijelentkezés!" }`

---

### GET /api/me

Aktuális felhasználó adatai.

**Válasz:**
```json
{
  "UserID": 1,
  "Username": "felhasznalo",
  "Email": "email@example.com",
  "IsAdmin": false,
  "IsActive": true,
  "CreatedAt": "2025-01-01 12:00:00",
  "Balance": 450
}
```

---

### PUT /api/me/password

Jelszó módosítás.

**Kérés:**
```json
{
  "current_password": "RegiJelszo123",
  "new_password": "UjJelszo456"
}
```

---

### GET /api/levels

Összes aktív szoba listázása, feloldási állapottal.

**Válasz:**
```json
[
  {
    "LevelID": 1,
    "Name": "A Könyvtárszoba",
    "Description": "Egy titokzatos könyvtár...",
    "Category": "Nehéz",
    "OrderNumber": 1,
    "IsActive": true,
    "IsUnlocked": true,
    "IsCompleted": false,
    "BackgroundUrl": "rooms/room1/background.png"
  }
]
```

---

### GET /api/levels/{id}

Egyetlen szoba részletei előrehaladási adatokkal.

**Válasz:** A szoba adatai + `TimeSpent`, `CompletedAt` mezők.

---

### GET /api/levels/{levelId}/questions

Szoba kérdéseinek lekérése.

**Válasz:**
```json
[
  {
    "QuestionID": 1,
    "LevelID": 1,
    "QuestionText": "Mi a kérdés?",
    "PositionX": 5,
    "PositionY": 2,
    "MoneyReward": 30,
    "Solved": true,
    "RewardDigit": 7,
    "Options": [
      { "OptionID": 1, "OptionText": "Válasz A", "IsCorrect": false },
      { "OptionID": 2, "OptionText": "Válasz B", "IsCorrect": true },
      { "OptionID": 3, "OptionText": "Válasz C", "IsCorrect": false },
      { "OptionID": 4, "OptionText": "Válasz D", "IsCorrect": false }
    ]
  }
]
```

- A `CorrectAnswer` szöveg soha nincs kitéve
- A `RewardDigit` csak megoldott kérdéseknél jelenik meg
- Az opciók véletlenszerű sorrendben érkeznek

---

### POST /api/questions/{id}/check-answer

Válasz ellenőrzése.

**Kérés:**
```json
{ "answer": "Válasz B" }
```

**Válasz (helyes):**
```json
{
  "correct": true,
  "message": "Helyes válasz!",
  "RewardDigit": 7,
  "MoneyReward": 30,
  "NewBalance": 530
}
```

**Válasz (helytelen):**
```json
{
  "correct": false,
  "message": "Helytelen válasz!",
  "WrongCount": 2,
  "Penalty": 30,
  "TimePenalty": 30,
  "NewBalance": 480
}
```

**Büntetési rendszer:**

| Hibás válasz # | Büntetés                      |
| -------------- | ----------------------------- |
| 1.             | -50 játékpénz                 |
| 2.             | +30 másodperc időbüntetés     |
| 3+             | +120 másodperc időbüntetés    |

---

### POST /api/levels/{levelId}/submit-code

Szobakód beküldése.

**Kérés:**
```json
{
  "code": "31472859605131",
  "timeSpent": 245
}
```

**Válasz (helyes):**
```json
{
  "correct": true,
  "message": "Gratulálunk! Sikeresen teljesítetted a szobát!",
  "Score": 755,
  "TimeSpent": 245,
  "CompletedAt": "2025-06-12 14:30:00",
  "TotalScore": 1500,
  "LevelsCompleted": 3,
  "NextLevel": { "LevelID": 4, "Name": "A Kapitány Kabinja", "OrderNumber": 4 }
}
```

**Pontszámítás:** `max(100, 1000 - timeSpent_másodperc)`

---

### DELETE /api/me/reset-progress

Saját előrehaladás visszaállítása. Csak akkor elérhető, ha minden aktív szoba teljesítve van.

**Törlődik:** UserProgress, UserAnswer, LeaderboardEntry. UserMoney nullázódik. Multiplayer munkamenetek elhagyása.

---

### GET /api/leaderboard

Top 10 ranglista.

**Válasz:**
```json
[
  {
    "UserID": 1,
    "Username": "felhasznalo",
    "Score": 2500,
    "LevelsCompleted": 5,
    "TimeTotal": 1200,
    "HintsUsed": 3
  }
]
```

---

### POST /api/reports

Hitelesített hibajelentés.

**Kérés:**
```json
{
  "Title": "Bug a szobában",
  "Category": "bug",
  "Message": "A kérdés nem jelenik meg...",
  "Page": "room"
}
```

---

### Multiplayer végpontok

#### POST /api/multiplayer/join

Többjátékos munkamenethez csatlakozás vagy létrehozás.

**Kérés:** `{ "level_id": 1 }`

**Logika:**
1. Ha a felhasználó már aktív munkamenetben van erre a szobára → azt adja vissza
2. Ha van `waiting` munkamenet → csatlakozás (2 játékos → `playing` státusz)
3. Ha nincs → új `waiting` munkamenet létrehozása

**Válasz:**
```json
{
  "id": 1,
  "LevelID": 1,
  "Status": "waiting",
  "SolvedQuestions": [],
  "Players": [
    { "UserID": 1, "Username": "jatekos1", "IsReady": false }
  ],
  "MyUserID": 1
}
```

#### GET /api/multiplayer/{sessionId}/state

Munkamenet állapotának lekérdezése (polling).

**Visszatérési formátum:** Ugyanaz, mint a join válasza, frissített adatokkal.

**Speciális viselkedés `abandoned` státusznál:** A maradó játékos szinthaladása törlődik, és lecsatlakoztatásra kerül.

#### POST /api/multiplayer/{sessionId}/solve

Megoldott kérdés jelentése.

**Kérés:** `{ "question_id": 42, "reward_digit": 7 }`

A `SolvedQuestions` JSON tömbhöz hozzáfűzi (duplikátumok kiszűrésével).

#### POST /api/multiplayer/{sessionId}/finish

Munkamenet befejezése. Mindkét játékosnak `UserProgress(Completed=true)` létrejön. A ranglista nem frissül.

#### DELETE /api/multiplayer/{sessionId}/leave

Kilépés a munkamenetből. Minden résztvevő szinthaladása törlődik. Üres munkamenet → törlés, egyébként → `abandoned` státusz.

---

## Admin végpontok

Middleware: `auth:sanctum` + `is_active` + `is_admin`  
Prefix: `/api/admin`

### GET /api/admin/stats

```json
{
  "totalUsers": 50,
  "activeUsers": 45,
  "totalLevels": 15,
  "totalQuestions": 300,
  "totalAnswers": 1200,
  "correctAnswers": 800,
  "completedRooms": 150,
  "newReports": 3
}
```

### GET /api/admin/users?q={keresés}

Felhasználók listázása keresővel (~Username vagy Email).

### PUT /api/admin/users/{id}

Felhasználó módosítása: `Username`, `Email`, `Password`, `IsAdmin`, `IsActive`.

### DELETE /api/admin/users/{id}

Felhasználó törlése (tokenekkel együtt).

### DELETE /api/admin/users/{id}/reset-progress

Bármely felhasználó előrehaladásának visszaállítása.

### GET /api/admin/levels

Összes szoba listázása (admin nézet).

### POST /api/admin/levels

Szoba létrehozása: `Name`, `Description`, `Category`, `OrderNumber`, `IsActive`, `BackgroundUrl`.

### PUT /api/admin/levels/{id}

Szoba módosítása.

### DELETE /api/admin/levels/{id}

Szoba törlése (kaszkádolva a kérdések is törlődnek).

### GET /api/admin/questions?level_id={id}

Kérdések listázása, opcionálisan szobánként szűrve.

### POST /api/admin/questions

Kérdés létrehozása 4 opcióval.

**Kérés:**
```json
{
  "LevelID": 1,
  "QuestionText": "Mi a kérdés?",
  "CorrectAnswer": "Válasz B",
  "RewardDigit": 7,
  "MoneyReward": 30,
  "PositionX": 5,
  "PositionY": 2,
  "Options": [
    { "OptionText": "Válasz A", "IsCorrect": false },
    { "OptionText": "Válasz B", "IsCorrect": true },
    { "OptionText": "Válasz C", "IsCorrect": false },
    { "OptionText": "Válasz D", "IsCorrect": false }
  ]
}
```

### PUT /api/admin/questions/{id}

Kérdés módosítása. A régi opciók törlődnek, újak jönnek létre.

### DELETE /api/admin/questions/{id}

Kérdés törlése (opciók, segítségek, válaszok is törlődnek).

### GET /api/admin/reports?status={new|seen|resolved}

Hibajelentések listázása, opcionálisan státusz szerint szűrve.

### PUT /api/admin/reports/{id}

Hibajelentés státusz frissítése: `{ "Status": "seen" }`

### DELETE /api/admin/reports/{id}

Hibajelentés törlése.

---

## Hibaválaszok

| HTTP kód | Jelentés                              |
| -------- | ------------------------------------- |
| 400      | Hibás kérés / validációs hiba         |
| 401      | Nem hitelesített (hiányzó/érvénytelen token) |
| 403      | Hozzáférés megtagadva (inaktív/nem admin)   |
| 404      | Erőforrás nem található               |
| 422      | Validációs hiba (Laravel formátum)    |
| 500      | Szerver hiba                          |

**Validációs hiba formátum (422):**
```json
{
  "message": "Validation failed",
  "errors": {
    "Email": ["The email field is required."],
    "Password": ["The password must be at least 6 characters."]
  }
}
```
