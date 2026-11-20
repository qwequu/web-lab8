<?php
require "security.php";
require "db.php";
require "functions.php";
require_login();

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    check_csrf();
    $company = trim($_POST["company"]);
    $phone = trim($_POST["phone"]);
    $uniform_type = trim($_POST["uniform_type"]);
    $employees = $_POST["employees"];
    $sets = $_POST["sets_per_employee"];
    $price = $_POST["price"];
    $deadline = $_POST["deadline"];

    if (empty($company)) $errors[] = "Вкажіть назву підприємства";
    if (!is_valid_phone($phone)) $errors[] = "Невірний формат телефону";
    if (empty($uniform_type)) $errors[] = "Вкажіть вид уніформи";
    if (!filter_var($employees, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) $errors[] = "Кількість працівників повинна бути цілим числом більше 0";
    if (!filter_var($sets, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) $errors[] = "Кількість комплектів повинна бути цілим числом більше 0";
    if (!is_numeric($price) || $price <= 0) $errors[] = "Ціна повинна бути більше 0";
    if (empty($deadline)) $errors[] = "Вкажіть термін";

    if (count($errors) == 0) {
        // телефон шифрується перед збереженням
        $phoneEncrypted = encrypt_data($phone);

        $stmt = $conn->prepare("INSERT INTO orders (company, phone_encrypted, uniform_type, employees, sets_per_employee, price, deadline)
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiids", $company, $phoneEncrypted, $uniform_type, $employees, $sets, $price, $deadline);
        $stmt->execute();

        header("Location: index.php?msg=Замовлення додано");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Нове замовлення</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Нове замовлення</h1>

    <?php foreach ($errors as $error) echo "<p class='error'>" . e($error) . "</p>"; ?>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <label>Підприємство: <input type="text" name="company"></label>
        <label>Телефон: <input type="text" name="phone" placeholder="+380..."></label>
        <label>Вид уніформи: <input type="text" name="uniform_type"></label>
        <label>Кількість працівників: <input type="number" name="employees"></label>
        <label>Комплектів на одного працівника: <input type="number" name="sets_per_employee" value="1"></label>
        <label>Ціна одного комплекту, грн: <input type="number" step="0.01" name="price"></label>
        <label>Термін виконання: <input type="date" name="deadline"></label>
        <button type="submit">Зберегти</button>
        <a href="index.php">Назад</a>
    </form>
</body>
</html>
