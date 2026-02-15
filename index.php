<?php
session_start();

/* ====== ADATBÁZIS ====== */
$conn = new mysqli("localhost", "root", "", "zsebszakács");
if ($conn->connect_error) die("Kapcsolódási hiba");

/* ====== TAB ====== */
$tab = $_GET["tab"] ?? "login";
$error = "";

/* ====== LOGIN ====== */
if (isset($_POST["do"]) && $_POST["do"] === "login") {
    $u = trim($_POST["username"] ?? "");
    $p = $_POST["password"] ?? "";

    $stmt = $conn->prepare("SELECT userID,password FROM users WHERE username=?");
    $stmt->bind_param("s",$u);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id,$hash);
        $stmt->fetch();

        if (password_verify($p,$hash)) {
            $_SESSION["user_id"] = (int)$id;
            $_SESSION["username"] = $u;
            header("Location: index.php");
            exit;
        }
    }
    $error = "Hibás felhasználónév vagy jelszó!";
    $tab = "login";
}

/* ====== REG ====== */
if (isset($_POST["do"]) && $_POST["do"] === "reg") {
    $u = trim($_POST["username"] ?? "");
    $e = trim($_POST["email"] ?? "");
    $p = $_POST["password"] ?? "";

    if ($u && $e && $p) {
        $hash = password_hash($p,PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username,email,password) VALUES (?,?,?)");
        $stmt->bind_param("sss",$u,$e,$hash);

        if ($stmt->execute()) {
            header("Location: index.php?tab=login");
            exit;
        }
    }
    $error = "Regisztrációs hiba!";
    $tab = "reg";
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
<base href="/vizsga/">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Zsebszakács</title>

<style>
*{ box-sizing:border-box; }

body{
    margin:0;
    font-family:Arial, sans-serif;
    min-height:100vh;
    background:url("/vizsga/hatter.jpg") center/cover no-repeat;
}

/* ====== TOP GOMBOK (NINCS BELÉPVE) ====== */
.top-right{
    position:fixed;
    top:20px;
    right:20px;
    display:flex;
    gap:10px;
    z-index:10;
}
.top-right a{
    background:#fff;
    color:#000;
    padding:8px 14px;
    border-radius:10px;
    text-decoration:none;
    font-weight:700;
}

/* ====== USERBOX (BEJELENTKEZVE) ====== */
.userbox{
    position:fixed;
    top:20px;
    right:20px;
    background:#fff;
    padding:10px 16px;
    border-radius:14px;
    box-shadow:0 10px 25px rgba(0,0,0,.25);
    display:flex;
    align-items:center;
    gap:12px;
    z-index:10;
}

.userbox .username{
    font-weight:800;
    color:#111;
    white-space:nowrap;
}
.userbox .add-btn{
    background:#4CAF50;
    color:#fff;
    padding:6px 12px;
    border-radius:10px;
    text-decoration:none;
    font-weight:700;
    font-size:14px;
    white-space:nowrap;
}
.userbox .logout-btn{
    background:#ff4d4d;
    color:#fff;
    padding:6px 12px;
    border-radius:10px;
    text-decoration:none;
    font-weight:700;
    font-size:14px;
    white-space:nowrap;
}
.userbox a:hover{ opacity:.9; }

/* ====== MENÜ (PC) ====== */
.book-menu{
  position: fixed;
  left: 12px;
  right: 12px;
  bottom: 12px;
  transform: none;

  width: auto;
  max-width: none;
  padding: 12px;

  background: rgba(255,255,255,.85);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border: 1px solid rgba(255,255,255,.55);

  border-radius: 18px;
  box-shadow: 0 18px 55px rgba(0,0,0,.22);
  z-index: 50;

  display: flex;
  flex-direction: column;
  gap: 10px;
}

.book-col{
    width:45%;
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:120px;
}
.menu-card{
    width:220px;
    background:#fff;
    padding:22px 0;
    border-radius:18px;
    text-align:center;
    font-size:20px;
    font-weight:700;
    text-decoration:none;
    color:#000;
    box-shadow:0 16px 40px rgba(0,0,0,.25);
}
.menu-card:hover{ opacity:.95; }

/* ====== AUTH ====== */
.modal{
    position:fixed;
    inset:0;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:16px;
}
.auth-box{
    width:360px;
    background:#fff;
    padding:28px;
    border-radius:18px;
    box-shadow:0 18px 50px rgba(0,0,0,.28);
}
.form input{
    width:100%;
    padding:12px;
    margin-bottom:12px;
    border-radius:12px;
    border:1px solid #ddd;
}
.btn{
    width:100%;
    padding:12px;
    border:none;
    border-radius:12px;
    background:#4f6ef7;
    color:#fff;
    font-weight:700;
    cursor:pointer;
}
.switch{ text-align:center; margin-top:12px; }
.error{
    background:#ffecec;
    padding:10px;
    border-radius:10px;
    margin-bottom:10px;
    color:#b10000;
}

/* ===== MOBIL: menü LENT + userbox fent ===== */
@media (max-width: 980px){

  /* userbox fent, 2 gomb + név */
  .userbox{
    top: 12px; left: 12px; right: 12px;
    width:auto;
    display:flex;
    flex-wrap:wrap;
    gap:8px;
    padding:10px 12px;
    border-radius:16px;
    z-index:999;
  }
  .userbox .add-btn,
  .userbox .logout-btn{
    flex:1 1 calc(50% - 8px);
    text-align:center;
    padding:10px 10px;
    font-size:14px;
    border-radius:14px;
  }
  .userbox .username{
    flex:1 1 100%;
    order:3;
    text-align:center;
    margin-top:2px;
    font-size:16px;
    line-height:1.2;
    white-space:normal;
  }

  /* a menü legyen LENT, fix panelként */
  .book-menu{
    position: fixed;
    left: 12px;
    right: 12px;
    bottom: 340px;
    top: auto;
    transform: none;

    width: auto;
    max-width: none;
    padding: 12px;

    background: rgba(255,255,255,.95);
    border-radius: 18px;
    box-shadow: 0 18px 55px rgba(0,0,0,.28);
    z-index: 50;

    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .book-col{
    width: 100%;
    gap: 10px;
  }

  .menu-card{
    width: 100%;
    padding: 14px 0;
    font-size: 17px;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0,0,0,.18);
  }

  /* hogy ne takarja ki a menü az oldalt (ha scroll lenne) */
  body{ padding-bottom: 260px; }

  /* auth doboz mobil */
  .auth-box{
    width: calc(100% - 24px);
    max-width: 420px;
    padding: 22px;
    border-radius: 18px;
  }
}

/* kis mobil: picit kisebb padding */
@media (max-width: 420px){
  body{ padding-bottom: 280px; }
  .book-menu{ padding: 10px; }
  .menu-card{ font-size: 16px; padding: 13px 0; }
}
</style>
</head>
<body>

<?php if (isset($_SESSION["username"])): ?>

<div class="userbox">
    <a href="add_recipe.php" class="add-btn">+ Új recept</a>
    <span class="username"><?= htmlspecialchars($_SESSION["username"]) ?></span>
    <a href="kijelentkezes.php" class="logout-btn">Kijelentkezés</a>
</div>

<div class="book-menu">
    <div class="book-col">
        <a href="receptek.php?cat=reggeli" class="menu-card">Reggeli</a>
        <a href="receptek.php?cat=ebed" class="menu-card">Ebéd</a>
    </div>
    <div class="book-col">
        <a href="receptek.php?cat=vacsora" class="menu-card">Vacsora</a>
        <a href="kedvencek.php" class="menu-card">Kedvencek</a>
    </div>
</div>

<?php else: ?>

<div class="top-right">
    <a href="index.php?tab=login">Bejelentkezés</a>
    <a href="index.php?tab=reg">Regisztráció</a>
</div>

<div class="modal">
<div class="auth-box">

<?php if ($error) echo "<div class='error'>".htmlspecialchars($error)."</div>"; ?>

<?php if ($tab==="reg"): ?>

<h2>Regisztráció</h2>
<form method="post" class="form">
    <input type="hidden" name="do" value="reg">
    <input name="username" placeholder="Felhasználónév" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Jelszó" required>
    <button class="btn">Regisztráció</button>
</form>
<div class="switch">Van már fiókod? <a href="index.php?tab=login">Bejelentkezés</a></div>

<?php else: ?>

<h2>Bejelentkezés</h2>
<form method="post" class="form">
    <input type="hidden" name="do" value="login">
    <input name="username" placeholder="Felhasználónév" required>
    <input type="password" name="password" placeholder="Jelszó" required>
    <button class="btn">Bejelentkezés</button>
</form>

<div style="text-align:center; margin-top:10px;">
    <a href="forgot_password.php" style="font-size:14px; color:#4f6ef7;">
        Elfelejtetted a jelszavad?
    </a>
</div>

<div class="switch">Nincs fiókod? <a href="index.php?tab=reg">Regisztráció</a></div>

<?php endif; ?>

</div>
</div>

<?php endif; ?>

</body>
</html>
