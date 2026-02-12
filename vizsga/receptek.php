<?php
session_start();
if (!isset($_SESSION["username"]) || !isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$user_id = (int)$_SESSION["user_id"];

$conn = new mysqli("localhost","root","","zsebszakács");
if ($conn->connect_error) die("Adatbázis hiba");
$conn->set_charset("utf8mb4");

/* ===== PARAMÉTEREK ===== */
$id  = (int)($_GET["id"] ?? 0);
$cat = $_GET["cat"] ?? "reggeli";
$ing = trim($_GET["ing"] ?? "");

/* ===== CAT NORMALIZÁLÁS (ebed -> ebéd) ===== */
$cat = mb_strtolower($cat, "UTF-8");
if ($cat === "ebed") $cat = "ebéd";

/* engedett cat-ek */
$allowed = ["reggeli","ebéd","vacsora","kedvencek"];
if (!in_array($cat, $allowed, true)) $cat = "reggeli";

/* ===== HÁTTÉR MAP ===== */
$bgMap = [
    "reggeli"   => "reggelihatter.jpg",
    "ebéd"      => "ebedhatter.jpg",
    "vacsora"   => "vacsorahatter.jpg",
    "kedvencek" => "hatter.jpg",
];

/* alap */
$bg = $bgMap[$cat] ?? "hatter.jpg";
$pageTitleCat = $cat;

/* ===== ID NÉZET: recept kategória DB-ből (háttér + cím) ===== */
if ($id > 0) {
    $rq = $conn->prepare("SELECT kategoria FROM receptek WHERE id=?");
    $rq->bind_param("i", $id);
    $rq->execute();
    $rrow = $rq->get_result()->fetch_assoc();

    if ($rrow && !empty($rrow["kategoria"])) {
        $k = trim(mb_strtolower($rrow["kategoria"], "UTF-8"));
        if ($k === "ebed") $k = "ebéd";

        $pageTitleCat = $k;
        if (isset($bgMap[$k])) $bg = $bgMap[$k];
    }
}

/* ===== KEDVENCEK BETÖLTÉSE (szív állapothoz) ===== */
$fav = [];
$fq = $conn->prepare("SELECT recept_id FROM kedvencek WHERE user_id=?");
$fq->bind_param("i", $user_id);
$fq->execute();
$fr = $fq->get_result();
while ($row = $fr->fetch_assoc()) {
    $fav[(int)$row["recept_id"]] = true;
}

/* ===== LEKÉRDEZÉS ===== */
$params = [];
$types  = "";
$sql    = "";

if ($id > 0) {
    // 1 recept (id alapján)
    $sql = "SELECT * FROM receptek WHERE id=?";
    $params[] = $id;
    $types .= "i";
} else {
    if ($cat === "kedvencek") {
        // kedvencek lista
        $sql = "
            SELECT r.*
            FROM receptek r
            INNER JOIN kedvencek k ON k.recept_id = r.id
            WHERE k.user_id = ?
        ";
        $params[] = $user_id;
        $types .= "i";
    } else {
        // normál kategória
        $sql = "SELECT * FROM receptek WHERE LOWER(kategoria) = ?";
        $params[] = $cat;
        $types .= "s";
    }
}

/* ===== HOZZÁVALÓ SZŰRŐ ===== */
if ($ing !== "") {
    if (stripos($sql, "WHERE") !== false) $sql .= " AND LOWER(hozzavalok) LIKE ?";
    else $sql .= " WHERE LOWER(hozzavalok) LIKE ?";
    $params[] = "%" . mb_strtolower($ing, "UTF-8") . "%";
    $types .= "s";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
<base href="/vizsga/">
<meta charset="UTF-8">
<title><?= htmlspecialchars(($pageTitleCat==="kedvencek" ? "Kedvencek" : ucfirst($pageTitleCat)." receptek")) ?> – Zsebszakács</title>

<style>
body{
    margin:0;
    font-family: Arial, sans-serif;
    min-height:100vh;
    background: url("/vizsga/<?= htmlspecialchars($bg) ?>") center/cover no-repeat fixed;
}
.overlay{
    min-height:100vh;
    background: rgba(0,0,0,.35);
    padding-top:80px;
}
.top{
    position:fixed;
    top:20px;
    right:20px;
    background:#fff;
    padding:10px 14px;
    border-radius:12px;
}
.container{
    max-width:900px;
    margin:auto;
    background:#fff;
    padding:30px;
    border-radius:20px;
    box-shadow:0 20px 60px rgba(0,0,0,.3);
}
.search-box{ margin:20px 0; }
.search-box input{
    width:100%;
    padding:12px 16px;
    border-radius:14px;
    border:1px solid #ccc;
    font-size:16px;
}

/* recept kártya */
.recept{
    margin-top:20px;
    padding:20px;
    border-radius:16px;
    background:#f9f9f9;
    position:relative;
}
.recept img{
    max-width:100%;
    border-radius:12px;
    margin:12px 0;
}

/* ❤️ szív */
.fav-btn{
    position:absolute;
    top:16px;
    right:16px;
    font-size:26px;
    cursor:pointer;
    user-select:none;
    color:#bbb;
    transition:transform .15s ease, color .15s ease;
}
.fav-btn:hover{ transform:scale(1.15); }
.fav-btn.active{ color:#e74c3c; }

/* dobbanás */
@keyframes favPop {
  0%   { transform: scale(1); }
  35%  { transform: scale(1.35); }
  70%  { transform: scale(0.95); }
  100% { transform: scale(1); }
}
.fav-btn.pop{ animation: favPop .28s ease; }

/* 🗑 törlés */
.del-btn{
    position:absolute;
    top:16px;
    right:56px; /* a szív mellé */
    background:#ffecec;
    color:#b10000;
    padding:6px 10px;
    border-radius:10px;
    font-size:14px;
    text-decoration:none;
    font-weight:700;
}
.del-btn:hover{ opacity:.9; }
</style>
</head>

<body>
<div class="overlay">

<div class="top">
<?= htmlspecialchars($_SESSION["username"]) ?> |
<a href="kijelentkezes.php">Kijelentkezés</a>
</div>

<div class="container">
<a href="index.php">← Vissza</a>

<h1><?= ($pageTitleCat==="kedvencek" ? "Kedvencek" : ucfirst($pageTitleCat)." receptek") ?></h1>

<!-- SZŰRŐ -->
<form method="get" class="search-box">
    <?php if ($id > 0): ?>
        <input type="hidden" name="id" value="<?= (int)$id ?>">
    <?php else: ?>
        <input type="hidden" name="cat" value="<?= htmlspecialchars($cat) ?>">
    <?php endif; ?>

    <input
        type="text"
        name="ing"
        placeholder="Hozzávaló keresése (pl. tojás, sajt, csirke)"
        value="<?= htmlspecialchars($ing) ?>"
    >
</form>

<?php if ($res->num_rows === 0): ?>
    <p>Nincs ilyen recept.</p>
<?php endif; ?>

<?php while ($r = $res->fetch_assoc()): ?>
    <?php $rid = (int)$r["id"]; ?>
    <div class="recept" id="recept-<?= $rid ?>">

        <!-- 🗑 TÖRLÉS CSAK SAJÁT RECEPTRE -->
        <?php if (isset($r["user_id"]) && (int)$r["user_id"] === $user_id): ?>
            <a class="del-btn"
               href="delete_recipe.php?id=<?= $rid ?>&cat=<?= urlencode($cat) ?>&ing=<?= urlencode($ing) ?>"
               onclick="return confirm('Biztos törlöd ezt a receptet?');">
               🗑 Törlés
            </a>
        <?php endif; ?>

        <!-- ❤️ SZÍV -->
        <span
            class="fav-btn <?= isset($fav[$rid]) ? "active" : "" ?>"
            onclick="toggleFav(this, <?= $rid ?>)"
            title="Kedvencekhez"
        >❤</span>

        <h2><?= htmlspecialchars($r['cim'] ?? "") ?></h2>
        <p>⏱ <?= (int)($r['ido'] ?? 0) ?> perc | 🔥 <?= (int)($r['kaloria'] ?? 0) ?> kcal</p>

        <?php if (!empty($r['kep'])): ?>
            <img src="kepek/<?= htmlspecialchars($r['kep']) ?>" alt="<?= htmlspecialchars($r['cim'] ?? "") ?>">
        <?php endif; ?>

        <p><?= nl2br(htmlspecialchars($r['leiras'] ?? "")) ?></p>
    </div>
<?php endwhile; ?>

<script>
function toggleFav(el, rid){
    fetch("toggle_fav.php", {
        method: "POST",
        headers: {"Content-Type":"application/x-www-form-urlencoded"},
        body: "rid=" + encodeURIComponent(rid),
        credentials: "same-origin"
    })
    .then(r => r.text())
    .then(txt => {
        const res = (txt || "").trim();

        if(res === "add"){
            el.classList.add("active");

            // dobbanás
            el.classList.remove("pop");
            void el.offsetWidth;
            el.classList.add("pop");

            // maradjon ott
            location.hash = "recept-" + rid;

        } else if(res === "del"){
            el.classList.remove("active");
            el.classList.remove("pop");
            location.hash = "recept-" + rid;

        } else {
            alert("Hiba: " + res);
        }
    })
    .catch(() => alert("Hálózati hiba"));
}
</script>

</div>
</div>
</body>
</html>
