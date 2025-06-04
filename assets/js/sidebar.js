document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
});

function initSidebar() {
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.overlay');
    const mainContent = document.getElementById('mainContent');

    if (!menuToggle || !sidebar) {
        console.error('Required sidebar elements not found');
        return;
    }

    function toggleSidebar() {
        if (sidebar && overlay) {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
            if (mainContent) {
                mainContent.classList.toggle('sidebar-expanded');
            }
        }
    }

    menuToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleSidebar();
    });

    if (overlay) {
        overlay.addEventListener('click', function() {
            if (sidebar.classList.contains('open')) {
                toggleSidebar();
            }
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('open')) {
            toggleSidebar();
        }
    });
}