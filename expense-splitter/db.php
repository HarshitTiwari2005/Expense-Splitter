<?php
// Database connection (XAMPP default: user=root, password="")
$host = "localhost";
$dbname = "expense_splitter";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

function h($str) { return htmlspecialchars($str ?? "", ENT_QUOTES, "UTF-8"); }
?>
