<?php
require_once 'includes/init.php';

try {
    // Get total songs count using prepared statement
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM songs");
    $stmt->execute();
    $total_records = $stmt->get_result()->fetch_assoc()['total'];

    // Pagination setup
    $limit = 10;
    $current_page = max(1, $_GET['page'] ?? 1);
    $total_page = ceil($total_records / $limit);
    $current_page = min($current_page, $total_page);
    $start = ($current_page - 1) * $limit;

    // Get songs for current page using prepared statement
    $stmt = $conn->prepare("SELECT * FROM songs ORDER BY title ASC LIMIT ?, ?");
    $stmt->bind_param("ii", $start, $limit);
    $stmt->execute();
    $songs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    error_log("Error in songs.php: " . $e->getMessage());
    $error = "An error occurred while loading songs";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Songs | MeandYou</title>
    <link rel="stylesheet" href="assets/css/songs.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include 'templates/header.php'; ?>
    <?php include 'modun/sidebar.php'; ?>

    <main class="main-content">
        <h2>Danh sách bài hát</h2>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php else: ?>
            <div class="song-list">
                <?php foreach ($songs as $song): ?>
                    <div class="song-card" 
                         data-audio="<?= htmlspecialchars($song['audio_path']) ?>"
                         data-song-name="<?= htmlspecialchars($song['title']) ?>" 
                         data-artist="<?= htmlspecialchars($song['artist']) ?>"
                         data-img="<?= htmlspecialchars($song['image_path']) ?>"
                         data-id="<?= $song['id'] ?>"
                         onclick="playSong(this)">
                        <div class="song-image">
                            <img src="<?= file_exists($song['image_path']) 
                                ? htmlspecialchars($song['image_path']) 
                                : 'assets/images/default-song.jpg' ?>" 
                                alt="<?= htmlspecialchars($song['title']) ?>"
                                onerror="this.src='assets/images/default-song.jpg'">
                            <div class="play-overlay">
                                <i class='bx bx-play-circle'></i>
                            </div>
                        </div>
                        <div class="song-info">
                            <h3><?= htmlspecialchars($song['title']) ?></h3>
                            <p class="artist"><?= htmlspecialchars($song['artist']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?= $current_page - 1 ?>" class="prev">Prev</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_page; $i++): ?>
                    <?php if ($i == $current_page): ?>
                        <span class="current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $i ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($current_page < $total_page): ?>
                    <a href="?page=<?= $current_page + 1 ?>" class="next">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php include 'modun/playbar.php'; ?>
    </main>

    <script src="assets/js/playMusic.js"></script>
</body>
</html>