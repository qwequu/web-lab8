<?php
// налаштування беруться зі змінних оточення (у Docker), інакше — значення за замовчуванням для XAMPP
define("DB_HOST", getenv("DB_HOST") ?: "localhost");
define("DB_USER", getenv("DB_USER") ?: "root");
define("DB_PASS", getenv("DB_PASS") ?: "");
define("DB_NAME", getenv("DB_NAME") ?: "uniform_secure");
define("ENCRYPTION_KEY", getenv("ENCRYPTION_KEY") ?: "k8Zt2pQ9vX4mW7nR1sY6bH3jL5cF0aEd");
?>
