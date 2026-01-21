<?php
$conn = new mysqli("localhost", "root", "", "zsebszakács");
if ($conn->connect_error) die("Hiba!");

$username = "Szentesi Bence"; // ide írd be a felhasználó nevét
$username = "Tornyai Gergő";
$plain_password = "jelszo123"; // a régi jelszó
$plain_password = "gergo123";

$hash = password_hash($plain_password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password=? WHERE username=?");
$stmt->bind_param("ss", $hash, $username);

if ($stmt->execute()) {
    echo "Jelszó sikeresen áthash-elve!";
} else {
    echo "Hiba: " . $stmt->error;
}
?>
