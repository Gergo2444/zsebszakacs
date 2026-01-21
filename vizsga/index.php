<?php
session_start();

$conn = new mysqli("localhost", "root", "", "zsebszakács"); // ha kell: zsebszakacs
if ($conn->connect_error) die("Kapcsolódási hiba: " . $conn->connect_error);

$tab = $_GET["tab"] ?? "login"; // login / reg
$error = "";

// Ha nincs belépve
if (!isset($_SESSION["username"])) {

  // LOGIN
  if (isset($_POST["do"]) && $_POST["do"] === "login") {
    $u = trim($_POST["username"] ?? "");
    $p = $_POST["password"] ?? "";

    $stmt = $conn->prepare("SELECT userID, password FROM users WHERE username=?");
    $stmt->bind_param("s", $u);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
      $stmt->bind_result($id, $hash);
      $stmt->fetch();

      if (password_verify($p, $hash)) {
        $_SESSION["user_id"] = $id;
        $_SESSION["username"] = $u;
        header("Location: index.php");
        exit;
      }
    }

    $error = "Hibás felhasználónév vagy jelszó!";
    $tab = "login";
  }

  // REG
  if (isset($_POST["do"]) && $_POST["do"] === "reg") {
    $u = trim($_POST["username"] ?? "");
    $e = trim($_POST["email"] ?? "");
    $p = $_POST["password"] ?? "";

    if ($u && $e && $p && filter_var($e, FILTER_VALIDATE_EMAIL)) {

      $check = $conn->prepare("SELECT userID FROM users WHERE username=? OR email=?");
      $check->bind_param("ss", $u, $e);
      $check->execute();
      $check->store_result();

      if ($check->num_rows == 0) {
        $hash = password_hash($p, PASSWORD_DEFAULT);

        $ins = $conn->prepare("INSERT INTO users (username,email,password) VALUES (?,?,?)");
        $ins->bind_param("sss", $u, $e, $hash);

        if ($ins->execute()) {
          header("Location: index.php?tab=login");
          exit;
        } else {
          $error = "Regisztrációs hiba: " . $ins->error;
        }
      } else {
        $error = "Már létezik ilyen felhasználó vagy email!";
      }

    } else {
      $error = "Hibás adatok!";
    }

    $tab = "reg";
  }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Zsebszakács</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

   


<?php if (isset($_SESSION["username"])): ?>

  

    <div class="userbox">
      <p>Bejelentkezve mint:<br><strong><?php echo htmlspecialchars($_SESSION["username"]); ?></strong></p>
      <a class="btn" href="kijelentkezes.php">Kijelentkezés</a>
    </div>
  </header>

  <main class="container">
    <div class="grid">
      <a href="receptek.php" class="card">Reggeli</a>
      <a href="receptek.php" class="card">Ebéd</a>
      <a href="receptek.php" class="card">Vacsora</a>
    </div>
  </main>

<?php else: ?>

  
    </div>
  </header>

  <div class="auth-box auth-float">
    <?php if ($error) echo "<p class='error'>".$error."</p>"; ?>

    <?php if ($tab === "reg"): ?>
      <form method="post" action="index.php?tab=reg" class="form">
        <input type="hidden" name="do" value="reg">
        <input name="username" placeholder="Felhasználónév" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Jelszó" required>
        <button type="submit" class="btn">Regisztráció</button>
      </form>
    <?php else: ?>
      <form method="post" action="index.php?tab=login" class="form">
        <input type="hidden" name="do" value="login">
        <input name="username" placeholder="Felhasználónév" required>
        <input type="password" name="password" placeholder="Jelszó" required>
        <button type="submit" class="btn">Bejelentkezés</button>
<a href="index.php?tab=reg" class="btn">Regisztráció</a>

      </form>
    <?php endif; ?>
  </div>

<?php endif; ?>

</body>
</html>

