<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$songId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = '';
$success = '';

// Fetch song data
$stmt = $conn->prepare("SELECT * FROM songs WHERE id = ?");
$stmt->bind_param("i", $songId);
$stmt->execute();
$song = $stmt->get_result()->fetch_assoc();

if (!$song) {
    header("Location: manage-songs.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $artist = trim($_POST['artist'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    
    try {
        $updates = [];
        $params = [];
        $types = "";

        // Basic info update
        $updates[] = "title = ?";
        $updates[] = "artist = ?";
        $updates[] = "genre = ?";
        $params[] = $title;
        $params[] = $artist;
        $params[] = $genre;
        $types .= "sss";

        // Handle image update
        if (!empty($_FILES['image']['name'])) {
            $imageDir = '../uploads/images/';
            $imagePath = $imageDir . basename($_FILES['image']['name']);
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                $updates[] = "image_path = ?";
                $params[] = 'uploads/images/' . basename($_FILES['image']['name']);
                $types .= "s";
            }
        }

        // Handle audio update
        if (!empty($_FILES['audio']['name'])) {
            $audioDir = '../uploads/audio/';
            $audioPath = $audioDir . basename($_FILES['audio']['name']);
            
            if (move_uploaded_file($_FILES['audio']['tmp_name'], $audioPath)) {
                $updates[] = "audio_path = ?";
                $params[] = 'uploads/audio/' . basename($_FILES['audio']['name']);
                $types .= "s";
            }
        }

        // Add song ID to params
        $params[] = $songId;
        $types .= "i";

        $sql = "UPDATE songs SET " . implode(", ", $updates) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            $success = "Song updated successfully";
        } else {
            $error = "Failed to update song";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Song - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'includes/admin-header.php'; ?>
    
    <main class="admin-main">
        <section class="edit-song-form">
            <h2>Edit Song: <?= htmlspecialchars($song['title']) ?></h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($song['title']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="artist">Artist</label>
                    <input type="text" id="artist" name="artist" value="<?= htmlspecialchars($song['artist']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="genre">Genre</label>
                    <select id="genre" name="genre" required>
                        <option value="Pop" <?= $song['genre'] === 'Pop' ? 'selected' : '' ?>>Pop</option>
                        <option value="Rock" <?= $song['genre'] === 'Rock' ? 'selected' : '' ?>>Rock</option>
                        <option value="Hip Hop" <?= $song['genre'] === 'Hip Hop' ? 'selected' : '' ?>>Hip Hop</option>
                        <option value="Jazz" <?= $song['genre'] === 'Jazz' ? 'selected' : '' ?>>Jazz</option>
                        <option value="Classical" <?= $song['genre'] === 'Classical' ? 'selected' : '' ?>>Classical</option>
                        <option value="Electronic" <?= $song['genre'] === 'Electronic' ? 'selected' : '' ?>>Electronic</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Current Cover Image</label>
                    <img src="../<?= htmlspecialchars($song['image_path']) ?>" alt="Current cover" class="current-image">
                    <label for="image">Change Cover Image (optional)</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>

                <div class="form-group">
                    <label>Current Audio File</label>
                    <audio controls src="../<?= htmlspecialchars($song['audio_path']) ?>"></audio>
                    <label for="audio">Change Audio File (optional)</label>
                    <input type="file" id="audio" name="audio" accept="audio/*">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn"><i class="fas fa-save"></i> Save Changes</button>
                    <a href="manage-songs.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                </div>
            </form>
        </section>
    </main>

    <?php include 'includes/admin-footer.php'; ?>
    <script src="../assets/js/admin-songs.js"></script>
</body>
</html>