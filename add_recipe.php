<?php
session_start();

if(!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$conn = new mysqli("localhost","root","","zsebszakács");
if($conn->connect_error) die("DB hiba");

$message = "";

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $cim = trim($_POST["cim"] ?? "");
    $kategoria = $_POST["kategoria"] ?? "";
    $ido = (int)($_POST["ido"] ?? 0);
    $kaloria = (int)($_POST["kaloria"] ?? 0);
    $hozzavalok = trim($_POST["hozzavalok"] ?? "");
    $leiras = trim($_POST["leiras"] ?? "");
    $user_id = (int)$_SESSION["user_id"];

    /* ===== KÉP FELTÖLTÉS ===== */
    $kepNev = null;

    if(isset($_FILES["kep"]) && $_FILES["kep"]["error"] !== UPLOAD_ERR_NO_FILE){

        if($_FILES["kep"]["error"] === 0){

            // max 3MB
            if($_FILES["kep"]["size"] > 3 * 1024 * 1024){
                $message = "<p style='color:red;'>A kép túl nagy (max 3MB)!</p>";
            } else {

                $allowedExt = ["jpg","jpeg","png","webp","jfif"];
                $fileName = $_FILES["kep"]["name"];
                $fileTmp  = $_FILES["kep"]["tmp_name"];
                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                // MIME ellenőrzés (biztonság)
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime  = finfo_file($finfo, $fileTmp);
                finfo_close($finfo);

                $allowedMime = ["image/jpeg","image/png","image/webp"];

                if(!in_array($ext, $allowedExt, true) || !in_array($mime, $allowedMime, true)){
                    $message = "<p style='color:red;'>Csak JPG/PNG/WEBP képet tölthetsz fel!</p>";
                } else {

                    if(!is_dir(__DIR__."/kepek")){
                        mkdir(__DIR__."/kepek", 0777, true);
                    }

                    $ujNev = bin2hex(random_bytes(8)) . "." . $ext;

                    if(move_uploaded_file($fileTmp, __DIR__."/kepek/".$ujNev)){
                        $kepNev = $ujNev; // DB-be csak a fájlnév
                    } else {
                        $message = "<p style='color:red;'>Nem sikerült feltölteni a képet!</p>";
                    }
                }
            }

        } else {
            $message = "<p style='color:red;'>Fájlfeltöltési hiba!</p>";
        }
    }

    if($message === "" && $cim && $kategoria && $leiras){

        $stmt = $conn->prepare("
            INSERT INTO receptek 
            (cim, kategoria, ido, kaloria, hozzavalok, leiras, kep, user_id) 
            VALUES (?,?,?,?,?,?,?,?)
        ");

        $stmt->bind_param("ssiisssi",
            $cim,
            $kategoria,
            $ido,
            $kaloria,
            $hozzavalok,
            $leiras,
            $kepNev,
            $user_id
        );

        $stmt->execute();

        $message = "<p style='color:green;'>Recept sikeresen hozzáadva!</p>";
    } elseif($message === "") {
        $message = "<p style='color:red;'>Tölts ki minden kötelező mezőt!</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
<base href="/vizsga/">
<meta charset="UTF-8">
<title>Új recept</title>
<style>
body{
    margin:0;
    font-family:Arial;
    background:url("/vizsga/hatter.jpg") center/cover no-repeat;
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
}

.box{
    background:#fff;
    padding:30px;
    border-radius:18px;
    width:450px;
    box-shadow:0 20px 60px rgba(0,0,0,.35);
}

input, textarea, select{
    width:100%;
    padding:12px;
    margin-bottom:12px;
    border-radius:12px;
    border:1px solid #ddd;
    font-size:14px;
}

button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:12px;
    background:#4f6ef7;
    color:#fff;
    font-weight:700;
    cursor:pointer;
}

button:hover{ opacity:.92; }

.back{
    text-align:center;
    margin-top:10px;
}

/* ===== SZÉP FILE INPUT (JS NÉLKÜL) ===== */
.file{
    margin-bottom:14px;
}

.file label{
    display:block;
    font-size:14px;
    color:#333;
    margin-bottom:8px;
    font-weight:700;
}

input[type="file"]{
    width:100%;
    border:1px solid #ddd;
    border-radius:12px;
    padding:10px;
    background:#fafafa;
}

input[type="file"]::file-selector-button{
    background:#4f6ef7;
    color:#fff;
    border:none;
    padding:10px 14px;
    border-radius:10px;
    cursor:pointer;
    font-weight:700;
    margin-right:12px;
}

input[type="file"]::file-selector-button:hover{
    opacity:.92;
}
/* ====== RESPONSIVE FIX: ADD_RECIPE ====== */
@media (max-width: 520px){
  body{
    align-items: flex-start;
    padding: 14px;
  }
  .box{
    width: 100%;
    border-radius: 18px;
    padding: 18px;
    margin-top: 70px; /* hogy ne legyen a tetején szűk */
  }
  input, textarea, select{
    font-size: 15px;
    border-radius: 14px;
  }
  button{
    border-radius: 14px;
  }
}

</style>
</head>
<body>

<div class="box">
<h2>Új recept hozzáadása</h2>

<?= $message ?>

<form method="post" enctype="multipart/form-data">

<input type="text" name="cim" placeholder="Recept címe" required>

<select name="kategoria" required>
    <option value="">Válassz kategóriát</option>
    <option value="Reggeli">Reggeli</option>
    <option value="Ebéd">Ebéd</option>
    <option value="Vacsora">Vacsora</option>
</select>

<input type="number" name="ido" placeholder="Elkészítési idő (perc)">

<input type="number" name="kaloria" placeholder="Kalória (kcal)">

<textarea name="hozzavalok" placeholder="Hozzávalók"></textarea>

<textarea name="leiras" placeholder="Elkészítés leírása" required></textarea>

<div class="file">
    <label>Kép (opcionális)</label>
    <input type="file" name="kep" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
</div>

<button type="submit">Recept mentése</button>

</form>

<div class="back">
<a href="index.php">← Vissza</a>
</div>
</div>

</body>
</html>
