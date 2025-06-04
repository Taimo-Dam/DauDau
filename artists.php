<?php
require_once 'includes/init.php';

try {
    // Get total artists count
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM artists");
    $stmt->execute();
    $total_records = $stmt->get_result()->fetch_assoc()['total'];

    // Pagination setup
    $limit = 10;
    $current_page = max(1, $_GET['page'] ?? 1);
    $total_page = ceil($total_records / $limit);
    $current_page = min($current_page, $total_page);
    $start = ($current_page - 1) * $limit;

    // Get artists for current page
    $stmt = $conn->prepare("SELECT * FROM artists LIMIT ?, ?");
    $stmt->bind_param("ii", $start, $limit);
    $stmt->execute();
    $artists = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    error_log("Error in artists.php: " . $e->getMessage());
    $error = "An error occurred while loading artists";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist | MeandYou</title>
    <link rel="stylesheet" href="assets/css/artists.css">
</head>
<body>
    <?php 
    include('templates/header.php');
    ?>

 <div class="main-content" id="mainContent">        
    <?php include('modun/sidebar.php'); ?>
        <h2>Danh sách nghệ sĩ</h2>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php else: ?>
            <div class="artist" id="artist-list">
                <?php foreach ($artists as $artist): ?>
                    <a href="artist-nghesi.php?name=<?= urlencode($artist['name']) ?>" class="card">
                        <img src="<?= htmlspecialchars($artist['image_path'] ?: 'assets/images/default-artist.jpg') ?>" 
                             alt="<?= htmlspecialchars($artist['name']) ?>"
                             onerror="this.src='assets/images/default-artist.jpg'">
                        <p><?= htmlspecialchars($artist['name']) ?></p>
                    </a>
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
    </main>

    <?php include('templates/footer.php'); ?>
    </div>
</body>
</html>
