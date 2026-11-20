<?php
require "security.php";
require "db.php";
require "functions.php";
require_login();

$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";
$status = isset($_GET["status"]) ? $_GET["status"] : "";

// параметризований запит: дані користувача не потрапляють у текст SQL
$sql = "SELECT * FROM orders WHERE company LIKE ?";
$params = ["%" . $search . "%"];
$types = "s";
if ($status != "") {
    $sql .= " AND status = ?";
    $params[] = $status;
    $types .= "s";
}
$sql .= " ORDER BY deadline";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Замовлення уніформи</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <p>Користувач: <b><?php echo e($_SESSION["user"]); ?></b> | <a href="logout.php">Вийти</a></p>
    <h1>Корпоративні замовлення уніформи</h1>

    <?php if (isset($_GET["msg"])) echo "<p class='success'>" . e($_GET["msg"]) . "</p>"; ?>

    <form method="get">
        Пошук за підприємством:
        <input type="text" name="search" value="<?php echo e($search); ?>">
        Статус:
        <select name="status">
            <option value="">усі</option>
            <option value="нове">нове</option>
            <option value="у виробництві">у виробництві</option>
            <option value="готове">готове</option>
            <option value="видане">видане</option>
        </select>
        <button type="submit">Знайти</button>
        <a href="index.php">Скинути</a>
    </form>

    <p><a href="add.php">+ Додати замовлення</a></p>

    <table>
        <tr>
            <th>№</th>
            <th>Підприємство</th>
            <th>Телефон</th>
            <th>Уніформа</th>
            <th>Працівників</th>
            <th>Комплектів на працівника</th>
            <th>Всього комплектів</th>
            <th>Ціна комплекту, грн</th>
            <th>Сума, грн</th>
            <th>Термін</th>
            <th>Статус</th>
            <th>Дії</th>
        </tr>
        <?php
        $totalEmployees = 0;
        $totalSets = 0;
        $totalSum = 0;
        while ($row = $result->fetch_assoc()) {
            $sets = total_sets($row["employees"], $row["sets_per_employee"]);
            $sum = order_total($row["employees"], $row["sets_per_employee"], $row["price"]);
            $totalEmployees += $row["employees"];
            $totalSets += $sets;
            $totalSum += $sum;
        ?>
        <tr>
            <td><?php echo e($row["id"]); ?></td>
            <td><?php echo e($row["company"]); ?></td>
            <td><?php echo e(decrypt_data($row["phone_encrypted"])); ?></td>
            <td><?php echo e($row["uniform_type"]); ?></td>
            <td><?php echo e($row["employees"]); ?></td>
            <td><?php echo e($row["sets_per_employee"]); ?></td>
            <td><?php echo $sets; ?></td>
            <td><?php echo e($row["price"]); ?></td>
            <td><?php echo number_format($sum, 2, ".", " "); ?></td>
            <td><?php echo e($row["deadline"]); ?></td>
            <td><?php echo e($row["status"]); ?></td>
            <td>
                <a href="edit.php?id=<?php echo e($row["id"]); ?>">Редагувати</a>
                <!-- видалення тільки через POST з CSRF-токеном -->
                <form method="post" action="delete.php" style="display:inline" onsubmit="return confirm('Видалити замовлення?')">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="id" value="<?php echo e($row["id"]); ?>">
                    <button type="submit">Видалити</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>

    <div class="summary">
        Всього працівників у замовленнях: <b><?php echo $totalEmployees; ?></b>,
        комплектів: <b><?php echo $totalSets; ?></b>,
        загальна сума: <b><?php echo number_format($totalSum, 2, ".", " "); ?> грн</b>
    </div>
</body>
</html>
