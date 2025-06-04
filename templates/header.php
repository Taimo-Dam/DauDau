<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

// Get user details if logged in
$user = isLoggedIn() ? getUserDetails() : null;

// Get recent playlists for sidebar
$recentPlaylists = [];
if (isLoggedIn()) {
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM playlists WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $recentPlaylists = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M&U Music</title>
    <link rel="icon" type="image/jpg" sizes="16x16" href="../images/logo.jpg">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css">
</head>
<body>
    <header>
        <div class="header-left">
            <a href="index.php" class="logo">M&U</a>

            
            <div class="search-container">
    <form action="search.php" method="GET" class="search-form">
        <input type="text" 
               name="query" 
               placeholder="Search For Musics, Artists, ..." 
               class="search-input" 
               id="searchInput"
               autocomplete="off"
               minlength="2"
               value="<?= htmlspecialchars($_GET['query'] ?? '') ?>">
        <button type="submit" class="search-button">
            <i class="fas fa-search"></i>
        </button>
    </form>
    <div class="search-results" id="searchResults"></div>
</div>
        </div>
        
        <div class="header-right">
            <nav>
                <a href="../about.php">About Us</a>
                <a href="../contact.php">Contact</a>
                <a href="../premium.php">Premium</a>
                
                <?php if (isLoggedIn() && $user): ?>
                    <div class="user-profile" id="userProfile">
                        <img  src="<?= htmlspecialchars($user['profile_picture'] ?? '../images/default-user.png') ?>" 
                             alt="Profile" class="profile-img">
                        <span><?= htmlspecialchars($user['username']) ?></span>
                        
                        <div class="profile-dropdown" id="profileDropdown">
                            <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                            <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                            <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>

                            <?php if ($user && $user['role'] === 'admin'): ?>
                                <a class="admin" href="admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="../login.php">Login</a>
                    <a href="../register.php"><button class="button">Sign up</button></a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <script src="../assets/js/header.js"></script>
</body>
</html>