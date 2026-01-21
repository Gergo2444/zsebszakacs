<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: bejelentkezes.php");
    exit;
}

$cat = $_GET["cat"] ?? "reggeli";
$allowed = ["reggeli","ebed","vacsora"];
if (!in_array($cat, $allowed, true)) $cat = "reggeli";

$recipes = [
  [
    "title" => "Banános zabkása",
    "cat" => "reggeli",
    "ingredients" => [
      "60 g zabpehely",
      "250 ml tej (vagy víz)",
      "1 banán",
      "1 tk méz (opcionális)",
      "csipet fahéj"
    ],
    "steps" => [
      "A tejet melegítsd fel, add hozzá a zabpelyhet.",
      "Főzd 3-5 percig, amíg sűrű lesz.",
      "A banánt karikázd rá, szórd meg fahéjjal.",
      "Ha kell, egy kis mézzel édesítsd."
    ],
    "tip" => "Mehet rá kakaópor, mogyoróvaj vagy pár szem dió is."
  ],
  [
    "title" => "Csirkés rizs",
    "cat" => "ebed",
    "ingredients" => [
      "200 g csirkemell",
      "150 g rizs",
      "1 ek olaj",
      "só, bors, pirospaprika",
      "1 gerezd fokhagyma (opcionális)"
    ],
    "steps" => [
      "A rizst főzd meg sós vízben.",
      "A csirkét kockázd fel, fűszerezd.",
      "Serpenyőben olajon pirítsd meg 8-10 perc alatt.",
      "Keverd össze a rizzsel, kész is."
    ],
    "tip" => "Dobhatsz fel egy kis kukoricával vagy savanyú ubival."
  ],
  // IDE jönnek a vacsorák is: "cat" => "vacsora"
];

$filtered = array_values(array_filter($recipes, fn($r) => $r["cat"] === $cat));

function catTitle($c){
  if ($c === "reggeli") return "Reggeli receptek";
  if ($c === "ebed") return "Ebéd receptek";
  return "Vacsora receptek";
}
?>
<!doctype html>
<html lang="hu">
<head>
  <meta charset="utf-8">
  <title><?php echo catTitle($cat); ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
  <a class="btn" href="index.php">← Vissza</a>
  <h1><?php echo catTitle($cat); ?></h1>

  <?php if (count($filtered) === 0): ?>
    <p>Nincs recept ebben a kategóriában.</p>
  <?php endif; ?>

  <?php foreach ($filtered as $r): ?>
    <div class="card" style="margin:15px 0;">
      <h2><?php echo htmlspecialchars($r["title"]); ?></h2>

      <h3>Hozzávalók</h3>
      <ul>
        <?php foreach ($r["ingredients"] as $i): ?>
          <li><?php echo htmlspecialchars($i); ?></li>
        <?php endforeach; ?>
      </ul>

      <h3>Elkészítés</h3>
      <ol>
        <?php foreach ($r["steps"] as $s): ?>
          <li><?php echo htmlspecialchars($s); ?></li>
        <?php endforeach; ?>
      </ol>

      <h3>Tipp</h3>
      <p><?php echo htmlspecialchars($r["tip"]); ?></p>
    </div>
  <?php endforeach; ?>

</div>

</body>
</html>
