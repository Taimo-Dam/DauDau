<?php
require_once 'includes/db.php';
require_once 'includes/init.php';
$isLoggedIn = isset($_SESSION['user_id']);


//lấy artist từ url
$artist_name = isset($_GET['name']) ? urldecode($_GET['name']) : '';
if (empty($artist_name)) {
    die("<p>Không tìm thấy nghệ sĩ trong URL.</p>");
}

//lấy ra thông tjn nghệ sĩ
try {
    $artist_stmt = $conn->prepare("SELECT name, image_path, profile_url, bio, background_image FROM artists WHERE name = ?");
    $artist_stmt->bind_param("s", $artist_name);
    $artist_stmt->execute();
    $artist_result = $artist_stmt->get_result();
    $artist = $artist_result->fetch_assoc();

} catch (Exception $e) {
    error_log("Lỗi khi lấy thông tin nghệ sĩ: " . $e->getMessage());

}

// Phân trang
$items_per_page = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

try {
    //tổng số bài 
    $count_stmt = $conn->prepare("SELECT COUNT(*) FROM songs WHERE artist = ?");
    $count_stmt->bind_param("s", $artist['name']);
    $count_stmt->execute();
    $total_songs = $count_stmt->get_result()->fetch_row()[0];
    $total_pages = ceil($total_songs / $items_per_page);

    //lấy bài hát cho trang hiện tại
    $stmt = $conn->prepare("SELECT * FROM songs WHERE artist = ? LIMIT ? OFFSET ?");
    $stmt->bind_param("sii", $artist['name'], $items_per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $songs = $result->fetch_all(MYSQLI_ASSOC);

    
} catch (Exception $e) {
    error_log("Lỗi khi lấy bài hát: " . $e->getMessage());
}

// Lấy danh sách các nghệ sĩ khác
try {
$other_artist_stmt = $conn->prepare("SELECT name, image_path, profile_url FROM artists WHERE name != ? ORDER BY RAND() LIMIT 6");
    $other_artist_stmt->bind_param("s", $artist['name']);
    $other_artist_stmt->execute();
    $other_artist_result = $other_artist_stmt->get_result();
    $artists = $other_artist_result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    error_log("Lỗi khi lấy danh sách nghệ sĩ: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars(substr($artist['bio'], 0, 160)); ?>">
    <title><?php echo htmlspecialchars($artist['name']); ?> | MeandYou</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/artist-nghesi.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/jpg" sizes="16x16" href="../images/logo.jpg">
</head>
<body>
     <div class="main-content" id="mainContent">
            <div class="header">
        <?php include('templates/header.php'); ?>
            </div>
        <?php include('modun/sidebar.php'); ?>
        
    <div class="artist-header" style="background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.7)), url('<?php echo htmlspecialchars($artist['background_image']); ?>');">
        <div class="artist-info">
            <h1 class="artist-name"><?php echo htmlspecialchars($artist['name']); ?></h1>
        </div>
    </div>

    <h2>Danh sách bài hát</h2>
    <div class="song-artist">
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
            <img src="<?= htmlspecialchars($song['image_path']) ?>" 
                 alt="<?= htmlspecialchars($song['title']) ?>"
                 onerror="this.src='assets/images/default-song.jpg'">
            <div class="song-info">
                <h3><?= htmlspecialchars($song['title']) ?></h3>
                <p class="artist"><?= htmlspecialchars($song['artist']) ?></p>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
     

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?name=<?php echo urlencode($artist['name']); ?>&page=<?php echo $page - 1; ?>" class="page-link">Previous</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?name=<?php echo urlencode($artist['name']); ?>&page=<?php echo $i; ?>" class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
        <?php if ($page < $total_pages): ?>
            <a href="?name=<?php echo urlencode($artist['name']); ?>&page=<?php echo $page + 1; ?>" class="page-link">Next</a>
        <?php endif; ?>
    </div>
    <h2>Các nghệ sĩ khác</h2>
    <div class="fans-container">
        <?php foreach ($artists as $other_artist): ?>
        <a href="artist-nghesi.php?name=<?= urlencode($other_artist['name']) ?>" class="card">
            <figure>
                <img src="<?php echo htmlspecialchars($other_artist['image_path']); ?>" alt="<?php echo htmlspecialchars($other_artist['name']); ?>">
                <figcaption><?php echo htmlspecialchars($other_artist['name']); ?></figcaption>
            </figure>
        </a>
        <?php endforeach; ?>
    </div>


    <h2>Thông tin nghệ sĩ</h2>
    <div class="artist-info-section">
        <img src="<?php echo htmlspecialchars($artist['image_path']); ?>" alt="<?php echo htmlspecialchars($artist['name']); ?>">
        <span><?php echo htmlspecialchars($artist['bio']); ?></span>
    </div>
<?php include('templates/footer.php'); ?>
</div>
</body>
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