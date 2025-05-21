<?php
// Start the session at the very beginning
session_start();

require_once 'includes/db.php';

try {
    // Fetch songs from database
    $stmt = $conn->prepare("SELECT * FROM songs WHERE artist = 'Sơn Tùng MTP'");
    $stmt->execute();
    $result = $stmt->get_result();
    $songs = $result->fetch_all(MYSQLI_ASSOC);

    if (empty($songs)) {
        echo "No songs found for Sơn Tùng MTP";
    }
} catch(Exception $e) {
    die("Error fetching songs: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sơn Tùng MTP </title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #121212;
            color: #fff;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Container Play Styles */
        .container-play {
            position: fixed;
            width: 100%;
            height: 80px;
            background: #282828;
            /* Darker background */
            bottom: 0;
            padding: 0 20px;
            display: none;
            box-shadow: 0px -2px 10px rgba(0, 0, 0, 0.5);
            /* Add shadow for depth */
        }

        .container-play.show {
            display: block;
        }

        /* Player Styles */
        .player {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Info Styles */
        .info {
            display: flex;
            align-items: center;
            gap: 15px;
            flex: 1;
        }

        .cover {
            height: 60px;
            width: 60px;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            /* Add shadow for depth */
        }

        .cover>img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            padding: 0;
        }

        .info>div {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .info>div span:first-child {
            font-weight: bold;
            color: #fff;
            /* White text */
        }

        .info>div span:last-child {
            font-size: 0.9em;
            color: #b3b3b3;
            /* Light grey text */
        }

        /* Controls Styles */
        .controls {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 2;
            gap: 12px;
        }

        .controls-button {
            display: flex;
            align-items: center;
            gap: 32px; /* Increased spacing between buttons */
        }

        .controls-button > button {
            background: transparent;
            border: none;
            border-radius: 50%;
            width: 44px; /* Slightly larger base size */
            height: 44px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            color: #b3b3b3;
            font-size: 28px; /* Larger default icon size */
        }

        .controls-button > button:hover {
            color: #fff;
            transform: scale(1.1);
            background: rgba(255, 255, 255, 0.1); /* Subtle hover effect */
        }

        /* Make play/pause button larger */
        .controls-button > #play {
            width: 54px; /* Larger size for main play button */
            height: 54px;
            font-size: 38px; /* Larger icon for play/pause */
            color: #fff; /* Always white */
        }

        .controls-button > #play:hover {
            transform: scale(1.08);
            background: rgba(255, 255, 255, 0.2);
        }

        /* Style progress bar */
        .controls-slider {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 0 8px;
        }

        .controls-slider > input {
            width: 100%;
            height: 5px;
            background: #4d4d4d;
            border-radius: 3px;
            outline: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .controls-slider > input:hover {
            height: 6px;
        }

        .controls-slider > input::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 14px;
            height: 14px;
            background: #fff;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            opacity: 0;
        }

        .controls-slider > input:hover::-webkit-slider-thumb {
            opacity: 1;
        }

        /* Time display */
        .controls-slider > span {
            font-size: 13px;
            color: #b3b3b3;
            min-width: 45px;
            text-align: center;
        }

        /* Volume Styles */
        .controls-volume {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            justify-content: flex-end;
            padding-right: 20px;
            min-width: 150px;
        }

        #progress {
            width: 100%;
        }

        #volume {
            width: 100px;
            height: 4px;
            background: #555;
            /* Dark volume background */
            border-radius: 2px;
            outline: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            cursor: pointer;
        }

        #volume::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 12px;
            height: 12px;
            background: #fff;
            /* White thumb */
            border-radius: 50%;
            cursor: pointer;
        }

        #volume-icon {
            color: #fff;
            /* White volume icon */
            font-size: 20px;
        }

        .song-artitst {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
            padding: 20px;
            background: #1e1e1e;
            height: 80vh;
            overflow-y: auto;
            align-items: start;
        }

        .song-card {
            position: relative;
            background-color: #282828;
            /* Darker card background */
            min-width: 170px;
            width: 190px;
            height: 230px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            /* Stronger shadow for depth */
            padding: 10px;
            text-align: center;
            overflow: hidden;
            transition: transform 0.3s ease;
            /* Smooth transition for hover effect */
        }

        .song-card:hover {
            transform: translateY(-5px);
            /* Slight lift on hover */
        }

        .song-card img {
            width: 100%;
            height: 150px;
            border-radius: 8px;
            object-fit: cover;
            margin-bottom: 10px;
            /* Space below the image */
            transition: opacity 0.3s ease;
            /* Smooth transition for opacity */
        }

        .song-card:hover img {
            opacity: 0.6;
            /* Dim the image on hover */
        }

        .song-card p {
            color: #fff;
            /* White text color */
            font-size: 1em;
            margin: 5px 0;
        }

        .play-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            background-color: rgba(0, 0, 0, 0.7);
            /* Semi-transparent background */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 2;
        }

        .play-overlay i {
            color: #fff;
            /* White play icon */
            font-size: 24px;
            /* Smaller icon size */
        }

        .song-card:hover .play-overlay {
            opacity: 1;
            /* Show overlay on hover */
        }

        /* Replace the existing artist-header related styles with these */
        .artist-header {
            position: relative;
            background-image: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.7)), 
                              url('images/sontungmtp/sontung-avatar.jfif');
            background-size: cover;
            background-position: center 25%;
            padding: 40px 24px;
            min-height: 400px;
            display: flex;
            align-items: flex-end;
        }

        .artist-info {
            position: relative;
            z-index: 2;
        }

        .artist-name {
            font-size: 96px;
            font-weight: 900;
            color: #fff;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
            margin: 0;
            letter-spacing: -2px;
        }

        /* Remove these styles if they exist */
        .artist-image,
        .artist-type,
        .artist-stats {
            display: none;
        }
    </style>
    <head>
        <?php include 'modun/header.php'; ?>
    </head>

<body>

    <div class="artist-header">
        <div class="artist-info">
            <h1 class="artist-name">Sơn Tùng M-TP</h1>
        </div>
    </div>

   <div class="song-artitst">
        <?php foreach ($songs as $song): ?>
        <div class="song-card" 
            data-audio="<?php echo htmlspecialchars($song['audio_path']); ?>"
            data-song-name="<?php echo htmlspecialchars($song['title']); ?>" 
            data-artist="<?php echo htmlspecialchars($song['artist']); ?>"
            data-img="<?php echo htmlspecialchars($song['image_path']); ?>">
            <div class="play-overlay">
                <i class='bx bx-play-circle'></i>
            </div>
            <img src="<?php echo htmlspecialchars($song['image_path']); ?>" alt="<?php echo htmlspecialchars($song['title']); ?>">
            <p><?php echo htmlspecialchars($song['title']); ?></p>
        </div>
        <?php endforeach; ?>
    </div>
    <audio id="audio" src=""></audio>
    <?php include 'modun/playbar.php'; ?>


</body>
</html>