<?php
require "security.php";
require "db.php";
require "functions.php";
require_login();

$id = (int)$_GET["id"];
$errors = [];
$statuses = ["нове", "у виробництві", "готове", "видане"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    check_csrf();
    $company = trim($_POST["company"]);
    $phone = trim($_POST["phone"]);
    $uniform_type = trim($_POST["uniform_type"]);
    $employees = $_POST["employees"];
    $sets = $_POST["sets_per_employee"];
    $price = $_POST["price"];
    $deadline = $_POST["deadline"];
    $status = $_POST["status"];

    if (empty($company)) $errors[] = "Вкажіть назву підприємства";
    if (!is_valid_phone($phone)) $errors[] = "Невірний формат телефону";
    if (!filter_var($employees, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) $errors[] = "Кількість працівників повинна бути цілим числом більше 0";
    if (!filter_var($sets, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) $errors[] = "Кількість комплектів повинна бути цілим числом більше 0";
    if (!is_numeric($price) || $price <= 0) $errors[] = "Ціна повинна бути більше 0";
    if (!in_array($status, $statuses)) $errors[] = "Невірний статус";

    if (count($errors) == 0) {
        $phoneEncrypted = encrypt_data($phone);
        $stmt = $conn->prepare("UPDATE orders SET company=?, phone_encrypted=?, uniform_type=?, employees=?,
                                sets_per_employee=?, price=?, deadline=?, status=? WHERE id=?");
        $stmt->bind_param("sssiidssi", $company, $phoneEncrypted, $uniform_type, $employees, $sets, $price, $deadline, $status, $id);
        $stmt->execute();

        header("Location: index.php?msg=Замовлення оновлено");
        exit;
    }
}

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
if (!$order) {
    die("Замовлення не знайдено");
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Редагування замовлення</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Редагування замовлення №<?php echo e($order["id"]); ?></h1>

    <?php foreach ($errors as $error) echo "<p class='error'>" . e($error) . "</p>"; ?>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <label>Підприємство: <input type="text" name="company" value="<?php echo e($order["company"]); ?>"></label>
        <label>Телефон: <input type="text" name="phone" value="<?php echo e(decrypt_data($order["phone_encrypted"])); ?>"></label>
        <label>Вид уніформи: <input type="text" name="uniform_type" value="<?php echo e($order["uniform_type"]); ?>"></label>
        <label>Кількість працівників: <input type="number" name="employees" value="<?php echo e($order["employees"]); ?>"></label>
        <label>Комплектів на одного працівника: <input type="number" name="sets_per_employee" value="<?php echo e($order["sets_per_employee"]); ?>"></label>
        <label>Ціна одного комплекту, грн: <input type="number" step="0.01" name="price" value="<?php echo e($order["price"]); ?>"></label>
        <label>Термін виконання: <input type="date" name="deadline" value="<?php echo e($order["deadline"]); ?>"></label>
        <label>Статус:
            <select name="status">
                <?php foreach ($statuses as $s) { ?>
                    <option value="<?php echo e($s); ?>" <?php if ($s == $order["status"]) echo "selected"; ?>><?php echo e($s); ?></option>
                <?php } ?>
            </select>
        </label>
        <button type="submit">Зберегти</button>
        <a href="index.php">Назад</a>
    </form>
</body>
</html>
