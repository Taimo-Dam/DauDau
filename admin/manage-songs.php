<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
// Check admin role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle song actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'delete':
                if (isset($_POST['song_id'])) {
                    $songId = intval($_POST['song_id']);
                    $stmt = $conn->prepare("DELETE FROM songs WHERE id = ?");
                    $stmt->bind_param("i", $songId);
                    $stmt->execute();
                }
                break;
        }
    }
}

// Fetch songs
$songs = $conn->query("
    SELECT s.*, COUNT(p.id) as play_count 
    FROM songs s 
    LEFT JOIN listening_history p ON s.id = p.song_id 
    GROUP BY s.id 
    ORDER BY s.created_at DESC
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Songs - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/jpg" sizes="16x16" href="../images/logo.jpg">
    <link src="images" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css">
</head>
<body>
    <?php include 'includes/admin-header.php'; ?>
    
    <main class="admin-main">
        <section class="manage-songs">
            <h2>Manage Songs</h2>
            
            <div class="add-song-section">
                <a href="add-song.php" class="btn"><i class="fas fa-plus-circle"></i> Add New Song</a>
            </div>

            <table class="songs-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Artist</th>
                        <th>Genre</th>
                        <th>Plays</th>
                        <th>Added On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($songs as $song): ?>
                    <tr>
                        <td>
    <img src="<?= file_exists('../' . $song['image_path']) 
        ? '../' . htmlspecialchars($song['image_path']) 
        : '../assets/images' ?>"
        alt="<?= htmlspecialchars($song['title']) ?>" 
        class="song-thumb">
                            <?= htmlspecialchars($song['title']) ?>
                        </td>
                        <td><?= htmlspecialchars($song['artist']) ?></td>
                        <td><?= htmlspecialchars($song['genre']) ?></td>
                        <td><?= number_format($song['play_count']) ?></td>
                        <td><?= date('Y-m-d', strtotime($song['created_at'])) ?></td>
                        <td>
                            <button onclick="editSong(<?= $song['id'] ?>)" class="btn-small">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteSong(<?= $song['id'] ?>)" class="btn-small delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <?php include 'includes/admin-footer.php'; ?>
    
    <script src="../assets/js/admin-songs.js"></script>
</body>
</html>