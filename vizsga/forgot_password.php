<?php
session_start();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
<meta charset="UTF-8">
<title>Jelszó visszaállítás</title>

<style>
body{
    margin:0;
    font-family: Arial, sans-serif;
    min-height:100vh;
    background:url("/vizsga/hatter.jpg") center/cover no-repeat;
    display:flex;
    align-items:center;
    justify-content:center;
}

.card{
    width:360px;
    background:#fff;
    padding:28px;
    border-radius:18px;
    box-shadow:0 20px 60px rgba(0,0,0,.35);
}

.card h1{
    margin-top:0;
    text-align:center;
}

.card p{
    font-size:14px;
    color:#555;
    text-align:center;
    margin-bottom:20px;
}

.form input{
    width:100%;
    padding:12px;
    margin-bottom:14px;
    border-radius:12px;
    border:1px solid #ddd;
    font-size:14px;
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

.btn:hover{
    opacity:.9;
}

.back{
    text-align:center;
    margin-top:14px;
}

.back a{
    text-decoration:none;
    font-size:14px;
    color:#4f6ef7;
}
</style>
</head>

<body>

<div class="card">
    <h1>Elfelejtett jelszó</h1>
    <p>Add meg az email címed, és küldünk egy jelszó-visszaállító linket.</p>

    <form method="post" class="form">
        <input type="email" name="email" placeholder="Email címed" required>
        <button class="btn">Küldés</button>
    </form>

    <div class="back">
        <a href="index.php">← Vissza a bejelentkezéshez</a>
    </div>
</div>

</body>
</html>
