<?php
session_start();

$conn = new mysqli("localhost", "root", "", "zsebszakács");
if ($conn->connect_error) die("Hiba!");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $u = trim($_POST["username"]);
    $p = $_POST["password"];

    $stmt = $conn->prepare("SELECT password FROM users WHERE username=?");
    $stmt->bind_param("s", $u);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hash);
        $stmt->fetch();

        if (password_verify($p, $hash)) {
            $_SESSION["username"] = $u;
            header("Location: index.php");
            exit;
        }
    }

    $error = "Hibás felhasználónév vagy jelszó!";
}
?>

<?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>

<form method="post">
    <input name="username" placeholder="Felhasználónév" required>
    <input type="password" name="password" placeholder="Jelszó" required>
    <button>Bejelentkezés</button>
</form>
