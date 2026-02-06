<?php
session_start();
header("Content-Type: text/plain; charset=utf-8");

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo "ERR: no_user_id";
    exit;
}

$conn = new mysqli("localhost","root","","zsebszakács");
if ($conn->connect_error) {
    http_response_code(500);
    echo "ERR: db_connect";
    exit;
}

$user_id = (int)$_SESSION["user_id"];
$rid = (int)($_POST["rid"] ?? 0);
if ($rid <= 0) {
    http_response_code(400);
    echo "ERR: bad_rid";
    exit;
}

$check = $conn->prepare("SELECT id FROM kedvencek WHERE user_id=? AND recept_id=?");
if(!$check){ echo "ERR: ".$conn->error; exit; }
$check->bind_param("ii", $user_id, $rid);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $del = $conn->prepare("DELETE FROM kedvencek WHERE user_id=? AND recept_id=?");
    if(!$del){ echo "ERR: ".$conn->error; exit; }
    $del->bind_param("ii", $user_id, $rid);
    if(!$del->execute()){ echo "ERR: ".$conn->error; exit; }
    echo "del";
} else {
    $add = $conn->prepare("INSERT INTO kedvencek (user_id, recept_id) VALUES (?,?)");
    if(!$add){ echo "ERR: ".$conn->error; exit; }
    $add->bind_param("ii", $user_id, $rid);
    if(!$add->execute()){ echo "ERR: ".$conn->error; exit; }
    echo "add";
}
