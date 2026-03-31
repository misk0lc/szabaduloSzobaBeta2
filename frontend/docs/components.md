# Komponensek

## Tartalomjegyzék

- [LoginComponent](#logincomponent)
- [RegisterComponent](#registercomponent)
- [GameComponent](#gamecomponent)
- [RoomComponent](#roomcomponent)
- [LeaderboardComponent](#leaderboardcomponent)
- [AdminComponent](#admincomponent)

---

## LoginComponent

**Mappa:** `src/app/auth/login/`  
**Útvonal:** `/login`  
**Guard:** `guestGuard`

### Cél
Bejelentkezési űrlap és nyilvános hibajelentés (nem bejelentkezett felhasználók számára).

### Főbb tulajdonságok

| Tulajdonság        | Típus         | Leírás                        |
| ------------------ | ------------- | ----------------------------- |
| `form`             | `FormGroup`   | Email + Password              |
| `hiba`             | `string`      | Hibaüzenet                    |
| `toltes`           | `boolean`     | Betöltés állapot              |
| `showReport`       | `boolean`     | Hibajelentés modál láthatóság |
| `reportCategory`   | `string`      | Kiválasztott kategória        |
| `reportSent`       | `boolean`     | Sikeres küldés jelző          |

### Főbb metódusok

| Metódus          | Leírás                                                      |
| ---------------- | ----------------------------------------------------------- |
| `onSubmit()`     | Űrlap validáció, bejelentkezés, átirányítás (admin→/admin, normál→/game) |
| `openReport()`   | Hibajelentés modál megnyitása                               |
| `closeReport()`  | Modál bezárása, állapot resetelése                          |
| `submitReport()` | Nyilvános hibajelentés küldése (opcionális email)           |

### Validáció

- **Email:** kötelező, email formátum
- **Password:** kötelező, min 6 karakter

### Hibajelentés kategóriák

`forgotten-password`, `bug`, `account`, `question`, `other`

### Stílusok

`auth.css` (közös) + `login.component.css` (report gomb)

---

## RegisterComponent

**Mappa:** `src/app/auth/register/`  
**Útvonal:** `/register`  
**Guard:** `guestGuard`

### Cél
Felhasználó regisztrációs űrlap.

### Validáció

| Mező                    | Szabálya                              |
| ----------------------- | ------------------------------------- |
| `Username`              | Kötelező, 3–50 karakter              |
| `Email`                 | Kötelező, email formátum             |
| `Password`              | Kötelező, min 6 karakter             |
| `Password_confirmation` | Kötelező, egyeznie kell a jelszóval  |

### Jelszó-egyezés ellenőrzés

Egyedi cross-field validator (`jelszóEgyezés`): ha a két jelszó nem egyezik, `{ nemEgyezik: true }` hibát ad vissza.

---

## GameComponent

**Mappa:** `src/app/pages/game/`  
**Útvonal:** `/game`  
**Guard:** `authGuard`

### Cél
A fő játékoldal — szobák böngészése és kiválasztása, multiplayer csatlakozás, jelszóváltoztatás, előrehaladás visszaállítás, hibajelentés.

### Főbb tulajdonságok

| Tulajdonság           | Típus            | Leírás                                              |
| --------------------- | ---------------- | --------------------------------------------------- |
| `user`                | `User`           | Bejelentkezett felhasználó                          |
| `levels`              | `Level[]`        | Összes szoba                                        |
| `multiJoining`        | `number \| null` | Melyik szobához csatlakozik multiplayerben          |
| `categoryOrder`       | `string[]`       | `['Könnyed', 'Közepes', 'Nehéz']`                  |
| `showReport`          | `boolean`        | Hibajelentés modál                                  |
| `showPasswordChange`  | `boolean`        | Jelszóváltoztatás modál                             |
| `resetLoading`        | `boolean`        | Reset betöltés állapot                              |

### Főbb metódusok

| Metódus                    | Leírás                                                   |
| -------------------------- | -------------------------------------------------------- |
| `ngOnInit()`               | Szobák betöltése                                         |
| `szobaValaszt(level)`      | Szóló mód: navigáció `/room/{id}`                        |
| `szobaMulti(level, event)` | Multi mód: `join()` → navigáció `/room/{id}/multi/{sid}` |
| `getAllapot(level)`         | Szoba állapot: `'completed'` / `'active'` / `'locked'`  |
| `submitPasswordChange()`   | Jelszóváltoztatás API hívás                              |
| `submitReport()`           | Hibajelentés API hívás                                   |
| `resetProgress()`          | Előrehaladás visszaállítás (megerősítéssel)              |

### Számított tulajdonságok

| Getter               | Leírás                                                      |
| -------------------- | ----------------------------------------------------------- |
| `groupedLevels`      | Szobák kategóriánként csoportosítva                         |
| `allLevelsCompleted` | `true` ha minden szoba teljesítve → reset banner megjelenik |

### Template jellemzők

- Sticky fejléc: logó, felhasználónév, Admin/Ranglista/Jelszó gombok, kijelentkezés
- Szobák nehézségi kategóriánként csoportosítva
- Szoba kártyák háttérképpel és állapot ikonokkal (✅/🚪/🔒)
- Aktív szobák: Egyjátékos + Többjátékos gombok
- Teljesített szobák: zöld keret
- Zárolt szobák: halvány overlay blur-rel
- Reset banner minden szoba teljesítése után
- Lebegő hibajelentés FAB (? gomb)

---

## RoomComponent

**Mappa:** `src/app/pages/room/`  
**Útvonal:** `/room/:id` vagy `/room/:id/multi/:sessionId`  
**Guard:** `authGuard`

### Cél
Az aktív játék komponens — interaktív szoba kérdésekkel, időzítővel, segítségekkel, számjegy-gyűjtéssel, kód beküldéssel és multiplayer támogatással.

### Főbb tulajdonságok

| Tulajdonság          | Típus                   | Leírás                                    |
| -------------------- | ----------------------- | ----------------------------------------- |
| `levelId`            | `number`                | Szoba azonosító                           |
| `level`              | `LevelDetail \| null`   | Szoba adatok                              |
| `questions`          | `QuestionState[]`       | Kérdések állapottal                       |
| `balance`            | `number`                | Játékos egyenlege                         |
| `isMultiplayer`      | `boolean`               | Többjátékos mód jelző                     |
| `multiSessionId`     | `number \| null`        | Munkamenet azonosító                      |
| `multiState`         | `MultiplayerState`      | Munkamenet állapot                        |
| `timeSpent`          | `number`                | Eltelt idő (másodperc)                    |
| `activeQuestion`     | `QuestionState \| null` | Nyitott kérdés                            |
| `selectedOption`     | `string \| null`        | Kiválasztott opció                        |
| `answerResult`       | `CheckAnswerResponse`   | Válasz eredmény                           |
| `codeInput`          | `string`                | Beírt kód                                 |
| `manualDigits`       | `string[]`              | Manuálisan beírt számjegyek               |

### Számított tulajdonságok

| Getter              | Leírás                                                      |
| ------------------- | ----------------------------------------------------------- |
| `timerDisplay`      | `MM:SS` formátum                                            |
| `collectedDigits`   | Összegyűjtött számjegyek (multi sessionből is)              |
| `mergedDigits`      | Megoldott + manuálisan beírt                                |
| `solvedCount`       | Megoldott kérdések száma                                    |
| `progressPercent`   | Előrehaladás százalékban                                    |
| `allSolved`         | Minden kérdés megoldva                                      |
| `canSubmitCode`     | Minden jegy kitöltve                                        |
| `roomTheme`         | `{icon, bg, accent}` a szoba neve alapján                   |

### Főbb metódusok

| Metódus                       | Leírás                                                 |
| ----------------------------- | ------------------------------------------------------ |
| `loadRoom()`                  | `forkJoin`: szoba + kérdések + egyenleg betöltése      |
| `openQuestion(qs)`            | Kérdés modál megnyitása                                |
| `checkAnswer()`               | Válasz ellenőrzés API + animáció                       |
| `use5050()`                   | 50/50 segítség: 2 rossz opció törlése, -25 pénz       |
| `submitCode()`                | Kód beküldés (szóló: API, multi: lokális)              |
| `startMultiplayerPolling()`   | 2 másodperces polling indítása                         |
| `leaveMulti()`                | Multiplayer elhagyás + navigáció                       |
| `copyMultiLink()`             | Megosztó link vágólapra másolása                       |

### Kérdés-válasz folyamat

1. Kérdés csomópontra kattintás → modál megnyitása
2. ABCD opció kiválasztása → `checkAnswer()` API hívás
3. **Helyes:** számjegy megjelenik + pénz animáció + 700ms után modál bezáródik
4. **Helytelen:** pénzbüntetés / időbüntetés animáció + opció visszaállítása

### 50/50 segítség

- 25 pénzbe kerül
- Megtartja a helyes opciót + 1 véletlenszerű helveset
- Eltávolítja a többi rossz opciót

### Multiplayer működés

1. `waiting` → megosztó link másolása
2. 2 játékos csatlakozik → automatikus `playing` státusz
3. Polling 2 másodpercenként szinkronizálja a megoldásokat
4. `abandoned` → automatikus átirányítás
5. `finished` → sikeres üzenet majd átirányítás
6. `ngOnDestroy()` → automatikus kilépés (nincs árva session)

### Szoba témák

A szoba neve alapján CSS egyedi tulajdonságokkal (`--room-accent`, `--room-bg`, `--room-glow`):

| Téma       | Kulcsszó  | Akcentus  | Háttér    |
| ---------- | --------- | --------- | --------- |
| Könyvtár   | könyvtár  | `#c19a6b` | `#1a1410` |
| Labor      | labor     | `#34d399` | `#0a1a14` |
| Pince      | pince     | `#fb923c` | `#1a0e09` |
| Hajó       | kapitány  | `#38bdf8` | `#0a1220` |
| Űr         | űr        | `#c19a6b` | `#1a1410` |
| Alapértelmezett | —    | `#c19a6b` | `#1a1410` |

---

## LeaderboardComponent

**Mappa:** `src/app/pages/leaderboard/`  
**Útvonal:** `/leaderboard`  
**Guard:** `authGuard`

### Cél
Globális játékos ranglista rendezési lehetőségekkel, statisztikákkal és dobogóval.

### Főbb tulajdonságok

| Tulajdonság  | Típus              | Leírás                                    |
| ------------ | ------------------ | ----------------------------------------- |
| `entries`    | `LeaderboardEntry[]`| Ranglista bejegyzések                    |
| `sortBy`     | `string`           | Rendezési szempont                        |
| `animatedIn` | `boolean`          | Belépő animáció jelző                     |

### Rendezési szempontok

| Érték    | Rendezés                  | Sorrend   |
| -------- | ------------------------- | --------- |
| `score`  | Pontszám                  | Csökkenő  |
| `levels` | Teljesített szobák        | Csökkenő  |
| `time`   | Összidő                   | Növekvő   |
| `hints`  | Használt segítségek       | Növekvő   |

### Template jellemzők

- Fejléc: navigáció Játékba és Admin-ba
- Statisztika sor: összjátékosok, átlag pontszám, átlag idő, legtöbb szoba
- Rendezés gombok (4 szempont)
- Top 3 dobogó (arany/ezüst/bronz kártyák pontszám barral)
- Teljes rangsor táblázat érem ikonokkal, „Te" jelvénnyel
- Üres/betöltés/hiba állapotok

---

## AdminComponent

**Mappa:** `src/app/pages/admin/`  
**Útvonal:** `/admin`  
**Guard:** `adminGuard`

### Cél
Teljes admin felület 5 fülsorral: Irányítópult, Felhasználók, Szobák, Kérdések, Hibajelentések.

### Elrendezés

Bal oldali navigáció + fő tartalom terület sticky fejléccel.

### 1. Irányítópult (Dashboard)

Statisztika rácsozat:
- Összes felhasználó (aktív szám)
- Szobák, kérdések száma
- Teljesített szobák
- Új hibajelentések (kattintható → Hibajelentések fülre navigál)
- Válasz helyességi arány sáv

### 2. Felhasználók

- Keresés név/email alapján
- Adattábla: ID, Név, Email, Admin jelvény, Aktív jelvény, Egyenleg, Pontszám
- Műveletek: Szerkesztés (modál), Aktiválás/Inaktiválás, Progress visszaállítás (megerősítéssel), Törlés (megerősítéssel)

### 3. Szobák

- „Új szoba" gomb
- Adattábla: ID, Sorrend, Név, Kategória jelvény, Leírás, Aktív
- Szerkesztő modál: Név, Leírás, Kategória legördülő (Könnyed/Közepes/Nehéz), Sorrend, Aktív jelölő, Háttérkép URL élő előnézettel

### 4. Kérdések

- Szoba szűrő legördülő + „Új kérdés" gomb
- Adattábla: ID, Szoba, Kérdés szöveg, Helyes válasz, Számjegy, Jutalom, Pozíció (X,Y)
- Szerkesztő modál: Szoba választó, Kérdés szöveg, RewardDigit (0–9), MoneyReward, PositionX (1–20), PositionY (1–4), ABCD opciók rádiógombbal a helyes válaszhoz

### 5. Hibajelentések

- Státusz szűrő: Összes / Új / Megtekintett / Megoldott
- Adattábla: ID, Kategória, Felhasználó/Email, Cím és Leírás, Oldal, Státusz jelvény, Dátum
- Műveletek: Megtekintettnek jelölés, Megoldottnak jelölés, Visszaállítás újra, Törlés

### Toast értesítések

Sikeres (zöld) és hiba (piros) állapothoz 3 másodperces auto-dismiss toast.
