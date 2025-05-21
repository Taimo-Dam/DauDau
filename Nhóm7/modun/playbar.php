<div class="container-play">
    <div class="player">
        <div class="info">
            <div class="cover">
                <img src="" class="cover" alt="Album" id="cover-image">
            </div>
            <div class="song-details">
                <span id="song-name"></span>
                <span id="song-artist"></span>
            </div>
        </div>
        <div class="controls">
            <div class="controls-button">
                <button class="btn" id="prev"><i class='bx bx-chevrons-left'></i></button>
                <button class="btn" id="play"><i class='bx bx-pause-circle'></i></button>
                <button class="btn" id="next"><i class='bx bx-chevrons-right'></i></button>
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
<script src="includes/playMusic.js"></script>
<style>
    
.container-play {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(18, 18, 18, 0.95);
    padding: 15px 30px;
    backdrop-filter: blur(10px);
    z-index: 100;
}

.player {
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 1200px;
    margin: 0 auto;
}

.info {
    display: flex;
    align-items: center;
    width: 300px;
}

.cover {
    width: 56px;
    height: 56px;
    border-radius: 8px;
    margin-right: 15px;
}

.song-details {
    display: flex;
    flex-direction: column;
}

.song-details span {
    color: white;
}

.controls {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    max-width: 600px;
}

.controls-button {
    display: flex;
    align-items: center;
    gap: 20px;
}

.btn {
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    font-size: 24px;
}

.controls-slider {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 10px;
}

#progress {
    flex: 1;
    height: 4px;
    -webkit-appearance: none;
    appearance: none;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 2px;
    cursor: pointer;
}

#progress::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 12px;
    height: 12px;
    background: #1db954;
    border-radius: 50%;
    cursor: pointer;
}

.controls-volume {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 150px;
}
#volume {
    width: 100px;
    height: 4px;
    -webkit-appearance: none;
    appearance: none;
    background: rgba(255, 255, 255, 0.1);
    background: rgba(255, 255, 255, 0.1);
    border-radius: 2px;
    cursor: pointer;
}

#volume::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 12px;
    height: 12px;
    background: #1db954;
    border-radius: 50%;
    cursor: pointer;
}

#current, #duration {
    color: #b3b3b3;
    font-size: 12px;
}

.bx {
    color: white;
}
</style>