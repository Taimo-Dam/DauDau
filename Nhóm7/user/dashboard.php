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
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../modun/header.php'; ?>

    <div class="dashboard-container">
        <h1>Welcome back, <?php echo htmlspecialchars($user['username']); ?>!</h1>
        
        <div class="dashboard-grid">
            <!-- Profile Section -->
            <div class="dashboard-card profile-section">
                <h2>Your Profile</h2>
                <img src="<?php echo htmlspecialchars($user['profile_pic'] ?? '../images/default-user.png'); ?>" 
                     alt="Profile Picture">
                <h3><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><i class="fas fa-calendar-alt"></i> Member since <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                <a href="edit-profile.php" class="button">Edit Profile</a>
            </div>

            <!-- Recent Playlists -->
            <div class="dashboard-card">
                <h2>Recent Playlists</h2>
                <?php if (!empty($recentPlaylists)): ?>
                    <?php foreach ($recentPlaylists as $playlist): ?>
                        <div class="playlist-item">
                            <i class="fas fa-music"></i>
                            <div>
                                <h4><?php echo htmlspecialchars($playlist['name']); ?></h4>
                                <small><?php echo date('M j, Y', strtotime($playlist['created_at'])); ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <a href="../playlists.php" class="button">View All Playlists</a>
                <?php else: ?>
                    <p>No playlists created yet.</p>
                    <a href="../create-playlist.php" class="button">Create Your First Playlist</a>
                <?php endif; ?>
            </div>

            <!-- Recent Activity -->
            <div class="dashboard-card">
                <h2>Recent Activity</h2>
                <?php if (!empty($recentActivity)): ?>
                    <ul class="activity-list">
                    <?php foreach ($recentActivity as $activity): ?>
                        <li>
                            <i class="fas fa-history"></i>
                            <span><?php echo htmlspecialchars($activity['description']); ?></span>
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
</body>
<style>.dashboard-container {
    max-width: 1200px;
    margin: 80px auto 20px;
    padding: 20px;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.dashboard-card {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
    transition: transform 0.3s ease;
}

.dashboard-card:hover {
    transform: translateY(-5px);
    background: rgba(255, 255, 255, 0.15);
}

.profile-section {
    text-align: center;
}

.profile-section img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    margin: 0 auto 15px;
    border: 3px solid #9147ff;
}

.profile-section h3 {
    color: #fff;
    margin: 10px 0;
}

.profile-section p {
    color: rgba(255, 255, 255, 0.7);
    margin: 5px 0;
}

.activity-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.activity-list li {
    padding: 12px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    gap: 10px;
}

.activity-list li:last-child {
    border-bottom: none;
}

.activity-list i {
    color: #9147ff;
}

.activity-list small {
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.85em;
    margin-left: auto;
}

.playlist-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transition: background-color 0.3s ease;
}

.playlist-item:last-child {
    border-bottom: none;
}

.playlist-item:hover {
    background: rgba(255, 255, 255, 0.05);
}

.playlist-item i {
    color: #9147ff;
    font-size: 1.2em;
}

.playlist-item h4 {
    margin: 0;
    color: #fff;
}

.playlist-item small {
    color: rgba(255, 255, 255, 0.5);
}

.button {
    display: inline-block;
    background: #9147ff;
    color: white;
    padding: 8px 16px;
    border-radius: 5px;
    text-decoration: none;
    margin-top: 15px;
    transition: background-color 0.3s ease;
}

.button:hover {
    background: #7c3aed;
}

/* Dashboard title */
.dashboard-container h1 {
    color: #fff;
    font-size: 2em;
    margin-bottom: 30px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

/* Card headers */
.dashboard-card h2 {
    color: #9147ff;
    margin-top: 0;
    margin-bottom: 20px;
    font-size: 1.5em;
}

@media (max-width: 768px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .dashboard-container {
        padding: 15px;
        margin-top: 60px;
    }
}</style>
</html>