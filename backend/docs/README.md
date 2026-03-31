# Szabadulószoba – Backend Dokumentáció

## Tartalomjegyzék

1. [Áttekintés](#áttekintés)
2. [Technológiai stack](#technológiai-stack)
3. [Telepítés és beállítás](#telepítés-és-beállítás)
4. [Adatbázis séma](database-schema.md)
5. [API végpontok](api-endpoints.md)
6. [Modellek és kapcsolatok](models.md)
7. [Kontrollerek](controllers.md)
8. [Middleware és autentikáció](middleware-auth.md)
9. [Játéklogika](game-logic.md)
10. [Seederek](seeders.md)

---

## Áttekintés

A Szabadulószoba backend egy **Laravel 12.x** alapú RESTful JSON API, amely a játék összes szerver-oldali logikáját kezeli: felhasználó-kezelés, szoba/szint menedzsment, kérdés-válasz rendszer, pontszámítás, multiplayer munkamenetek és hibajelentések.

Az API a **Laravel Sanctum** tokenalapú autentikációt használja Bearer tokenekkel.

## Technológiai stack

| Komponens      | Technológia                          |
| -------------- | ------------------------------------ |
| Framework      | Laravel 12.x (PHP ^8.2)             |
| Autentikáció   | Laravel Sanctum 4.3 (Bearer token)  |
| Adatbázis      | MySQL (alapértelmezett), SQLite is   |
| API stílus     | RESTful JSON                         |
| Multiplayer    | Polling-alapú (nincs WebSocket)      |

### Függőségek (composer.json)

**Produkciós:**
- `laravel/framework ^12.0`
- `laravel/sanctum ^4.3`
- `laravel/tinker ^2.10.1`

**Fejlesztői:**
- `fakerphp/faker`, `laravel/pail`, `laravel/pint`, `laravel/sail`
- `mockery/mockery`, `nunomaduro/collision`, `phpunit/phpunit ^11.5.3`

---

## Telepítés és beállítás

### Előfeltételek

- PHP >= 8.2
- Composer
- MySQL szerver (vagy SQLite)
- Node.js (opcionális, ha Vite-ot használunk)

### Lépések

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

### Környezeti változók (.env)

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

### CORS konfiguráció (config/cors.php)

- Engedélyezett útvonal: `api/*`, `sanctum/csrf-cookie`
- Minden origin, metódus és header engedélyezett (`*`)
- Credentials: `false`

### Sanctum konfiguráció (config/sanctum.php)

- Stateful domének: `localhost`, `localhost:3000`, `127.0.0.1`, `127.0.0.1:8000`, `::1`
- Token lejárat: nincs (null)
