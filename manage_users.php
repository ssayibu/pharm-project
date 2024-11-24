<?php
session_start();
require 'db.php';

// Only allow admins to manage users
if ($_SESSION['role'] != 'admin') {
    echo "Access denied.";
    exit;
}

// Fetch all users for display
$users = $pdo->query("SELECT user_id, username, role FROM users")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (empty($username) || empty($password) || empty($role)) {
        echo "All fields are required.";
        exit;
    }

    try {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password_hash, $role]);
        echo "User added successfully!";
    } catch (Exception $e) {
        echo "Failed to add user: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
</head>
<body>
    <h2>Manage Users</h2>

    <h3>Add New User</h3>
    <form method="POST" action="manage_users.php">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
            <option value="admin">Admin</option>
            <option value="pharmacist">Pharmacist</option>
            <option value="cashier">Cashier</option>
        </select>
        <button type="submit">Add User</button>
    </form>

    <h3>Existing Users</h3>
    <table>
        <tr>
            <th>Username</th>
            <th>Role</th>
        </tr>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['username']); ?></td>
            <td><?= htmlspecialchars($user['role']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
