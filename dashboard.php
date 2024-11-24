<?php
session_start();
if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['role'] == 'admin') {
    echo "<a href='manage_users.php'>Manage Users</a>";
}

if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'pharmacist') {
    echo "<a href='update_stock.php'>Update Stock</a>";
}

echo "<a href='add_medicine.php'>Add Medicine</a>";
echo "<a href='record_sale.php'>Record Sale</a>";
?>
