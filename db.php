<?php
$host = 'localhost';
$db = 'pharmacy_management';
$user = $ROOT_USER; // replace with your MySQL username
$pass = $ROOT_PASSWORD;     // replace with your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
?>
