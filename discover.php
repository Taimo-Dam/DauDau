<?php
require_once 'includes/db.php';
// Lấy genres
$genres = fetchAll("SELECT * FROM genres_discover"); //là 1 hàm đn~ trong db.php, thực thi truy vấn và trả về kq truy vấn thường là mảng
if (!$genres && isset($conn->error)) {
    echo "Error genres: " . $conn->error;
}

// Lấy nghệ sĩ / playlist nổi bật
$featured = fetchAll("SELECT * FROM artists ORDER BY RAND() LIMIT 4");
if (!$featured && isset($conn->error)) {
    echo "Error featured: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/css/discover.css">
    <title>Discover</title>
</head>
<body>
    <div class="main-content" id="mainContent">
        <?php include('templates/header.php'); ?>
        <?php include('modun/sidebar.php'); ?>
        
        <div class="discover-container">
            <h2 id="discover-section">Music Genres</h2>
            <div class="grid">
                <?php foreach ($genres as $genre): ?>
                    <div class="card" data-genre="<?= htmlspecialchars($genre['name']) ?>">
                        <a href="genre.php?name=<?= htmlspecialchars($genre['name']) ?>" 
                           class="card-link"
                           onclick="handleGenreClick(event, '<?= htmlspecialchars($genre['name']) ?>')">
                            <img src="<?= htmlspecialchars($genre['image_path']) ?>" 
                                 alt="<?= htmlspecialchars($genre['name']) ?>">
                            <p><?= htmlspecialchars($genre['name']) ?></p>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <h2>Nghệ Sĩ Nổi Bật</h2>
            <div class="grid">
                <?php foreach ($featured as $item): ?>
            <a href="artist-nghesi.php?name=<?= urlencode($item['name']) ?>" class="card"> 
                <img src="<?= ($item['image_path']) ?>" alt="<?= ($item['name']) ?>">
                <p><?= ($item['name']) ?></p>
            </a> 
        <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>