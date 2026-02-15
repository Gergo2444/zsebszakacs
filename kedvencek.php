<?php
session_start();

if (!isset($_SESSION["user_id"])) {
  header("Location: index.php");
  exit;
}

$bg = "kedvencekhatter.jpg";

$conn = new mysqli("localhost", "root", "", "zsebszakács");
if ($conn->connect_error) die("Kapcsolódási hiba");
$conn->set_charset("utf8mb4");

$user_id = (int)$_SESSION["user_id"];

$stmt = $conn->prepare("
  SELECT r.id, r.cim, r.kep
  FROM kedvencek k
  JOIN receptek r ON r.id = k.recept_id
  WHERE k.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

$favs = [];
while ($row = $res->fetch_assoc()) $favs[] = $row;
?>
<!DOCTYPE html>
<html lang="hu">
<head>
<base href="/vizsga/">
<meta charset="UTF-8">
<title>Kedvencek</title>
<style>
body{
  margin:0;
  font-family:Arial, sans-serif;
  min-height:100vh;
  background:url("/vizsga/<?php echo $bg; ?>") center/cover no-repeat fixed;
}
.topbar{ position:fixed; top:20px; left:20px; }
.topbar a{
  background:#fff; padding:8px 14px; border-radius:12px;
  text-decoration:none; color:#000; font-weight:700;
}
.userbox{
  position:fixed; top:20px; right:20px;
  background:#fff; padding:10px 14px; border-radius:12px; font-weight:700;
}
.container{
  max-width:900px;
  margin:140px auto 40px;
  background:rgba(255,255,255,.95);
  border-radius:20px;
  padding:30px;
  box-shadow:0 20px 60px rgba(0,0,0,.25);
}
.grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(220px,1fr));
  gap:14px;
  margin-top:16px;
}
.card{
  display:block;
  background:#fff;
  border-radius:14px;
  padding:14px;
  text-decoration:none;
  color:#000;
  box-shadow:0 10px 25px rgba(0,0,0,.12);
}
.card img{
  width:100%;
  height:140px;
  object-fit:cover;
  border-radius:12px;
  display:block;
  margin-bottom:10px;
}
.card h3{ margin:0; font-size:18px; text-align:center; }
.empty{ font-size:18px; color:#555; }
/* ====== RESPONSIVE FIX: KEDVENCEK ====== */
@media (max-width: 600px){
  .overlay{ padding-top: 86px; }
  .box{
    width: calc(100% - 24px);
    padding: 16px;
    border-radius: 18px;
  }
  .card{
    gap: 12px;
    padding: 10px;
    border-radius: 14px;
  }
  .card img{
    width: 76px;
    height: 60px;
    border-radius: 12px;
  }
}

</style>
</head>
<body>

<div class="topbar"><a href="index.php">← Vissza</a></div>
<div class="userbox"><?php echo htmlspecialchars($_SESSION["username"] ?? ""); ?></div>

<div class="container">
  <h1>Kedvenc receptek</h1>

  <?php if (empty($favs)): ?>
    <p class="empty">Még nincs kedvenc recepted ❤️</p>
  <?php else: ?>
    <div class="grid">
      <?php foreach ($favs as $f): ?>
        <a class="card" href="receptek.php?id=<?php echo (int)$f["id"]; ?>">
          <?php
            $kep = trim($f["kep"] ?? "");
            if ($kep !== "" && file_exists(__DIR__ . "/kepek/" . $kep)) {
              echo '<img src="kepek/' . rawurlencode($kep) . '" alt="">';
            }
          ?>
          <h3><?php echo htmlspecialchars($f["cim"] ?? ""); ?></h3>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

</body>
</html>