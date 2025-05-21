<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fix the path resolution and set UTF-8
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';
mysqli_set_charset($conn, "utf8mb4");

$searchResults = [
    'songs' => [],
    'artists' => []
];

if (isset($_GET['query'])) {
    try {
        $search = $_GET['query']; // Remove mysqli_real_escape_string as we're using prepared statements
        
        // Search songs with improved Vietnamese character support
        $songsQuery = "SELECT * FROM songs 
                      WHERE title LIKE ? 
                      OR artist LIKE ? 
                      OR LOWER(CONVERT(title USING utf8mb4)) LIKE LOWER(?)
                      OR LOWER(CONVERT(artist USING utf8mb4)) LIKE LOWER(?)
                      ORDER BY 
                        CASE 
                            WHEN artist = ? THEN 1
                            WHEN artist LIKE ? THEN 2
                            ELSE 3
                        END";
        
        $stmt = $conn->prepare($songsQuery);
        $searchParam = "%$search%";
        $stmt->bind_param("ssssss", 
            $searchParam, 
            $searchParam,
            $searchParam,
            $searchParam,
            $search,
            $searchParam
        );
        $stmt->execute();
        $searchResults['songs'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Search artists with improved matching
        $artistsQuery = "SELECT DISTINCT artist, 
                       MIN(image_path) as image_path,
                       COUNT(*) as song_count 
                       FROM songs 
                       WHERE artist LIKE ?
                       OR LOWER(CONVERT(artist USING utf8mb4)) LIKE LOWER(?)
                       GROUP BY artist 
                       ORDER BY 
                        CASE 
                            WHEN artist = ? THEN 1
                            WHEN artist LIKE ? THEN 2
                            ELSE 3
                        END";
        
        $stmt = $conn->prepare($artistsQuery);
        $stmt->bind_param("ssss", 
            $searchParam,
            $searchParam,
            $search,
            $searchParam
        );
        $stmt->execute();
        $searchResults['artists'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
    } catch (Exception $e) {
        error_log("Search error: " . $e->getMessage());
        $searchResults['error'] = 'An error occurred while searching';
    }
}

// Include header
include 'modun/header.php';
?>

<div class="search-results-page">
    <h1>Kết quả tìm kiếm cho "<?= htmlspecialchars($_GET['query'] ?? '') ?>"</h1>

    <?php if (!empty($searchResults['songs'])): ?>
    <section class="songs-section">
        <h2>Bài hát</h2>
        <div class="songs-grid">
            <?php foreach ($searchResults['songs'] as $song): ?>
            <div class="song-card" 
                 data-audio="<?= htmlspecialchars($song['audio_path']) ?>"
                 data-title="<?= htmlspecialchars($song['title']) ?>"
                 data-artist="<?= htmlspecialchars($song['artist']) ?>"
                 data-img="<?= htmlspecialchars($song['image_path']) ?>">
                <div class="play-overlay">
                    <i class='bx bx-play-circle'></i>
                </div>
                <img src="<?= htmlspecialchars($song['image_path']) ?>" 
                     alt="<?= htmlspecialchars($song['title']) ?>">
                <h3><?= htmlspecialchars($song['title']) ?></h3>
                <p><?= htmlspecialchars($song['artist']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($searchResults['artists'])): ?>
    <section class="artists-section">
        <h2>Nghệ sĩ</h2>
        <div class="artists-grid">
            <?php foreach ($searchResults['artists'] as $artist): ?>
            <div class="artist-card">
                <img src="<?= htmlspecialchars($artist['image_path']) ?>" 
                     alt="<?= htmlspecialchars($artist['artist']) ?>">
                <h3><?= htmlspecialchars($artist['artist']) ?></h3>
                <p><?= $artist['song_count'] ?> bài hát</p>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if (empty($searchResults['songs']) && empty($searchResults['artists'])): ?>
    <p class="no-results">Không tìm thấy kết quả cho "<?= htmlspecialchars($_GET['query']) ?>"</p>
    <?php endif; ?>
</div>

<audio id="audio" src=""></audio>
<?php include 'modun/playbar.php'; ?>

<style>
.search-results-page {
    padding: 80px 20px 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.songs-grid, .artists-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    padding: 20px 0;
}

.song-card, .artist-card {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    padding: 15px;
    transition: transform 0.3s ease;
    cursor: pointer;
}

.song-card:hover, .artist-card:hover {
    transform: translateY(-5px);
    background: rgba(255, 255, 255, 0.2);
}

.no-results {
    text-align: center;
    color: #888;
    padding: 40px;
}
</style>