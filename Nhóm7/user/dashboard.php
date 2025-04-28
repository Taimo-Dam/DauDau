<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Include header after session and required files
include __DIR__ . '/../modun/header.php';

// Get user information
$userId = $_SESSION['user_id'];
try {
    // Fetch user details
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if (!$user) {
        die("User not found.");
    }

    // Fetch user's playlists using mysqli instead of PDO
    $query = "SELECT * FROM playlists WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $recentPlaylists = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Fetch recent activity using mysqli
    $query = "SELECT * FROM user_activity WHERE user_id = ? ORDER BY created_at DESC LIMIT 10";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $recentActivity = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $error = "An error occurred while loading your dashboard.";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - M&U Music</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .dashboard-card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .profile-section img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }
        .activity-list {
            list-style: none;
            padding: 0;
        }
        .activity-list li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .playlist-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
        }
    </style>
</head>
<body>
    <?php include '../modun/header.php'; ?>

    <div class="dashboard-container">
        <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
        
        <div class="dashboard-grid">
            <!-- Profile Section -->
            <div class="dashboard-card profile-section">
                <h2>Profile</h2>
                <img src="<?php echo htmlspecialchars($user['profile_pic'] ?? '../images/default-user.png'); ?>" alt="Profile Picture">
                <h3><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
                <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
                <p>Member since: <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                <a href="edit-profile.php" class="button">Edit Profile</a>
            </div>

            <!-- Recent Playlists -->
            <div class="dashboard-card">
                <h2>Your Recent Playlists</h2>
                <?php if ($recentPlaylists): ?>
                    <?php foreach ($recentPlaylists as $playlist): ?>
                        <div class="playlist-item">
                            <i class="fas fa-music"></i>
                            <div>
                                <h4><?php echo htmlspecialchars($playlist['name']); ?></h4>
                                <small><?php echo date('M j, Y', strtotime($playlist['created_at'])); ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No playlists created yet.</p>
                <?php endif; ?>
                <a href="../playlists.php" class="button">View All Playlists</a>
            </div>

            <!-- Recent Activity -->
            <div class="dashboard-card">
                <h2>Recent Activity</h2>
                <?php if ($recentActivity): ?>
                    <ul class="activity-list">
                    <?php foreach ($recentActivity as $activity): ?>
                        <li>
                            <i class="fas fa-history"></i>
                            <?php echo htmlspecialchars($activity['description']); ?>
                            <small><?php echo date('M j, Y H:i', strtotime($activity['created_at'])); ?></small>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No recent activity.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include '../modun/footer.html'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add any dashboard-specific JavaScript here
    });
    </script>
</body>
</html>