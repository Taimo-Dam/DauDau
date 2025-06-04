<?php
// Ensure this file is included within the admin context
if (!defined('IS_ADMIN_PAGE')) {
    exit('Direct access not permitted');
}

try {
    // Fetch recent songs with play count
    $stmtSongs = $conn->query("
        SELECT s.*, 
               COUNT(lh.id) as play_count,
               s.created_at
        FROM songs s
        LEFT JOIN listening_history lh ON s.id = lh.song_id
        GROUP BY s.id
        ORDER BY s.created_at DESC
        LIMIT 10
    ");

    if ($stmtSongs) {
        $recentSongs = $stmtSongs->fetch_all(MYSQLI_ASSOC);
        
        if (!empty($recentSongs)) {
            echo "<ul class='recent-list'>";
            foreach ($recentSongs as $song) {
                echo "<li class='recent-item'>";
                echo "<div class='song-info'>";
                echo "<img src='" . htmlspecialchars('../' . $song['image_path']) . "' 
                           alt='" . htmlspecialchars($song['title']) . "' 
                           class='song-thumb'
                           onerror=\"this.src='../assets/images/default-song.jpg'\">";
                echo "<div class='song-details'>";
                echo "<h4>" . htmlspecialchars($song['title']) . "</h4>";
                echo "<p>" . htmlspecialchars($song['artist']) . "</p>";
                echo "</div>";
                echo "</div>";
                echo "<div class='song-stats'>";
                echo "<span class='play-count'><i class='fas fa-play'></i> " . number_format($song['play_count']) . "</span>";
                echo "<span class='added-date'>" . date('d M Y', strtotime($song['created_at'])) . "</span>";
                echo "</div>";
                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p class='no-data'>No songs have been added yet.</p>";
        }
    } else {
        echo "<p class='error'>Error fetching recent songs.</p>";
    }
} catch (Exception $e) {
    error_log("Error in recent-songs.php: " . $e->getMessage());
    echo "<p class='error'>Error loading recent songs.</p>";
}
?>