function editUser(userId) {
    window.location.href = `edit-user.php?id=${userId}`;
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        fetch('manage-users.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete&user_id=${userId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to delete user: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the user');
        });
    }
}

function updateRole(userId, role) {
    fetch('manage-users.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=update_role&user_id=${userId}&role=${role}`
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert('Failed to update role: ' + data.error);
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the role');
        location.reload();
    });
}