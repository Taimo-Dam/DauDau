<?php

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/init.php';


// Get recent playlists for sidebar
$recentPlaylists = [];
if (isLoggedIn()) {
    $userId = $_SESSION['user_id'];
    $playlistsQuery = "SELECT * FROM playlists WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
    $stmt = $conn->prepare($playlistsQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $recentPlaylists = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css">


<button class="menu-toggle" id="menuToggle">
    <i class='bx bx-menu'></i>
</button>

<div class="sidebar">
    <nav class="sidebar-nav">
        <a href="../../index.php" class="nav-link">
            <i class='bx bxs-home'></i>
            <span>Home</span>
        </a>
        <a href="index.php?page=#discover" class="nav-link">
            <i class='bx bx-music'></i>
            <span>Discover</span>
        </a>
        <a href="albums.php" class="nav-link" data-title="Albums">
            <i class="fas fa-compact-disc"></i>
            <span>Albums</span>
        </a>
        <a href="artists.php" class="nav-link" data-title="Artists">
            <i class="fas fa-microphone"></i>
            <span>Artists</span>
        </a>
        
        <div class="sidebar-content">
            <ul class="nav-list">
                <li class="nav-category">Library</li>
                <li class="nav-item">
                    <a href="recent.php" class="nav-link" data-title="Recently Added">
                        <i class="fas fa-history"></i>
                        <span>Recently Added</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../most-played.php" class="nav-link" data-title="Most Played">
                        <i class="fas fa-fire"></i>
                        <span>Most Played</span>
                    </a>
                </li>
                
                <li class="nav-category">Playlist and Favorite</li>
                <li class="nav-item">
                    <a href="favorites.php" class="nav-link" data-title="Your Favorites">
                        <i class="fas fa-heart"></i>
                        <span>Your Favorites</span>
                    </a>
                </li>
                
                <?php if (!empty($recentPlaylists)): ?>
                    <?php foreach($recentPlaylists as $playlist): ?>
                    <li class="nav-item">
                        <a href="playlist.php?id=<?= htmlspecialchars($playlist['id']) ?>" data-title="<?= htmlspecialchars($playlist['name']) ?>" class="nav-link">
                            <i class="fas fa-list"></i>
                            <span class="links_name"><?= htmlspecialchars($playlist['name']) ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <li class="nav-item">
                    <a href="create-playlist.php" class="nav-link blue" data-title="Add Playlist">
                        <i class="fas fa-plus"></i>
                        <span>Add Playlist</span>
                    </a>
                </li>
                
                <li class="nav-category">General</li>
                <li class="nav-item">
                    <a href="settings.php" class="nav-link" data-title="Settings">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>
        </div>
    </nav>
</div>

<div class="overlay"></div>
<script src="../assets/js/sidebar.js"></script>