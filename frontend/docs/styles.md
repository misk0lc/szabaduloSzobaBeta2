# Stílusrendszer

## Tartalomjegyzék

- [Szín paletta](#szín-paletta)
- [Globális stílusok](#globális-stílusok)
- [Közös auth stílusok](#közös-auth-stílusok)
- [Komponens stílusok](#komponens-stílusok)
- [Szoba témák](#szoba-témák)
- [Megosztott report rendszer](#megosztott-report-rendszer)
- [Reszponzív design](#reszponzív-design)

---

## Szín paletta

| Szín                | Kód       | Használat                              |
| ------------------- | --------- | -------------------------------------- |
| Elsődleges háttér   | `#1a1410` | Body, kártyák, fejléc                  |
| Elsődleges szöveg   | `#e8ddd0` | Body szöveg, címek                     |
| Arany akcentus      | `#c19a6b` | Gombok, keretek, kiemelések            |
| Halvány szöveg      | `#8a7a6a` | Másodlagos szöveg, címkék              |
| Kártya háttér       | `#2a1f15` | Szoba kártyák, modálok                 |
| Hiba                | `#fc8181` | Validációs hibák, piros állapotok      |
| Siker               | `#34d399` | Teljesített állapotok, zöld jelvények  |
| Multiplayer kék     | `#7dd3fc` | Multi gombok, jelvények                |
| Admin narancs       | `#fb923c` | Admin jelvény                          |

---

## Globális stílusok

**Fájl:** `src/styles.css`

### CSS Reset

```css
* { margin: 0; padding: 0; box-sizing: border-box; }
```

### Body

```css
body {
  font-family: 'Segoe UI', sans-serif;
  background: #1a1410;
  color: #e8ddd0;
}
```

### Megosztott report rendszer

A `styles.css` tartalmazza az összes report-hoz kapcsolódó stílust, amelyeket a Game, Room és Login komponensek közösen használnak:

- `.report-fab` — Lebegő akció gomb (52px kör, jobb alsó sarok)
- `.report-overlay` — Modál háttér overlay
- `.report-modal` — Modál konténer
- `.report-modal-header` — Fejléc bezárás gombbal
- `.report-modal-body` — Tartalom terület
- `.report-category-grid` — Kategória gombok rácsozata
- `.report-cat-btn` — Kategória gombok
- `.report-submit` — Beküldés gomb
- `.report-success` — Sikeres küldés állapot

Ez a centralizált megközelítés megelőzi a stíluskód duplikálást a három komponens között.

---

## Közös auth stílusok

**Fájl:** `src/app/auth/auth.css`

A Login és Register komponensek közös stílusfájlja.

### Fő osztályok

| Osztály        | Leírás                                              |
| -------------- | --------------------------------------------------- |
| `.oldal`       | Teljes viewport központosított elrendezés           |
| `.kartya`      | Kártya konténer (max-width: 400px, sötét barna)     |
| `.mezo`        | Űrlap mező wrapper (címke + input)                  |
| `.hiba-szoveg` | Validációs hiba szöveg (piros)                      |
| `.hiba-doboz`  | Hiba doboz (piros háttér)                           |
| `button`       | Teljes szélességű arany akció gomb                  |
| `.link`        | Alul elhelyezett link szöveg arany akcentussal      |

---

## Komponens stílusok

### GameComponent (`game.component.css`)

- Sticky fejléc (`backdrop-filter: blur`)
- Szoba kártyák CSS Grid rácsozata
- Kategória szekciók színes jelvényekkel
- Szoba állapotok:
  - Teljesített: zöld keret, átlátszóság
  - Aktív: normál, gombok láthatók
  - Zárolt: halvány overlay blur-rel
- Egyjátékos/Többjátékos gombok (arany vs. kék)
- Reset banner (arany háttér)
- Jelszóváltoztatás modál

### RoomComponent (`room.component.css`)

A legnagyobb stílusfájl (~20 KB).

- **Szoba témák:** CSS egyedi tulajdonságok (`--room-accent`, `--room-bg`, `--room-glow`)
- **Kérdés csomópontok:** Abszolút pozícionálás a rácson, ragyogás/pulzálás animáció
- **Számjegy tálca:** Összegyűjtött számjegyek megjelenítése
- **Kérdés modál:** ABCD opció gombok, válasz animációk (helyes=zöld, helytelen=piros)
- **Kód beküldés modál:** Számjegy előnézet, szövegbevitel
- **Multiplayer overlay:** Várakozás, link megosztás, játékos chipek
- **Időzítő animáció:** Piros pulzálás 10 perc után

### LeaderboardComponent (`leaderboard.component.css`)

- Dobogó kártyák (arany/ezüst/bronz)
- Statisztika sor
- Rendezés gombok
- Rangsor táblázat sor animációkkal
- Érem stílusok (1.–3. hely)
- „Te" jelvény kiemelés

### AdminComponent (`admin.component.css`)

- Bal oldali navigáció
- Statisztika rácsozat szín kódolással
- Adattáblák sortördeléssel
- Szerkesztő overlay modálok
- Toast értesítések (zöld siker, piros hiba)
- Űrlap stílusok (inputok, select, checkbox)

---

## Szoba témák

A `RoomComponent` CSS egyedi tulajdonságokat használ a szoba vizuális témájához:

```css
:host {
  --room-accent: #c19a6b;  /* Alapértelmezett */
  --room-bg: #1a1410;
  --room-glow: rgba(193, 154, 107, 0.4);
}
```

### Elérhető témák

| Téma       | Kulcsszó     | Akcentus  | Háttér    | Ragyogás szín               |
| ---------- | ------------ | --------- | --------- | --------------------------- |
| Könyvtár   | `könyvtár`   | `#c19a6b` | `#1a1410` | `rgba(193, 154, 107, 0.4)`  |
| Labor      | `labor`      | `#34d399` | `#0a1a14` | (zöld)                      |
| Pince      | `pince`      | `#fb923c` | `#1a0e09` | (narancs)                   |
| Hajó       | `kapitány`   | `#38bdf8` | `#0a1220` | (kék)                       |
| Űr         | `űr`         | `#c19a6b` | `#1a1410` | (arany)                     |

A téma a szoba `Name` mezőjéből határozódik meg kulcsszó egyezéssel a `roomTheme` getter-ben.

---

## Reszponzív design

Az alkalmazás reszponzív breakpoint-okat használ:

| Breakpoint    | Érintett komponensek              |
| ------------- | --------------------------------- |
| `≤ 900px`     | Game: kártyák oszlopváltás        |
| `≤ 768px`     | Leaderboard: dobogó vertikális    |
| `≤ 700px`     | Admin: sidebar összecsukás        |
| `≤ 600px`     | Game: fejléc stackelés            |
| `≤ 480px`     | Room: modál teljes szélességű     |

Az admin panel és a játékszoba különösen optimalizált mobil nézetekhez.
