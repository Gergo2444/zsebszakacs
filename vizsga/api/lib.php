<?php
// api/lib.php
declare(strict_types=1);

session_start();

function api_db(): mysqli {
    static $conn = null;
    if ($conn instanceof mysqli) return $conn;

    $conn = new mysqli("localhost", "root", "", "zsebszakács");
    if ($conn->connect_error) {
        api_json(["ok"=>false,"error"=>"db_connect"], 500);
        exit;
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}

function api_json($data, int $status = 200): void {
    http_response_code($status);
    header("Content-Type: application/json; charset=utf-8");
    header("Cache-Control: no-store");
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function api_method(): string {
    $m = $_SERVER["REQUEST_METHOD"] ?? "GET";
    // method override support
    if ($m === "POST") {
        $hdr = $_SERVER["HTTP_X_HTTP_METHOD_OVERRIDE"] ?? "";
        if ($hdr) return strtoupper($hdr);
        if (isset($_POST["_method"])) return strtoupper((string)$_POST["_method"]);
    }
    return strtoupper($m);
}

function api_body_json(): array {
    $ct = $_SERVER["CONTENT_TYPE"] ?? $_SERVER["HTTP_CONTENT_TYPE"] ?? "";
    if (stripos($ct, "application/json") !== false) {
        $raw = file_get_contents("php://input");
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }
    return [];
}

function api_require_auth(): array {
    if (!isset($_SESSION["user_id"])) {
        api_json(["ok"=>false,"error"=>"unauthorized"], 401);
        exit;
    }
    return [
        "user_id" => (int)$_SESSION["user_id"],
        "username" => (string)($_SESSION["username"] ?? "")
    ];
}

function api_route(string $pattern, string $path, array &$params): bool {
    // pattern like /recipes/{id}
    $re = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $pattern);
    $re = '#^' . $re . '$#';
    if (preg_match($re, $path, $m)) {
        foreach ($m as $k=>$v) if (!is_int($k)) $params[$k] = $v;
        return true;
    }
    return false;
}

function api_clean_path(): string {
    $uri = $_SERVER["REQUEST_URI"] ?? "/";
    $path = parse_url($uri, PHP_URL_PATH) ?? "/";
    // If hosted under /api, keep relative path
    $script = $_SERVER["SCRIPT_NAME"] ?? "";
    if ($script && str_ends_with($script, "/index.php")) {
        $base = substr($script, 0, -strlen("/index.php"));
        if ($base !== "" && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
            if ($path === "") $path = "/";
        }
    }
    return rtrim($path, "/") ?: "/";
}

// Polyfills for PHP < 8
if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool {
        return $needle === '' || substr($haystack, -strlen($needle)) === $needle;
    }
}
