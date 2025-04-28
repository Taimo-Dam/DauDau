<?php
// filepath: c:\xampp\htdocs\web\Nhóm7\admin\dashboard.php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if user is logged in and has an admin role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch statistics for dashboard
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
    <link rel="stylesheet" href="../css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="admin-header">
        <h1>Admin Dashboard</h1>
        <nav>
            <a href="../index.php">Home</a>
            <a href="../user/logout.php">Logout</a>
        </nav>
    </header>
    
    <main class="admin-main">
        <section class="dashboard-stats">
            <div class="stat">
                <h2>Total Users</h2>
                <p><?php echo htmlspecialchars($totalUsers); ?></p>
            </div>
            <div class="stat">
                <h2>Total Songs</h2>
                <p><?php echo htmlspecialchars($totalSongs); ?></p>
            </div>
            <div class="stat">
                <h2>Total Playlists</h2>
                <p><?php echo htmlspecialchars($totalPlaylists); ?></p>
            </div>
        </section>
        
        <section class="recent-activity">
            <h2>Recent User Activity</h2>
            <?php
            try {
                // Fetch recent activity (adjust table name and fields as needed)
                $stmtActivity = $conn->query("SELECT * FROM user_activity_log ORDER BY created_at DESC LIMIT 10");
                $activities = $stmtActivity->fetch_assoc();
                if ($activities) {
                    echo "<ul>";
                    foreach ($activities as $activity) {
                        echo "<li>" . htmlspecialchars($activity['description']) . " - " . htmlspecialchars($activity['created_at']) . "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No recent activity.</p>";
                }
            } catch (Exception $e) {
                error_log("Error fetching user activity: " . $e->getMessage());
                echo "<p>Error fetching recent activity.</p>";
            }
            ?>
        </section>
    </main>
    
    <footer class="admin-footer">
        <p>&copy; <?php echo date("Y"); ?> M&U Music. All rights reserved.</p>
    </footer>
</body>
</html>