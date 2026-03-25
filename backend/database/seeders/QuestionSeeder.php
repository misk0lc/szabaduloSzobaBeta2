<?php

namespace Database\Seeders;

use App\Models\Hint;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $levelQuestions = [

            // ─── 1. SZOBA: A Könyvtárszoba (LevelID = 1) ──────────────────
            1 => [
                ['text' => 'Melyik évben jelent meg Shakespeare első szonettje?',
                 'answer' => '1609', 'digit' => 3, 'money' => 50, 'x' => 1, 'y' => 2,
                 'hint' => 'A szonetteket egy kvartóban adták ki, II. Jakab uralkodása alatt.', 'cost' => 30,
                 'wrong' => ['1564', '1623', '1590']],

                ['text' => 'Hány fejezetre oszlik Dante Isteni Színjátéka?',
                 'answer' => '100', 'digit' => 1, 'money' => 40, 'x' => 2, 'y' => 1,
                 'hint' => 'Pokol, Purgatórium és Paradicsom – mindegyikből 33 ének, plusz egy bevezető.', 'cost' => 25,
                 'wrong' => ['33', '99', '108']],

                ['text' => 'Melyik betű hiányzik: "K_nyvtár"?',
                 'answer' => 'ö', 'digit' => 4, 'money' => 30, 'x' => 3, 'y' => 3,
                 'hint' => 'Ez egy két pontos magánhangzó.', 'cost' => 15,
                 'wrong' => ['o', 'ő', 'u']],

                ['text' => 'Mi a Bibliában az első könyv neve?',
                 'answer' => 'Genezis', 'digit' => 7, 'money' => 45, 'x' => 4, 'y' => 2,
                 'hint' => 'A világ teremtéséről szól, latinul "keletkezés" a jelentése.', 'cost' => 20,
                 'wrong' => ['Exodus', 'Zsoltárok', 'Jelenések']],

                ['text' => 'Hány betű van a magyar ábécében?',
                 'answer' => '44', 'digit' => 2, 'money' => 35, 'x' => 5, 'y' => 4,
                 'hint' => 'Több mint az angolban, mert a digráfok (cs, dz...) is beleszámítanak.', 'cost' => 20,
                 'wrong' => ['26', '40', '42']],

                ['text' => 'Ki írta a "Pál utcai fiúk" regényt?',
                 'answer' => 'Molnár Ferenc', 'digit' => 8, 'money' => 40, 'x' => 6, 'y' => 1,
                 'hint' => 'Magyar szerző, a 20. század elején élt.', 'cost' => 25,
                 'wrong' => ['Jókai Mór', 'Mikszáth Kálmán', 'Arany János']],

                ['text' => 'Melyik számrendszert használják a számítógépek?',
                 'answer' => 'bináris', 'digit' => 5, 'money' => 50, 'x' => 7, 'y' => 3,
                 'hint' => 'Csak 0 és 1 számjegyeket tartalmaz.', 'cost' => 30,
                 'wrong' => ['decimális', 'hexadecimális', 'oktális']],

                ['text' => 'Hány oldala van egy könyvnek, ha 200 lapja van?',
                 'answer' => '400', 'digit' => 9, 'money' => 30, 'x' => 8, 'y' => 2,
                 'hint' => 'Minden lapnak két oldala van.', 'cost' => 15,
                 'wrong' => ['200', '300', '100']],

                ['text' => 'Mi a könyv latin neve?',
                 'answer' => 'liber', 'digit' => 6, 'money' => 45, 'x' => 9, 'y' => 4,
                 'hint' => 'Ebből ered a "könyvtár" szó latinban is.', 'cost' => 25,
                 'wrong' => ['codex', 'pagina', 'volumen']],

                ['text' => 'Hány betű van a "KÖNYVESPOLC" szóban?',
                 'answer' => '10', 'digit' => 0, 'money' => 25, 'x' => 10, 'y' => 1,
                 'hint' => 'Számold meg egyenként a betűket!', 'cost' => 10,
                 'wrong' => ['9', '11', '12']],

                ['text' => 'Mi a Gutenberg-galaxis kifejezés jelentése?',
                 'answer' => 'nyomtatott kultúra', 'digit' => 3, 'money' => 55, 'x' => 11, 'y' => 3,
                 'hint' => 'Marshall McLuhan alkotta a kifejezést.', 'cost' => 30,
                 'wrong' => ['digitális világ', 'kéziratos kor', 'szóbeli hagyomány']],

                ['text' => 'Melyik évben nyomtatta Gutenberg az első Bibliát?',
                 'answer' => '1455', 'digit' => 1, 'money' => 50, 'x' => 12, 'y' => 2,
                 'hint' => 'A 15. század közepén, Mainzban történt.', 'cost' => 25,
                 'wrong' => ['1440', '1492', '1501']],

                ['text' => 'Hány kötetből áll Tolsztoj "Háború és béke" regénye?',
                 'answer' => '4', 'digit' => 4, 'money' => 40, 'x' => 13, 'y' => 4,
                 'hint' => 'Az eredeti orosz kiadás kötetszámára gondolj.', 'cost' => 20,
                 'wrong' => ['2', '3', '6']],

                ['text' => 'Mi az ISBN rövidítés teljes neve?',
                 'answer' => 'International Standard Book Number', 'digit' => 7, 'money' => 60, 'x' => 14, 'y' => 1,
                 'hint' => 'Minden könyvnek egyedi ilyen azonosítója van.', 'cost' => 35,
                 'wrong' => ['International Serial Book Name', 'Index Standard Book Notation', 'International System Book Number']],

                ['text' => 'Hány fejezet van a Harry Potter első kötetében?',
                 'answer' => '17', 'digit' => 2, 'money' => 35, 'x' => 15, 'y' => 3,
                 'hint' => 'A Bölcsek Köve 17 fejezetre tagolódik.', 'cost' => 20,
                 'wrong' => ['15', '19', '22']],

                ['text' => 'Ki volt az első magyar Nobel-díjas irodalomban?',
                 'answer' => 'Kertész Imre', 'digit' => 8, 'money' => 55, 'x' => 16, 'y' => 2,
                 'hint' => '2002-ben kapta, a Sorstalanság c. regényéért.', 'cost' => 30,
                 'wrong' => ['Esterházy Péter', 'Nádas Péter', 'Márai Sándor']],

                ['text' => 'Melyik betű a leggyakoribb az angol nyelvben?',
                 'answer' => 'e', 'digit' => 5, 'money' => 30, 'x' => 17, 'y' => 4,
                 'hint' => 'A Scrabble-ben is a legtöbb ilyen betűlap van.', 'cost' => 15,
                 'wrong' => ['a', 't', 's']],

                ['text' => 'Hány novellából áll Maupassant életműve hozzávetőleg?',
                 'answer' => '300', 'digit' => 9, 'money' => 45, 'x' => 18, 'y' => 1,
                 'hint' => 'A francia mester több száz rövid elbeszélést írt.', 'cost' => 25,
                 'wrong' => ['100', '200', '500']],

                ['text' => 'Mi a könyvtári jelzet angol neve?',
                 'answer' => 'call number', 'digit' => 6, 'money' => 40, 'x' => 19, 'y' => 3,
                 'hint' => 'Ezzel hívják elő a katalógusban a könyvet.', 'cost' => 20,
                 'wrong' => ['shelf code', 'book index', 'library tag']],

                ['text' => 'Melyik városban található az Alexandriai Könyvtár?',
                 'answer' => 'Alexandria', 'digit' => 0, 'money' => 50, 'x' => 20, 'y' => 2,
                 'hint' => 'Egyiptomban van, az ókor leghíresebb könyvtára volt.', 'cost' => 25,
                 'wrong' => ['Kairó', 'Athén', 'Róma']],
            ],

            // ─── 2. SZOBA: A Laboratorium (LevelID = 2) ───────────────────
            2 => [
                ['text' => 'Mi a víz kémiai képlete?',
                 'answer' => 'H2O', 'digit' => 5, 'money' => 30, 'x' => 1, 'y' => 1,
                 'hint' => 'Hidrogén és oxigén alkotja.', 'cost' => 15,
                 'wrong' => ['CO2', 'H2O2', 'HO']],

                ['text' => 'Hány proton van a szén atomban?',
                 'answer' => '6', 'digit' => 2, 'money' => 35, 'x' => 2, 'y' => 3,
                 'hint' => 'A periódusos rendszer 6. eleme.', 'cost' => 20,
                 'wrong' => ['4', '8', '12']],

                ['text' => 'Mi az Avogadro-szám értéke (×10^23)?',
                 'answer' => '6.022', 'digit' => 7, 'money' => 60, 'x' => 3, 'y' => 2,
                 'hint' => 'Egy mól anyagban ennyi részecske van.', 'cost' => 35,
                 'wrong' => ['3.011', '9.033', '12.044']],

                ['text' => 'Melyik gáz alkotja a levegő ~78%-át?',
                 'answer' => 'nitrogén', 'digit' => 1, 'money' => 40, 'x' => 4, 'y' => 4,
                 'hint' => 'N2 képletű, szagtalan és színtelen.', 'cost' => 20,
                 'wrong' => ['oxigén', 'argon', 'szén-dioxid']],

                ['text' => 'Mi a pH-ja a tiszta víznek?',
                 'answer' => '7', 'digit' => 4, 'money' => 30, 'x' => 5, 'y' => 1,
                 'hint' => 'Sem savas, sem lúgos – semleges.', 'cost' => 15,
                 'wrong' => ['5', '9', '6.5']],

                ['text' => 'Melyik elem vegyjele Au?',
                 'answer' => 'arany', 'digit' => 8, 'money' => 45, 'x' => 6, 'y' => 3,
                 'hint' => 'A latin "aurum" szóból ered.', 'cost' => 25,
                 'wrong' => ['ezüst', 'réz', 'platina']],

                ['text' => 'Hány elektron fér az első elektronhéjra?',
                 'answer' => '2', 'digit' => 3, 'money' => 35, 'x' => 7, 'y' => 2,
                 'hint' => 'A legbelső héj befogadóképessége korlátozott.', 'cost' => 20,
                 'wrong' => ['4', '6', '8']],

                ['text' => 'Mi az abszolút nulla fok Celsiusban?',
                 'answer' => '-273.15', 'digit' => 6, 'money' => 55, 'x' => 8, 'y' => 4,
                 'hint' => '0 Kelvin egyenértékű ezzel.', 'cost' => 30,
                 'wrong' => ['-100', '-200', '-300']],

                ['text' => 'Melyik a legnehezebb természetes elem?',
                 'answer' => 'urán', 'digit' => 9, 'money' => 50, 'x' => 9, 'y' => 1,
                 'hint' => 'Radioaktív, atomszáma 92.', 'cost' => 25,
                 'wrong' => ['ólom', 'arany', 'vas']],

                ['text' => 'Mi az ozon képlete?',
                 'answer' => 'O3', 'digit' => 0, 'money' => 30, 'x' => 10, 'y' => 3,
                 'hint' => 'Három oxigénatom alkotja.', 'cost' => 15,
                 'wrong' => ['O2', 'O4', 'CO3']],

                ['text' => 'Hány atomból áll egy vízmolekula?',
                 'answer' => '3', 'digit' => 5, 'money' => 25, 'x' => 11, 'y' => 2,
                 'hint' => '2 hidrogén + 1 oxigén.', 'cost' => 10,
                 'wrong' => ['2', '4', '5']],

                ['text' => 'Mi a fény sebessége vákuumban (km/s)?',
                 'answer' => '299792', 'digit' => 2, 'money' => 65, 'x' => 12, 'y' => 4,
                 'hint' => 'Kb. 300 000 km/s, de a pontos érték...', 'cost' => 40,
                 'wrong' => ['150000', '200000', '350000']],

                ['text' => 'Melyik elem a periódusos rendszer 1. eleme?',
                 'answer' => 'hidrogén', 'digit' => 7, 'money' => 30, 'x' => 13, 'y' => 1,
                 'hint' => 'A legkönnyebb és leggyakoribb elem az univerzumban.', 'cost' => 15,
                 'wrong' => ['hélium', 'lítium', 'oxigén']],

                ['text' => 'Mi a DNS rövidítés teljes neve magyarul?',
                 'answer' => 'dezoxiribonukleinsav', 'digit' => 1, 'money' => 60, 'x' => 14, 'y' => 3,
                 'hint' => 'Az örökítő anyag neve.', 'cost' => 35,
                 'wrong' => ['ribonukleinsav', 'aminosavlánc', 'fehérjesav']],

                ['text' => 'Hány bázispár van az emberi genomban kb. (milliárd)?',
                 'answer' => '3', 'digit' => 4, 'money' => 55, 'x' => 15, 'y' => 2,
                 'hint' => 'Kb. 3 milliárd bázispár alkotja.', 'cost' => 30,
                 'wrong' => ['1', '6', '10']],

                ['text' => 'Mi az Einstein legismertebb képlete?',
                 'answer' => 'E=mc2', 'digit' => 8, 'money' => 45, 'x' => 16, 'y' => 4,
                 'hint' => 'Energia, tömeg és fénysebesség kapcsolata.', 'cost' => 25,
                 'wrong' => ['F=ma', 'E=hf', 'PV=nRT']],

                ['text' => 'Melyik részecskének nincs töltése?',
                 'answer' => 'neutron', 'digit' => 3, 'money' => 40, 'x' => 17, 'y' => 1,
                 'hint' => 'Az atommag egyik alkotója, neve is a semlegességre utal.', 'cost' => 20,
                 'wrong' => ['proton', 'elektron', 'foton']],

                ['text' => 'Mi a konyhasó kémiai neve?',
                 'answer' => 'nátrium-klorid', 'digit' => 6, 'money' => 35, 'x' => 18, 'y' => 3,
                 'hint' => 'NaCl képletű vegyület.', 'cost' => 20,
                 'wrong' => ['kálium-klorid', 'nátrium-szulfát', 'kalcium-klorid']],

                ['text' => 'Hány gramm egy mól víz?',
                 'answer' => '18', 'digit' => 9, 'money' => 40, 'x' => 19, 'y' => 2,
                 'hint' => '2×1 (H) + 16 (O) = ?', 'cost' => 20,
                 'wrong' => ['16', '20', '36']],

                ['text' => 'Melyik gáz felelős az üvegházhatásért főleg?',
                 'answer' => 'szén-dioxid', 'digit' => 0, 'money' => 30, 'x' => 20, 'y' => 4,
                 'hint' => 'CO2 képletű, az égés mellékterméke.', 'cost' => 15,
                 'wrong' => ['nitrogén', 'oxigén', 'hidrogén']],
            ],

            // ─── 3. SZOBA: A Kastély Pincéje (LevelID = 3) ────────────────
            3 => [
                ['text' => 'Melyik évben épült a Budai Vár?',
                 'answer' => '1265', 'digit' => 6, 'money' => 55, 'x' => 1, 'y' => 2,
                 'hint' => 'IV. Béla idejében, a tatárjárás után.', 'cost' => 30,
                 'wrong' => ['1000', '1400', '1526']],

                ['text' => 'Hány méter mélyen van egy tipikus várárok?',
                 'answer' => '5', 'digit' => 3, 'money' => 30, 'x' => 2, 'y' => 1,
                 'hint' => 'Általában 3-7 méter mélységű szokott lenni.', 'cost' => 15,
                 'wrong' => ['2', '10', '20']],

                ['text' => 'Mi a neve a várak felvonóhídjának?',
                 'answer' => 'felvonóhíd', 'digit' => 8, 'money' => 25, 'x' => 3, 'y' => 3,
                 'hint' => 'A várárok felett húzódik, fel lehet húzni.', 'cost' => 10,
                 'wrong' => ['kapuhíd', 'vonóhíd', 'lánchíd']],

                ['text' => 'Melyik fegyver volt a középkor legfélelmetesebb ostromgépe?',
                 'answer' => 'trebuchet', 'digit' => 1, 'money' => 60, 'x' => 4, 'y' => 4,
                 'hint' => 'Ellenegyensúlyos katapult, franciáktól ered a neve.', 'cost' => 35,
                 'wrong' => ['balliszta', 'katapult', 'mangonell']],

                ['text' => 'Hány évig tartott a Száz Éves Háború valójában?',
                 'answer' => '116', 'digit' => 4, 'money' => 50, 'x' => 5, 'y' => 1,
                 'hint' => 'A neve ellenére nem pont 100 évig tartott (1337-1453).', 'cost' => 25,
                 'wrong' => ['100', '87', '130']],

                ['text' => 'Mi a neve a vár parancsnokának latinul?',
                 'answer' => 'castellan', 'digit' => 7, 'money' => 45, 'x' => 6, 'y' => 3,
                 'hint' => 'A latin "castellum" szóból ered.', 'cost' => 25,
                 'wrong' => ['dominus', 'prefectus', 'comes']],

                ['text' => 'Milyen anyagból készültek a középkori zárak?',
                 'answer' => 'vas', 'digit' => 2, 'money' => 35, 'x' => 7, 'y' => 2,
                 'hint' => 'Fémes, erős anyag, rozsdásodik.', 'cost' => 20,
                 'wrong' => ['fa', 'bronz', 'réz']],

                ['text' => 'Mi a neve a vár legbelső, legerősebb tornyának?',
                 'answer' => 'donjon', 'digit' => 5, 'money' => 55, 'x' => 8, 'y' => 4,
                 'hint' => 'Angolul "keep"-nek hívják.', 'cost' => 30,
                 'wrong' => ['bástyatorony', 'kaputorony', 'őrtorony']],

                ['text' => 'Hány esztendős volt Mátyás király, amikor trónra lépett?',
                 'answer' => '15', 'digit' => 9, 'money' => 50, 'x' => 9, 'y' => 1,
                 'hint' => '1458-ban koronázták meg, 1443-ban született.', 'cost' => 25,
                 'wrong' => ['18', '20', '25']],

                ['text' => 'Mi a neve a középkori páncélnak?',
                 'answer' => 'páncélzat', 'digit' => 0, 'money' => 35, 'x' => 10, 'y' => 3,
                 'hint' => 'Acélból készült, lovagok viselték.', 'cost' => 20,
                 'wrong' => ['vért', 'láncing', 'bőrpáncél']],

                ['text' => 'Melyik városban található a Hohenzollern-kastély?',
                 'answer' => 'Hechingen', 'digit' => 6, 'money' => 65, 'x' => 11, 'y' => 2,
                 'hint' => 'Baden-Württemberg tartományban, Németországban.', 'cost' => 40,
                 'wrong' => ['Berlin', 'München', 'Stuttgart']],

                ['text' => 'Hány évig tartott a keresztes hadjáratok kora nagyjából?',
                 'answer' => '200', 'digit' => 3, 'money' => 55, 'x' => 12, 'y' => 4,
                 'hint' => '1095-től kb. 1291-ig tartott.', 'cost' => 30,
                 'wrong' => ['50', '100', '300']],

                ['text' => 'Mi az íjász másik neve?',
                 'answer' => 'nyilas', 'digit' => 8, 'money' => 30, 'x' => 13, 'y' => 1,
                 'hint' => 'A Nyilas csillagkép neve is ebből ered.', 'cost' => 15,
                 'wrong' => ['lándzsás', 'kardos', 'kopjás']],

                ['text' => 'Hány vár található Magyarországon hozzávetőleg?',
                 'answer' => '1000', 'digit' => 1, 'money' => 50, 'x' => 14, 'y' => 3,
                 'hint' => 'Romokban is számítanak, kb. ezernyi van.', 'cost' => 25,
                 'wrong' => ['100', '300', '500']],

                ['text' => 'Mi a neve a kőből épített védőfal tetején lévő fogaknak?',
                 'answer' => 'pártázat', 'digit' => 4, 'money' => 60, 'x' => 15, 'y' => 2,
                 'hint' => 'Alternáló magasabb és alacsonyabb részek.', 'cost' => 35,
                 'wrong' => ['lőrés', 'mellvéd', 'bástyafok']],

                ['text' => 'Melyik királyunk építtette a visegrádi palotát?',
                 'answer' => 'Mátyás', 'digit' => 7, 'money' => 45, 'x' => 16, 'y' => 4,
                 'hint' => 'A Hollós melléknéven is ismert királyunk.', 'cost' => 25,
                 'wrong' => ['István', 'Béla', 'Zsigmond']],

                ['text' => 'Mi a neve a várat körülvevő vizes ároknak?',
                 'answer' => 'vizesárok', 'digit' => 2, 'money' => 30, 'x' => 17, 'y' => 1,
                 'hint' => 'A várat víz veszi körül ebben az esetben.', 'cost' => 15,
                 'wrong' => ['szárazárok', 'csatorna', 'sánc']],

                ['text' => 'Hány tonna követ mozgattak a Notre-Dame építésekor kb.?',
                 'answer' => '5000', 'digit' => 5, 'money' => 65, 'x' => 18, 'y' => 3,
                 'hint' => 'Több ezer tonna kő kellett hozzá.', 'cost' => 35,
                 'wrong' => ['1000', '2000', '10000']],

                ['text' => 'Mi a neve a középkori mesterségek szervezetének?',
                 'answer' => 'céh', 'digit' => 9, 'money' => 35, 'x' => 19, 'y' => 2,
                 'hint' => 'Rövid, egyszerű szó, kézművesek alkották.', 'cost' => 20,
                 'wrong' => ['rend', 'gilda', 'szövetség']],

                ['text' => 'Melyik évszakban ostromoltak leggyakrabban a középkorban?',
                 'answer' => 'nyár', 'digit' => 0, 'money' => 40, 'x' => 20, 'y' => 4,
                 'hint' => 'Amikor az utak járhatók és az élelem bőséges.', 'cost' => 20,
                 'wrong' => ['tél', 'ősz', 'tavasz']],
            ],

            // ─── 4. SZOBA: A Kapitány Kabinja (LevelID = 4) ───────────────
            4 => [
                ['text' => 'Mi a neve az iránytű magnetikus északi irányának?',
                 'answer' => 'mágneses észak', 'digit' => 4, 'money' => 50, 'x' => 1, 'y' => 2,
                 'hint' => 'Nem egyezik meg a földrajzi északi pólussal.', 'cost' => 25,
                 'wrong' => ['földrajzi észak', 'igaz észak', 'csillagészak']],

                ['text' => 'Hány fok egy teljes szélrózsa?',
                 'answer' => '360', 'digit' => 9, 'money' => 30, 'x' => 2, 'y' => 1,
                 'hint' => 'Ugyanannyi, mint egy teljes kör.', 'cost' => 15,
                 'wrong' => ['180', '270', '90']],

                ['text' => 'Mi a neve a hajó legfőbb kormányzójának?',
                 'answer' => 'kapitány', 'digit' => 2, 'money' => 25, 'x' => 3, 'y' => 3,
                 'hint' => 'Ez a szoba is az övé!', 'cost' => 10,
                 'wrong' => ['navigátor', 'kormányos', 'első tiszt']],

                ['text' => 'Melyik óceán a legnagyobb?',
                 'answer' => 'Csendes-óceán', 'digit' => 7, 'money' => 40, 'x' => 4, 'y' => 4,
                 'hint' => 'A Föld felszínének majdnem felét lefedi.', 'cost' => 20,
                 'wrong' => ['Atlanti-óceán', 'Indiai-óceán', 'Jeges-tenger']],

                ['text' => 'Hány égtája van a szélrózsának?',
                 'answer' => '32', 'digit' => 5, 'money' => 45, 'x' => 5, 'y' => 1,
                 'hint' => 'A teljes szélrózsán 32 irány szerepel.', 'cost' => 25,
                 'wrong' => ['4', '8', '16']],

                ['text' => 'Mi a neve a hajó hátsó részének?',
                 'answer' => 'far', 'digit' => 3, 'money' => 30, 'x' => 6, 'y' => 3,
                 'hint' => 'Az orrral ellentétes rész.', 'cost' => 15,
                 'wrong' => ['orr', 'fedélzet', 'tatár']],

                ['text' => 'Melyik évben süllyedt el a Titanic?',
                 'answer' => '1912', 'digit' => 8, 'money' => 45, 'x' => 7, 'y' => 2,
                 'hint' => 'Az első és egyben utolsó útján, április 15-én.', 'cost' => 25,
                 'wrong' => ['1905', '1918', '1920']],

                ['text' => 'Hány méter a Titanic hossza?',
                 'answer' => '269', 'digit' => 1, 'money' => 55, 'x' => 8, 'y' => 4,
                 'hint' => 'Kb. 270 méter, majdnem három futballpálya.', 'cost' => 30,
                 'wrong' => ['200', '300', '350']],

                ['text' => 'Mi a neve a kalózok zászlajának?',
                 'answer' => 'Jolly Roger', 'digit' => 6, 'money' => 40, 'x' => 9, 'y' => 1,
                 'hint' => 'Koponyás-keresztcsontú fekete zászló.', 'cost' => 20,
                 'wrong' => ['Black Flag', 'Death Banner', 'Skull Cross']],

                ['text' => 'Hány tengeri mérföld = 1 km kb.?',
                 'answer' => '0.54', 'digit' => 0, 'money' => 60, 'x' => 10, 'y' => 3,
                 'hint' => '1 tengeri mérföld = 1.852 km, tehát fordítva...', 'cost' => 35,
                 'wrong' => ['1', '1.85', '0.8']],

                ['text' => 'Mi a neve a hajótest alján lévő gerincnek?',
                 'answer' => 'gerinc', 'digit' => 4, 'money' => 45, 'x' => 11, 'y' => 2,
                 'hint' => 'Az emberi testben is van ilyen!', 'cost' => 25,
                 'wrong' => ['talpgerenda', 'fenéklap', 'bordázat']],

                ['text' => 'Ki találta fel a szextánst (1731)?',
                 'answer' => 'John Hadley', 'digit' => 9, 'money' => 65, 'x' => 12, 'y' => 4,
                 'hint' => '1731-ben alkotta meg az eszközt.', 'cost' => 40,
                 'wrong' => ['Isaac Newton', 'James Cook', 'Francis Drake']],

                ['text' => 'Hány szélességi fok adja meg az egyenlítőt?',
                 'answer' => '0', 'digit' => 2, 'money' => 30, 'x' => 13, 'y' => 1,
                 'hint' => 'Ez az alapvonal, ahonnan az északi és déli szélességet mérik.', 'cost' => 15,
                 'wrong' => ['90', '45', '180']],

                ['text' => 'Hány fokos a tájolás észak-keletre?',
                 'answer' => '45', 'digit' => 7, 'money' => 40, 'x' => 14, 'y' => 3,
                 'hint' => 'Az észak (0°) és a kelet (90°) között félúton.', 'cost' => 20,
                 'wrong' => ['30', '60', '135']],

                ['text' => 'Mi a SOS kód Morse-ban?',
                 'answer' => '... --- ...', 'digit' => 5, 'money' => 55, 'x' => 15, 'y' => 2,
                 'hint' => '3 pont, 3 vonal, 3 pont.', 'cost' => 30,
                 'wrong' => ['--- ... ---', '.. -- ..', '.... .... ....']],

                ['text' => 'Melyik tenger a legsósabb a világon?',
                 'answer' => 'Holt-tenger', 'digit' => 3, 'money' => 45, 'x' => 16, 'y' => 4,
                 'hint' => 'Ide nem lehet elsüllyedni úszás közben.', 'cost' => 25,
                 'wrong' => ['Vörös-tenger', 'Mediterrán-tenger', 'Kaszpi-tenger']],

                ['text' => 'Hány millió km² a Csendes-óceán területe?',
                 'answer' => '165', 'digit' => 8, 'money' => 60, 'x' => 17, 'y' => 1,
                 'hint' => 'Kb. 165 millió km², a Föld felszínének ~33%-a.', 'cost' => 35,
                 'wrong' => ['82', '106', '200']],

                ['text' => 'Mi a neve a hajó fedélzetén lévő fő árbocnak?',
                 'answer' => 'főárboc', 'digit' => 1, 'money' => 35, 'x' => 18, 'y' => 3,
                 'hint' => 'A legmagasabb és legerősebb árboc a hajón.', 'cost' => 20,
                 'wrong' => ['előárboc', 'tatárboc', 'vitorlaárboc']],

                ['text' => 'Hány csomó = 1 tengeri mérföld/óra?',
                 'answer' => '1', 'digit' => 6, 'money' => 25, 'x' => 19, 'y' => 2,
                 'hint' => 'A csomó és a tengeri mérföld/óra ugyanaz!', 'cost' => 10,
                 'wrong' => ['2', '1.852', '0.5']],

                ['text' => 'Melyik felfedező kerülte meg elsőként a Földet?',
                 'answer' => 'Magellán', 'digit' => 0, 'money' => 50, 'x' => 20, 'y' => 4,
                 'hint' => 'Portugál felfedező, 1519-ben indult útnak.', 'cost' => 25,
                 'wrong' => ['Kolumbusz', 'Vasco da Gama', 'Drake']],
            ],

            // ─── 5. SZOBA: Az Űrállomás (LevelID = 5) ─────────────────────
            5 => [
                ['text' => 'Melyik bolygó a legnagyobb a Naprendszerben?',
                 'answer' => 'Jupiter', 'digit' => 7, 'money' => 40, 'x' => 1, 'y' => 2,
                 'hint' => 'Nagy Vörös Folt nevű vihar tombol rajta.', 'cost' => 20,
                 'wrong' => ['Szaturnusz', 'Neptunusz', 'Uránusz']],

                ['text' => 'Hány perc alatt ér a fény a Napból a Földre?',
                 'answer' => '8', 'digit' => 3, 'money' => 35, 'x' => 2, 'y' => 1,
                 'hint' => 'Kb. 8 perc, pontosan 8 perc 20 másodperc.', 'cost' => 20,
                 'wrong' => ['1', '15', '30']],

                ['text' => 'Mi a neve az ISS-nek magyarul?',
                 'answer' => 'Nemzetközi Űrállomás', 'digit' => 9, 'money' => 30, 'x' => 3, 'y' => 3,
                 'hint' => 'International Space Station – magyarul...', 'cost' => 15,
                 'wrong' => ['Globális Kutatóállomás', 'Egyesített Űrbázis', 'Kozmikus Laboratórium']],

                ['text' => 'Hány milliárd éves az univerzum hozzávetőleg?',
                 'answer' => '13.8', 'digit' => 1, 'money' => 65, 'x' => 4, 'y' => 4,
                 'hint' => 'A Nagy Bumm óta telt el ennyi idő.', 'cost' => 40,
                 'wrong' => ['4.5', '6', '20']],

                ['text' => 'Melyik bolygónak van látványos gyűrűrendszere?',
                 'answer' => 'Szaturnusz', 'digit' => 5, 'money' => 35, 'x' => 5, 'y' => 1,
                 'hint' => 'Jégből és kőzetből álló gyűrűi vannak.', 'cost' => 20,
                 'wrong' => ['Jupiter', 'Mars', 'Vénusz']],

                ['text' => 'Mi a neve a Föld egyetlen természetes holdjának?',
                 'answer' => 'Hold', 'digit' => 2, 'money' => 25, 'x' => 6, 'y' => 3,
                 'hint' => 'Minden este látható az égbolton (ha tiszta).', 'cost' => 10,
                 'wrong' => ['Phobos', 'Titan', 'Ganümédész']],

                ['text' => 'Hány km/s az első kozmikus sebesség?',
                 'answer' => '7.9', 'digit' => 8, 'money' => 60, 'x' => 7, 'y' => 2,
                 'hint' => 'Ez a Föld körüli keringéshez szükséges sebesség.', 'cost' => 35,
                 'wrong' => ['11.2', '3.5', '5']],

                ['text' => 'Melyik űrhajós volt az első ember a Holdon?',
                 'answer' => 'Neil Armstrong', 'digit' => 4, 'money' => 45, 'x' => 8, 'y' => 4,
                 'hint' => '1969. július 20-án lépett a Hold felszínére.', 'cost' => 25,
                 'wrong' => ['Buzz Aldrin', 'Jurij Gagarin', 'John Glenn']],

                ['text' => 'Mi a neve a Mars egyik holdjának?',
                 'answer' => 'Phobos', 'digit' => 6, 'money' => 55, 'x' => 9, 'y' => 1,
                 'hint' => 'A másik Deimos. Ez a görög "félelem" szó.', 'cost' => 30,
                 'wrong' => ['Titan', 'Io', 'Europa']],

                ['text' => 'Hány bolygó van a Naprendszerben?',
                 'answer' => '8', 'digit' => 0, 'money' => 25, 'x' => 10, 'y' => 3,
                 'hint' => 'Plútót 2006-ban törölték a listáról.', 'cost' => 10,
                 'wrong' => ['9', '7', '10']],

                ['text' => 'Mi a neve a fekete lyuk körüli határnak?',
                 'answer' => 'eseményhorizont', 'digit' => 7, 'money' => 70, 'x' => 11, 'y' => 2,
                 'hint' => 'Ezen belülről már semmi sem tud kijutni.', 'cost' => 45,
                 'wrong' => ['szingularitás', 'akkréciós korong', 'gravitációs határ']],

                ['text' => 'Melyik évben jutott először ember a Holdra?',
                 'answer' => '1969', 'digit' => 3, 'money' => 40, 'x' => 12, 'y' => 4,
                 'hint' => 'Az Apollo 11 küldetés éve.', 'cost' => 20,
                 'wrong' => ['1961', '1972', '1965']],

                ['text' => 'Mi az oxigén vegyjele?',
                 'answer' => 'O', 'digit' => 9, 'money' => 20, 'x' => 13, 'y' => 1,
                 'hint' => 'Az ábécé 15. betűje.', 'cost' => 10,
                 'wrong' => ['Ox', 'Og', 'Or']],

                ['text' => 'Hány Kelvin a Nap felszíni hőmérséklete?',
                 'answer' => '5778', 'digit' => 1, 'money' => 65, 'x' => 14, 'y' => 3,
                 'hint' => 'Kb. 5500 Celsius fok, ami kb. ennyi Kelvin.', 'cost' => 40,
                 'wrong' => ['1000', '3000', '10000']],

                ['text' => 'Mi a neve a Tejútrendszer középpontja körüli fekete lyuknak?',
                 'answer' => 'Sagittarius A*', 'digit' => 5, 'money' => 75, 'x' => 15, 'y' => 2,
                 'hint' => 'A Nyilas csillagkép irányában található.', 'cost' => 45,
                 'wrong' => ['Andromeda X', 'Cygnus X-1', 'M87*']],

                ['text' => 'Hány évig tart a Halley-üstökös keringési ideje?',
                 'answer' => '75', 'digit' => 2, 'money' => 55, 'x' => 16, 'y' => 4,
                 'hint' => 'Kb. 75-76 évenként látható a Földről.', 'cost' => 30,
                 'wrong' => ['25', '50', '100']],

                ['text' => 'Melyik bolygón van az Olimposz-hegy (legmagasabb a Naprendszerben)?',
                 'answer' => 'Mars', 'digit' => 8, 'money' => 50, 'x' => 17, 'y' => 1,
                 'hint' => 'A vörös bolygón, ~22 km magas.', 'cost' => 25,
                 'wrong' => ['Vénusz', 'Jupiter', 'Hold']],

                ['text' => 'Mi a neve az űrruha levegőellátó rendszerének rövidítve?',
                 'answer' => 'PLSS', 'digit' => 4, 'money' => 60, 'x' => 18, 'y' => 3,
                 'hint' => 'Portable Life Support System.', 'cost' => 35,
                 'wrong' => ['ALSS', 'ECLSS', 'BLSS']],

                ['text' => 'Hány napja van a Mars évének?',
                 'answer' => '687', 'digit' => 6, 'money' => 55, 'x' => 19, 'y' => 2,
                 'hint' => 'A Mars lassabban kering a Nap körül, mint a Föld.', 'cost' => 30,
                 'wrong' => ['365', '500', '800']],

                ['text' => 'Mi a neve az űrben való mozgáshoz szükséges egyenletnek?',
                 'answer' => 'Tsiolkovsky-egyenlet', 'digit' => 0, 'money' => 70, 'x' => 20, 'y' => 4,
                 'hint' => 'Orosz rakétatudós nevét viseli.', 'cost' => 45,
                 'wrong' => ['Newton-egyenlet', 'Kepler-egyenlet', 'Hohmann-egyenlet']],
            ],

            // ─── 6. SZOBA: A Játékszoba (Könnyed) ─────────────────────────
            6 => [
                ['text' => 'Hány szín van a szivárványban?',
                 'answer' => '7', 'digit' => 1, 'money' => 20, 'x' => 1, 'y' => 1,
                 'hint' => 'Piros, narancs, sárga, zöld, kék, indigó, ibolya.', 'cost' => 10,
                 'wrong' => ['5', '6', '8']],

                ['text' => 'Hány láb van egy macskának?',
                 'answer' => '4', 'digit' => 2, 'money' => 15, 'x' => 2, 'y' => 2,
                 'hint' => 'Négylábú emlős.', 'cost' => 5,
                 'wrong' => ['2', '6', '8']],

                ['text' => 'Mi a fővárosa Magyarországnak?',
                 'answer' => 'Budapest', 'digit' => 3, 'money' => 20, 'x' => 3, 'y' => 1,
                 'hint' => 'Buda és Pest egyesüléséből jött létre 1873-ban.', 'cost' => 10,
                 'wrong' => ['Pécs', 'Debrecen', 'Győr']],

                ['text' => 'Hány perc van egy órában?',
                 'answer' => '60', 'digit' => 4, 'money' => 15, 'x' => 4, 'y' => 3,
                 'hint' => 'Az óramutató egyszer kerüli meg a számlapot.', 'cost' => 5,
                 'wrong' => ['30', '90', '100']],

                ['text' => 'Melyik szín keletkezik a kék és sárga keveréséből?',
                 'answer' => 'zöld', 'digit' => 5, 'money' => 20, 'x' => 5, 'y' => 2,
                 'hint' => 'A fű és a levél színe.', 'cost' => 10,
                 'wrong' => ['lila', 'narancs', 'barna']],

                ['text' => 'Hány betű van a "MACSKA" szóban?',
                 'answer' => '6', 'digit' => 6, 'money' => 15, 'x' => 6, 'y' => 1,
                 'hint' => 'M-A-C-S-K-A – számold meg!', 'cost' => 5,
                 'wrong' => ['4', '5', '7']],

                ['text' => 'Mi az ellentéte a "hideg" szónak?',
                 'answer' => 'meleg', 'digit' => 7, 'money' => 15, 'x' => 7, 'y' => 3,
                 'hint' => 'Ha kimész a napra nyáron, ezt érzed.', 'cost' => 5,
                 'wrong' => ['forró', 'langyos', 'izzó']],

                ['text' => 'Hány hónap van egy évben?',
                 'answer' => '12', 'digit' => 8, 'money' => 15, 'x' => 8, 'y' => 2,
                 'hint' => 'Január, február... december – összesen ennyi.', 'cost' => 5,
                 'wrong' => ['10', '11', '13']],

                ['text' => 'Melyik állat ugrik a legtöbbet?',
                 'answer' => 'kenguru', 'digit' => 9, 'money' => 20, 'x' => 9, 'y' => 1,
                 'hint' => 'Ausztrál erszényes állat.', 'cost' => 10,
                 'wrong' => ['béka', 'ló', 'nyúl']],

                ['text' => 'Hány oldala van egy háromszögnek?',
                 'answer' => '3', 'digit' => 0, 'money' => 15, 'x' => 10, 'y' => 3,
                 'hint' => 'A neve is megmondja!', 'cost' => 5,
                 'wrong' => ['4', '5', '6']],
            ],

            // ─── 7. SZOBA: A Kávézó (Könnyed) ─────────────────────────────
            7 => [
                ['text' => 'Melyik gyümölcsből készül a narancsszörp?',
                 'answer' => 'narancs', 'digit' => 2, 'money' => 20, 'x' => 1, 'y' => 1,
                 'hint' => 'A neve is megmondja!', 'cost' => 5,
                 'wrong' => ['citrom', 'alma', 'szőlő']],

                ['text' => 'Hány csésze fér egy literbe kb.?',
                 'answer' => '4', 'digit' => 5, 'money' => 20, 'x' => 2, 'y' => 2,
                 'hint' => 'Egy átlagos csésze kb. 250 ml.', 'cost' => 10,
                 'wrong' => ['2', '6', '10']],

                ['text' => 'Mi a tejes kávé olasz neve?',
                 'answer' => 'latte', 'digit' => 1, 'money' => 25, 'x' => 3, 'y' => 1,
                 'hint' => 'Olasz szó, tejjel készül.', 'cost' => 10,
                 'wrong' => ['cappuccino', 'espresso', 'macchiato']],

                ['text' => 'Hány gramm cukor van egy kockacukorban kb.?',
                 'answer' => '4', 'digit' => 7, 'money' => 20, 'x' => 4, 'y' => 3,
                 'hint' => 'Kis kocka, nem sok cukor.', 'cost' => 10,
                 'wrong' => ['1', '10', '20']],

                ['text' => 'Melyik italban nincs koffein?',
                 'answer' => 'víz', 'digit' => 3, 'money' => 20, 'x' => 5, 'y' => 2,
                 'hint' => 'A legtermészetesebb ital.', 'cost' => 5,
                 'wrong' => ['kávé', 'tea', 'cola']],

                ['text' => 'Hány dkg = 1 kg?',
                 'answer' => '100', 'digit' => 9, 'money' => 15, 'x' => 6, 'y' => 1,
                 'hint' => '1 kg = 1000 g = 100 dkg.', 'cost' => 5,
                 'wrong' => ['10', '50', '1000']],

                ['text' => 'Melyik városban alapítottak elsőként kávézót Európában?',
                 'answer' => 'Velence', 'digit' => 6, 'money' => 30, 'x' => 7, 'y' => 3,
                 'hint' => 'Olaszországi vízi város, 1647 körül.', 'cost' => 15,
                 'wrong' => ['Párizs', 'Bécs', 'London']],

                ['text' => 'Mi a cappuccino jellegzetessége?',
                 'answer' => 'tejhab', 'digit' => 4, 'money' => 20, 'x' => 8, 'y' => 2,
                 'hint' => 'A tetején vastagon ott van.', 'cost' => 10,
                 'wrong' => ['karamell', 'csokoládé', 'fahéj']],

                ['text' => 'Hány kalória van egy átlagos süteményben kb.?',
                 'answer' => '300', 'digit' => 8, 'money' => 20, 'x' => 9, 'y' => 1,
                 'hint' => 'Kb. 200-400 kalória között szokott lenni.', 'cost' => 10,
                 'wrong' => ['50', '100', '1000']],

                ['text' => 'Hány dl = 1 liter?',
                 'answer' => '10', 'digit' => 0, 'money' => 15, 'x' => 10, 'y' => 3,
                 'hint' => 'Deciliter = 1/10 liter.', 'cost' => 5,
                 'wrong' => ['5', '100', '1000']],
            ],

            // ─── 8. SZOBA: Az Osztályterem (Könnyed) ───────────────────────
            8 => [
                ['text' => 'Hány az 5×5?',
                 'answer' => '25', 'digit' => 3, 'money' => 15, 'x' => 1, 'y' => 1,
                 'hint' => 'Öt ötös.', 'cost' => 5,
                 'wrong' => ['20', '30', '15']],

                ['text' => 'Mi Magyarország nemzeti jelképe?',
                 'answer' => 'korona', 'digit' => 6, 'money' => 20, 'x' => 2, 'y' => 2,
                 'hint' => 'Szent István kapott ilyet a pápától.', 'cost' => 10,
                 'wrong' => ['zászló', 'sas', 'kard']],

                ['text' => 'Hány folyó van Magyarország közepén (fő folyó)?',
                 'answer' => '1', 'digit' => 9, 'money' => 20, 'x' => 3, 'y' => 1,
                 'hint' => 'A Duna Budapest közepén folyik át.', 'cost' => 10,
                 'wrong' => ['2', '3', '5']],

                ['text' => 'Mennyi 10+10?',
                 'answer' => '20', 'digit' => 1, 'money' => 15, 'x' => 4, 'y' => 3,
                 'hint' => 'Tíz meg tíz.', 'cost' => 5,
                 'wrong' => ['10', '15', '30']],

                ['text' => 'Melyik évszakban van a leghosszabb nap?',
                 'answer' => 'nyár', 'digit' => 4, 'money' => 20, 'x' => 5, 'y' => 2,
                 'hint' => 'Június 21. a nyári napforduló.', 'cost' => 10,
                 'wrong' => ['tél', 'tavasz', 'ősz']],

                ['text' => 'Hány sarok van egy négyzetnek?',
                 'answer' => '4', 'digit' => 7, 'money' => 15, 'x' => 6, 'y' => 1,
                 'hint' => 'Minden oldalhoz tartozik egy sarok.', 'cost' => 5,
                 'wrong' => ['3', '5', '6']],

                ['text' => 'Mi a víz halmazállapota szobahőmérsékleten?',
                 'answer' => 'folyékony', 'digit' => 2, 'money' => 20, 'x' => 7, 'y' => 3,
                 'hint' => 'Sem szilárd, sem gáznemű.', 'cost' => 10,
                 'wrong' => ['szilárd', 'gáznemű', 'plazma']],

                ['text' => 'Hány az 100 - 37?',
                 'answer' => '63', 'digit' => 5, 'money' => 20, 'x' => 8, 'y' => 2,
                 'hint' => 'Száztól vond le harminchetett.', 'cost' => 10,
                 'wrong' => ['57', '73', '67']],

                ['text' => 'Ki festette a Mona Lisát?',
                 'answer' => 'Leonardo da Vinci', 'digit' => 8, 'money' => 25, 'x' => 9, 'y' => 1,
                 'hint' => 'Olasz reneszánsz mester.', 'cost' => 10,
                 'wrong' => ['Michelangelo', 'Raphael', 'Botticelli']],

                ['text' => 'Hány kontinens van a Földön?',
                 'answer' => '7', 'digit' => 0, 'money' => 20, 'x' => 10, 'y' => 3,
                 'hint' => 'Afrika, Amerika (2), Ázsia, Ausztrália, Európa, Antarktisz.', 'cost' => 10,
                 'wrong' => ['5', '6', '8']],
            ],

            // ─── 9. SZOBA: A Kert (Könnyed) ────────────────────────────────
            9 => [
                ['text' => 'Melyik virág jelképe a szerelemnek?',
                 'answer' => 'rózsa', 'digit' => 4, 'money' => 20, 'x' => 1, 'y' => 1,
                 'hint' => 'Piros, tüskés szárú virág.', 'cost' => 10,
                 'wrong' => ['tulipán', 'napraforgó', 'liliom']],

                ['text' => 'Hány szirom van egy tulipánon általában?',
                 'answer' => '6', 'digit' => 7, 'money' => 20, 'x' => 2, 'y' => 2,
                 'hint' => 'Kívül 3, belül 3 – összesen 6.', 'cost' => 10,
                 'wrong' => ['3', '4', '5']],

                ['text' => 'Mi a neve a fa tápanyagot szállító szöveteinek?',
                 'answer' => 'háncs', 'digit' => 2, 'money' => 25, 'x' => 3, 'y' => 1,
                 'hint' => 'A kéreg alatt van, lefelé szállítja a cukrot.', 'cost' => 10,
                 'wrong' => ['gyökér', 'xilém', 'parenchima']],

                ['text' => 'Hány éves egy fa, ha 50 évgyűrűje van?',
                 'answer' => '50', 'digit' => 5, 'money' => 15, 'x' => 4, 'y' => 3,
                 'hint' => 'Minden év egy évgyűrű.', 'cost' => 5,
                 'wrong' => ['25', '100', '200']],

                ['text' => 'Melyik rovaron keres nektárt?',
                 'answer' => 'méh', 'digit' => 1, 'money' => 20, 'x' => 5, 'y' => 2,
                 'hint' => 'Mézet is csinál belőle.', 'cost' => 10,
                 'wrong' => ['lepke', 'hangya', 'bogár']],

                ['text' => 'Mi a fotoszintézis terméke?',
                 'answer' => 'oxigén', 'digit' => 9, 'money' => 25, 'x' => 6, 'y' => 1,
                 'hint' => 'A növények ezt adják a levegőbe.', 'cost' => 10,
                 'wrong' => ['szén-dioxid', 'nitrogén', 'hidrogén']],

                ['text' => 'Hány szirom van egy margarétán?',
                 'answer' => '13', 'digit' => 3, 'money' => 25, 'x' => 7, 'y' => 3,
                 'hint' => 'Szeret, nem szeret... páratlan szám jellemzi.', 'cost' => 10,
                 'wrong' => ['10', '12', '20']],

                ['text' => 'Melyik fa levelei sárgulnak el ősszel elsőként?',
                 'answer' => 'nyárfa', 'digit' => 6, 'money' => 20, 'x' => 8, 'y' => 2,
                 'hint' => 'Már az első szélre remeg és sárgul.', 'cost' => 10,
                 'wrong' => ['tölgy', 'fenyő', 'bükk']],

                ['text' => 'Mi a neve a virág szárát végén lévő porzónak?',
                 'answer' => 'portok', 'digit' => 8, 'money' => 25, 'x' => 9, 'y' => 1,
                 'hint' => 'A pollen itt keletkezik.', 'cost' => 10,
                 'wrong' => ['bibeszál', 'vacok', 'csésze']],

                ['text' => 'Hány cm-t nő egy bambusz naponta kb.?',
                 'answer' => '90', 'digit' => 0, 'money' => 30, 'x' => 10, 'y' => 3,
                 'hint' => 'A leggyorsabban növő növény, majdnem 1 méter naponta.', 'cost' => 15,
                 'wrong' => ['1', '10', '30']],
            ],

            // ─── 10. SZOBA: A Cukrászda (Könnyed) ─────────────────────────
            10 => [
                ['text' => 'Mi a tortaalaphoz szükséges fő alapanyag?',
                 'answer' => 'liszt', 'digit' => 6, 'money' => 20, 'x' => 1, 'y' => 1,
                 'hint' => 'Búzából őrölt fehér por.', 'cost' => 10,
                 'wrong' => ['cukor', 'tojás', 'vaj']],

                ['text' => 'Hány tojás kell általában egy süteményhez (alap)?',
                 'answer' => '3', 'digit' => 2, 'money' => 20, 'x' => 2, 'y' => 2,
                 'hint' => 'A legtöbb alaprecept ennyit ír.', 'cost' => 10,
                 'wrong' => ['1', '6', '10']],

                ['text' => 'Melyik ország hozta létre a macaron süteményt?',
                 'answer' => 'Franciaország', 'digit' => 9, 'money' => 25, 'x' => 3, 'y' => 1,
                 'hint' => 'Párizsi elegáns cukrászdák specialitása.', 'cost' => 10,
                 'wrong' => ['Olaszország', 'Belgium', 'Ausztria']],

                ['text' => 'Hány Celsius fokon olvad a csoki kb.?',
                 'answer' => '34', 'digit' => 5, 'money' => 25, 'x' => 4, 'y' => 3,
                 'hint' => 'Majdnem a testünk hőmérsékletén, ezért olvad a szánkban.', 'cost' => 10,
                 'wrong' => ['50', '20', '100']],

                ['text' => 'Mi a neve a habverő angol megfelelőjének?',
                 'answer' => 'whisk', 'digit' => 1, 'money' => 20, 'x' => 5, 'y' => 2,
                 'hint' => 'Angolul így kérd a cukrásznál.', 'cost' => 10,
                 'wrong' => ['mixer', 'beater', 'blender']],

                ['text' => 'Hány szelet van egy hagyományos tortában?',
                 'answer' => '8', 'digit' => 4, 'money' => 20, 'x' => 6, 'y' => 1,
                 'hint' => 'Általában 8 cikkre szokták szeletelni.', 'cost' => 10,
                 'wrong' => ['4', '6', '12']],

                ['text' => 'Mi a neve a fánk lyukjából kivágott kis golyónak?',
                 'answer' => 'fánklyuk', 'digit' => 7, 'money' => 20, 'x' => 7, 'y' => 3,
                 'hint' => 'Amerikában "donut hole" a neve.', 'cost' => 10,
                 'wrong' => ['mini fánk', 'fánkpufi', 'gömbfánk']],

                ['text' => 'Melyik hozzávaló adja a sütemény barna kérgét?',
                 'answer' => 'cukor', 'digit' => 3, 'money' => 20, 'x' => 8, 'y' => 2,
                 'hint' => 'Maillard-reakció és karamellizáció – mindkettő ezt igényli.', 'cost' => 10,
                 'wrong' => ['liszt', 'tojás', 'vaj']],

                ['text' => 'Hány gramm az egy evőkanálnyi méz kb.?',
                 'answer' => '21', 'digit' => 8, 'money' => 25, 'x' => 9, 'y' => 1,
                 'hint' => 'Kb. 20-22 gramm, a méz sűrű.', 'cost' => 10,
                 'wrong' => ['10', '50', '100']],

                ['text' => 'Mi a neve a sajttortában használt alapnak?',
                 'answer' => 'keksz', 'digit' => 0, 'money' => 20, 'x' => 10, 'y' => 3,
                 'hint' => 'Összezúzva, vajjal keverve teszik a formába.', 'cost' => 10,
                 'wrong' => ['piskóta', 'tészta', 'muffin']],
            ],

            // ─── 11. SZOBA: A Detektív Irodája (Közepes) ──────────────────
            11 => [
                ['text' => 'Ki írta a Sherlock Holmes históriákat?',
                 'answer' => 'Arthur Conan Doyle', 'digit' => 3, 'money' => 40, 'x' => 1, 'y' => 1,
                 'hint' => 'Skót orvos-szerző, a 19. században élt.', 'cost' => 20,
                 'wrong' => ['Agatha Christie', 'Edgar Allan Poe', 'Raymond Chandler']],

                ['text' => 'Mi a neve a bűnjelnek angolul?',
                 'answer' => 'evidence', 'digit' => 7, 'money' => 35, 'x' => 2, 'y' => 2,
                 'hint' => 'Amit a nyomozó gyűjt.', 'cost' => 20,
                 'wrong' => ['clue', 'proof', 'suspect']],

                ['text' => 'Hány érzékszerve van az embernek?',
                 'answer' => '5', 'digit' => 1, 'money' => 30, 'x' => 3, 'y' => 1,
                 'hint' => 'Látás, hallás, szaglás, ízlés, tapintás.', 'cost' => 15,
                 'wrong' => ['4', '6', '7']],

                ['text' => 'Mi a neve a lábnyom vizsgálatának?',
                 'answer' => 'nyomrögzítés', 'digit' => 5, 'money' => 45, 'x' => 4, 'y' => 3,
                 'hint' => 'Gipsszel is lehet rögzíteni.', 'cost' => 25,
                 'wrong' => ['daktiloszkópia', 'balisztika', 'profilalkotás']],

                ['text' => 'Melyik tudományág foglalkozik az ujjlenyomatokkal?',
                 'answer' => 'daktiloszkópia', 'digit' => 9, 'money' => 50, 'x' => 5, 'y' => 2,
                 'hint' => 'A görög "daktylos" (ujj) szóból ered.', 'cost' => 30,
                 'wrong' => ['kriminológia', 'toxikológia', 'patológia']],

                ['text' => 'Mi a neve a titkos üzenetek tanulmányozásának?',
                 'answer' => 'kriptográfia', 'digit' => 4, 'money' => 45, 'x' => 6, 'y' => 1,
                 'hint' => 'A görög "kryptos" (rejtett) szóból ered.', 'cost' => 25,
                 'wrong' => ['sztenográfia', 'paleográfia', 'szemafor']],

                ['text' => 'Hány éve állt fenn a berlini fal?',
                 'answer' => '28', 'digit' => 2, 'money' => 40, 'x' => 7, 'y' => 3,
                 'hint' => '1961-ben épült, 1989-ben bontották le.', 'cost' => 20,
                 'wrong' => ['10', '20', '40']],

                ['text' => 'Mi a neve az anonim bejelentőnek?',
                 'answer' => 'informátor', 'digit' => 6, 'money' => 35, 'x' => 8, 'y' => 2,
                 'hint' => 'Angolul "informant" vagy "tipster".', 'cost' => 20,
                 'wrong' => ['tanú', 'gyanúsított', 'besúgó']],

                ['text' => 'Melyik évben alapítottak meg az FBI-t?',
                 'answer' => '1908', 'digit' => 8, 'money' => 55, 'x' => 9, 'y' => 1,
                 'hint' => 'A 20. század elején, Theodore Roosevelt elnöksége alatt.', 'cost' => 30,
                 'wrong' => ['1865', '1935', '1945']],

                ['text' => 'Mi az alias szó jelentése?',
                 'answer' => 'álnév', 'digit' => 0, 'money' => 35, 'x' => 10, 'y' => 3,
                 'hint' => 'Bűnözők gyakran használnak ilyet.', 'cost' => 20,
                 'wrong' => ['fedőszto', 'tettes', 'helyszín']],
            ],

            // ─── 12. SZOBA: A Múzeum (Közepes) ────────────────────────────
            12 => [
                ['text' => 'Melyik civilizáció építette a piramisokat?',
                 'answer' => 'egyiptomiak', 'digit' => 5, 'money' => 40, 'x' => 1, 'y' => 1,
                 'hint' => 'Fáraók sírjai, az ókori Keleten.', 'cost' => 20,
                 'wrong' => ['görögök', 'rómaiak', 'maják']],

                ['text' => 'Hány éve épült a Nagy Fal (hozzávetőleg)?',
                 'answer' => '2000', 'digit' => 2, 'money' => 45, 'x' => 2, 'y' => 2,
                 'hint' => 'Kr.e. 7. száztól fokozatosan építették.', 'cost' => 25,
                 'wrong' => ['500', '1000', '5000']],

                ['text' => 'Mi a neve a régi görög amfiteátrumnak?',
                 'answer' => 'théátron', 'digit' => 8, 'money' => 40, 'x' => 3, 'y' => 1,
                 'hint' => 'Ebből ered a "théáter" szavunk.', 'cost' => 20,
                 'wrong' => ['stadion', 'agora', 'akropolisz']],

                ['text' => 'Hány csodája van az ókori világnak?',
                 'answer' => '7', 'digit' => 6, 'money' => 35, 'x' => 4, 'y' => 3,
                 'hint' => 'Hét csoda volt, ebből ma csak egy áll: a piramisok.', 'cost' => 20,
                 'wrong' => ['5', '10', '12']],

                ['text' => 'Ki volt Kleopátra férje?',
                 'answer' => 'Antonius', 'digit' => 4, 'money' => 45, 'x' => 5, 'y' => 2,
                 'hint' => 'Marcus Antonius, a római hadvezér.', 'cost' => 25,
                 'wrong' => ['Caesar', 'Octavianus', 'Pompeius']],

                ['text' => 'Melyik városban van a Louvre múzeum?',
                 'answer' => 'Párizs', 'digit' => 1, 'money' => 30, 'x' => 6, 'y' => 1,
                 'hint' => 'A világ legtöbbet látogatott múzeuma.', 'cost' => 15,
                 'wrong' => ['London', 'Berlin', 'Madrid']],

                ['text' => 'Hány évig tartott a Pax Romana?',
                 'answer' => '200', 'digit' => 7, 'money' => 50, 'x' => 7, 'y' => 3,
                 'hint' => 'Kr.u. 27-től 180-ig, kb. két évszázad.', 'cost' => 30,
                 'wrong' => ['50', '100', '400']],

                ['text' => 'Mi a neve a régi görög harcos sisakjának?',
                 'answer' => 'korinthoszi sisak', 'digit' => 3, 'money' => 45, 'x' => 8, 'y' => 2,
                 'hint' => 'Korintosz városáról kapta a nevét.', 'cost' => 25,
                 'wrong' => ['athéni sisak', 'spártai sisak', 'makedón sisak']],

                ['text' => 'Melyik évben omlott le Római Birodalom nyugati fele?',
                 'answer' => '476', 'digit' => 9, 'money' => 55, 'x' => 9, 'y' => 1,
                 'hint' => 'Az 5. század végén, Romulus Augustulus alatt.', 'cost' => 30,
                 'wrong' => ['410', '500', '380']],

                ['text' => 'Mi a neve a mérési tartálynak az ókori Rómában (folyadékra)?',
                 'answer' => 'amphora', 'digit' => 0, 'money' => 40, 'x' => 10, 'y' => 3,
                 'hint' => 'Két füles agyagedény, bor tárolásra is használták.', 'cost' => 20,
                 'wrong' => ['urna', 'hidria', 'krater']],
            ],

            // ─── 13. SZOBA: A Téli Kunyhó (Közepes) ───────────────────────
            13 => [
                ['text' => 'Hány fokos a víz fagyáspontja Celsius-ban?',
                 'answer' => '0', 'digit' => 7, 'money' => 30, 'x' => 1, 'y' => 1,
                 'hint' => 'A jég olvadási pontja is ugyanennyi.', 'cost' => 15,
                 'wrong' => ['-10', '4', '-4']],

                ['text' => 'Mi a neve a hópihe szimmetriájának?',
                 'answer' => 'hatszoros szimmetria', 'digit' => 3, 'money' => 45, 'x' => 2, 'y' => 2,
                 'hint' => 'Hat ág, hatszoros tengely.', 'cost' => 25,
                 'wrong' => ['négyszeres szimmetria', 'háromszoros szimmetria', 'nyolcszoros szimmetria']],

                ['text' => 'Hány Celsius fokban fagy meg a tengervíz kb.?',
                 'answer' => '-2', 'digit' => 5, 'money' => 40, 'x' => 3, 'y' => 1,
                 'hint' => 'A só csökkenti a fagyáspontot.', 'cost' => 20,
                 'wrong' => ['0', '-5', '-10']],

                ['text' => 'Mi a lavinák leggyakoribb kiváltó oka?',
                 'answer' => 'friss hó', 'digit' => 1, 'money' => 45, 'x' => 4, 'y' => 3,
                 'hint' => 'Az instabil friss réteg csúszik meg.', 'cost' => 25,
                 'wrong' => ['eső', 'szél', 'földrengés']],

                ['text' => 'Hány méter per másodpercnél számít viharnak a szél?',
                 'answer' => '25', 'digit' => 9, 'money' => 50, 'x' => 5, 'y' => 2,
                 'hint' => 'Kb. 90 km/h feletti szélerőnél.', 'cost' => 25,
                 'wrong' => ['10', '15', '50']],

                ['text' => 'Mi a neve a sarkvidéki fénynek?',
                 'answer' => 'sarki fény', 'digit' => 4, 'money' => 35, 'x' => 6, 'y' => 1,
                 'hint' => 'Északi változata aurora borealis.', 'cost' => 20,
                 'wrong' => ['napfény', 'zodiákális fény', 'biolumineszcencia']],

                ['text' => 'Hány rétegű ruházatot ajánlanak extrém hidegben?',
                 'answer' => '3', 'digit' => 2, 'money' => 35, 'x' => 7, 'y' => 3,
                 'hint' => 'Alap-, közép- és külső réteg.', 'cost' => 20,
                 'wrong' => ['1', '2', '5']],

                ['text' => 'Mi a hidegvérű állatok másik neve?',
                 'answer' => 'poikiloterm', 'digit' => 6, 'money' => 50, 'x' => 8, 'y' => 2,
                 'hint' => 'Testük hőmérséklete a környezethez igazodik.', 'cost' => 30,
                 'wrong' => ['homeotherm', 'endotherm', 'mezotherm']],

                ['text' => 'Hány fokos volt a rekord hideg a Földön (Celsius)?',
                 'answer' => '-89.2', 'digit' => 8, 'money' => 60, 'x' => 9, 'y' => 1,
                 'hint' => '1983-ban mérték Antarktiszon, Vostok állomáson.', 'cost' => 35,
                 'wrong' => ['-50', '-70', '-100']],

                ['text' => 'Mi a hólabda angolul?',
                 'answer' => 'snowball', 'digit' => 0, 'money' => 25, 'x' => 10, 'y' => 3,
                 'hint' => 'Snow = hó, ball = labda.', 'cost' => 10,
                 'wrong' => ['iceball', 'frostball', 'coldball']],
            ],

            // ─── 14. SZOBA: A Hajógyár (Közepes) ──────────────────────────
            14 => [
                ['text' => 'Mi a neve a hajótest legalsó szerkezeti elemének?',
                 'answer' => 'gerinc', 'digit' => 4, 'money' => 40, 'x' => 1, 'y' => 1,
                 'hint' => 'Az emberi testnek is van ilyen!', 'cost' => 20,
                 'wrong' => ['borda', 'fenék', 'talpgerenda']],

                ['text' => 'Hány tonna az átlagos modern óceánjáró (ezer tonna)?',
                 'answer' => '100', 'digit' => 8, 'money' => 45, 'x' => 2, 'y' => 2,
                 'hint' => 'A nagy luxushajók kb. 100-230 ezer tonnásak.', 'cost' => 25,
                 'wrong' => ['10', '50', '500']],

                ['text' => 'Melyik anyagból készül a modern hajótest?',
                 'answer' => 'acél', 'digit' => 2, 'money' => 35, 'x' => 3, 'y' => 1,
                 'hint' => 'Erős és korrózióálló fém.', 'cost' => 20,
                 'wrong' => ['alumínium', 'titán', 'fa']],

                ['text' => 'Mi a neve a hajó felső, vízszintes padlójának?',
                 'answer' => 'fedélzet', 'digit' => 6, 'money' => 35, 'x' => 4, 'y' => 3,
                 'hint' => 'Ezen sétálnak az utasok.', 'cost' => 20,
                 'wrong' => ['orrfedél', 'talppadló', 'burkolat']],

                ['text' => 'Hány km/h a legsebbesebb hajó rekordja kb.?',
                 'answer' => '120', 'digit' => 5, 'money' => 50, 'x' => 5, 'y' => 2,
                 'hint' => 'A Spirit of Australia víziröppentyű tartja.', 'cost' => 30,
                 'wrong' => ['50', '80', '200']],

                ['text' => 'Mi a neve a hajó stabilitását biztosító vízteli tartálynak?',
                 'answer' => 'ballaszt', 'digit' => 1, 'money' => 45, 'x' => 6, 'y' => 1,
                 'hint' => 'Súlyként dienál az egyensúly megtartásához.', 'cost' => 25,
                 'wrong' => ['ciszterna', 'tartály', 'fenékvíz']],

                ['text' => 'Melyik folyamaton megy át az acél gyártásnál?',
                 'answer' => 'olvasztás', 'digit' => 7, 'money' => 40, 'x' => 7, 'y' => 3,
                 'hint' => 'Nagyon magas hőmérsékleten olvasztják meg.', 'cost' => 20,
                 'wrong' => ['hengerlés', 'öntés', 'kovácsolás']],

                ['text' => 'Mi a neve a hajó orrán lévő vízalatti nyúlványnak?',
                 'answer' => 'bulb', 'digit' => 3, 'money' => 50, 'x' => 8, 'y' => 2,
                 'hint' => 'Csökkenti a hullámállást.', 'cost' => 25,
                 'wrong' => ['keel', 'stem', 'skeg']],

                ['text' => 'Hány méter magas az Empire State Building?',
                 'answer' => '443', 'digit' => 9, 'money' => 45, 'x' => 9, 'y' => 1,
                 'hint' => 'Az antennával együtt 443 méter.', 'cost' => 25,
                 'wrong' => ['300', '500', '600']],

                ['text' => 'Mi a neve a hullámtörő gátnak a kikötőnél?',
                 'answer' => 'móló', 'digit' => 0, 'money' => 35, 'x' => 10, 'y' => 3,
                 'hint' => 'A hajók mellé kötnek ki.', 'cost' => 20,
                 'wrong' => ['gát', 'töltés', 'rakpart']],
            ],

            // ─── 15. SZOBA: A Varázslatos Könyvtár (Közepes) ──────────────
            15 => [
                ['text' => 'Mi a neve a varázslók szervezetének Harry Potterben?',
                 'answer' => 'Mágiaügyi Minisztérium', 'digit' => 6, 'money' => 40, 'x' => 1, 'y' => 1,
                 'hint' => 'A Ministry of Magic magyar neve.', 'cost' => 20,
                 'wrong' => ['Roxfort', 'Azkaban', 'Kviddics-szövetség']],

                ['text' => 'Hány varázspálca fér az Ollivander üzletébe?',
                 'answer' => 'több ezer', 'digit' => 2, 'money' => 35, 'x' => 2, 'y' => 2,
                 'hint' => 'Tele van polcokkal a plafonig.', 'cost' => 20,
                 'wrong' => ['100', '500', '1000']],

                ['text' => 'Mi a neve az ógörög varázslástudománynak?',
                 'answer' => 'mágia', 'digit' => 8, 'money' => 35, 'x' => 3, 'y' => 1,
                 'hint' => 'A perzsa "magus" szóból ered.', 'cost' => 20,
                 'wrong' => ['alkímia', 'asztrológia', 'jóslás']],

                ['text' => 'Hány sárkányfajta szerepel a "Tűz és jég dala" sorozatban?',
                 'answer' => '3', 'digit' => 4, 'money' => 40, 'x' => 4, 'y' => 3,
                 'hint' => 'Daenerys három sárkánya: Drogon, Rhaegal, Viserion.', 'cost' => 20,
                 'wrong' => ['1', '5', '7']],

                ['text' => 'Mi a neve a boszorkányok gyülekezőhelyének?',
                 'answer' => 'szombat', 'digit' => 7, 'money' => 45, 'x' => 5, 'y' => 2,
                 'hint' => 'A "Walpurgis-éj" is egy ilyen esemény neve.', 'cost' => 25,
                 'wrong' => ['kovén', 'rituálé', 'sabbat']],

                ['text' => 'Hány kötetből áll az eredeti Harry Potter sorozat?',
                 'answer' => '7', 'digit' => 1, 'money' => 30, 'x' => 6, 'y' => 1,
                 'hint' => 'Az utolsó a Halál ereklyéi.', 'cost' => 15,
                 'wrong' => ['5', '6', '8']],

                ['text' => 'Mi a neve az alkimisták által keresett csodaanyagnak?',
                 'answer' => 'bölcsek köve', 'digit' => 5, 'money' => 45, 'x' => 7, 'y' => 3,
                 'hint' => 'Fémeket arannyá változtatna, és halhatatlanságot adna.', 'cost' => 25,
                 'wrong' => ['örök élet elixírje', 'arany formula', 'mesterelixír']],

                ['text' => 'Hány éves Harry Potter a Roxfortba kerülésekor?',
                 'answer' => '11', 'digit' => 3, 'money' => 30, 'x' => 8, 'y' => 2,
                 'hint' => 'Az első könyvben, 1991-ben.', 'cost' => 15,
                 'wrong' => ['9', '12', '13']],

                ['text' => 'Mi a neve Gandalf fehér lovának?',
                 'answer' => 'Shadowfax', 'digit' => 9, 'money' => 50, 'x' => 9, 'y' => 1,
                 'hint' => 'A lovak ura, Tolkien Gyűrűk Urából.', 'cost' => 25,
                 'wrong' => ['Roheryn', 'Arod', 'Asfaloth']],

                ['text' => 'Hány könyvből áll a Gyűrűk Ura?',
                 'answer' => '3', 'digit' => 0, 'money' => 35, 'x' => 10, 'y' => 3,
                 'hint' => 'A Gyűrű szövetsége, A két torony, A király visszatér.', 'cost' => 20,
                 'wrong' => ['1', '2', '6']],
            ],
        ];

        foreach ($levelQuestions as $levelId => $questions) {
            foreach ($questions as $q) {
                $question = Question::create([
                    'LevelID'       => $levelId,
                    'QuestionText'  => $q['text'],
                    'CorrectAnswer' => $q['answer'],
                    'RewardDigit'   => $q['digit'],
                    'MoneyReward'   => $q['money'],
                    'PositionX'     => $q['x'],
                    'PositionY'     => $q['y'],
                ]);

                Hint::create([
                    'QuestionID' => $question->QuestionID,
                    'HintText'   => $q['hint'],
                    'Cost'       => $q['cost'],
                    'HintOrder'  => 1,
                ]);

                // Helyes opció
                QuestionOption::create([
                    'QuestionID' => $question->QuestionID,
                    'OptionText' => $q['answer'],
                    'IsCorrect'  => true,
                ]);

                // Hamis opciók
                foreach ($q['wrong'] as $wrongAnswer) {
                    QuestionOption::create([
                        'QuestionID' => $question->QuestionID,
                        'OptionText' => $wrongAnswer,
                        'IsCorrect'  => false,
                    ]);
                }
            }
        }
    }
}
