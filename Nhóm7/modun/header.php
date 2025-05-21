<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Use __DIR__ for reliable path resolution
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

// Debug login state
error_log('Session data: ' . print_r($_SESSION, true));
error_log('IsLoggedIn: ' . (isLoggedIn() ? 'true' : 'false'));

// Function to get user details
function getUserDetails() {
    if (isLoggedIn()) {
        global $conn;
        $userId = $_SESSION['user_id'];
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    return null;
}

// Get user details if logged in
$user = null;
if (isLoggedIn()) {
    $user = getUserDetails();
}

// Handle search functionality
$searchResults = [];
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    
    // Search in songs
    $songsQuery = "SELECT * FROM songs WHERE title LIKE '%$search%' LIMIT 5";
    $songsResult = mysqli_query($conn, $songsQuery);
    
    // Search in artists
    $artistsQuery = "SELECT * FROM artists WHERE name LIKE '%$search%' LIMIT 5";
    $artistsResult = mysqli_query($conn, $artistsQuery);
    
    // Search in albums
    $albumsQuery = "SELECT * FROM albums WHERE title LIKE '%$search%' LIMIT 5";
    $albumsResult = mysqli_query($conn, $albumsQuery);
    
    // Combine results
    $searchResults = [
        'songs' => mysqli_fetch_all($songsResult, MYSQLI_ASSOC),
        'artists' => mysqli_fetch_all($artistsResult, MYSQLI_ASSOC),
        'albums' => mysqli_fetch_all($albumsResult, MYSQLI_ASSOC)
    ];
}

// Get recent playlists for sidebar
$recentPlaylists = [];
if (isLoggedIn()) {
    $userId = $_SESSION['user_id'];
    $playlistsQuery = "SELECT * FROM playlists WHERE user_id = $userId ORDER BY created_at DESC LIMIT 5";
    $playlistsResult = mysqli_query($conn, $playlistsQuery);
    $recentPlaylists = mysqli_fetch_all($playlistsResult, MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ M&U</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
    /* Search Bar */
    .search-container {
        position: relative;
        display: flex;
        align-items: center;
        margin-top: 10px;
    }

    .search-input {
        width: 300px;
        padding: 10px 20px;
        border-radius: 25px;
        border: none;
        background-color: #333;
        color: #fff;
        font-size: 16px;
        padding-left: 40px;
        margin-right: 450px;
    }

    .search-input::placeholder {
        color: #bbb;
    }

    .search-icon {
        position: absolute;
        left: 10px;
        color: #bbb;
        font-size: 20px;
    }

    /* Search Results Dropdown */
    .search-results {
        position: absolute;
        top: 50px;
        left: 0;
        width: 350px;
        background-color: #2d2d2d;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        z-index: 1000;
        display: none;
        overflow: hidden;
    }

    .search-results.show {
        display: block;
    }

    .result-section {
        padding: 10px;
    }

    .result-section h3 {
        color: #ff00ff;
        margin: 5px 0;
        font-size: 14px;
        text-transform: uppercase;
    }

    .result-item {
        padding: 8px 15px;
        display: flex;
        align-items: center;
        color: #fff;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .result-item:hover {
        background-color: rgba(255, 0, 255, 0.2);
    }

    .result-item img {
        width: 40px;
        height: 40px;
        border-radius: 5px;
        margin-right: 10px;
        object-fit: cover;
    }

    .view-all {
        display: block;
        text-align: center;
        padding: 10px;
        color: #00ffff;
        text-decoration: none;
        background-color: #242424;
        transition: all 0.3s ease;
    }

    .view-all:hover {
        background-color: #333;
    }

    .result-info {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .result-title {
        font-weight: bold;
        margin-bottom: 2px;
    }

    .no-results {
        padding: 20px;
        text-align: center;
        color: #888;
    }

    .result-item small {
        color: #888;
        font-size: 12px;
    }

    header {
        background-color: #1e1e1e;
        padding: 10px 20px;
        display: flex;
        position: fixed;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        z-index: 998;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
    }

    .header-left {
        font-size: 24px;
        color: white;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 30px;
    }

    /* Header Right */
    .header-right {
        position: relative;
        display: flex;
        justify-content: space-between;
        padding: 10px;
    }

    .header-right nav {
        display: flex;
        align-items: center;
        gap: 20px;  /* Space between items */
    }   

    .header-right nav a {
        color: white;
        text-decoration: none;
        white-space: nowrap;  /* Prevent text wrapping */
    }

    .logo {
        font-size: 30px;
        font-weight: bold;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    nav a {
        margin: 0 10px;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    nav a:hover {
        color: #ff00ff;
    }

    /* User Profile Dropdown */
    .user-profile {
        position: relative;
        display: flex;
        align-items: center;
        cursor: pointer;
        margin-left: 20px;  /* Space between nav items and profile */
    }

    .profile-img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 10px;
        border: 2px solid #ff00ff;
    }

    .profile-dropdown {
        position: absolute;
        top: 50px;
        right: 0;
        width: 200px;
        background-color: #2d2d2d;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        padding: 10px 0;
        display: none;
        z-index: 1000;
    }

    .profile-dropdown.show {
        display: block;
    }

    .profile-dropdown a {
        display: block;
        padding: 10px 20px;
        color: #fff;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .profile-dropdown a:hover {
        background-color: rgba(255, 0, 255, 0.2);
    }

    .profile-dropdown .logout {
        border-top: 1px solid #444;
        color: #ff5555;
    }

    /* From Uiverse.io by cssbuttons-io */
    .button {
        --glow-color: rgb(217, 176, 255);
        --glow-spread-color: rgba(191, 123, 255, 0.781);
        --enhanced-glow-color: rgb(231, 206, 255);
        --btn-color: rgb(100, 61, 136);
        border: .25em solid var(--glow-color);
        padding: 1em 2em;
        color: var(--glow-color);
        font-size: 15px;
        font-weight: bold;
        background-color: var(--btn-color);
        border-radius: 1em;
        outline: none;
        box-shadow: 0 0 1em .25em var(--glow-color),
               0 0 4em 1em var(--glow-spread-color),
               inset 0 0 .75em .25em var(--glow-color);
        text-shadow: 0 0 .5em var(--glow-color);
        position: relative;
        transition: all 0.3s;
    }
   
    .button::after {
        pointer-events: none;
        content: "";
        position: absolute;
        top: 120%;
        left: 0;
        height: 100%;
        width: 100%;
        background-color: var(--glow-spread-color);
        filter: blur(2em);
        opacity: .7;
        transform: perspective(1.5em) rotateX(35deg) scale(1, .6);
    }
   
    .button:hover {
        color: var(--btn-color);
        background-color: var(--glow-color);
        box-shadow: 0 0 1em .25em var(--glow-color),
               0 0 4em 2em var(--glow-spread-color),
               inset 0 0 .75em .25em var(--glow-color);
    }
   
    .button:active {
        box-shadow: 0 0 0.6em .25em var(--glow-color),
               0 0 2.5em 2em var(--glow-spread-color),
               inset 0 0 .5em .25em var(--glow-color);
    }

    .button1 {
        background-color: #9400D3;
        padding: 10px 20px;
        color: black;
        font-size: 18px;
        border: none;
        cursor: pointer;
        border-radius: 30px;
        margin: 0px 5px;
    }

    .button2 {
        background-color: #121212;
        padding: 10px 20px;
        color: rgb(51, 223, 242);
        font-size: 18px;
        border: 2px solid rgb(51, 223, 242);
        cursor: pointer;
        border-radius: 30px;
        margin: 0px 5px;
    }

    .header-right .button {
        margin-left: 10px;  /* Space between login and signup button */
        padding: 8px 16px;  /* Smaller padding for better alignment */
        height: auto;      /* Remove fixed height if any */
    }

    .menu-toggle {
        position: fixed;
        top: 40px;
        right: 10px;
        font-size: 24px;
        color: white;
        cursor: pointer;
        z-index: 1000;
    }

    .sidebar {
        position: fixed;
        top: 0;
        right: -400px;
        width: 260px;
        height: 100vh;
        background: #2d2d2d;
        box-shadow: -5px 0 10px rgba(255, 0, 255, 0.5);
        transition: 0.3s ease-in-out;
        display: flex;
        flex-direction: column;
        z-index: 999;
        overflow-y: auto;
    }

    .sidebar.open {
        right: 0;
    }

    .sidebar-content {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .logo {
        font-size: 24px;
        font-weight: bold;
        color: #ff00ff;
        text-shadow: 0 0 10px rgba(255, 0, 255, 0.8);
        background: linear-gradient(to right, #ff00ff, #00ffff);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-top: 10px;
    }

    .logo span {
        color: #00ffff;
    }

    .group-title {
        color: #ff00ff;
        font-size: 12px;
        text-transform: uppercase;
        font-weight: bold;
        padding: 5px 15px 5px 15px;
        letter-spacing: 1px;
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
        margin: 0;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .sidebar ul li {
        margin: 5px 0;
    }

    .sidebar ul li {
        position: relative;
        width: 100%;
        margin: 0 5px;
        list-style: none;
        line-height: 15px;
    }

    .sidebar ul li a {
        display: flex;
        align-items: center;
        color: #fff;
        text-decoration: none;
        padding: 5px 10px; /* Reduced padding */
        border-radius: 5px;
        transition: all 0.3s ease;
        font-size: 14px; /* Smaller font */
        height: 35px; /* Fixed compact height */
    }

    .sidebar ul li a i {
        margin-right: 5px;
        font-size: 20px;
    }

    .sidebar ul li a:hover {
        background: rgba(255, 0, 255, 0.3);
        color: #ff00ff;
        box-shadow: 0px 0px 10px rgba(255, 0, 255, 0.7);
    }

    .sidebar ul li a.active {
        background: rgba(255, 0, 255, 0.2);
        color: #ff00ff;
    }

    .sidebar ul li a.blue {
        color: #00ffff;
    }

    .sidebar ul li a.blue:hover {
        background: rgba(0, 255, 255, 0.3);
        box-shadow: 0px 0px 10px rgba(0, 255, 255, 0.7);
    }

    .sidebar ul li a.red {
        color: #ff00ff;
    }

    .sidebar ul li a.red:hover {
        background: rgba(255, 0, 255, 0.3);
        box-shadow: 0px 0px 10px rgba(255, 0, 255, 0.7);
    }

    /* Current playing bar in sidebar 
    .now-playing {
        background: rgba(0, 0, 0, 0.3);
        padding: 15px;
        border-radius: 10px;
        margin-top: 15px;
        display: flex;
        flex-direction: column;
        align-items: center.
    }

    .now-playing img {
        width: 100%;
        height: auto;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .now-playing-info {
        text-align: center;
        width: 100%;
    }

    .song-title {
        color: #fff;
        font-weight: bold;
        margin-bottom: 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .artist-name {
        color: #bbb;
        font-size: 12px;
        margin-bottom: 10px;
    }

    .controls {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 10px;
    }

    .controls button {
        background: none;
        border: none;
        color: #fff;
        font-size: 18px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .controls button:hover {
        color: #ff00ff;
        transform: scale(1.2);
    }

    .play-button {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(to right, #ff00ff, #00ffff);
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 0 10px rgba(255, 0, 255, 0.5);
    }

    .progress-container {
        width: 100%;
        background: #444;
        height: 5px;
        border-radius: 5px;
        margin-top: 15px;
        position: relative.
    }

    .progress-bar {
        background: linear-gradient(to right, #ff00ff, #00ffff);
        height: 100%;
        border-radius: 5px;
        width: 30%.
    }

    .time-info {
        display: flex;
        justify-content: space-between;
        width: 100%;
        color: #bbb;
        font-size: 12px;
        margin-top: 5px.
    } */

    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: none;
        z-index: 997;
    }

    .overlay.show {
        display: block;
    }

    /* Toast Notification */
    .toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #333;
        color: #fff;
        padding: 15px 25px;
        border-radius: 5px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s ease;
        z-index: 9999;
    }

    .toast.show {
        opacity: 1;
        transform: translateY(0);
    }

    .toast.success {
        border-left: 5px solid #00ffff;
    }

    .toast.error {
        border-left: 5px solid #ff5555;
    }

    .toast i {
        margin-right: 10px;
        font-size: 20px;
    }
    </style>
</head>
<body>
    <header>
        <div class="header-left">
            <div class="logo">M&U</div>
            <div class="search-container">
                <form id="searchForm" method="GET" action="../search.php">
                    <input type="text" 
                           name="query" 
                           placeholder="Search For Musics, Artists, ..." 
                           class="search-input" 
                           id="searchInput"
                           autocomplete="off">
                    <i class="search-icon fas fa-search"></i>
                </form>
                
                <!-- Live Search Results Dropdown -->
                <div class="search-results" id="searchResults"></div>
            </div>
        </div>
        <div class="header-right">
            <nav>
                <a href="../about.php">About Us</a>
                <a href="../contact.php">Contact</a>
                <a href="../premium.php">Premium</a>
                
                <?php if (isLoggedIn() && $user): ?>
                <!-- User is logged in, show profile -->
                <div class="user-profile" id="userProfile">
                    <img src="<?= htmlspecialchars($user['profile_pic'] ?? '../images/default-user.png') ?>" 
                         alt="Profile" class="profile-img">
                    <span><?= htmlspecialchars($user['username']) ?></span>
                    
                    <!-- Profile Dropdown -->
                    <div class="profile-dropdown" id="profileDropdown">
                        <a href="../profile.php"><i class="fas fa-user"></i> My Profile</a>
                        <a href="../settings.php"><i class="fas fa-cog"></i> Settings</a>
                        <a href="../user/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
                <?php else: ?>  
                <!-- User is not logged in, show login/signup buttons -->
                <a href="../login.php">Login</a>
                <a href="../register.php"><button class="button">Sign up</button></a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="menu-toggle" onclick="toggleMenu()">
        <i class="fas fa-bars"></i>
    </div>

    <nav class="sidebar" id="sidebar">
        <div class="sidebar-content">
            <h1 class="logo">Me and <span>You</span></h1>
            <ul>
                <li class="group-title">Menu</li>
                <li><a href="../index.php" class="active"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="discover.php"><i class="fas fa-compass"></i> Discover</a></li>
                <li><a href="albums.php"><i class="fas fa-compact-disc"></i> Albums</a></li>
                <li><a href="artists.php"><i class="fas fa-microphone"></i> Artists</a></li>
                
                <li class="group-title">Library</li>
                <li><a href="recent.php"><i class="fas fa-history"></i> Recently Added</a></li>
                <li><a href="most-played.php"><i class="fas fa-fire"></i> Most Played</a></li>
                
                <li class="group-title">Playlist and Favorite</li>
                <li><a href="favorites.php"><i class="fas fa-heart"></i> Your Favorites</a></li>
                
                <?php if (!empty($recentPlaylists)): ?>
                    <?php foreach($recentPlaylists as $playlist): ?>
                    <li><a href="playlist.php?id=<?= $playlist['id'] ?>">
                        <i class="fas fa-list"></i> <?= $playlist['name'] ?>
                    </a></li>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <li><a href="create-playlist.php" class="blue"><i class="fas fa-plus"></i> Add Playlist</a></li>
                
                <li class="group-title">General</li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <?php if (isLoggedIn()): ?>
                <li><a href="../user/logout.php" class="red"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                <?php endif; ?>
            </ul>
            
            <?php
            // Show currently playing if a song is in session
            if (isset($_SESSION['current_song'])): 
                $currentSong = $_SESSION['current_song'];
            ?>
            <div class="now-playing">
                <img src="<?= $currentSong['cover'] ?>" alt="<?= $currentSong['title'] ?>">
                <div class="now-playing-info">
                    <div class="song-title"><?= $currentSong['title'] ?></div>
                    <div class="artist-name"><?= $currentSong['artist'] ?></div>
                    
                    <div class="controls">
                        <button><i class="fas fa-step-backward"></i></button>
                        <button class="play-button"><i class="fas fa-pause"></i></button>
                        <button><i class="fas fa-step-forward"></i></button>
                    </div>
                    
                    <div class="progress-container">
                        <div class="progress-bar"></div>
                    </div>
                    
                    <div class="time-info">
                        <span>1:30</span>
                        <span>3:45</span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </nav>

    <div class="overlay" id="overlay"></div>
    
    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <i class="fas fa-check-circle"></i>
        <span id="toastMessage">Operation successful!</span>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const menuToggle = document.querySelector(".menu-toggle");
        const sidebar = document.getElementById("sidebar");
        const overlay = document.getElementById("overlay");
        const searchInput = document.getElementById("searchInput");
        const searchResults = document.getElementById("searchResults");
        const userProfile = document.getElementById("userProfile");
        const profileDropdown = document.getElementById("profileDropdown");
        
        // Toggle sidebar
        menuToggle.addEventListener("click", function () {
            sidebar.classList.toggle("open");
            overlay.classList.toggle("show");
        });
        
        overlay.addEventListener("click", function () {
            sidebar.classList.remove("open");
            overlay.classList.remove("show");
            searchResults.classList.remove("show");
            if (profileDropdown) profileDropdown.classList.remove("show");
        });

        // Search functionality
        searchInput.addEventListener("focus", function() {
            if (searchInput.value.length > 0) {
                searchResults.classList.add("show");
            }
        });

        searchInput.addEventListener("input", function() {
            if (searchInput.value.length > 0) {
                // In a real app, you would make an AJAX request here
                // For now, we'll just show the dropdown
                searchResults.classList.add("show");
            } else {
                searchResults.classList.remove("show");
            }
        });

        document.addEventListener("click", function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.remove("show");
            }
        });

        // User profile dropdown
        if (userProfile) {
            userProfile.addEventListener("click", function(e) {
                e.stopPropagation();
                profileDropdown.classList.toggle("show");
            });

            document.addEventListener("click", function(e) {
                if (!userProfile.contains(e.target)) {
                    profileDropdown.classList.remove("show");
                }
            });
        }

        // Toast function
        window.showToast = function(message, type = 'success') {
            const toast = document.getElementById("toast");
            const toastMessage = document.getElementById("toastMessage");
            
            toast.className = "toast " + type + " show";
            toastMessage.textContent = message;
            
            setTimeout(function() {
                toast.className = toast.className.replace("show", "");
            }, 3000);
        };
        
        // Example usage:
        // showToast("Welcome back!", "success");
        // showToast("Something went wrong", "error");

        // Add this inside your DOMContentLoaded event listener
        let searchTimeout;

        searchInput.addEventListener("input", function() {
            clearTimeout(searchTimeout);
            
            if (this.value.length >= 2) {
                searchTimeout = setTimeout(() => {
                    fetchSearchResults(this.value);
                }, 300);
            } else {
                searchResults.classList.remove("show");
            }
        });

        // Replace the existing fetchSearchResults function
        function fetchSearchResults(query) {
            fetch(`../includes/search_ajax.php?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Search error:', data.error);
                        searchResults.classList.remove("show");
                        return;
                    }

                    let html = '';

                    // Add artists section first (if any)
                    if (data.artists && data.artists.length > 0) {
                        html += '<div class="result-section"><h3>Nghệ sĩ</h3>';
                        data.artists.forEach(artist => {
                            html += `
                                <a href="../artist.php?name=${encodeURIComponent(artist.artist)}" class="result-item">
                                    <img src="${artist.image_path}" alt="${artist.artist}">
                                    <div class="result-info">
                                        <div class="result-title">${artist.artist}</div>
                                        <small>${artist.song_count} bài hát</small>
                                    </div>
                                </a>`;
                        });
                        html += '</div>';
                    }

                    // Add songs section
                    if (data.songs && data.songs.length > 0) {
                        html += '<div class="result-section"><h3>Bài hát</h3>';
                        data.songs.forEach(song => {
                            html += `
                                <a href="../song.php?id=${song.id}" class="result-item">
                                    <img src="${song.image_path}" alt="${song.title}">
                                    <div class="result-info">
                                        <div class="result-title">${song.title}</div>
                                        <small>${song.artist}</small>
                                    </div>
                                </a>`;
                        });
                        html += '</div>';
                    }

                    if (!data.artists?.length && !data.songs?.length) {
                        html = '<div class="no-results">Không tìm thấy kết quả</div>';
                    } else {
                        html += `<a href="../search.php?query=${encodeURIComponent(query)}" class="view-all">Xem tất cả kết quả</a>`;
                    }

                    searchResults.innerHTML = html;
                    searchResults.classList.add("show");
                })
                .catch(error => {
                    console.error('Search error:', error);
                    searchResults.innerHTML = '<div class="no-results">Đã xảy ra lỗi</div>';
                    searchResults.classList.add("show");
                });
        }

        // Close search results when clicking outside
        document.addEventListener("click", function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.remove("show");
            }
        });

        // Prevent form submission on enter
        document.getElementById("searchForm").addEventListener("submit", function(e) {
            e.preventDefault();
            window.location.href = `../search.php?query=${encodeURIComponent(searchInput.value)}`;
        });

        // Sidebar position fix
        function adjustSidebar() {
            const headerHeight = document.querySelector('header').offsetHeight;
            sidebar.style.top = `${headerHeight}px`;
            sidebar.style.height = `calc(100vh - ${headerHeight}px)`;
        }

        // Call on load and resize
        adjustSidebar();
        window.addEventListener('resize', adjustSidebar);

        // Toggle sidebar with overlay
        function toggleMenu() {
            sidebar.classList.toggle("open");
            overlay.classList.toggle("show");
        }

        menuToggle.addEventListener("click", toggleMenu);

        // Close sidebar when clicking overlay
        overlay.addEventListener("click", function() {
            sidebar.classList.remove("open");
            overlay.classList.remove("show");
        });
    });
    </script>
</body>
</html>