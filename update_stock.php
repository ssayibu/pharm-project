<?php
session_start();
require 'db.php';

// Ensure only admins and pharmacists can update stock
if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'pharmacist') {
    echo "Access denied.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $medicine_id = $_POST['medicine_id'];
    $quantity = $_POST['quantity'];

    if (!is_numeric($quantity) || $quantity < 0) {
        echo "Invalid quantity.";
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE stock SET quantity = ? WHERE medicine_id = ?");
        $stmt->execute([$quantity, $medicine_id]);
        echo "Stock updated successfully!";
    } catch (Exception $e) {
        echo "Failed to update stock: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Stock</title>
</head>
<body>
    <h2>Update Stock</h2>
    <form method="POST" action="update_stock.php">
        <select name="medicine_id" required>
            <?php
            $stmt = $pdo->query("SELECT medicine_id, name FROM medicines");
            while ($medicine = $stmt->fetch()) {
                echo "<option value='{$medicine['medicine_id']}'>{$medicine['name']}</option>";
            }
            ?>
        </select>
        <input type="number" name="quantity" placeholder="New Quantity" required>
        <button type="submit">Update Stock</button>
    </form>
</body>
</html>
