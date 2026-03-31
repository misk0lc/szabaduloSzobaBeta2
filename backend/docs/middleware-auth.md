# Middleware és autentikáció

## Autentikációs folyamat

### 1. Regisztráció

```
POST /api/register
  → User létrehozás
  → UserMoney létrehozás (Amount=0)
  → LeaderboardEntry létrehozás
  → Sanctum Bearer token generálás
  ← Token visszaadása
```

### 2. Bejelentkezés

```
POST /api/login
  → Email/jelszó ellenőrzés
  → IsActive ellenőrzés
  → Összes korábbi token törlése (egyetlen aktív token policy)
  → Új Sanctum Bearer token generálás
  ← Token visszaadása
```

### 3. Hitelesített kérések

Minden hitelesített API kérésnek tartalmaznia kell:

```
Authorization: Bearer <token>
```

A token a `personal_access_tokens` táblában tárolódik. Nincs automatikus lejárat.

### 4. Kijelentkezés

```
POST /api/logout
  → Aktuális token törlése
```

---

## Middleware-ek

A middleware-ek a `bootstrap/app.php`-ban vannak regisztrálva:

```php
$middleware->alias([
    'is_admin'  => \App\Http\Middleware\IsAdmin::class,
    'is_active' => \App\Http\Middleware\IsActive::class,
]);
```

### IsActive

**Fájl:** `app/Http/Middleware/IsActive.php`  
**Alias:** `is_active`

Ellenőrzi, hogy a bejelentkezett felhasználó aktív-e (`IsActive === true`).

**Hibás esetben:** 403 — `"A fiók inaktív."`

**Használat:** Minden hitelesített végpontnál alkalmazva (`auth:sanctum` után).

### IsAdmin

**Fájl:** `app/Http/Middleware/IsAdmin.php`  
**Alias:** `is_admin`

Ellenőrzi, hogy a felhasználó admin jogosultsággal rendelkezik-e (`IsAdmin === true`).

**Hibás esetben:** 403 — `"Hozzáférés megtagadva. Admin jogosultság szükséges."`

**Használat:** A `/api/admin/*` végpontoknál.

---

## Middleware csoportok az útvonalakon

| Útvonal csoport     | Middleware lánc                           |
| -------------------- | ---------------------------------------- |
| Publikus (register, login, reports/public) | Nincs middleware            |
| Hitelesített         | `auth:sanctum` → `is_active`             |
| Admin                | `auth:sanctum` → `is_active` → `is_admin`|

---

## ChecksLevelUnlock Trait

**Fájl:** `app/Http/Traits/ChecksLevelUnlock.php`

A szintek feloldásának logikáját tartalmazza. Három kontroller használja: `LevelController`, `QuestionController`, `ProgressController`.

### isLevelUnlocked(int $userId, int $levelId): bool

**Logika:**
1. Megkeresi a szintet (aktívnak kell lennie)
2. Lekéri az összes aktív szintet ugyanabban a `Category`-ban, `OrderNumber` szerint rendezve
3. Ha ez az **első** szint a kategóriájában → mindig feloldott
4. Egyébként ellenőrzi, hogy az **előző szint** ugyanabban a kategóriában `Completed=true` a `user_progress`-ben

**Kategóriák független progressziója:**
- `Könnyed` (Easy): Room 6 → 7 → 8 → 9 → 10
- `Közepes` (Medium): Room 11 → 12 → 13 → 14 → 15
- `Nehéz` (Hard): Room 1 → 2 → 3 → 4 → 5

Minden kategória first szobája mindig elérhető — a játékos szabadon választhat nehézségi szintet.

---

## Token kezelés

### Sanctum konfiguráció

| Beállítás         | Érték                                                        |
| ----------------- | ------------------------------------------------------------ |
| Stateful domének  | `localhost`, `localhost:3000`, `127.0.0.1`, `127.0.0.1:8000` |
| Guard             | `web`                                                        |
| Token lejárat     | `null` (nincs automatikus lejárat)                           |

### Egyetlen aktív token policy

Minden bejelentkezésnél a rendszer **törli az összes korábbi tokent** a felhasználónak:

```php
$user->tokens()->delete();
$token = $user->createToken('auth-token')->plainTextToken;
```

Ez biztosítja, hogy egyszerre csak egy aktív session legyen felhasználónként.
