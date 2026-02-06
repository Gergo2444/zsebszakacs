<?php
session_start();

/* ===== VÉDELEM ===== */
if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit;
}

/* ===== HÁTTÉR ===== */
$bg = "kedvencekhatter.jpg";

/* ===== KEDVENCEK (SESSION ALAPÚ, EGYSZERŰ) ===== */
$favs = $_SESSION["favorites"] ?? [];
?>
<!DOCTYPE html>
<html lang="hu">
<head>
<meta charset="UTF-8">
<title>Kedvencek – Zsebszakács</title>

<style>
/* ===== HÁTTÉR ===== */
body{
    margin:0;
    font-family: Arial, sans-serif;
    min-height:100vh;
    background: url("/vizsga/<?php echo $bg; ?>") center/cover no-repeat;
}

/* ===== FEJLÉC ===== */
.topbar{
    position:fixed;
    top:20px;
    left:20px;
}
.topbar a{
    background:#fff;
    padding:8px 14px;
    border-radius:12px;
    text-decoration:none;
    color:#000;
    font-weight:700;
}

/* ===== USER ===== */
.userbox{
    position:fixed;
    top:20px;
    right:20px;
    background:#fff;
    padding:10px 14px;
    border-radius:12px;
    font-weight:700;
}

/* ===== TARTALOM ===== */
.container{
    max-width:900px;
    margin:140px auto;
    background:rgba(255,255,255,.95);
    border-radius:20px;
    padding:30px;
    box-shadow:0 20px 60px rgba(0,0,0,.25);
}

h1{
    margin-top:0;
}

/* ===== KÁRTYÁK ===== */
.card{
    background:#f7f7f7;
    padding:18px;
    border-radius:14px;
    margin-bottom:12px;
    font-size:18px;
}

.empty{
    font-size:18px;
    color:#555;
}
</style>
</head>
<body>

<!-- VISSZA -->
<div class="topbar">
    <a href="index.php">← Vissza</a>
</div>

<!-- USER -->
<div class="userbox">
    <?php echo htmlspecialchars($_SESSION["username"]); ?>
</div>

<!-- TARTALOM -->
<div class="container">
    <h1>Kedvenc receptek</h1>

    <?php if (empty($favs)): ?>
        <p class="empty">Még nincs kedvenc recepted ❤️</p>
    <?php else: ?>
        <?php foreach ($favs as $fav): ?>
            <div class="card">
                <?php echo htmlspecialchars($fav); ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
