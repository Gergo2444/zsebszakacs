<?php
session_start();
if (!isset($_SESSION["username"]) || !isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$user_id = (int)$_SESSION["user_id"];

/* ===== KATEGÓRIA ===== */
$cat = $_GET["cat"] ?? "reggeli";
$allowed = ["reggeli","ebed","vacsora","kedvencek"];
if (!in_array($cat, $allowed, true)) {
    $cat = "reggeli";
}

/* ===== KERESŐ (HOZZÁVALÓ) ===== */
$ing = trim($_GET["ing"] ?? "");

/* ===== ADATBÁZIS ===== */
$conn = new mysqli("localhost","root","","zsebszakács");
if ($conn->connect_error) die("Adatbázis hiba");

/* ===== HÁTTÉR KÉP ===== */
$bgMap = [
    "reggeli"   => "reggelihatter.jpg",
    "ebed"      => "ebedhatter.jpg",
    "vacsora"   => "vacsorahatter.jpg",
    "kedvencek" => "hatter.jpg"
];
$bg = $bgMap[$cat] ?? "hatter.jpg";

/* ===== KEDVENCEK BETÖLTÉSE (hogy elsőre is piros legyen) ===== */
$fav = [];
$fq = $conn->prepare("SELECT recept_id FROM kedvencek WHERE user_id=?");
$fq->bind_param("i", $user_id);
$fq->execute();
$fr = $fq->get_result();
while ($row = $fr->fetch_assoc()) {
    $fav[(int)$row["recept_id"]] = true; // gyors lookup
}

/* ===== LEKÉRDEZÉS ===== */
$params = [];
$types  = "";

if ($cat === "kedvencek") {
    // Kedvencek: csak a user kedvenceit listázzuk
    $sql = "
        SELECT r.*
        FROM receptek r
        INNER JOIN kedvencek k ON k.recept_id = r.id
        WHERE k.user_id = ?
    ";
    $params[] = $user_id;
    $types .= "i";
} else {
    // Normál kategória: receptek táblából kategória szerint
    $sql = "SELECT * FROM receptek WHERE LOWER(kategoria) = ?";
    $params[] = $cat;
    $types .= "s";
}

// Hozzávaló szűrő mindkettőhöz
if ($ing !== "") {
    $sql .= " AND LOWER(hozzavalok) LIKE ?";
    $params[] = "%".mb_strtolower($ing)."%";
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
<meta charset="UTF-8">
<title><?= ucfirst($cat) ?> – Zsebszakács</title>

<style>
body{
    margin:0;
    font-family: Arial, sans-serif;
    min-height:100vh;
    background: url("<?= htmlspecialchars($bg) ?>") center/cover no-repeat;
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

/* ===== KERESŐ ===== */
.search-box{
    margin:20px 0;
}
.search-box input{
    width:100%;
    padding:12px 16px;
    border-radius:14px;
    border:1px solid #ccc;
    font-size:16px;
}

/* ===== RECEPT ===== */
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

/* ❤️ SZÍV GOMB */
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
.fav-btn:hover{
    transform:scale(1.15);
}
.fav-btn.active{
    color:#e74c3c;
}
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

<h1><?= ($cat==="kedvencek" ? "Kedvencek" : ucfirst($cat)." receptek") ?></h1>

<form method="get" class="search-box">
    <input type="hidden" name="cat" value="<?= htmlspecialchars($cat) ?>">
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
    <div class="recept">

        <!-- ❤️ SZÍV (DB alapján elsőre is piros, ha kedvenc) -->
        <span
            class="fav-btn <?= isset($fav[$rid]) ? "active" : "" ?>"
            onclick="toggleFav(this, <?= $rid ?>)"
            title="Kedvencekhez"
        >❤</span>

        <h2><?= htmlspecialchars($r['cim']) ?></h2>
        <p>⏱ <?= (int)$r['ido'] ?> perc | 🔥 <?= (int)$r['kaloria'] ?> kcal</p>

        <?php if (!empty($r['kep'])): ?>
            <img src="<?= htmlspecialchars($r['kep']) ?>" alt="<?= htmlspecialchars($r['cim']) ?>">
        <?php endif; ?>

        <p><?= nl2br(htmlspecialchars($r['leiras'])) ?></p>
    </div>
<?php endwhile; ?>

<script>
function toggleFav(el, rid){
    fetch("toggle_fav.php", {
        method: "POST",
        headers: {"Content-Type":"application/x-www-form-urlencoded"},
        body: "rid=" + encodeURIComponent(rid)
    })
    .then(r => r.text())
    .then(txt => {
        const res = (txt || "").trim();
        if(res === "add") el.classList.add("active");
        else if(res === "del") el.classList.remove("active");
        else alert("Hiba: " + res);
    })
    .catch(() => alert("Hálózati hiba"));
}
</script>

</div>
</div>
</body>
</html>

