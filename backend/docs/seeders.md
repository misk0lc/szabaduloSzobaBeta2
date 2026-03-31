# Seederek

## Futtatás

```bash
php artisan db:seed
```

A `DatabaseSeeder` a következő sorrendben hívja meg a seedereket:

1. `UserSeeder`
2. `LevelSeeder`
3. `QuestionSeeder`
4. `ReportSeeder`

---

## UserSeeder

**Fájl:** `database/seeders/UserSeeder.php`

3 teszt felhasználót hoz létre, mindegyikhez 500 induló pénzzel és üres ranglista bejegyzéssel:

| Felhasználónév | Email                   | Jelszó        | Admin |
| -------------- | ----------------------- | ------------- | ----- |
| `admin`        | `admin@szabadulo.hu`    | `Admin1234`   | Igen  |
| `jatekos1`     | `jatekos1@szabadulo.hu` | `Jatekos1234` | Nem   |
| `jatekos2`     | `jatekos2@szabadulo.hu` | `Jatekos1234` | Nem   |

---

## LevelSeeder

**Fájl:** `database/seeders/LevelSeeder.php`

15 szobát hoz létre 3 nehézségi kategóriában:

### Nehéz kategória

| Sorrend | Név                  |
| ------- | -------------------- |
| 1       | A Könyvtárszoba      |
| 2       | A Laboratorium       |
| 3       | A Kastély Pincéje    |
| 4       | A Kapitány Kabinja   |
| 5       | Az Űrállomás         |

### Könnyed kategória

| Sorrend | Név             |
| ------- | --------------- |
| 6       | A Játékszoba    |
| 7       | A Kávézó        |
| 8       | Az Osztályterem |
| 9       | A Kert          |
| 10      | A Cukrászda     |

### Közepes kategória

| Sorrend | Név                    |
| ------- | ---------------------- |
| 11      | A Detektív Irodája     |
| 12      | A Múzeum               |
| 13      | A Téli Kunyhó          |
| 14      | A Hajógyár             |
| 15      | A Varázslatos Könyvtár |

Minden szobához tartozik egy `BackgroundUrl` (`rooms/roomN/background.png`).

---

## QuestionSeeder

**Fájl:** `database/seeders/QuestionSeeder.php`

**Szobánként 20 kérdést** hoz létre. Minden kérdéshez:

- Kérdés szöveg
- Helyes válasz
- `RewardDigit` (0–9) — a szobakód egy számjegye
- `MoneyReward` (20–70) — helyes válaszért járó pénz
- Pozíció a rácson (`PositionX`: 1–20, `PositionY`: 1–4)
- **1 segítség** (hint) költséggel (10–45 pénz)
- **4 opció** (1 helyes + 3 helytelen)

A szobakód minden szoba kérdéseinek `RewardDigit` értékeiből áll össze, a `PositionX` alapú sorrendben.

---

## ReportSeeder

**Fájl:** `database/seeders/ReportSeeder.php`

7 minta hibajelentést hoz létre:

| Cím                  | Kategória            | Státusz    | UserID |
| -------------------- | -------------------- | ---------- | ------ |
| Bejelentkezési hiba  | bug                  | new        | 2      |
| Elfelejtett jelszó   | forgotten-password   | new        | null   |
| Kérdés hibás         | question             | seen       | 2      |
| Oldal nem tölt be    | bug                  | new        | 3      |
| Fiók törlése         | account              | resolved   | 3      |
| Szoba nem nyílik     | bug                  | seen       | null   |
| Általános visszajelzés | other              | new        | 2      |

A `UserID: null` rekordok nyilvános (nem hitelesített) bejelentéseket szimulálnak.
