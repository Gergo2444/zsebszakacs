<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';


$message = "";

if(isset($_POST["email"])){

    $conn = new mysqli("localhost","root","","zsebszakács");
    if ($conn->connect_error) die("DB hiba");

    $email = trim($_POST["email"]);

    // Ellenőrizzük létezik-e az email
    $stmt = $conn->prepare("SELECT userID FROM users WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows == 1){

        $token = bin2hex(random_bytes(32));

        $stmt2 = $conn->prepare("UPDATE users SET reset_token=? WHERE email=?");
        $stmt2->bind_param("ss",$token,$email);
        $stmt2->execute();

        $mail = new PHPMailer(true);

       try{
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    // 🔥 IDE A SAJÁT GMAIL CÍMED
    $mail->Username = 'gezabela76@gmail.com';

    // 🔥 IDE A 16 KARAKTERES APP JELSZÓ (SZÓKÖZ NÉLKÜL)
    $mail->Password = 'twlwxnhdqzixggzn';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';

    $mail->setFrom('gezabela76@gmail.com', 'Zsebszakács');
    $mail->addAddress($email);

    
    $resetLink = "http://localhost/vizsga/reset_password.php?token=" . $token;

$mail->isHTML(false); // Sima szöveges email

$mail->Subject = "Jelszó visszaállítás - Zsebszakács";

$mail->Body = 
"Szia!

Jelszó-visszaállítási kérelmet kaptunk a Zsebszakács fiókodhoz.

Az új jelszó beállításához kattints az alábbi linkre:

$resetLink

A link biztonsági okokból 30 percig érvényes.

Ha nem te kérted a jelszó módosítást, hagyd figyelmen kívül ezt az üzenetet.

--
Zsebszakács rendszer
";

$mail->AltBody = $mail->Body;

    $mail->send();

    $message = "<p style='color:green;text-align:center;'>Email elkuldve!</p>";

} catch (Exception $e){
    $message = "<p style='color:red;text-align:center;'>Email hiba: ".$mail->ErrorInfo."</p>";
}

    } else {
        $message = "<p style='color:red;text-align:center;'>Nincs ilyen email!</p>";
    }
}
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

input{
    width:100%;
    padding:12px;
    margin-bottom:14px;
    border-radius:12px;
    border:1px solid #ddd;
}

button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:12px;
    background:#4f6ef7;
    color:#fff;
    font-weight:700;
}
</style>

</style>
</head>

<body>

<div class="card">
    <h1>Elfelejtett jelszó</h1>
    <p>Add meg az email címed, és küldünk egy jelszó-visszaállító linket.</p>

    <?php echo $message; ?>

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
