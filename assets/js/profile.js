document.addEventListener('DOMContentLoaded', function() {
    // Avatar upload handling
    const avatarInput = document.getElementById('avatar-input');
    const avatarImage = document.querySelector('.profile-avatar');

    if (avatarImage) {
        avatarImage.addEventListener('click', function() {
            avatarInput.click();
        });

        avatarInput.addEventListener('change', handleAvatarUpload);
    }

    // Song interactions
    document.querySelectorAll('.song-button').forEach(button => {
        button.addEventListener('click', handleSongAction);
    });
});

function handleAvatarUpload(e) {
    const file = e.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('avatar', file);

    fetch('includes/update_avatar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector('.profile-avatar').src = 'uploads/avatars/' + data.filename;
            showAlert('Avatar updated successfully!', 'success');
        } else {
            showAlert(data.error || 'Failed to update avatar', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Failed to upload image', 'error');
    });
}

function handleSongAction(e) {
    const button = e.currentTarget;
    const action = button.getAttribute('title').toLowerCase();
    const songItem = button.closest('.song-item');
    const songId = songItem.dataset.songId;

    switch(action) {
        case 'play':
            playSong(songId);
            break;
        case 'add to playlist':
            showPlaylistModal(songId);
            break;
        case 'like':
            toggleLike(songId, button);
            break;
    }
}

function showAlert(message, type) {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    
    document.querySelector('.container').insertBefore(alert, document.querySelector('.profile-header'));
    
    setTimeout(() => alert.remove(), 3000);
}