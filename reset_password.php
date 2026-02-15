<?php
session_start();
$conn = new mysqli("localhost","root","","zsebszakács");
if ($conn->connect_error) die("DB hiba");

$token = $_GET["token"] ?? "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST["token"];
    $new = $_POST["password"];

    $stmt = $conn->query("
        SELECT * FROM password_resets
        WHERE used = 0 AND expires_at > NOW()
    ");

    while ($row = $stmt->fetch_assoc()) {
        if (password_verify($token, $row["token_hash"])) {

            $hash = password_hash($new, PASSWORD_DEFAULT);

            $conn->query("
                UPDATE users SET password='$hash'
                WHERE userID=".$row["user_id"]
            );

            $conn->query("
                UPDATE password_resets SET used=1
                WHERE id=".$row["id"]
            );

            echo "Jelszó sikeresen frissítve!";
            exit;
        }
    }

    $error = "Érvénytelen vagy lejárt token!";
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Új jelszó</title></head>
<body>
<h2>Új jelszó megadása</h2>

<?php if ($error): ?>
<p style="color:red"><?= $error ?></p>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
    <input type="password" name="password" placeholder="Új jelszó" required>
    <button>Mentés</button>
</form>
</body>
</html>
