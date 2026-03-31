# Szabadulószoba – Frontend Dokumentáció

## Tartalomjegyzék

1. [Áttekintés](#áttekintés)
2. [Technológiai stack](#technológiai-stack)
3. [Telepítés és beállítás](#telepítés-és-beállítás)
4. [Projektstruktúra](project-structure.md)
5. [Routing és navigáció](routing.md)
6. [Komponensek](components.md)
7. [Szolgáltatások (Services)](services.md)
8. [Modellek és interfészek](models.md)
9. [Guardok és interceptor](guards-interceptor.md)
10. [Stílusrendszer](styles.md)

---

## Áttekintés

A Szabadulószoba frontend egy **Angular 19** alapú single-page alkalmazás, amely egy interaktív szabadulószoba játékot valósít meg. A felhasználók szobákat választanak, kérdésekre válaszolnak, számjegyeket gyűjtenek, és kódokat adnak be a szobák teljesítéséhez. A rendszer támogatja a szóló és többjátékos módot, ranglistát, admin panelt és hibajelentő rendszert.

## Technológiai stack

| Komponens        | Technológia                     |
| ---------------- | ------------------------------- |
| Framework        | Angular 19.2.0                  |
| Nyelv            | TypeScript 5.7.2                |
| Build rendszer   | @angular-devkit/build-angular   |
| Csomagkezelő     | npm                             |
| HTTP kliens      | Angular HttpClient + interceptor|
| Routing          | Angular Router (lazy loading)   |
| Reaktív formok   | Angular FormsModule             |
| Felület nyelve   | Magyar                          |

### Függőségek

**Futtatási:**
- `@angular/common`, `@angular/core`, `@angular/forms`, `@angular/router` — ^19.2.0
- `rxjs ~7.8.0`
- `zone.js ~0.15.0`

**Fejlesztői:**
- `@angular-devkit/build-angular ^19.2.19`
- `@angular/cli ^19.2.19`
- `typescript ~5.7.2`
- Karma + Jasmine tesztelés

---

## Telepítés és beállítás

### Előfeltételek

- Node.js (LTS ajánlott)
- npm

### Lépések

```bash
# 1. Navigálás a frontend mappába
cd frontend

# 2. Függőségek telepítése
npm install

# 3. Fejlesztői szerver indítása
npm start
# vagy: npx ng serve

# 4. Produkciós build
npm run build
# vagy: npx ng build
```

### Elérhető scriptek

| Script  | Parancs                                      |
| ------- | -------------------------------------------- |
| `start` | `ng serve` (fejlesztői szerver)              |
| `build` | `ng build` (produkciós build)                |
| `watch` | `ng build --watch --configuration development` |
| `test`  | `ng test` (unit tesztek)                     |

### Build konfiguráció

- **Output mappa:** `dist/frontend`
- **Globális stílusok:** `src/styles.css`
- **Statikus eszközök:** `public/` mappa (szoba háttérképek)
- **Produkciós limitek:**
  - Kezdeti csomag: figyelmeztetés 500 kB-nál, hiba 1 MB-nál
  - Komponens stílusok: figyelmeztetés 20 kB-nál, hiba 40 kB-nál

### API kapcsolat

Az alkalmazás dinamikusan építi fel az API URL-t:

```typescript
http://${window.location.hostname}:8001/api
```

Ez lehetővé teszi, hogy bármilyen hostról működjön — nem szükséges environment beállítás.

---

## Gyors navigáció

| Téma                   | Fájl                                              |
| ---------------------- | ------------------------------------------------- |
| Projektstruktúra       | [project-structure.md](project-structure.md)      |
| Útvonalak és guardok   | [routing.md](routing.md)                         |
| Összes komponens       | [components.md](components.md)                    |
| API szolgáltatások     | [services.md](services.md)                        |
| Adatmodellek           | [models.md](models.md)                            |
| Autentikáció           | [guards-interceptor.md](guards-interceptor.md)    |
| CSS architektúra       | [styles.md](styles.md)                            |
