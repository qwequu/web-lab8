<?php
// перевірка стану застосунку для моніторингу
require_once "config.php";
header("Content-Type: application/json");

$status = ["app" => "ok", "time" => date("c")];
try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $status["database"] = "ok";
} catch (Exception $e) {
    $status["database"] = "error";
    http_response_code(503);
}
echo json_encode($status);
?>
