<?php

require_once __DIR__ . '/../includes/init.php';

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/playbar.css">
</head>
<body>
    <!-- ADD THIS AUDIO ELEMENT -->
    <audio id="audio" preload="metadata"></audio>
    
    <div class="container-play">
        <div class="player">
            <div class="info">
                <div class="cover">
                    <img src="" class="cover" alt="Bìa bài hát" id="cover-image">
                </div>
                <div>
                    <span id="song-name"></span>
                    <span id="song-artist"></span>
                </div>
            </div>
            <div class="controls">
                <div class="controls-button">
                    <button class="btn" id="rewind"><i class='bx bx-rewind'></i></button>
                    <button class="btn" id="prev"><i class='bx bx-chevrons-left'></i></button>
                    <button class="btn" id="play"><i class='bx bx-pause-circle'></i></button>
                    <button class="btn" id="next"><i class='bx bx-chevrons-right'></i></button>
                    <button class="btn" id="fast-forward"><i class='bx bx-fast-forward'></i></button>
                </div>
                <div class="controls-slider">
                    <span id="current">00:00</span>
                    <input type="range" id="progress" value="0" min="0" max="100">
                    <span id="duration">00:00</span>
                </div>
            </div>
            <div class="controls-volume">
                <i class='bx bx-volume-full' id="volume-icon"></i>
                <input type="range" id="volume" min="0" max="1" step="0.01" value="1">
            </div>
        </div>
    </div>
    
    <script src="../assets/js/playMusic.js"></script>
</body>
</html>