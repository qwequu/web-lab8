<?php
require "security.php";
require "db.php";
require_login();

// видалення дозволено тільки POST-запитом з правильним CSRF-токеном
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(405);
    die("Метод не дозволено");
}
check_csrf();

$id = (int)$_POST["id"];
$stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: index.php?msg=Замовлення видалено");
exit;
?>
