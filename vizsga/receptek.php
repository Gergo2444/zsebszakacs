<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: bejelentkezes.php");
    exit;
}
?>
<!doctype html>
<html lang="hu">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Receptek | Zsebszakács</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<main class="box">
  <a class="btn" href="index.php">← Vissza</a>




  <h1>Banános zabkása:</h1>
  <h2>Hozzávalók:</h2>
  <ul>
    <li>60 g zabpehely</li>
    <li>250 ml tej (vagy víz)</li>
    <li>1 banán</li>
    <li>1 tk méz (opcionális)</li>
    <li>csipet fahéj</li>
  </ul>

  <h2>Elkészítés:</h2>
  <ol>
    <li>A tejet melegítsd fel, add hozzá a zabpelyhet.</li>
    <li>Főzd 3–5 percig, amíg sűrű lesz.</li>
    <li>A banánt karikázd rá, szórd meg fahéjjal.</li>
    <li>Ha kell, egy kis mézzel édesítsd.</li>
  </ol>

  <h2>Tipp:</h2>
  <p>Mehet rá kakaópor, mogyoróvaj vagy pár szem dió is.</p>
</main>

<main class="box">
  <h1>Csirkés rizs:</h1>
  <h2>Hozzávalók:</h2>
  <ul>
    <li>200 g csirkemell</li>
    <li>150 g rizs</li>
    <li>1 ek olaj</li>
    <li>só, bors, pirospaprika</li>
    <li>1 gerezd fokhagyma (opcionális)</li>
  </ul>

  <h2>Elkészítés:</h2>
  <ol>
    <li>A rizst főzd meg sós vízben.</li>
    <li>A csirkét kockázd fel, fűszerezd.</li>
    <li>Serpenyőben olajon pirítsd meg 8–10 perc alatt.</li>
    <li>Keverd össze a rizzsel, kész is.</li>
  </ol>

  <h2>Tipp:</h2>
  <p>Dobd fel egy kis kukoricával vagy savanyú ubival.</p>
</main>

<main class="box">
  <h1>Sajtos-sonkás melegszendvics:</h1>
  <h2>Hozzávalók:</h2>
  <ul>
    <li>2 szelet kenyér</li>
    <li>2 szelet sonka</li>
    <li>2 szelet sajt</li>
    <li>kevés vaj</li>
    <li>ketchup / tejföl (opcionális)</li>
  </ul>

  <h2>Elkészíté:</h2>
  <ol>
    <li>Kend meg a kenyeret vékonyan vajjal.</li>
    <li>Tedd rá a sonkát és a sajtot.</li>
    <li>Sütőben 200°C-on 6–8 perc (vagy szendvicssütőben).</li>
    <li>Amikor a sajt megolvadt, kész.</li>
  </ol>

  <h2>Tipp:</h2>
  <p>Egy kis lilahagymával vagy paradicsommal még jobb.</p>
</main>




<!-- a te meglévő tartalmad innen mehet tovább változtatás nélkül -->

</body>
</html>
