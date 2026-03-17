<?php

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {

    $dsn = "mysql:host=" . $_ENV['DB_HOST'] .
           ";port=" . $_ENV['DB_PORT'] .
           ";dbname=" . $_ENV['DB_DATABASE'] .
           ";charset=utf8mb4";

    $pdo = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}