# Játéklogika

## Tartalomjegyzék

- [Szoba felépítés](#szoba-felépítés)
- [Kérdés-válasz rendszer](#kérdés-válasz-rendszer)
- [Büntetési rendszer](#büntetési-rendszer)
- [Szobakód mechanika](#szobakód-mechanika)
- [Pontszámítás](#pontszámítás)
- [Szint feloldási logika](#szint-feloldási-logika)
- [Multiplayer rendszer](#multiplayer-rendszer)
- [Pénzrendszer](#pénzrendszer)
- [Előrehaladás visszaállítása](#előrehaladás-visszaállítása)

---

## Szoba felépítés

Minden szoba (level) a következőkből áll:
- **20 kérdés** egy 20×4-es rácson pozicionálva (`PositionX`: 1–20, `PositionY`: 1–4)
- Minden kérdéshez **4 opció** (ABCD) tartozik, amelyből 1 helyes
- Minden kérdéshez tartozik egy **RewardDigit** (0–9), amely a szoba kódjának egy számjegye
- Opcionálisan **segítségek** (hints) vásárolhatók

## Kérdés-válasz rendszer

1. A játékos kiválasztja az egyik opciót (A/B/C/D)
2. A rendszer case-insensitive összehasonlítást végez a `CorrectAnswer` mezővel
3. Minden válasz naplózásra kerül a `user_answers` táblába
4. Egy már helyesen megválaszolt kérdésre nem lehet újra válaszolni

### Helyes válasz
- A `MoneyReward` összeg hozzáadódik az egyenleghez
- A `RewardDigit` felfedésre kerül
- A kérdés „megoldott" státuszba kerül

### Helytelen válasz
- A büntetési rendszer lép életbe (lásd alább)

---

## Büntetési rendszer

A büntetés a kérdésenkénti hibás válaszok számától függ:

| Hibás válasz # | Büntetés típusa       | Értéke              |
| -------------- | --------------------- | ------------------- |
| 1.             | Pénzbüntetés          | **-50** játékpénz   |
| 2.             | Időbüntetés           | **+30** másodperc   |
| 3.             | Időbüntetés           | **+120** másodperc  |
| 4+             | Időbüntetés           | **+120** másodperc  |

Megjegyzés:
- Az 1. hibás válasznál a pénzegyenleg csökken (de nem mehet 0 alá)
- A 2. hibás válaztól kezdve időbüntetés adódik
- A hibás válaszok száma a `user_answers` táblából számolódik adott felhasználó + kérdés kombináció alapján

---

## Szobakód mechanika

Minden szobának van egy **kódja**, amely a szoba összes kérdésének `RewardDigit` értékéből áll, `PositionX` szerint növekvő sorrendben.

**Példa:**
Ha egy szobában 20 kérdés van, és a RewardDigitek a PositionX sorrendjében: `3, 1, 4, 7, 2, 8, 5, 9, 6, 0, ...`, akkor a szobakód: `3147285960...`

A kód beküldésekor:
1. A szerver felépíti a helyes kódot a kérdésekből
2. Összehasonlítja a beküldött kóddal
3. Helyes egyezésnél a szoba teljesítettnek számít

---

## Pontszámítás

Szobánkénti pontszám:

```
Pontszám = max(100, 1000 - eltelt_idő_másodpercben)
```

| Eltelt idő        | Pontszám |
| ------------------ | -------- |
| < 1 perc (60s)    | 940      |
| 5 perc (300s)     | 700      |
| 10 perc (600s)    | 400      |
| 15 perc (900s)    | 100      |
| 15+ perc (900s+)  | 100      |

- A minimum pontszám mindig **100** pont
- Az eltelt idő tartalmazza az időbüntetéseket is
- A pontszám a `leaderboard` tábla `Score` mezőjéhez adódik (kumulatív)
- **Multiplayer szobák NEM adnak ranglistapontot**

---

## Szint feloldási logika

A szobák 3 független nehézségi kategóriába vannak sorolva:

| Kategória | Szobák             |
| --------- | ------------------ |
| Könnyed   | Room 6–10          |
| Közepes   | Room 11–15         |
| Nehéz     | Room 1–5           |

**Feloldási szabályok:**
1. Minden kategória **első szobája mindig elérhető** → a játékos szabadon választhat nehézséget
2. A kategórián belüli következő szoba csak az **előző teljesítése után** válik elérhetővé
3. A 3 kategória teljesen **független** egymástól
4. Inaktív (`IsActive=false`) szobák nem jelennek meg

---

## Multiplayer rendszer

### Matchmaking

1. `POST /api/multiplayer/join` kéréssel a játékos csatlakozni próbál
2. Ha már van aktív (waiting/playing) munkamenete erre a szobára → azt adja vissza
3. Ha van `waiting` státuszú munkamenet → csatlakozik (max. 2 játékos → `playing`)
4. Ha nincs → új `waiting` munkamenet jön létre

A matchmaking **DB tranzakciót és `lockForUpdate` zárolást** használ a versenyhelyzetek elkerülésére.

### Állapotszinkronizáció

- **Polling alapú** — a kliens 2 másodpercenként lekérdezi a munkamenet állapotát
- Nincs WebSocket vagy real-time kommunikáció
- A megoldott kérdések a `SolvedQuestions` JSON mezőben szinkronizálódnak

### Megoldott kérdés formátum

```json
{
  "SolvedQuestions": [
    { "id": 42, "digit": 7 },
    { "id": 53, "digit": 3 }
  ]
}
```

### Munkamenet életciklus

```
waiting → playing → finished
                  ↘ abandoned (ha valaki kilép)
```

| Állapot    | Leírás                                            |
| ---------- | ------------------------------------------------- |
| `waiting`  | 1 játékos várakozik társra                        |
| `playing`  | 2 játékos aktívan játszik                         |
| `finished` | Mindkét játékos teljesítette a szobát             |
| `abandoned`| Egy játékos kilépett, a másik automatikusan visszairányításra kerül |

### Multiplayer vs. Szólóhoz képest

| Tulajdonság        | Szóló | Multiplayer |
| ------------------ | ----- | ----------- |
| Ranglista pontok   | Igen  | **Nem**     |
| Progress mentés    | Igen  | Igen        |
| Max. játékosszám   | 1     | 2           |
| Kód beküldés       | API   | Lokális     |

---

## Pénzrendszer

- Regisztrációkor a játékos **0 pénzzel** indul (a seederek 500-at adnak teszteléshez)
- Helyes válaszért **MoneyReward** (20–70) pénz járba
- Hibás válaszért (1. próbálkozás) **-50** pénz büntetés
- 50/50 segítség (kliens oldalon) **-25** pénzbe kerül
- A pénzegyenleg nem mehet 0 alá

---

## Előrehaladás visszaállítása

### Önkiszolgáló reset

- Csak akkor elérhető, ha **minden aktív szoba teljesítve** van
- Endpoint: `DELETE /api/me/reset-progress`

### Admin reset

- Bármely felhasználó előrehaladása bármikor visszaállítható
- Endpoint: `DELETE /api/admin/users/{id}/reset-progress`

### Mit töröl a reset?

| Adat                     | Törlődik?           |
| ------------------------ | ------------------- |
| `user_progress`          | Igen (minden rekord)|
| `user_answers`           | Igen (minden rekord)|
| `leaderboard`            | Igen (törlés)       |
| `user_money`             | Nullázás (Amount=0) |
| Multiplayer munkamenetek | Eltávolítás + üres sessions törlése |
| Felhasználói fiók        | **Nem** (megmarad)  |
