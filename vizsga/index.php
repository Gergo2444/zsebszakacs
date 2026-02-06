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
    $u = trim($_POST["username"]);
    $p = $_POST["password"];

    $stmt = $conn->prepare("SELECT userID,password FROM users WHERE username=?");
    $stmt->bind_param("s",$u);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id,$hash);
        $stmt->fetch();

        if (password_verify($p,$hash)) {
            $_SESSION["user_id"] = $id;
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
    $u = trim($_POST["username"]);
    $e = trim($_POST["email"]);
    $p = $_POST["password"];

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
<meta charset="UTF-8">
<title>Zsebszakács</title>

<style>
body{
    margin:0;
    font-family:Arial, sans-serif;
    min-height:100vh;
    background:url("/vizsga/hatter.jpg") center/cover no-repeat;
}

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

.userbox{
    position:fixed;
    top:20px;
    right:20px;
    background:#fff;
    padding:10px;
    border-radius:12px;
    z-index:10;
}

.book-menu{
    position:absolute;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%);
    width:900px;
    display:flex;
    justify-content:space-between;
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

.modal{
    position:fixed;
    inset:0;
    display:flex;
    align-items:center;
    justify-content:center;
}

.auth-box{
    width:360px;
    background:#fff;
    padding:28px;
    border-radius:18px;
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
}

.switch{
    text-align:center;
    margin-top:12px;
}

.error{
    background:#ffecec;
    padding:10px;
    border-radius:10px;
    margin-bottom:10px;
    color:#b10000;
}
</style>
</head>
<body>

<?php if (isset($_SESSION["username"])): ?>

<div class="userbox">
<b><?= htmlspecialchars($_SESSION["username"]) ?></b><br>
<a href="kijelentkezes.php">Kijelentkezés</a>
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

<?php if ($error) echo "<div class='error'>$error</div>"; ?>

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

<!-- 🔑 ELFELEJTETT JELSZÓ LINK (EZ A + KÓD) -->
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
