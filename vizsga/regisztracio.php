<?php
$conn = new mysqli("localhost", "root", "", "zsebszakács");
if ($conn->connect_error) die("Hiba!");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $u = trim($_POST["username"]);
    $e = trim($_POST["email"]);
    $p = $_POST["password"];

    if ($u && $e && $p && filter_var($e, FILTER_VALIDATE_EMAIL)) {
       $check = $conn->prepare("SELECT userID FROM users WHERE username=? OR email=?");
        $check->bind_param("ss", $u, $e);
        $check->execute();
        $check->store_result();

        if ($check->num_rows == 0) {
            $hash = password_hash($p, PASSWORD_DEFAULT);
            $ins = $conn->prepare("INSERT INTO users (username,email,password) VALUES (?,?,?)");
            $ins->bind_param("sss", $u, $e, $hash);
            $ins->execute();
            $ins->execute();
            header("Location: bejelentkezes.php");
            exit;

        } else {
            echo "Már létezik!";
        }
    } else {
        echo "Hibás adatok!";
    }
}
?>

<form method="post">
    <input name="username" placeholder="Felhasználónév" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Jelszó" required>
    <button>Regisztráció</button>
</form>
