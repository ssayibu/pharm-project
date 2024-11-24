<?php
session_start();
require 'db.php';

// Check if the user is logged in and has the required role
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'pharmacist'])) {
    echo "Access denied.";
    exit;
}

$error = "";
$success = "";

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Data validation
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $manufacturer = trim($_POST['manufacturer']);
    $expiry_date = $_POST['expiry_date'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    if (empty($name) || empty($price) || empty($quantity)) {
        $error = "Medicine name, price, and quantity are required.";
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = "Invalid price. Price should be a positive number.";
    } elseif (!is_numeric($quantity) || $quantity <= 0) {
        $error = "Invalid quantity. Quantity should be a positive integer.";
    } elseif (!empty($expiry_date) && !DateTime::createFromFormat('Y-m-d', $expiry_date)) {
        $error = "Invalid date format. Please use YYYY-MM-DD.";
    } else {
        // No validation errors, proceed to database insertion
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO medicines (name, description, manufacturer, expiry_date, price) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $manufacturer, $expiry_date, $price]);

            // Get the last inserted medicine ID to update stock
            $medicine_id = $pdo->lastInsertId();
            $stmt = $pdo->prepare("INSERT INTO stock (medicine_id, quantity) VALUES (?, ?)");
            $stmt->execute([$medicine_id, $quantity]);

            $pdo->commit();
            $success = "Medicine added successfully!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Failed to add medicine: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Medicine</title>
</head>
<body>
    <h2>Add Medicine</h2>

    <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php elseif ($success): ?>
        <p style="color: green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="POST" action="add_medicine.php">
        <input type="text" name="name" placeholder="Medicine Name" value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>" required>
        
        <textarea name="description" placeholder="Description"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
        
        <input type="text" name="manufacturer" placeholder="Manufacturer" value="<?= isset($_POST['manufacturer']) ? htmlspecialchars($_POST['manufacturer']) : '' ?>">
        
        <input type="date" name="expiry_date" placeholder="Expiry Date (YYYY-MM-DD)" value="<?= isset($_POST['expiry_date']) ? htmlspecialchars($_POST['expiry_date']) : '' ?>">
        
        <input type="number" name="price" step="0.01" placeholder="Price" value="<?= isset($_POST['price']) ? htmlspecialchars($_POST['price']) : '' ?>" required>
        
        <input type="number" name="quantity" placeholder="Quantity" value="<?= isset($_POST['quantity']) ? htmlspecialchars($_POST['quantity']) : '' ?>" min="1" required>
        
        <button type="submit">Add Medicine</button>
    </form>
</body>
</html>
