<?php
require "security.php";
require "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    check_csrf();
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    // підготовлений запит замість підстановки рядка в SQL
    $stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user["password_hash"])) {
        session_regenerate_id(true);
        $_SESSION["user"] = $user["username"];
        header("Location: index.php");
        exit;
    } else {
        $error = "Невірний логін або пароль";
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Вхід</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Вхід до системи</h1>
    <?php if ($error) echo "<p class='error'>" . e($error) . "</p>"; ?>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <label>Логін: <input type="text" name="username"></label>
        <label>Пароль: <input type="password" name="password"></label>
        <button type="submit">Увійти</button>
    </form>
</body>
</html>
