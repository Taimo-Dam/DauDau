<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check admin role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'delete':
                if (isset($_POST['user_id'])) {
                    $userId = intval($_POST['user_id']);
                    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
                    $stmt->bind_param("i", $userId);
                    $stmt->execute();
                }
                break;
                
            case 'update_role':
                if (isset($_POST['user_id']) && isset($_POST['role'])) {
                    $userId = intval($_POST['user_id']);
                    $role = $_POST['role'];
                    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
                    $stmt->bind_param("si", $role, $userId);
                    $stmt->execute();
                }
                break;
        }
    }
}

// Fetch users
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin </title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'includes/admin-header.php'; ?>
    
    <main class="admin-main">
        <section class="manage-users">
            <h2>Manage Users</h2>
            
            <div class="add-user-section">
                <a href="add-user.php" class="btn"><i class="fas fa-user-plus"></i> Add New User</a>
            </div>

            <table class="users-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <select name="role" onchange="updateRole(<?= $user['id'] ?>, this.value)"
                                    <?= $user['role'] === 'admin' ? 'disabled' : '' ?>>
                                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                <option value="premium" <?= $user['role'] === 'premium' ? 'selected' : '' ?>>Premium</option>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </td>
                        <td><?= date('Y-m-d H:i', strtotime($user['created_at'])) ?></td>
                        <td>
                            <button onclick="editUser(<?= $user['id'] ?>)" class="btn-small">
                                <i class="fas fa-edit"></i>
                            </button>
                            <?php if ($user['role'] !== 'admin'): ?>
                            <button onclick="deleteUser(<?= $user['id'] ?>)" class="btn-small delete">
                                <i class="fas fa-trash"></i>
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <?php include 'includes/admin-footer.php'; ?>
    
    <script src="../assets/js/admin-users.js"></script>
</body>
</html>