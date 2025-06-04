<?php
require_once('includes/db.php');
require_once('includes/init.php');

// Check if user is logged in and store the state
$isLoggedIn = isset($_SESSION['user_id']);
                
try {
    // Get genre from URL parameter
    $genre = isset($_GET['name']) ? $_GET['name'] : '';
    
    // Modified query to use 'genre' column instead of 'genres'
    $stmt = $conn->prepare("SELECT s.*, COALESCE(s.play_count, 0) as play_count 
                           FROM songs s 
                           WHERE s.genre = ?
                           ORDER BY s.play_count DESC
                           ");
    $stmt->bind_param('s', $genre);
    $stmt->execute();
    $result = $stmt->get_result();
    $songs = $result->fetch_all(MYSQLI_ASSOC);

    // Get all available genres
    $allGenres = [];
    foreach ($songs as $song) {
        // Split genre if it contains multiple values
        $songGenres = explode(',', $song['genre']);
        foreach ($songGenres as $g) {
            $g = trim($g);
            if (!empty($g) && !in_array($g, $allGenres)) {
                $allGenres[] = $g;
            }
        }
    }
} catch(Exception $e) {
    die("Lỗi khi lấy bài hát: " . $e->getMessage());
}
?>               
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($genre); ?> | M&U</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" type="image/jpg" sizes="16x16" href="../images/logo.jpg">
    <link rel="stylesheet" href="assets/css/genre.css">
    <link rel="stylesheet" href="assets/css/playbar.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="main-content" id="mainContent">
        <div class="header">
            <?php include('templates/header.php'); ?>
        </div>
        <?php include('modun/sidebar.php'); ?>

        <div class="albums-container">
            <h1><?php echo htmlspecialchars($genre); ?></h1>

            <div class="albums-grid">
                <?php if (empty($songs)): ?>
                    <p class="no-songs">No songs found for this genre.</p>
                <?php else: ?>
                        <?php foreach ($songs as $song): ?>
                            <div class="song-card" 
                                <?php if ($isLoggedIn): ?>
                                 data-id="<?= htmlspecialchars($song['id']) ?>"
                                 data-audio="<?= htmlspecialchars($song['audio_path']) ?>"
                                 data-song-name="<?= htmlspecialchars($song['title']) ?>"
                                 data-artist="<?= htmlspecialchars($song['artist']) ?>"
                                 data-img="<?= htmlspecialchars($song['image_path']) ?>"
                                 onclick="playSongCard(this)"
                                <?php else: ?>
                                 onclick="requireLogin()"
                                    <?php endif; ?>>
                            <div class="play-overlay">
                                <i class='bx bx-play-circle'></i>
                            </div>
                            <div class="album-image">
                                <img src="<?php echo htmlspecialchars($song['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($song['title']); ?>">
                            </div>
                            <div class="album-info">
                                <h3><?= htmlspecialchars($song['title']) ?></h3>
                                <p class="artist"><?= htmlspecialchars($song['artist']) ?></p>
                                <span class="play-count"><?= number_format($song['play_count']) ?> plays</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include('templates/footer.php'); ?>

<script>
        function requireLogin() {
    Swal.fire({
        title: 'Đăng nhập để nghe nhạc',
        text: 'Bạn cần đăng nhập để nghe nhạc. Bạn có muốn đăng nhập không?',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Đăng nhập',
        cancelButtonText: 'Để sau',
        confirmButtonColor: '#7c3aed', // Primary purple color
        cancelButtonColor: '#4b5563', // Gray color
        background: '#1a1625', // Dark background
        color: '#fff', // White text
        iconColor: '#7c3aed', // Purple icon
        reverseButtons: true,
        customClass: {
            popup: 'custom-popup',
            confirmButton: 'custom-confirm-button',
            cancelButton: 'custom-cancel-button'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.href);
        } else {
            const Toast = Swal.mixin({
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#1a1625',
                color: '#fff',
                iconColor: '#7c3aed',
                customClass: {
                    popup: 'custom-toast'
                }
            });
            
            Toast.fire({
                icon: 'info',
                title: 'Bạn có thể đăng nhập bất cứ lúc nào để nghe nhạc'
            });
        }
    });
}

</script>
</body>
    <?php include('modun/playbar.php'); ?>

</html>