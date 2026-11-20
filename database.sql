-- База даних для лабораторної роботи №7 (захищена версія застосунку з ЛР6)

CREATE DATABASE IF NOT EXISTS uniform_secure CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE uniform_secure;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL       -- зберігається тільки хеш пароля
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company VARCHAR(150) NOT NULL,
    phone_encrypted VARCHAR(255) NOT NULL,    -- телефон у зашифрованому вигляді
    uniform_type VARCHAR(100) NOT NULL,
    employees INT NOT NULL,
    sets_per_employee INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    deadline DATE NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'нове',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
