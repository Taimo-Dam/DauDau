<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Fetch songs from database
try {
    // Fetch all songs
    $stmt = $conn->prepare("SELECT * FROM songs ORDER BY title ASC");
    $stmt->execute();
    $songs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Fetch featured artists
    $stmt = $conn->prepare("SELECT DISTINCT artist FROM songs LIMIT 5");
    $stmt->execute();
    $artists = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="vi">
<body>
    <div class="container">
        <!-- Bên trái: Bài nhạc chính -->
        <div class="main-song">
            <div class="container-img-main" data-audio="<?= htmlspecialchars($songs[0]['audio_path'] ?? '') ?>">
                <div class="play-overlay">
                    <div class="play-button playing"></div>
                </div>
                <img src="<?= htmlspecialchars($songs[0]['image_path'] ?? 'images/default.jpg') ?>" alt="Bìa nhạc">
            </div>
            <h2><?= htmlspecialchars($songs[0]['title'] ?? 'No Title') ?></h2>
            <p><?= htmlspecialchars($songs[0]['artist'] ?? 'Unknown Artist') ?></p>
            <button class="random-button">Phát ngẫu nhiên</button>
        </div>

        <!-- Bên phải: Danh sách nhạc và nghệ sĩ -->
        <div class="right-panel">
            <div class="song-list">
                <h3>Danh sách bài hát</h3>
                <?php foreach ($songs as $song): ?>
                <div class="song-item" 
                     data-audio="<?= htmlspecialchars($song['audio_path']) ?>"
                     data-title="<?= htmlspecialchars($song['title']) ?>"
                     data-artist="<?= htmlspecialchars($song['artist']) ?>"
                     data-cover="<?= htmlspecialchars($song['image_path']) ?>">
                    <div class="song-info">
                        <div class="container-img-list">
                            <div class="play-overlay-list">
                                <div class="play-button-list"></div>
                            </div>
                            <img src="<?= htmlspecialchars($song['image_path']) ?>" alt="<?= htmlspecialchars($song['title']) ?>">
                        </div>
                        <span><?= htmlspecialchars($song['title']) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <audio id="audio" src=""></audio>

    <div class="container-bottom">
        <h3>Nghệ sĩ nổi bật</h3>
        <div class="artist-images">
            <?php foreach ($artists as $artist): ?>
            <div class="container-artist__conten">
                <img src="<?= htmlspecialchars($artist['image_path'] ?? 'images/sontung-avatar.jfif') ?>">
                <span><?= htmlspecialchars($artist['artist']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
    <!-- Add BoxIcons and script properly -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const audio = document.getElementById('audio');
    const songItems = document.querySelectorAll('.song-item');
    let currentlyPlaying = null;

    songItems.forEach(item => {
        item.addEventListener('click', function() {
            const audioPath = this.dataset.audio;
            const title = this.dataset.title;
            const artist = this.dataset.artist;
            const image = this.dataset.image;

            // Update play bar info
            document.querySelector('.track-name').textContent = title;
            document.querySelector('.track-artist').textContent = artist;
            document.querySelector('.track-art img').src = image;

            // Handle playing/pausing
            if (currentlyPlaying === this && !audio.paused) {
                audio.pause();
                this.querySelector('.play-button-list').classList.remove('playing');
            } else {
                if (currentlyPlaying) {
                    currentlyPlaying.querySelector('.play-button-list').classList.remove('playing');
                }
                audio.src = audioPath;
                audio.play();
                this.querySelector('.play-button-list').classList.add('playing');
                currentlyPlaying = this;
            }

            // Update play bar controls
            document.querySelector('.playpause-track').classList.toggle('active', !audio.paused);
        });
    });

    // Random button functionality
    document.querySelector('.random-button').addEventListener('click', function() {
        const randomIndex = Math.floor(Math.random() * songItems.length);
        songItems[randomIndex].click();
    });
});
</script>
<style>
        body {
            padding-top: 50px;
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #1a1a2e, #16213e);
            color: #fff;
        }

        .container {
            display: flex;
            flex-direction: row;
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .main-song {
            width: 30%;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease;
        }

        .main-song:hover {
            transform: translateY(-5px);
        }

        .main-song:hover .play-overlay,
        .main-song:hover .container-img-main img {
            opacity: 1;
        }

        .container-img-main {
            position: relative;
            width: 100%;
            height: 60%;
            overflow: hidden;
            border-radius: 10px;
        }

        .container-img-main img {
            width: 100%;
            border-radius: 10px;
            transition: opacity 0.3s ease;
            opacity: 1;
            /* Trạng thái mặc định */
        }

        .main-song:hover .container-img-main img {
            opacity: 0.5;
            /* Hiệu ứng mờ khi hover */
        }

        .play-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60px;
            height: 60px;
            background-color: rgba(0, 0, 0, 0.6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            /* Ẩn nút play mặc định */
            transition: opacity 0.3s ease;
            z-index: 2;
        }

        .main-song:hover .play-overlay {
            opacity: 1;
            /* Hiển thị nút play khi hover */
        }

        .play-button {
            position: absolute;
            top: 50%;
            left: 55%;
            transform: translate(-50%, -50%);
            width: 0;
            height: 0;
            border-left: 23px solid white;
            border-top: 12px solid transparent;
            border-bottom: 12px solid transparent;
        }

        /* dấu 2 gạch */
        .play-button.playing {
            width: 23px;
            height: 14px;
            border: none;
            background: none;
            position: absolute;
            left: 60%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: transparent;
        }

        .play-button.playing::before,
        .play-button.playing::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 8px;
            height: 18px;
            background-color: white;
        }

        .play-button.playing::before {
            left: 0;
        }

        .play-button.playing::after {
            right: 0;
        }

        .random-button {
            padding: 12px 24px;
            border-radius: 25px;
            background: linear-gradient(45deg, #9147ff, #3e1487);
            border: none;
            color: white;
            margin-top: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .random-button:hover {
            background: linear-gradient(45deg, #3e1487, #9147ff);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(145, 152, 229, 0.3);
        }

        .right-panel {
            width: 70%;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .song-list {
            background: rgba(26, 26, 46, 0.9);
            padding: 20px;
            border-radius: 15px;
            height: 450px;
            overflow-y: scroll;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        .song-item {
            position: relative;
            display: flex;
            align-items: center;
            padding: 15px;
            cursor: pointer;
            border-radius: 8px;
            margin-bottom: 8px;
            background: rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .song-item:hover {
            background: rgba(145, 152, 229, 0.15);
            transform: translateX(5px);
        }

        .song-info {
            display: flex;
            align-items: center;
            flex: 1;
            position: relative;
        }

        .song-info img {
            width: 40px;
            height: 40px;
            border-radius: 5px;
            margin-right: 10px;
            object-fit: cover;
            position: relative;
            z-index: 1;
        }

        /* Hiệu ứng hover cho ảnh trong danh sách nhạc */
        .container-img-list {
            position: relative;
            width: 40px;
            height: 40px;
        }

        .container-img-list img {
            width: 100%;
            height: 100%;
            border-radius: 5px;
            object-fit: cover;
            transition: opacity 0.3s ease;
            opacity: 1;
            /* Trạng thái mặc định */
        }

        .song-item:hover .container-img-list img {
            opacity: 0.5;
            /* Hiệu ứng mờ khi hover */
        }

        .play-overlay-list {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 15px;
            height: 15px;
            background-color: rgba(0, 0, 0, 0.6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            /* Ẩn nút play mặc định */
            transition: opacity 0.3s ease;
            z-index: 2;
        }



        .song-item:hover .play-overlay-list {
            opacity: 1;
            /* Hiển thị nút play khi hover */
        }

        .play-button-list {
            width: 0;
            height: 0;
            border-left: 6px solid white;
            border-top: 4px solid transparent;
            border-bottom: 4px solid transparent;
        }

        .container-bottom h3 {
            text-align: center;
            margin-top: 20px;
        }


        .artist-images {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin: 20px auto;
        }

        .container-artist__conten {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin: 15px;
            background: rgba(26, 26, 46, 0.9);
            padding: 20px;
            border-radius: 15px;
            width: 180px;
            height: 220px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .container-artist__conten:hover {
            transform: translateY(-10px);
            background: rgba(145, 152, 229, 0.15);
        }

        .container-artist__conten img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 10px;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .container-artist__conten:hover img {
            border-color: rgba(145, 152, 229, 0.5);
            transform: scale(1.05);
        }

        .container-artist__conten span {
            margin-top: 10px;
            font-weight: 500;
            color: #fff;
            opacity: 0.9;
        }

        .genres {
            margin-top: 30px;
        }

        .genres h3 {
            margin-bottom: 10px;
        }

        .genre-tag {
            display: inline-block;
            background-color: #3d3d5c;
            padding: 8px 12px;
            border-radius: 20px;
            margin: 5px;
        }

        .genre-tag:hover {
            background-color: #57577e;
            cursor: pointer;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background: rgba(26, 26, 46, 0.9);
            margin-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Custom scrollbar for song-list */
        .song-list::-webkit-scrollbar {
            width: 8px;
        }

        .song-list::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        .song-list::-webkit-scrollbar-thumb {
            background: rgba(145, 152, 229, 0.5);
            border-radius: 4px;
        }

        .song-list::-webkit-scrollbar-thumb:hover {
            background: rgba(145, 152, 229, 0.7);
        }

            .play-button-list.playing {
        width: 12px;
        height: 12px;
        border: none;
        position: relative;
        }

        .play-button-list.playing::before,
        .play-button-list.playing::after {
            content: '';
            position: absolute;
            width: 3px;
            height: 12px;
            background-color: white;
        }

        .play-button-list.playing::before {
            left: 2px;
        }

        .play-button-list.playing::after {
            right: 2px;
        }
        .play-button-list.playing::before,
        .play-button-list.playing::after {
            border-radius: 2px;
        }

        .play-button-list.playing::before {
            transform: rotate(45deg);
        }

        .play-button-list.playing::after {
            transform: rotate(-45deg);
        }
</style>