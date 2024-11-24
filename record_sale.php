<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $medicine_id = $_POST['medicine_id'];
    $quantity_sold = $_POST['quantity'];
    $user_id = $_SESSION['user_id'];

    // Fetch price and available quantity
    $stmt = $pdo->prepare("SELECT price, quantity FROM medicines JOIN stock ON medicines.medicine_id = stock.medicine_id WHERE medicines.medicine_id = ?");
    $stmt->execute([$medicine_id]);
    $medicine = $stmt->fetch();

    if ($medicine && $medicine['quantity'] >= $quantity_sold) {
        $total_price = $quantity_sold * $medicine['price'];
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("INSERT INTO sales (user_id, medicine_id, quantity_sold, total_price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $medicine_id, $quantity_sold, $total_price]);

            $stmt = $pdo->prepare("UPDATE stock SET quantity = quantity - ? WHERE medicine_id = ?");
            $stmt->execute([$quantity_sold, $medicine_id]);

            $pdo->commit();
            echo "Sale recorded successfully!";
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "Failed to record sale: " . $e->getMessage();
        }
    } else {
        echo "Insufficient stock!";
    }
}
?>
