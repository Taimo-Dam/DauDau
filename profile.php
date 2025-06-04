<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    // Get user info with prepared statement
    $stmt = $conn->prepare("
        SELECT id, username, email, telephone, profile_picture, bio, status
        FROM users 
        WHERE id = ?
    ");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit();
    }

    // Set default avatar path
    $avatarPath = !empty($user['profile_picture']) ? $user['profile_picture'] : 'assets/images/default-avatar.jpg';

    // Get user's playlists
    $stmtPlaylists = $conn->prepare("
        SELECT p.*, COUNT(pm.music_id) as song_count 
        FROM playlists p
        LEFT JOIN playlist_music pm ON p.id = pm.playlist_id
        WHERE p.user_id = ?
        GROUP BY p.id
    ");
    $stmtPlaylists->bind_param("i", $_SESSION['user_id']);
    $stmtPlaylists->execute();
    $playlists = $stmtPlaylists->get_result()->fetch_all(MYSQLI_ASSOC);

    // Get recently played songs
    $stmtRecent = $conn->prepare("
        SELECT s.*, h.listened_at 
        FROM listening_history h
        JOIN songs s ON h.song_id = s.id
        WHERE h.user_id = ?
        ORDER BY h.listened_at DESC
        LIMIT 20
    ");
    $stmtRecent->bind_param("i", $_SESSION['user_id']);
    $stmtRecent->execute();
    $recent_songs = $stmtRecent->get_result()->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    error_log("Error in profile.php: " . $e->getMessage());
    $error_message = "An error occurred while loading your profile. Please try again later.";
}

// Update the profile section in HTML
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($user['username']) ?>'s Profile - M&U Music</title>
    <link rel="stylesheet" href="assets/css/profile.css">
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link rel="stylesheet" href="assets/css/playbar.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include('templates/header.php'); ?>
    <?php include('modun/sidebar.php'); ?>

    <div class="main-content">
        <div class="profile-header">
            <div class="profile-info">
                <img src="<?= htmlspecialchars($avatarPath) ?>" alt="Profile" class="profile-avatar">
                <h1><?= htmlspecialchars($user['username']) ?></h1>
                <p class="bio"><?= htmlspecialchars($user['bio'] ?? '') ?></p>
                <div class="profile-stats">
                    <span><i class='bx bx-envelope'></i> <?= htmlspecialchars($user['email']) ?></span>
                    <span><i class='bx bx-phone'></i> <?= htmlspecialchars($user['telephone']) ?></span>
                    <span><i class='bx bx-user-pin'></i> <?= htmlspecialchars($user['role']) ?></span>
                </div>
            </div>
        </div>

        <section class="playlists-section">
            <h2 class="section-title">Your Playlists</h2>
            <div class="playlists-grid">
                <!-- Create New Playlist Card -->
                <div class="song-card create-playlist" onclick="createNewPlaylist()">
                    <div class="play-overlay">
                        <i class='bx bx-plus-circle'></i>
                    </div>
                    <div class="card-content">
                        <i class='bx bx-plus'></i>
                        <p>Create New Playlist</p>
                    </div>
                </div>

                <!-- Existing Playlists -->
                <?php foreach ($playlists as $playlist): ?>
                <div class="song-card">
                    <div class="play-overlay">
                        <i class='bx bx-play-circle'></i>
                    </div>
                    <h3><?= htmlspecialchars($playlist['name']) ?></h3>
                    <p><?= $playlist['song_count'] ?> songs</p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <section class="recent-section">
            <h2 class="section-title">Recently Played</h2>
            <div class="songs-grid">
                <?php if (!empty($recent_songs)): ?>
                    <?php foreach ($recent_songs as $song): ?>
                        <div class="song-card" 
                             data-id="<?= htmlspecialchars($song['id']) ?>"
                             data-audio="<?= htmlspecialchars($song['audio_path']) ?>"
                             data-song-name="<?= htmlspecialchars($song['title']) ?>"
                             data-artist="<?= htmlspecialchars($song['artist']) ?>"
                             data-img="<?= htmlspecialchars($song['image_path']) ?>"
                             onclick="playSongCard(this)">
                            <div class="song-image">
                                <img src="<?= htmlspecialchars($song['image_path']) ?>" 
                                     alt="<?= htmlspecialchars($song['title']) ?>"
                                     onerror="this.src='assets/images/default-song.jpg'">
                                <div class="play-overlay">
                                    <i class='bx bx-play-circle'></i>
                                </div>
                            </div>
                            <div class="song-info">
                                <h3><?= htmlspecialchars($song['title']) ?></h3>
                                <p class="artist"><?= htmlspecialchars($song['artist']) ?></p>
                                <span class="played-at">
                                    <?= date('d/m/Y H:i', strtotime($song['listened_at'])) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-songs">No recently played songs</p>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <?php include('modun/playbar.php'); ?>

    <script src="assets/js/playMusic.js"></script>
    <script>
        function createNewPlaylist() {
            // Implement playlist creation logic
        }
    </script>
</body>
</html>