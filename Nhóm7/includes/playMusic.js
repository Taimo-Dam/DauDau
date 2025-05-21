// xử lý cho phần hiện thanh nhạc khi bấm vào
const audioPlayer = document.getElementById('audio');
const songCards = document.querySelectorAll('.song-card');
const imgScrThanhNhac = document.getElementById('cover-image');
const containerPlay = document.querySelector('.container-play');

const playButtonCard = document.querySelector('.play-overlay');

// sử lý riêng cho thanh nhạc
const audio = document.getElementById("audio");
const playButton = document.getElementById("play");
const duration = document.getElementById("duration");
const current = document.getElementById("current");
const progress = document.getElementById("progress");
const volume = document.getElementById("volume");
const volumeIcon = document.getElementById("volume-icon");

const songName = document.getElementById('song-name');
const artistName = document.getElementById('song-artist');

let currentAudioSrc = '';
let currentTime = 0;
let currentCard = null; // Thêm biến để theo dõi card hiện tại
document.addEventListener('DOMContentLoaded', function() {
        const songCards = document.querySelectorAll('.song-card');
        const playbarContainer = document.getElementById('playbarContainer');
        const audio = document.getElementById('audio');

        songCards.forEach(card => {
            card.addEventListener('click', function() {
                // Show playbar
                playbarContainer.style.display = 'block';
                
                // Update audio and playbar info
                const songData = {
                    audio: this.dataset.audio,
                    name: this.dataset.songName,
                    artist: this.dataset.artist,
                    img: this.dataset.img
                };

                // Update audio source
                audio.src = songData.audio;
                
                // Update playbar info
                document.getElementById('song-name').textContent = songData.name;
                document.getElementById('song-artist').textContent = songData.artist;
                document.getElementById('cover-image').src = songData.img;

                // Play the song
                audio.play();
            });
        });
    });
songCards.forEach(card => {
    card.addEventListener('click', () => {
        const audioSrc = card.getAttribute('data-audio');
        const currentImg = card.querySelector('img');
        const playButtonCard = card.querySelector('.play-overlay i');
        // lấy tên bài hát và tên ca sĩ
        const songNameValue = card.getAttribute('data-song-name');
        const artistNameValue = card.getAttribute('data-artist');
        const imageSrc = card.getAttribute('data-img');

        // Nếu click vào card khác
        if (currentCard && currentCard !== card) {
            // Reset card cũ
            const oldPlayButton = currentCard.querySelector('.play-overlay i');
            oldPlayButton.className = 'bx bx-play-circle';
            // Reset thời gian của bài cũ
            currentTime = 0;
        }

        // Cập nhật card hiện tại
        currentCard = card;

        // hiện thanh nhạc
        containerPlay.classList.add('show');

        // hiện ảnh bài hát
        imgScrThanhNhac.src = imageSrc;
        // gán tên bài hát và tên ca sĩ
        songName.textContent = songNameValue;
        artistName.textContent = artistNameValue;

        if (audioPlayer.src.includes(audioSrc)) {
            if (audioPlayer.paused) {
                audioPlayer.play();
                // hiện ảnh bật pause
                playButtonCard.className = 'bx bx-pause-circle';
                playButton.innerHTML = "<i class='bx bx-pause-circle'></i>";
            } else {
                audioPlayer.pause();
                // hiện ảnh bật play
                playButtonCard.className = 'bx bx-play-circle';
                playButton.innerHTML = "<i class='bx bx-play-circle'></i>";
            }
        } else {
            // Khi chọn bài mới
            currentAudioSrc = audioSrc;
            audioPlayer.src = audioSrc;
            audioPlayer.currentTime = 0; // Reset thời gian về 0
            audioPlayer.play();

            // Cập nhật tất cả icon về play
            document.querySelectorAll('.play-overlay i').forEach(icon => {
                icon.className = 'bx bx-play-circle';
            });

            // Cập nhật icon play/pause cho bài hiện tại
            playButtonCard.className = 'bx bx-pause-circle';
            playButton.innerHTML = "<i class='bx bx-pause-circle'></i>";
        }
    });
});

// Cập nhật event listener của playButton
playButton.addEventListener('click', function () {
    if (!currentCard) return; // Nếu chưa có card nào được chọn

    const playButtonCard = currentCard.querySelector('.play-overlay i');

    if (audio.paused) {
        audio.play();
        playButtonCard.className = 'bx bx-pause-circle';
        playButton.innerHTML = "<i class='bx bx-pause-circle'></i>";
    } else {
        audio.pause();
        playButtonCard.className = 'bx bx-play-circle';
        playButton.innerHTML = "<i class='bx bx-play-circle'></i>";
    }
});

// Cập nhật event listener của audio
audioPlayer.addEventListener('ended', () => {
    // Khi bài hát kết thúc
    if (currentCard) {
        const playButtonCard = currentCard.querySelector('.play-overlay i');
        playButtonCard.className = 'bx bx-play-circle';
        playButton.innerHTML = "<i class='bx bx-play-circle'></i>";
        currentTime = 0;
    }
});

// Lưu thời gian hiện tại khi tạm dừng
audioPlayer.addEventListener('pause', () => {
    currentTime = audioPlayer.currentTime;
});

// Khôi phục thời gian khi play lại
audioPlayer.addEventListener('play', () => {
    if (currentTime > 0) {
        audioPlayer.currentTime = currentTime;
    }
});

function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const secon = Math.floor(seconds % 60);
    return `${minutes < 10 ? '0' + minutes : minutes}:${secon < 10 ? '0' + secon : secon}`;
};

audio.addEventListener('loadedmetadata', function () {
    duration.textContent = formatTime(audio.duration);
});

audio.addEventListener('timeupdate', function () {
    current.textContent = formatTime(audio.currentTime);
    progress.value = (audio.currentTime / audio.duration) * 100;
});

progress.addEventListener("input", () => {
    audio.currentTime = (progress.value / 100) * audio.duration;
});

// cập nhật giá trị của thanh âm lượng 0.0 - 1.0
volume.addEventListener("input", function () {
    audio.volume = volume.value;
});