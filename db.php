<?php

declare(strict_types=1);

$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=practicum4;charset=utf8mb4';
$user = 'root';
$pass = '';

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO(
        $dsn,
        $user,
        $pass,
        $options
    );
} catch (PDOException $e) {

    error_log(
        'Database connection error: '
        . $e->getMessage()
    );

    throw new RuntimeException(
        'Не вдалося підключитися до бази даних.'
    );
}