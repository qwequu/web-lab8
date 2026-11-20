<?php
// функції для захисту застосунку

// у Docker-контейнері HTTPS забезпечує хостинг/проксі, тому перевірку можна вимкнути змінною FORCE_HTTPS=0
$forceHttps = getenv("FORCE_HTTPS") !== "0";

// налаштування cookie сесії
session_set_cookie_params([
    "httponly" => true,     // cookie недоступна з JavaScript
    "secure" => $forceHttps, // передається тільки через HTTPS
    "samesite" => "Strict"  // не відправляється з інших сайтів
]);
session_start();

// перенаправлення на HTTPS, якщо сторінку відкрито через HTTP
if ($forceHttps && (empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] === "off")) {
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit;
}

// захист від XSS: екранування даних перед виводом
function e($text) {
    return htmlspecialchars($text, ENT_QUOTES, "UTF-8");
}

// захист від CSRF: створення токена
function csrf_token() {
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["csrf_token"];
}

// перевірка токена при POST-запиті
function check_csrf() {
    if (!isset($_POST["csrf_token"]) || !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])) {
        http_response_code(403);
        die("Помилка: невірний CSRF-токен");
    }
}

// перевірка, чи користувач увійшов у систему
function require_login() {
    if (!isset($_SESSION["user"])) {
        header("Location: login.php");
        exit;
    }
}

// шифрування персональних даних (AES-256-CBC)
function encrypt_data($text) {
    $iv = random_bytes(16);
    $encrypted = openssl_encrypt($text, "AES-256-CBC", ENCRYPTION_KEY, 0, $iv);
    return base64_encode($iv . $encrypted);
}

function decrypt_data($data) {
    $data = base64_decode($data);
    $iv = substr($data, 0, 16);
    $encrypted = substr($data, 16);
    return openssl_decrypt($encrypted, "AES-256-CBC", ENCRYPTION_KEY, 0, $iv);
}
?>
