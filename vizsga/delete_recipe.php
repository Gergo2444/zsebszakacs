<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$conn = new mysqli("localhost","root","","zsebszakács");
if ($conn->connect_error) die("DB hiba");

$user_id = (int)$_SESSION["user_id"];
$id = (int)($_GET["id"] ?? 0);
$cat = $_GET["cat"] ?? "reggeli";
$ing = $_GET["ing"] ?? "";

if ($id <= 0) {
    header("Location: receptek.php?cat=".urlencode($cat)."&ing=".urlencode($ing));
    exit;
}

/* lekérjük a receptet, de csak ha a user-é */
$stmt = $conn->prepare("SELECT kep FROM receptek WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 1) {
    $row = $res->fetch_assoc();
    $kep = $row["kep"];

    // kedvencekből is töröljük a hivatkozást (ha van)
    $d1 = $conn->prepare("DELETE FROM kedvencek WHERE recept_id=?");
    $d1->bind_param("i", $id);
    $d1->execute();

    // recept törlése
    $del = $conn->prepare("DELETE FROM receptek WHERE id=? AND user_id=?");
    $del->bind_param("ii", $id, $user_id);
    $del->execute();

    // kép fájl törlése (ha volt)
    if (!empty($kep)) {
        $file = basename($kep);
        $path = __DIR__ . "/kepek/" . $file;
        if (is_file($path)) @unlink($path);
    }
}

header("Location: receptek.php?cat=".urlencode($cat)."&ing=".urlencode($ing));
exit;
