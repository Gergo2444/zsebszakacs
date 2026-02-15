<?php
// api/index.php
declare(strict_types=1);

require __DIR__ . "/lib.php";

$method = api_method();
$path = api_clean_path();
$params = [];

// Basic CORS (optional) - allows same-origin by default; enable if needed
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Headers: Content-Type, X-HTTP-Method-Override");
// header("Access-Control-Allow-Methods: GET,POST,PUT,PATCH,DELETE,OPTIONS");
// if ($method === "OPTIONS") { http_response_code(204); exit; }

$conn = api_db();

/* ===== ROUTES ===== */

// Health
if ($method === "GET" && $path === "/") {
    api_json(["ok"=>true,"name"=>"zsebszakács-api","version"=>"1.0.0"]);
    exit;
}

// Auth: login
if ($method === "POST" && $path === "/auth/login") {
    $data = api_body_json();
    $u = trim((string)($data["username"] ?? ($_POST["username"] ?? "")));
    $p = (string)($data["password"] ?? ($_POST["password"] ?? ""));

    if ($u === "" || $p === "") {
        api_json(["ok"=>false,"error"=>"missing_fields"], 400);
        exit;
    }

    $stmt = $conn->prepare("SELECT userID, username, password FROM users WHERE username=?");
    $stmt->bind_param("s", $u);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    if (!$row || !password_verify($p, $row["password"])) {
        api_json(["ok"=>false,"error"=>"invalid_credentials"], 401);
        exit;
    }

    $_SESSION["user_id"] = (int)$row["userID"];
    $_SESSION["username"] = (string)$row["username"];

    api_json(["ok"=>true,"user"=>["id"=>(int)$row["userID"],"username"=>(string)$row["username"]]]);
    exit;
}

// Auth: register
if ($method === "POST" && $path === "/auth/register") {
    $data = api_body_json();
    $u = trim((string)($data["username"] ?? ($_POST["username"] ?? "")));
    $e = trim((string)($data["email"] ?? ($_POST["email"] ?? "")));
    $p = (string)($data["password"] ?? ($_POST["password"] ?? ""));

    if ($u === "" || $e === "" || $p === "") {
        api_json(["ok"=>false,"error"=>"missing_fields"], 400);
        exit;
    }
    if (!filter_var($e, FILTER_VALIDATE_EMAIL)) {
        api_json(["ok"=>false,"error"=>"invalid_email"], 400);
        exit;
    }

    $check = $conn->prepare("SELECT userID FROM users WHERE username=? OR email=?");
    $check->bind_param("ss", $u, $e);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        api_json(["ok"=>false,"error"=>"already_exists"], 409);
        exit;
    }

    $hash = password_hash($p, PASSWORD_DEFAULT);
    $ins = $conn->prepare("INSERT INTO users (username,email,password) VALUES (?,?,?)");
    $ins->bind_param("sss", $u, $e, $hash);
    if (!$ins->execute()) {
        api_json(["ok"=>false,"error"=>"db_insert_failed"], 500);
        exit;
    }

    api_json(["ok"=>true,"user"=>["id"=>$conn->insert_id,"username"=>$u,"email"=>$e]], 201);
    exit;
}

// Auth: logout
if ($method === "POST" && $path === "/auth/logout") {
    session_destroy();
    api_json(["ok"=>true]);
    exit;
}

// Auth: me
if ($method === "GET" && $path === "/auth/me") {
    if (!isset($_SESSION["user_id"])) {
        api_json(["ok"=>true,"user"=>null]);
        exit;
    }
    api_json(["ok"=>true,"user"=>["id"=>(int)$_SESSION["user_id"],"username"=>(string)($_SESSION["username"] ?? "")]]);
    exit;
}

// Recipes collection: GET /recipes?cat=reggeli|ebed|vacsora OR /recipes?id=...
if ($method === "GET" && $path === "/recipes") {
    $auth = api_require_auth();
    $user_id = $auth["user_id"];

    $id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
    $cat = (string)($_GET["cat"] ?? "");

    if ($id > 0) {
        $stmt = $conn->prepare("SELECT * FROM receptek WHERE id=?");
        $stmt->bind_param("i", $id);
    } else if ($cat !== "") {
        $stmt = $conn->prepare("SELECT * FROM receptek WHERE kategoria=?");
        $stmt->bind_param("s", $cat);
    } else {
        // default: all categories
        $stmt = $conn->prepare("SELECT * FROM receptek");
    }
    $stmt->execute();
    $res = $stmt->get_result();

    // favorites for user
    $favMap = [];
    $st = $conn->prepare("SELECT recept_id FROM kedvencek WHERE user_id=?");
    $st->bind_param("i", $user_id);
    $st->execute();
    $fr = $st->get_result();
    while ($r = $fr->fetch_assoc()) $favMap[(int)$r["recept_id"]] = true;

    $items = [];
    while ($row = $res->fetch_assoc()) {
        $rid = (int)$row["id"];
        $row["id"] = $rid;
        $row["ido"] = (int)$row["ido"];
        $row["kaloria"] = (int)$row["kaloria"];
        $row["user_id"] = (int)$row["user_id"];
        $row["is_favorite"] = isset($favMap[$rid]);
        $items[] = $row;
    }
    api_json(["ok"=>true,"recipes"=>$items]);
    exit;
}

// Recipe create: POST /recipes (multipart or json)
if ($method === "POST" && $path === "/recipes") {
    $auth = api_require_auth();
    $user_id = $auth["user_id"];

    $data = api_body_json();
    // accept either JSON body or form fields (same names as add_recipe.php)
    $cim = trim((string)($data["cim"] ?? ($_POST["cim"] ?? "")));
    $kategoria = (string)($data["kategoria"] ?? ($_POST["kategoria"] ?? ""));
    $ido = (int)($data["ido"] ?? ($_POST["ido"] ?? 0));
    $kaloria = (int)($data["kaloria"] ?? ($_POST["kaloria"] ?? 0));
    $hozzavalok = trim((string)($data["hozzavalok"] ?? ($_POST["hozzavalok"] ?? "")));
    $leiras = trim((string)($data["leiras"] ?? ($_POST["leiras"] ?? "")));

    if ($cim === "" || $kategoria === "" || $leiras === "") {
        api_json(["ok"=>false,"error"=>"missing_fields"], 400);
        exit;
    }

    // image upload like existing logic
    $kepNev = null;
    if (isset($_FILES["kep"]) && $_FILES["kep"]["error"] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES["kep"]["error"] !== 0) {
            api_json(["ok"=>false,"error"=>"upload_error"], 400);
            exit;
        }
        if ($_FILES["kep"]["size"] > 3 * 1024 * 1024) {
            api_json(["ok"=>false,"error"=>"image_too_large"], 400);
            exit;
        }
        $allowedExt = ["jpg","jpeg","png","webp"];
        $allowedMime = ["image/jpeg","image/png","image/webp"];
        $fileName = $_FILES["kep"]["name"];
        $fileTmp  = $_FILES["kep"]["tmp_name"];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $fileTmp);
        finfo_close($finfo);

        if (!in_array($ext, $allowedExt, true) || !in_array($mime, $allowedMime, true)) {
            api_json(["ok"=>false,"error"=>"invalid_image_type"], 400);
            exit;
        }

        $dir = dirname(__DIR__) . "/kepek";
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $ujNev = bin2hex(random_bytes(8)) . "." . $ext;
        if (!move_uploaded_file($fileTmp, $dir . "/" . $ujNev)) {
            api_json(["ok"=>false,"error"=>"upload_save_failed"], 500);
            exit;
        }
        $kepNev = $ujNev;
    }

    $stmt = $conn->prepare("
        INSERT INTO receptek (cim, kategoria, ido, kaloria, hozzavalok, leiras, kep, user_id)
        VALUES (?,?,?,?,?,?,?,?)
    ");
    $stmt->bind_param("ssiisssi", $cim, $kategoria, $ido, $kaloria, $hozzavalok, $leiras, $kepNev, $user_id);
    if (!$stmt->execute()) {
        api_json(["ok"=>false,"error"=>"db_insert_failed"], 500);
        exit;
    }

    api_json(["ok"=>true,"recipe_id"=>$conn->insert_id], 201);
    exit;
}

// Recipe delete: DELETE /recipes/{id}
if (api_route("/recipes/{id}", $path, $params)) {
    $rid = (int)$params["id"];

    if ($method === "DELETE") {
        $auth = api_require_auth();
        $user_id = $auth["user_id"];

        if ($rid <= 0) {
            api_json(["ok"=>false,"error"=>"bad_id"], 400);
            exit;
        }

        $stmt = $conn->prepare("SELECT kep FROM receptek WHERE id=? AND user_id=?");
        $stmt->bind_param("ii", $rid, $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        if (!$row) {
            api_json(["ok"=>false,"error"=>"not_found"], 404);
            exit;
        }
        $kep = (string)($row["kep"] ?? "");

        // delete favorites refs
        $d1 = $conn->prepare("DELETE FROM kedvencek WHERE recept_id=?");
        $d1->bind_param("i", $rid);
        $d1->execute();

        // delete recipe
        $del = $conn->prepare("DELETE FROM receptek WHERE id=? AND user_id=?");
        $del->bind_param("ii", $rid, $user_id);
        $del->execute();

        // delete file if exists
        if ($kep !== "") {
            $file = basename($kep);
            $pathImg = dirname(__DIR__) . "/kepek/" . $file;
            if (is_file($pathImg)) @unlink($pathImg);
        }

        api_json(["ok"=>true]);
        exit;
    }
}

// Favorites list: GET /favorites
if ($method === "GET" && $path === "/favorites") {
    $auth = api_require_auth();
    $user_id = $auth["user_id"];

    $stmt = $conn->prepare("
        SELECT r.id, r.cim, r.kep
        FROM kedvencek k
        JOIN receptek r ON r.id = k.recept_id
        WHERE k.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $items = [];
    while ($row = $res->fetch_assoc()) {
        $row["id"] = (int)$row["id"];
        $items[] = $row;
    }
    api_json(["ok"=>true,"favorites"=>$items]);
    exit;
}

// Favorites toggle: PUT /favorites/{recipeId} (or POST as toggle)
if (api_route("/favorites/{id}", $path, $params)) {
    $rid = (int)$params["id"];
    if (in_array($method, ["PUT","POST","DELETE"], true)) {
        $auth = api_require_auth();
        $user_id = $auth["user_id"];

        if ($rid <= 0) {
            api_json(["ok"=>false,"error"=>"bad_id"], 400);
            exit;
        }

        $check = $conn->prepare("SELECT id FROM kedvencek WHERE user_id=? AND recept_id=?");
        $check->bind_param("ii", $user_id, $rid);
        $check->execute();
        $check->store_result();
        $exists = $check->num_rows > 0;

        if ($method === "DELETE") {
            if ($exists) {
                $del = $conn->prepare("DELETE FROM kedvencek WHERE user_id=? AND recept_id=?");
                $del->bind_param("ii", $user_id, $rid);
                $del->execute();
            }
            api_json(["ok"=>true,"favorite"=>false]);
            exit;
        }

        // PUT/POST => toggle
        if ($exists) {
            $del = $conn->prepare("DELETE FROM kedvencek WHERE user_id=? AND recept_id=?");
            $del->bind_param("ii", $user_id, $rid);
            $del->execute();
            api_json(["ok"=>true,"favorite"=>false]);
        } else {
            $add = $conn->prepare("INSERT INTO kedvencek (user_id, recept_id) VALUES (?,?)");
            $add->bind_param("ii", $user_id, $rid);
            $add->execute();
            api_json(["ok"=>true,"favorite"=>true]);
        }
        exit;
    }
}

// Fallback
api_json(["ok"=>false,"error"=>"not_found","path"=>$path,"method"=>$method], 404);
