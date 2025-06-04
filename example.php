<?php
require_once 'includes/db.php';

// Get unique artists with their first image
$artists = fetchAll("SELECT DISTINCT artist, 
                    MIN(image_path) as image_path,
                    COUNT(*) as song_count 
                    FROM songs 
                    GROUP BY artist 
                    ORDER BY artist");

// Get featured artists
$featured = fetchAll("SELECT * FROM featured_artists_discover");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/css/discover.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Artists | M&U</title>
</head>
<style>/* Add to existing styles */
body {
    transition: opacity 0.3s ease;
}

.card {
    position: relative;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    overflow: hidden;
}

.card-link {
    text-decoration: none;
    color: var(--text-color);
    display: block;
    padding: 15px;
}

.card img {
    width: 100%;
    aspect-ratio: 1;
    object-fit: cover;
    border-radius: 8px;
    transition: transform 0.3s ease;
}

.card-info {
    padding: 10px 0;
    text-align: center;
}

.artist-name {
    font-size: 1.1rem;
    font-weight: bold;
    margin-bottom: 5px;
    color: var(--text-color);
}

.song-count {
    font-size: 0.9rem;
    color: var(--text-secondary);
}

.card:hover img {
    transform: scale(1.05);
}
</style>
<body>
            <?php include('templates/header.php'); ?>
        <?php include('modun/sidebar.php'); ?>
    <div class="main-content" id="mainContent">

        
        <div class="discover-container">
            <h2 id="discover-section">All Artists</h2>
            <div class="grid">
                <?php foreach ($artists as $artist): ?>
                    <div class="card" data-artist="<?= htmlspecialchars($artist['artist']) ?>">
                        <a href="artist.php?name=<?= htmlspecialchars($artist['artist']) ?>" 
                           class="card-link"
                           onclick="handleArtistClick(event, '<?= htmlspecialchars($artist['artist']) ?>')">
                            <img src="<?= htmlspecialchars($artist['image_path']) ?>" 
                                 alt="<?= htmlspecialchars($artist['artist']) ?>">
                            <div class="card-info">
                                <p class="artist-name"><?= htmlspecialchars($artist['artist']) ?></p>
                                <span class="song-count"><?= $artist['song_count'] ?> songs</span>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
            <?php include('templates/footer.php'); ?>

    </div>

    <script>
        function handleArtistClick(event, artistName) {
            event.preventDefault();
            
            // Store the selected artist in session storage
            sessionStorage.setItem('selectedArtist', artistName);
            
            // Navigate to the artist page with smooth transition
            document.body.style.opacity = '0';
            setTimeout(() => {
                window.location.href = `artist.php?name=${encodeURIComponent(artistName)}`;
            }, 300);
        }

        // Add hover and click effects
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-10px)';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0)';
            });
            
            card.addEventListener('click', () => {
                card.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    card.style.transform = 'scale(1)';
                }, 150);
            });
        });
    </script>
    <?php include('modun/playbar.php'); ?>
</body>
</html>