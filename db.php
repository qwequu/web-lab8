<?php
require_once "config.php";

// режим, при якому помилки MySQL викидаються як винятки
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    // користувачу не показуємо деталі помилки
    error_log($e->getMessage());
    die("Помилка підключення до бази даних");
}
?>
