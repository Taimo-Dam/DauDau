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

<style>
/* Admin Dashboard Styles */
body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    background: linear-gradient(to right, #1a1a2e, #16213e);
    color: #fff;
    min-height: 100vh;
}

.admin-header {
    background: rgba(26, 26, 46, 0.95);
    padding: 20px;
    position: fixed;
    width: 100%;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.admin-header h1 {
    margin: 0;
    color: #fff;
    font-size: 24px;
}

.admin-header nav {
    display: flex;
    gap: 20px;
    font-size: 16px;
    margin-right: 50px;
}

.admin-header nav a {
    color: #fff;
    text-decoration: none;
    padding: 8px 16px;
    border-radius: 5px;
    background: rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.admin-header nav a:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

.admin-main {
    padding: 100px 20px 40px;
    max-width: 1200px;
    margin: 0 auto;
}

.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat {
    background: rgba(255, 255, 255, 0.1);
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    backdrop-filter: blur(10px);
    transition: transform 0.3s ease;
}

.stat:hover {
    transform: translateY(-5px);
    background: rgba(255, 255, 255, 0.15);
}

.stat h2 {
    margin: 0 0 10px;
    font-size: 18px;
    color: #9147ff;
}

.stat p {
    margin: 0;
    font-size: 24px;
    font-weight: bold;
}

.recent-activity {
    background: rgba(26, 26, 46, 0.9);
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.recent-activity h2 {
    color: #9147ff;
    margin-top: 0;
    margin-bottom: 20px;
    font-size: 20px;
}

.recent-activity ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.recent-activity li {
    padding: 12px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transition: background-color 0.3s ease;
}

.recent-activity li:last-child {
    border-bottom: none;
}

.recent-activity li:hover {
    background: rgba(255, 255, 255, 0.05);
}

.admin-footer {
    background: rgba(26, 26, 46, 0.95);
    padding: 20px;
    text-align: center;
    position: fixed;
    bottom: 0;
    width: 100%;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.3);
}

.admin-footer p {
    margin: 0;
    color: rgba(255, 255, 255, 0.7);
    font-size: 14px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .dashboard-stats {
        grid-template-columns: 1fr;
    }

    .admin-header {
        flex-direction: column;
        gap: 15px;
        padding: 15px;
    }

    .admin-main {
        padding: 120px 15px 80px;
    }
} 
 </style>
</html>