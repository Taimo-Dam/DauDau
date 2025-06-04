<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Albums | M&U</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/albums.css">
</head>
<body>
    <div class="main-content" id="mainContent">
        <div class="header">
            <?php include('templates/header.php'); ?>
        </div>
        <?php include('modun/sidebar.php'); ?>

        <div class="albums-container">
            <h1>Popular Albums</h1>
            
            <div class="albums-grid">
                <?php
                require_once('includes/db.php');
                
                $sql = "SELECT * FROM albums ORDER BY release_date DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<div class='album-card' data-album-id='{$row['id']}'>";
                        echo "<div class='album-image'>";
                        echo "<img src='{$row['cover_image']}' alt='{$row['title']}'>";
                        echo "<div class='album-overlay'>";
                        echo "<i class='bx bx-play-circle'></i>";
                        echo "</div>";
                        echo "</div>";
                        echo "<div class='album-info'>";
                        echo "<h3>{$row['title']}</h3>";
                        echo "<p>{$row['artist']}</p>";
                        echo "<span class='album-year'>" . date('Y', strtotime($row['release_date'])) . "</span>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p class='no-albums'>No albums found</p>";
                }
                ?>
            </div>
        </div>
    </div>

    <?php include('templates/footer.php'); ?>
</body>
</html>