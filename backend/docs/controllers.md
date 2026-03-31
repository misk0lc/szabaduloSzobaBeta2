# Kontrollerek

## Tartalomjegyzék

- [AuthController](#authcontroller)
- [LevelController](#levelcontroller)
- [QuestionController](#questioncontroller)
- [ProgressController](#progresscontroller)
- [LeaderboardController](#leaderboardcontroller)
- [MultiplayerController](#multiplayercontroller)
- [ReportController](#reportcontroller)
- [AdminController](#admincontroller)
- [HintController](#hintcontroller)

---

## AuthController

**Fájl:** `app/Http/Controllers/AuthController.php`

### register(Request)

Új felhasználó regisztrálása.

- Validálja: `Username` (egyedi, max 50), `Email` (egyedi, max 100), `Password` (min 6, betű+szám)
- Létrehozza a felhasználót, a `user_money` (Amount=0) és `leaderboard` rekordot
- Bearer tokent generál Sanctum-mal
- HTTP 201

### login(Request)

Bejelentkezés.

- Validálja az email/jelszó párost
- Ellenőrzi az `IsActive` státuszt
- Törli az összes korábbi tokent (egyetlen aktív token policy)
- Visszaadja a Bearer tokent

### logout(Request)

Kijelentkezés — törli az aktuális access tokent.

### me(Request)

Visszaadja az aktuális felhasználó adatait: `UserID`, `Username`, `Email`, `IsAdmin`, `IsActive`, `CreatedAt`, `Balance`.

### changePassword(Request)

Jelszóváltoztatás. Ellenőrzi a régi jelszót, beállítja az újat.

---

## LevelController

**Fájl:** `app/Http/Controllers/LevelController.php`  
**Trait:** `ChecksLevelUnlock`

### index(Request)

Összes aktív szoba listázása, a következő kiszámított mezőkkel:
- `IsUnlocked` — kategórián belüli feloldási logika alapján
- `IsCompleted` — `user_progress` tábla alapján
- `IsActive` — mindig `true` (csak aktívak)

### show(Request, int $id)

Egyetlen szoba részletei. Ellenőrzi a feloldási jogosultságot. Visszaadja az előrehaladási adatokat.

---

## QuestionController

**Fájl:** `app/Http/Controllers/QuestionController.php`  
**Trait:** `ChecksLevelUnlock`

### index(Request, int $levelId)

Szoba kérdéseinek listázása:
- Opciókat véletlenszerű sorrendben adja vissza
- `CorrectAnswer` sosincs kitéve
- `RewardDigit` csak megoldott kérdéseknél jelenik meg
- Az `Options` tartalmazza az `IsCorrect` jelzőt

### checkAnswer(Request, int $id)

Válasz ellenőrzése — **a játék fő logikai végpontja**.

**Folyamat:**
1. Nem engedi az újbóli válaszadást (ha már helyes válasz volt)
2. Case-insensitive string összehasonlítás a `CorrectAnswer` mezővel
3. Naplózza a választ a `user_answers` táblába

**Helyes válasz esetén:**
- Hozzáadja a `MoneyReward`-ot az egyenleghez
- Visszaadja a `RewardDigit`-et

**Helytelen válasz esetén — büntetési rendszer:**

| Hibás válasz sorszáma | Büntetés                    |
| --------------------- | --------------------------- |
| 1.                    | -50 pénz                    |
| 2.                    | +30 másodperc időbüntetés   |
| 3+                    | +120 másodperc időbüntetés  |

A hibás válaszok száma a `user_answers` táblából számolódik (adott felhasználó + kérdés).

---

## ProgressController

**Fájl:** `app/Http/Controllers/ProgressController.php`  
**Trait:** `ChecksLevelUnlock`

### submitCode(Request, int $levelId)

Szobakód beküldése.

**Helyes kód meghatározása:**
- Az összes kérdés `RewardDigit` értékét `PositionX` szerint rendezi
- Összefűzi egy stringgé

**Sikeres teljesítés esetén:**
- Létrehozza/frissíti a `user_progress` rekordot (`Completed=true`)
- Pontszámítás: `max(100, 1000 - timeSpent)`
- Frissíti a ranglista `Score`, `LevelsCompleted`, `TimeTotal` értékeket
- Visszaadja a következő szoba adatait

### resetProgress(Request)

Saját előrehaladás visszaállítása. **Csak akkor elérhető, ha az összes aktív szoba teljesítve van.**

### doResetProgress(int $userId) — statikus

Teljes reset:
- Törlés: `UserProgress`, `UserAnswer`, `LeaderboardEntry`
- Nullázás: `UserMoney`
- Multiplayer munkamenetekből eltávolítás, üres munkamenetek törlése

### doResetLevelProgress(int $userId, int $levelId) — statikus

Egyetlen szoba előrehaladásának törlése. A multiplayer `leave` használja.

---

## LeaderboardController

**Fájl:** `app/Http/Controllers/LeaderboardController.php`

### index()

Top 10 ranglista-bejegyzés, `Score` szerint csökkenő sorrendben.

Visszaadott mezők: `UserID`, `Username`, `Score`, `LevelsCompleted`, `TimeTotal`, `HintsUsed`.

---

## MultiplayerController

**Fájl:** `app/Http/Controllers/MultiplayerController.php`

### join(Request)

Matchmaking logika:
1. Ha a felhasználó már aktív munkamenetben van → azt adja vissza
2. `waiting` munkamenet keresése az adott szobához → csatlakozás (ha 2 játékos → `playing`)
3. Ha nincs ilyen → új `waiting` munkamenet létrehozása

DB tranzakciót és `lockForUpdate` zárolást használ a versenyhelyzetek elkerülésére.

### state(Request, int $sessionId)

Polling végpont — visszaadja a munkamenet aktuális állapotát.

**Speciális viselkedés `abandoned` státusznál:**
- A maradó játékos szinthaladása törlődik
- Lecsatlakoztatásra kerül
- Üres munkamenet törlődik

### solve(Request, int $sessionId)

Megoldott kérdés jelentése — `{question_id, reward_digit}` a `SolvedQuestions` JSON tömbhöz.
Duplikátumok kiszűrésre kerülnek.

### finish(Request, int $sessionId)

Munkamenet befejezése — `Completed=true` minden résztvevőnél. **Ranglista nem frissül.**

### leave(Request, int $sessionId)

Kilépés:
- Minden résztvevő szinthaladása törlődik
- Ha nincs maradó játékos → munkamenet törlése
- Ha van → státusz `abandoned`

---

## ReportController

**Fájl:** `app/Http/Controllers/ReportController.php`

### store(Request)

Hitelesített hibajelentés. A `UserID` automatikusan hozzárendelődik.

### storePublic(Request)

Nyilvános hibajelentés (nem szükséges bejelentkezés). Opcionális `ContactEmail`. `UserID` = null.

---

## AdminController

**Fájl:** `app/Http/Controllers/AdminController.php`

### Statisztika

**stats()** — Visszaadja: `totalUsers`, `activeUsers`, `totalLevels`, `totalQuestions`, `totalAnswers`, `correctAnswers`, `completedRooms`, `newReports`.

### Felhasználó-kezelés

| Metódus                        | Leírás                                             |
| ------------------------------ | -------------------------------------------------- |
| `users(Request)`               | Felhasználók listázása keresővel (`?q=` paraméter) |
| `updateUser(Request, int $id)` | Felhasználó módosítása (jelszó is újrahashelődik)  |
| `deleteUser(int $id)`          | Felhasználó és tokenek törlése                     |
| `resetUserProgress(int $id)`   | Bármely felhasználó előrehaladásának visszaállítása |

### Szobakezelés

| Metódus                         | Leírás           |
| ------------------------------- | ---------------- |
| `levels()`                      | Összes szoba     |
| `createLevel(Request)`          | Szoba létrehozás |
| `updateLevel(Request, int $id)` | Szoba módosítás  |
| `deleteLevel(int $id)`          | Szoba törlés     |

### Kérdéskezelés

| Metódus                            | Leírás                                                   |
| ---------------------------------- | -------------------------------------------------------- |
| `questions(Request)`               | Kérdések listázása (`?level_id=` szűrő)                 |
| `createQuestion(Request)`          | Kérdés létrehozás 4 opcióval                             |
| `updateQuestion(Request, int $id)` | Kérdés módosítás (régi opciók törlődnek, újak jönnek)    |
| `deleteQuestion(int $id)`          | Kérdés törlés (kaszkád: opciók, segítségek, válaszok)    |

### Hibajelentés-kezelés

| Metódus                           | Leírás                                             |
| --------------------------------- | -------------------------------------------------- |
| `reports(Request)`                | Listázás (`?status=` szűrő)                       |
| `updateReport(Request, int $id)`  | Státusz módosítás: `new` / `seen` / `resolved`    |
| `deleteReport(int $id)`           | Hibajelentés törlése                               |

---

## HintController

**Fájl:** `app/Http/Controllers/HintController.php`

> **Megjegyzés:** Ez a kontroller **nincs regisztrálva** a `routes/api.php`-ban. A metódusok léteznéek, de az API-n keresztül nem érhetők el.

### index(int $questionId)

Kérdéshez tartozó segítségek listázása (a `HintText` nélkül).

### buy(Request, int $id)

Segítség megvásárlása:
- Levonja a `Cost` értéket az egyenlegből
- Növeli a ranglista `HintsUsed` számlálóját
- Visszaadja a `HintText`-et
