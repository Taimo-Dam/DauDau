// Audio player elements
const audio = new Audio();
const songCards = document.querySelectorAll('.song-card');
const imgScrThanhNhac = document.getElementById('cover-image');
const containerPlay = document.querySelector('.container-play');

// Player controls and info elements
const controls = {
    play: document.getElementById("play"),
    duration: document.getElementById("duration"),
    current: document.getElementById("current"),
    progress: document.getElementById("progress"),
    volume: document.getElementById("volume"),
    volumeIcon: document.getElementById("volume-icon"),
    next: document.getElementById('next'),
    prev: document.getElementById('prev'),
    songName: document.getElementById('song-name'),
    artistName: document.getElementById('song-artist')
};

// State management
const playerState = {
    currentCard: null,
    isDragging: false,
    isPlaying: false,
    currentSong: null
};

// Format time with error handling
function formatTime(seconds) {
    if (isNaN(seconds) || seconds < 0) return '0:00';
    const minutes = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${minutes}:${secs.toString().padStart(2, '0')}`;
}

// Add this function for updating player state
function updatePlayButtonState() {
    if (!controls.play) return;
    
    if (playerState.isPlaying) {
        controls.play.innerHTML = "<i class='bx bx-pause-circle'></i>";
    } else {
        controls.play.innerHTML = "<i class='bx bx-play-circle'></i>";
    }

    // Update all song card play buttons
    document.querySelectorAll('.play-overlay i').forEach(icon => {
        const card = icon.closest('.song-card');
        if (card === playerState.currentCard && playerState.isPlaying) {
            icon.className = 'bx bx-pause-circle';
        } else {
            icon.className = 'bx bx-play-circle';
        }
    });
}

// Update UI elements
function updatePlayButtonIcons(isPlaying) {
    if (!controls.play) return;
    
    document.querySelectorAll('.play-overlay i').forEach(icon => {
        icon.className = 'bx bx-play-circle';
    });
    
    if (playerState.currentCard && isPlaying) {
        const currentCardButton = playerState.currentCard.querySelector('.play-overlay i');
        if (currentCardButton) {
            currentCardButton.className = 'bx bx-pause-circle';
        }
        controls.play.innerHTML = "<i class='bx bx-pause-circle'></i>";
    } else {
        controls.play.innerHTML = "<i class='bx bx-play-circle'></i>";
    }
}

// Enhanced playSongCard function with error handling
async function playSongCard(card, shouldPlay = true) {
    if (!card) return;
    
    try {
        const songData = {
            audio: card.getAttribute('data-audio'),
            image: card.getAttribute('data-img'),
            name: card.getAttribute('data-song-name'),
            artist: card.getAttribute('data-artist'),
            id: card.getAttribute('data-id')
        };

        // Validate required data
        if (!songData.audio) throw new Error('Audio source missing');
        
        const isSameSong = playerState.currentCard === card && 
                          audio.src.includes(songData.audio);
        
        playerState.currentCard = card;
        containerPlay.classList.add('show');
        
        // Update UI
        if (imgScrThanhNhac) imgScrThanhNhac.src = songData.image || 'assets/images/default-song.jpg';
        if (controls.songName) controls.songName.textContent = songData.name || 'Unknown Song';
        if (controls.artistName) controls.artistName.textContent = songData.artist || 'Unknown Artist';
        
        if (!isSameSong) {
            audio.src = songData.audio;
            audio.currentTime = 0;
            controls.progress.value = 0;
            
            // Update play history
            if (songData.id) {
                await updateHistory(songData.id);
            }
        }
        
        if (shouldPlay) {
            if (audio.paused || !isSameSong) {
                await audio.play();
            } else {
                audio.pause();
            }
        }
        
        updatePlayButtonIcons(!audio.paused);
        
    } catch (error) {
        console.error('Error playing song:', error);
        showNotification('Error playing song. Please try again.');
    }
}

// Show notification
function showNotification(message, type = 'error') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Update the updateHistory function
async function updateHistory(songId, retries = 3) {
    if (!songId) return;
    
    for (let i = 0; i < retries; i++) {
        try {
            const formData = new FormData();
            formData.append('songId', songId);

            const response = await fetch('../includes/updateHistory.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Play count update response:', data); // Debug log
            
            if (data.success) {
                // Update UI across pages
                broadcastPlayCount(songId);
                return data;
            }
            
        } catch (error) {
            console.error(`Attempt ${i + 1} failed:`, error);
            if (i === retries - 1) throw error;
            await new Promise(resolve => setTimeout(resolve, 1000 * (i + 1)));
        }
    }
}

// Song card click handlers
songCards.forEach(card => {
    card.addEventListener('click', () => {
        playSongCard(card, true);
    });
});

// Main play button event listener
if (controls.play) {
    controls.play.addEventListener('click', function () {
        if (!playerState.currentCard) return;

        if (audio.paused) {
            audio.play();
        } else {
            audio.pause();
        }
    });
}

// ============ PROGRESS BAR HANDLING ============

// Handle progress bar input (dragging)
controls.progress.addEventListener('input', function(e) {
    if (!audio.duration) return;
    
    playerState.isDragging = true;
    const percent = this.value / 100;
    audio.currentTime = percent * audio.duration;
    controls.current.textContent = formatTime(audio.currentTime);
    
    console.log('Progress input:', this.value, 'Time:', formatTime(audio.currentTime));
});

// Handle when user stops dragging
controls.progress.addEventListener('change', function(e) {
    playerState.isDragging = false;
    console.log('Progress change complete');
});

// Handle mouse events for more responsive control
controls.progress.addEventListener('mousedown', function(e) {
    playerState.isDragging = true;
});

controls.progress.addEventListener('mouseup', function(e) {
    setTimeout(() => {
        playerState.isDragging = false;
    }, 100); // Small delay to prevent conflicts
});

// Handle clicking on progress bar track
controls.progress.addEventListener('click', function(e) {
    if (!audio.duration) return;
    
    // Calculate percentage based on click position
    const rect = this.getBoundingClientRect();
    const percent = (e.clientX - rect.left) / rect.width;
    const clampedPercent = Math.max(0, Math.min(1, percent));
    
    // Update audio and progress bar
    audio.currentTime = clampedPercent * audio.duration;
    this.value = clampedPercent * 100;
    controls.current.textContent = formatTime(audio.currentTime);
    
    console.log('Progress clicked:', clampedPercent * 100 + '%');
});

// Audio event listeners
audio.addEventListener('loadedmetadata', function () {
    if (controls.duration) {
        controls.duration.textContent = formatTime(audio.duration);
    }
    controls.progress.max = 100;
    controls.progress.value = 0;
});

audio.addEventListener('timeupdate', function () {
    if (!audio.duration || playerState.isDragging) return;
    
    const percent = (audio.currentTime / audio.duration) * 100;
    controls.progress.value = percent;

    
    if (controls.current) {
        controls.current.textContent = formatTime(audio.currentTime);
    controls.progress.style.background = `linear-gradient(to right, #1db954 ${percent}%, #4d4d4d ${percent}%)`;

    }
});

audio.addEventListener('play', () => {
    playerState.isPlaying = true;
    updatePlayButtonState();
    console.log('Song playing:', {
        src: audio.src,
        currentSong: playerState.currentCard ? {
            id: playerState.currentCard.dataset.id,
            name: playerState.currentCard.dataset.songName,
            artist: playerState.currentCard.dataset.artist
        } : 'No song data'
    });
});

audio.addEventListener('pause', () => {
    playerState.isPlaying = false;
    updatePlayButtonState();
});

audio.addEventListener('ended', () => {
    // Auto-play next song
    const nextSong = findNextSong('next');
    if (nextSong) {
        playSongCard(nextSong, true);
    } else {
        updatePlayButtonIcons(false);
        controls.progress.value = 0;
    }
});

// Volume control
if (controls.volume) {
    controls.volume.addEventListener("input", function () {
        audio.volume = this.value;
        
        // Update volume icon based on level
        if (controls.volumeIcon) {
            if (this.value == 0) {
                controls.volumeIcon.className = 'bx bx-volume-mute';
            } else if (this.value < 0.5) {
                controls.volumeIcon.className = 'bx bx-volume-low';
            } else {
                controls.volumeIcon.className = 'bx bx-volume-full';
            }
        }
    });
    
    // Set initial volume
    audio.volume = controls.volume.value;
}

// Navigation functions
function findNextSong(direction = 'next') {
    if (!playerState.currentCard) return null;
    
    const cards = Array.from(songCards);
    const currentIndex = cards.indexOf(playerState.currentCard);
    
    if (direction === 'next') {
        return cards[(currentIndex + 1) % cards.length];
    } else {
        return cards[(currentIndex - 1 + cards.length) % cards.length];
    }
}

// Next/Previous button event listeners
if (controls.next) {
    controls.next.addEventListener('click', () => {
        const nextSong = findNextSong('next');
        if (nextSong) {
            playSongCard(nextSong, true);
        }
    });
}

if (controls.prev) {
    controls.prev.addEventListener('click', () => {
        const prevSong = findNextSong('prev');
        if (prevSong) {
            playSongCard(prevSong, true);
        }
    });
}


// Add keyboard shortcuts
document.addEventListener('keydown', (e) => {
    if (e.target.tagName === 'INPUT') return;
    
    switch(e.key.toLowerCase()) {
        case ' ':
            e.preventDefault();
            controls.play.click();
            break;
        case 'arrowright':
            if (e.ctrlKey) controls.next?.click();
            break;
        case 'arrowleft':
            if (e.ctrlKey) controls.prev?.click();
            break;
        case 'm':
            if (controls.volume) controls.volume.value = controls.volume.value > 0 ? 0 : 1;
            break;
    }
});

// BroadcastChannel for cross-page updates
const playCountChannel = new BroadcastChannel('play_count_channel');

function broadcastPlayCount(songId) {
    playCountChannel.postMessage({ songId });
}

playCountChannel.onmessage = (event) => {
    if (event.data.songId) {
        updateSongCardsUI(event.data.songId);
    }
};

