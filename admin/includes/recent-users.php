<?php
// Ensure this file is included within the admin context
if (!defined('IS_ADMIN_PAGE')) {
    exit('Direct access not permitted');
}

try {
    // Fetch recent users with their stats
    $stmtUsers = $conn->query("
        SELECT u.*, 
               COUNT(DISTINCT p.id) as playlist_count,
               COUNT(DISTINCT lh.id) as listen_count,
               u.created_at
        FROM users u
        LEFT JOIN playlists p ON u.id = p.user_id
        LEFT JOIN listening_history lh ON u.id = lh.user_id
        GROUP BY u.id
        ORDER BY u.created_at DESC
        LIMIT 10
    ");

    if ($stmtUsers) {
        $recentUsers = $stmtUsers->fetch_all(MYSQLI_ASSOC);
        
        if (!empty($recentUsers)) {
            echo "<ul class='recent-list'>";
            foreach ($recentUsers as $user) {
                echo "<li class='recent-item'>";
                echo "<div class='user-info'>";
                echo "<img src='" . (file_exists('../uploads/avatars/' . ($user['avatar'] ?? 'default-avatar.jpg')) ? '../uploads/avatars/' . ($user['avatar'] ?? 'default-avatar.jpg') : '../uploads/avatars/default-avatar.jpg') . "' 
                           alt='" . htmlspecialchars($user['username']) . "' 
                           class='user-avatar'>";
                echo "<div class='user-details'>";
                echo "<h4>" . htmlspecialchars($user['username']) . "</h4>";
                echo "<p class='user-email'>" . htmlspecialchars($user['email']) . "</p>";
                echo "</div>";
                echo "</div>";
                echo "<div class='user-stats'>";
                echo "<span class='role-badge " . htmlspecialchars($user['role']) . "'>" . 
                     htmlspecialchars(ucfirst($user['role'])) . "</span>";
                echo "<span class='join-date'>" . date('d M Y', strtotime($user['created_at'])) . "</span>";
                echo "</div>";
                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p class='no-data'>No users have registered yet.</p>";
        }
    } else {
        echo "<p class='error'>Error fetching recent users.</p>";
    }
} catch (Exception $e) {
    error_log("Error in recent-users.php: " . $e->getMessage());
    echo "<p class='error'>Error loading recent users.</p>";
}
?>