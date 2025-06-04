<?php
require_once('includes/db.php');
require_once('includes/functions.php');
require_once('includes/init.php');
// Check if user is logged in and store the state
$isLoggedIn = isset($_SESSION['user_id']);

try {
    // Get period from URL parameter (default to 'all-time')
    $period = isset($_GET['period']) ? $_GET['period'] : 'all-time';
    
    // Build date condition based on period
    $dateCondition = '';
    switch($period) {
        case 'today':
            $dateCondition = "AND DATE(last_played) = CURDATE()";
            break;
        case 'week':
            $dateCondition = "AND last_played >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
            break;
        case 'month':
            $dateCondition = "AND last_played >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
            break;
        default:
            $dateCondition = ""; // all-time
    }

    // Get most played songs
    $query = "
        SELECT 
            s.*,
            COALESCE(s.play_count, 0) as play_count,
            a.name as artist_name,
            a.image_path as artist_image
        FROM songs s
        LEFT JOIN artists a ON s.artist = a.name
        WHERE s.play_count > 0 
        $dateCondition
        ORDER BY s.play_count DESC
        LIMIT 10
    ";
    
    $result = $conn->query($query);
    if (!$result) {
        throw new Exception($conn->error);
    }
    
    $songs = $result->fetch_all(MYSQLI_ASSOC);

} catch(Exception $e) {
    error_log("Error in most-played.php: " . $e->getMessage());
    $error = "Không thể tải danh sách bài hát.";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bài Hát Được Nghe Nhiều | MeandYou</title>
    <link rel="stylesheet" href="../assets/css/css.css">
    <link rel="stylesheet" href="../assets/css/playbar.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/jpg" sizes="16x16" href="../images/logo.jpg">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include('templates/header.php'); ?>

    <div class="main-content" id="mainContent">
        <?php include('modun/sidebar.php'); ?>

        <div class="content-wrapper">
            <!-- Most Played Songs Section -->
            <section class="most-played">
                <div class="section-header">
                    <h2>Bài Hát Được Nghe Nhiều Nhất</h2>
                    <div class="period-filter">
                        <a href="?period=today" class="<?= $period === 'today' ? 'active' : '' ?>">Hôm nay</a>
                        <a href="?period=week" class="<?= $period === 'week' ? 'active' : '' ?>">Tuần này</a>
                        <a href="?period=month" class="<?= $period === 'month' ? 'active' : '' ?>">Tháng này</a>
                        <a href="?period=all-time" class="<?= $period === 'all-time' ? 'active' : '' ?>">Tất cả</a>
                    </div>
                </div>

                <?php if (isset($error)): ?>
                    <div class="error-message"><?= htmlspecialchars($error) ?></div>
                <?php elseif (empty($songs)): ?>
                    <p class="no-songs">Chưa có bài hát nào được phát.</p>
                <?php else: ?>
                    <div class="songs-grid">
                        <?php foreach ($songs as $index => $song): ?>
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
                                <div class="rank"><?= $index + 1 ?></div>
                                <img src="<?= htmlspecialchars($song['image_path']) ?>" 
                                     alt="<?= htmlspecialchars($song['title']) ?>"
                                     loading="lazy"
                                     onerror="this.src=' ../images/default-song.jpg'">
                                <div class="song-info">
                                    <h3><?= htmlspecialchars($song['title']) ?></h3>
                                    <p class="artist"><?= htmlspecialchars($song['artist']) ?></p>
                                    <span class="play-count">
                                        <?= number_format($song['play_count']) ?> lượt nghe
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
        <?php include('templates/footer.php'); ?>
    </div>
</body>
</html>

<style>
:root {
    --primary-color: #7c3aed;
    --card-bg: rgba(26, 22, 37, 0.6);
    --text-color: #fff;
    --text-secondary: rgba(255, 255, 255, 0.7);
    --shadow-color: rgba(124, 58, 237, 0.2);
}
.most-played {
    padding: 2rem;
    color: var(--text-color);
}

.section-header {
    display: flex
;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.period-filter {
    display: flex;
    gap: 1rem;
}

.period-filter a {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    background: var(--card-bg);     
    color: var(--text-color);
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid rgba(124, 58, 237, 0.2);
}

.period-filter a.active,
.period-filter a:hover {
    background: var(--primary-color);
    color: #fff;
}

.songs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 2rem;
}

.song-card {
    position: relative;
    background: var(--card-bg);
    border-radius: 12px;
    padding: 1rem;
    display: flex
;
    align-items: center;
    gap: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 1px solid rgba(124, 58, 237, 0.1);
    backdrop-filter: blur(10px);
}

.song-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.rank {
    position: absolute;
    top: -10px;
    left: -10px;
    width: 30px;
    height: 30px;
    background: var(--primary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: bold;
    box-shadow: 0 2px 10px var(--shadow-color);
}

.song-card img {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    object-fit: cover;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

.song-info {
    flex: 1;
    overflow: hidden;
}

.song-info h3 {
    margin: 0;
    font-size: 1rem;
    color: var(--text-color);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.song-info .artist {
    color: var(--text-secondary);
    font-size: 0.9rem;
    margin: 0.25rem 0;
}

.play-count {
    font-size: 0.8rem;
    color: var(--text-secondary);
    display: flex   ;
    align-items: center;
    gap: 0.25rem;
}

.content-wrapper {
    margin-left: 260px; /* Match sidebar width */
    padding: 20px;
    min-height: calc(100vh - 160px); /* Account for header and playbar */
}

/* Additional responsive adjustments */
@media (max-width: 1024px) {
    .content-wrapper {
        margin-left: 0;
    }   
}

@media (max-width: 768px) {
    .content-wrapper {
        padding: 15px;
    }

    .section-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .period-filter {
        width: 100%;
        overflow-x: auto;
        padding-bottom: 10px;
    }
    
    .period-filter::-webkit-scrollbar {
        height: 4px;
    }
    
    .songs-grid {
        grid-template-columns: 1fr;
    }
}
</style>
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
