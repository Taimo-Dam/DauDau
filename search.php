<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Initialize variables
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$results = [];
$searchError = '';

if (!empty($query)) {
    try {
        // Debug: Print connection status
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        $searchTerm = "%" . $conn->real_escape_string($query) . "%";
        
        $sql = "SELECT *
            FROM songs
            WHERE LOWER(title) LIKE LOWER(?)
            OR LOWER(artist) LIKE LOWER(?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ss", $searchTerm, $searchTerm);

        // Debug: Print actual query
        error_log("Search query: " . str_replace('?', "'$searchTerm'", $sql));

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("Get result failed: " . $stmt->error);
        }

        $results = $result->fetch_all(MYSQLI_ASSOC);

        // Debug: Print result count
        error_log("Found " . count($results) . " results for query: $query");

    } catch (Exception $e) {
        $searchError = "Search error: " . $e->getMessage();
        error_log($searchError);
    }
}

include __DIR__ . '/templates/header.php';
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="assets/css/sidebar.css" />
    <link rel="stylesheet" href="assets/css/playbar.css" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/search.css">
</head>
<body>

    <div class="main-content" id="mainContent">
            <div class="header">
        <?php include('templates/header.php'); ?>
    </div>
        <?php include('modun/sidebar.php'); ?>
<div class="search-results-container">
    <h2>Search Results for "<?= htmlspecialchars($query) ?>"</h2>

    <?php if ($searchError): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($searchError) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($results)): ?>
        <div class="hot-songs search">
                        <?php foreach ($results as $result): ?>
                <div class="song-card" 
                    data-audio="<?php echo htmlspecialchars($result['audio_path']); ?>"
                    data-song-name="<?php echo htmlspecialchars($result['title']); ?>" 
                    data-artist="<?php echo htmlspecialchars($result['artist']); ?>"
                    data-img="<?php echo htmlspecialchars($result['image_path']); ?>">
                    <div class="play-overlay">
                        <i class='bx bx-play-circle'></i>
                    </div>
                    <img src="<?php echo htmlspecialchars($result['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($result['title']); ?>">
                    <p><?php echo htmlspecialchars($result['title']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
    <?php else: ?>
        <div class="no-results">
            <i class='bx bx-search-alt-2'></i>
            <p>No results found for "<?= htmlspecialchars($query) ?>"</p>
            <small>Try different keywords please!</small>
        </div>
    <?php endif; ?>

        <audio id="audio" src=""></audio>
    <?php include('modun/playbar.php'); ?>
</div>

<?php include __DIR__ .('/templates/footer.php'); ?>
</div>
</body>