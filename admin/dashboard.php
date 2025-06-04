<?php
// filepath: c:\xampp\htdocs\web\Nhóm7\admin\dashboard.php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

define('IS_ADMIN_PAGE', true);

// Check admin role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch dashboard statistics
try {
    $stmtUsers = $conn->query("SELECT COUNT(*) AS total_users FROM users");
    $totalUsers = $stmtUsers->fetch_assoc()['total_users'] ?? 0;
    
    $stmtSongs = $conn->query("SELECT COUNT(*) AS total_songs FROM songs");
    $totalSongs = $stmtSongs->fetch_assoc()['total_songs'] ?? 0;
    
    $stmtPlaylists = $conn->query("SELECT COUNT(*) AS total_playlists FROM playlists");
    $totalPlaylists = $stmtPlaylists->fetch_assoc()['total_playlists'] ?? 0;
} catch (Exception $e) {
    error_log("Error fetching dashboard stats: " . $e->getMessage());
    $totalUsers = $totalSongs = $totalPlaylists = "N/A";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - M&U Music</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="admin-header">
        <h1>Admin Dashboard</h1>
        <nav>
            <a href="manage-users.php"><i class="fas fa-users"></i> Users</a>
            <a href="manage-songs.php"><i class="fas fa-music"></i> Songs</a>
            <a href="manage-roles.php"><i class="fas fa-user-shield"></i> Roles</a>
            <a href="../index.php"><i class="fas fa-home"></i> Home</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </header>
    
    <main class="admin-main">
        <section class="dashboard-stats">
            <div class="stat">
                <h2><i class="fas fa-users"></i> Total Users</h2>
                <p><?php echo htmlspecialchars($totalUsers); ?></p>
            </div>
            <div class="stat">
                <h2><i class="fas fa-music"></i> Total Songs</h2>
                <p><?php echo htmlspecialchars($totalSongs); ?></p>
            </div>
            <div class="stat">
                <h2><i class="fas fa-list"></i> Total Playlists</h2>
                <p><?php echo htmlspecialchars($totalPlaylists); ?></p>
            </div>
        <section class="recent-activity">
            <h2>Recent User Activity</h2>
            <?php
            try {
                // Fetch recent activity
                $stmtActivity = $conn->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 10");
                if ($stmtActivity) {
                    // Fetch all results as an array
                    $activities = $stmtActivity->fetch_all(MYSQLI_ASSOC);
                    
                    if (!empty($activities)) {
                        echo "<ul>";
                        foreach ($activities as $activity) {
                            echo "<li>" . htmlspecialchars($activity['username']) . " - " . htmlspecialchars($activity['created_at']) . "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<p>No recent activity.</p>";
                    }
                } else {
                    echo "<p>Error fetching recent activity.</p>";
                }
            } catch (Exception $e) {
                error_log("Error fetching user activity: " . $e->getMessage());
                echo "<p>Error fetching recent activity.</p>";
            }
            ?>
        </section>
    </main>        
        <section class="quick-actions">
            <h2>Quick Actions</h2>
            <div class="action-buttons">
                <a href="add-user.php" class="btn"><i class="fas fa-user-plus"></i> Add User</a>
                <a href="add-song.php" class="btn"><i class="fas fa-plus-circle"></i> Add Song</a>
                <a href="manage-roles.php" class="btn"><i class="fas fa-users-cog"></i> Manage Roles</a>
            </div>
        </section>

        <section class="recent-activity">
            <h2>Recent Activity</h2>
            <div class="activity-tabs">
                <button class="tab-btn active" data-tab="users">Users</button>
                <button class="tab-btn" data-tab="songs">Songs</button>
            </div>
            <div class="tab-content active" id="users-tab">
                <?php include 'includes/recent-users.php'; ?>
            </div>
            <div class="tab-content" id="songs-tab">
                <?php include 'includes/recent-songs.php'; ?>
            </div>
        </section>
    </main>

    <footer class="admin-footer">
        <p>&copy; <?php echo date("Y"); ?> M&U Music. All rights reserved.</p>
    </footer>

    <script src="../assets/js/admin.js"></script>
</body>

</html>