<?php
session_start();

// Include database connection and functions
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user data from database
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // User not found in database
        session_destroy();
        header("Location: login.php");
        exit();
    }

    // Get user's playlists
    $stmtPlaylists = $pdo->prepare("SELECT * FROM playlists WHERE user_id = ?");
    $stmtPlaylists->execute([$_SESSION['user_id']]);
    $playlists = $stmtPlaylists->fetchAll();

    // Get recently played songs
    $stmtRecent = $pdo->prepare("
        SELECT s.* FROM music m
        JOIN user_activity ua ON m.id = ua.music_id
        WHERE ua.user_id = ? AND ua.action = 'play'
        ORDER BY ua.created_at DESC LIMIT 3
    ");
    $stmtRecent->execute([$_SESSION['user_id']]);
    $recent_songs = $stmtRecent->fetchAll();

} catch (PDOException $e) {
    error_log("Error in profile.php: " . $e->getMessage());
    // Instead of redirecting to error.php, set an error message
    $error_message = "An error occurred while loading your profile. Please try again later.";
    // Continue with the rest of the page, but show the error message where needed
}

// Get statistics
$stats = [
    'playlists' => count($playlists),
    'followers' => 0, // You'll need to implement followers system
    'following' => 0  // You'll need to implement following system
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - M&U Music</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #0a0a0a;
            color: white;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .gradient-text {
            font-weight: bold;
            color: #ff00ff;
            text-shadow: 0 0 10px rgba(255, 0, 255, 0.8);
            background: linear-gradient(to right, #ff00ff, #00ffff);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #333;
        }
        
        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid;
            border-image: linear-gradient(to right, #ff00ff, #00ffff) 1;
            margin-right: 30px;
        }
        
        .profile-info h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .profile-stats {
            display: flex;
            margin: 15px 0;
        }
        
        .stat-item {
            margin-right: 20px;
        }
        
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #ff69b4;
        }
        
        .profile-actions {
            margin-top: 15px;
        }
        
        .btn {
            padding: 8px 20px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-weight: bold;
            margin-right: 10px;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: linear-gradient(to right, #ff00ff, #00ffff);
            color: white;
        }
        
        .btn-secondary {
            background-color: #333;
            color: white;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 0, 255, 0.4);
        }
        
        .section-title {
            font-size: 24px;
            margin: 30px 0 20px;
        }
        
        .playlist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .playlist-card {
            background-color: #222;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s;
        }
        
        .playlist-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(255, 105, 180, 0.3);
        }
        
        .playlist-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        
        .playlist-details {
            padding: 15px;
        }
        
        .playlist-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .playlist-songs {
            color: #999;
            font-size: 14px;
        }
        
        .recent-songs {
            margin-top: 30px;
        }
        
        .song-item {
            display: flex;
            align-items: center;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #222;
            border-radius: 10px;
            transition: background-color 0.3s;
        }
        
        .song-item:hover {
            background-color: #333;
        }
        
        .song-image {
            width: 60px;
            height: 60px;
            border-radius: 5px;
            margin-right: 15px;
        }
        
        .song-info {
            flex-grow: 1;
        }
        
        .song-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .song-artist {
            color: #999;
            font-size: 14px;
        }
        
        .song-actions {
            display: flex;
        }
        
        .song-button {
            background: none;
            border: none;
            color: white;
            font-size: 16px;
            margin-left: 15px;
            cursor: pointer;
            transition: color 0.3s;
        }
        
        .song-button:hover {
            color: #ff69b4;
        }
        
        .subscription-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            margin-left: 10px;
        }
        
        .free-badge {
            background-color: #555;
            color: white;
        }
        
        .premium-badge {
            background: linear-gradient(to right, #ff00ff, #00ffff);
            color: white;
        }

        /* Footer Styles (สำคัญเพื่อให้สอดคล้องกับ footer.html) */
        .footer {
            background-color: #111;
            color: white;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 50px;
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
            width: 100%;
            max-width: 1200px;
            padding: 0 20px;
        }
        
        .footer-section {
            flex: 1;
            padding: 10px;
        }
        
        .footer-section.about {
            flex: 2;
        }
        
        .footer-section h3 {
            margin-bottom: 10px;
            font-size: 18px;
            font-weight: bold;
            color: #ff00ff;
            text-shadow: 0 0 10px rgba(255, 0, 255, 0.8);
            background: linear-gradient(to right, #ff00ff, #00ffff);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .footer-section ul {
            list-style: none;
            padding: 0;
        }
        
        .footer-section ul li {
            margin-bottom: 10px;
        }
        
        .footer-section ul li a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-section ul li a:hover {
            color: #ff69b4;
        }
        
        .footer-bottom {
            text-align: center;
            padding: 10px 20px;
            border-top: 1px solid #444;
            margin-top: 20px;
            width: 100%;
        }
        
        .footer-bottom p {
            margin: 10px 0;
        }
        
        .footer-bottom .social-icons a {
            color: white;
            margin: 0 10px;
            font-size: 20px;
            transition: color 0.3s;
        }
        
        .footer-bottom .social-icons a:hover {
            color: #ff69b4;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($error_message)): ?>
            <div class="error-alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        <div class="profile-header">
            <img src="images/<?php echo $user['avatar']; ?>" alt="Profile Avatar" class="profile-avatar">
            <div class="profile-info">
                <h1 class="gradient-text"><?php echo $user['username']; ?></h1>
                <p><?php echo $user['email']; ?></p>
                <div class="subscription-badge <?php echo strtolower($user['subscription']); ?>-badge">
                    <?php echo $user['subscription']; ?>
                </div>
                <p>Member since: <?php echo $user['joined_date']; ?></p>
                
                <div class="profile-stats">
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $stats['playlists']; ?></div>
                        <div class="stat-label">Playlists</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $stats['followers']; ?></div>
                        <div class="stat-label">Followers</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $stats['following']; ?></div>
                        <div class="stat-label">Following</div>
                    </div>
                </div>
                
                <div class="profile-actions">
                    <a href="settings.php" class="btn btn-secondary">Edit Profile</a>
                    <?php if ($user['subscription'] == 'Free'): ?>
                        <a href="premium.php" class="btn btn-primary">Upgrade to Premium</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <h2 class="section-title gradient-text">My Playlists</h2>
        <div class="playlist-grid">
            <?php foreach ($playlists as $playlist): ?>
                <div class="playlist-card">
                    <img src="images/<?php echo $playlist['image']; ?>" alt="<?php echo $playlist['name']; ?>" class="playlist-image">
                    <div class="playlist-details">
                        <div class="playlist-name"><?php echo $playlist['name']; ?></div>
                        <div class="playlist-songs"><?php echo $playlist['songs']; ?> songs</div>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="playlist-card">
                <div style="height: 150px; display: flex; justify-content: center; align-items: center; background-color: #333;">
                    <i class="fas fa-plus" style="font-size: 40px; color: #ff69b4;"></i>
                </div>
                <div class="playlist-details">
                    <div class="playlist-name">Create New Playlist</div>
                </div>
            </div>
        </div>
        
        <h2 class="section-title gradient-text">Recently Played</h2>
        <div class="recent-songs">
            <?php foreach ($recent_songs as $song): ?>
                <div class="song-item">
                    <img src="images/<?php echo $song['image']; ?>" alt="<?php echo $song['title']; ?>" class="song-image">
                    <div class="song-info">
                        <div class="song-title"><?php echo $song['title']; ?></div>
                        <div class="song-artist"><?php echo $song['artist']; ?></div>
                    </div>
                    <div class="song-actions">
                        <button class="song-button" title="Play"><i class="fas fa-play"></i></button>
                        <button class="song-button" title="Add to playlist"><i class="fas fa-plus"></i></button>
                        <button class="song-button" title="Like"><i class="far fa-heart"></i></button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <?php include('footer.html'); ?>
</body>
</html>