<?php
require_once 'includes/db.php';
require_once __DIR__ . '../includes/init.php';
// Check login
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

$sql = "
    SELECT s.*, MAX(h.listened_at) as listened_at 
    FROM listening_history h
    JOIN songs s ON h.song_id = s.id
    WHERE h.user_id = ?
    GROUP BY s.id, s.title, s.artist, s.audio_path, s.image_path
    ORDER BY listened_at DESC
    LIMIT 20
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$history = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử nghe nhạc | MeandYou</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="images/logo.png" rel="icon">
    <link rel="stylesheet" href="assets/css/recent.css">
    <link rel="stylesheet" href="assets/css/playbar.css">
</head>
<body>
            <?php include('templates/header.php'); ?>
        <?php include('modun/sidebar.php'); ?>
    <div class="main-content" id="mainContent">


        <div class="content-wrapper">
            <div class="main-left">
                <h2>Lịch sử nghe nhạc</h2>
            </div>

            <div class="history-container">
                <?php if (count($history) > 0): ?>
                    <?php foreach ($history as $song): ?>
                        <div class="song-card"
                            data-id="<?= htmlspecialchars($song['id']) ?>"
                             data-audio="<?= htmlspecialchars($song['audio_path']) ?>"
                             data-song-name="<?= htmlspecialchars($song['title']) ?>" 
                             data-artist="<?= htmlspecialchars($song['artist']) ?>"
                             data-id="<?= htmlspecialchars($song['id']) ?>"
                             data-img="<?= htmlspecialchars($song['image_path']) ?>"
                             onclick="playSongCard(this)">
                            <div class="song-image">
                                <img src="<?= htmlspecialchars($song['image_path']) ?>" 
                                     alt="<?= htmlspecialchars($song['title']) ?>">
                                <div class="play-overlay">
                                    <i class='bx bx-play-circle'></i>
                                </div>
                            </div>
                            <div class="song-info">
                                <h3><?= htmlspecialchars($song['title']) ?></h3>
                                <p class="artist"><?= htmlspecialchars($song['artist']) ?></p>
                                <span class="listened-at">
                                    <?= date('d/m/Y H:i', strtotime($song['listened_at'])) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-history">
                        <p>Bạn chưa nghe bài hát nào.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
            <?php include('templates/footer.php'); ?>

    </div>
    <?php include('modun/playbar.php'); ?>
</body>
</html>
