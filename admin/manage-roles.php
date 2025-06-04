<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check admin role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'change_role':
                if (isset($_POST['user_id']) && isset($_POST['new_role'])) {
                    $userId = intval($_POST['user_id']);
                    $newRole = $_POST['new_role'];
                    
                    // Validate role
                    $validRoles = ['user', 'premium', 'admin'];
                    if (in_array($newRole, $validRoles)) {
                        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
                        $stmt->bind_param("si", $newRole, $userId);
                        
                        if ($stmt->execute()) {
                            $success_message = "Role updated successfully";
                        } else {
                            $error_message = "Failed to update role";
                        }
                    } else {
                        $error_message = "Invalid role selected";
                    }
                }
                break;
        }
    }
}

// Fetch all users with their roles
$users = $conn->query("
    SELECT id, username, email, role, created_at 
    FROM users 
    ORDER BY created_at DESC
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Roles - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'includes/admin-header.php'; ?>
    
    <main class="admin-main">
        <section class="manage-roles">
            <h2>Manage User Roles</h2>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <table class="roles-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Current Role</th>
                        <th>Actions</th>
                        <th>Joined Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <span class="role-badge <?= htmlspecialchars($user['role']) ?>">
                                <?= htmlspecialchars(ucfirst($user['role'])) ?>
                            </span>
                        </td>
                        <td>
                            <select class="role-select" 
                                    onchange="changeRole(<?= $user['id'] ?>, this.value)"
                                    <?= $user['role'] === 'admin' ? 'disabled' : '' ?>>
                                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                <option value="premium" <?= $user['role'] === 'premium' ? 'selected' : '' ?>>Premium</option>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </td>
                        <td><?= date('Y-m-d H:i', strtotime($user['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <?php include 'includes/admin-footer.php'; ?>

    <script>
    function changeRole(userId, newRole) {
        if (confirm('Are you sure you want to change this user\'s role?')) {
            fetch('manage-roles.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=change_role&user_id=${userId}&new_role=${newRole}`
            })
            .then(response => response.text())
            .then(() => location.reload())
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update role');
            });
        }
    }
    </script>
</body>
</html>

