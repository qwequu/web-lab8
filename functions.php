<?php
// функції розрахунку і перевірки даних (покриті тестами)

// загальна кількість комплектів
function total_sets($employees, $setsPerEmployee) {
    return $employees * $setsPerEmployee;
}

// загальна вартість замовлення
function order_total($employees, $setsPerEmployee, $price) {
    if ($employees < 0 || $setsPerEmployee < 0 || $price < 0) {
        throw new InvalidArgumentException("Значення не можуть бути від'ємними");
    }
    return round(total_sets($employees, $setsPerEmployee) * $price, 2);
}

// перевірка номера телефону
function is_valid_phone($phone) {
    return preg_match("/^\+?[0-9]{10,13}$/", $phone) === 1;
}
?>
