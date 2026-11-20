<?php
// створення користувача (запускається один раз, потім файл можна видалити)
require "security.php";
require "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    check_csrf();
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if (strlen($username) < 3 || strlen($password) < 8) {
        $message = "Логін мінімум 3 символи, пароль мінімум 8 символів";
    } else {
        // пароль зберігається тільки у вигляді хешу
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hash);
        try {
            $stmt->execute();
            $message = "Користувача створено";
        } catch (mysqli_sql_exception $ex) {
            $message = "Такий користувач вже існує";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Реєстрація</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Реєстрація користувача</h1>
    <?php if ($message) echo "<p>" . e($message) . "</p>"; ?>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <label>Логін: <input type="text" name="username"></label>
        <label>Пароль: <input type="password" name="password"></label>
        <button type="submit">Створити</button>
    </form>
    <p><a href="login.php">Вхід</a></p>
</body>
</html>
