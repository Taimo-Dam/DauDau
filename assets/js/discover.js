document.addEventListener('DOMContentLoaded', function() {
            // Discover functionality
            const cards = document.querySelectorAll('.card');
            
            cards.forEach(card => {
                card.addEventListener('click', function(e) {
                    // Don't trigger if clicking on sidebar elements
                    if (e.target.closest('.sidebar') || e.target.closest('#menuToggle')) {
                        return;
                    }
                    
                    const link = this.querySelector('a');
                    if (link) {
                        link.click();
                    }
                });
            });

            // Add hover effects
            cards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    // Don't apply hover if sidebar is being interacted with
                    if (!document.querySelector('.sidebar.open')) {
                        card.style.transform = 'scale(1.05)';
                        card.style.transition = 'transform 0.3s ease';
                    }
                });
                
                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'scale(1)';
                });
            });
        });

        // Handle genre clicks
        function handleGenreClick(event, genreName) {
            event.preventDefault();
            event.stopPropagation(); // Stop event from bubbling up
            
            sessionStorage.setItem('selectedGenre', genreName);
            window.location.href = `genre.php?name=${encodeURIComponent(genreName)}`;
        }

        // Export discover initialization if needed by other scripts
        window.initDiscover = function() {
            const discoverElements = document.querySelectorAll('.discover-item');
            discoverElements.forEach(element => {
                element.addEventListener('click', function(e) {
                    // Only handle click if not interacting with sidebar
                    if (!e.target.closest('.sidebar') && !e.target.closest('#menuToggle')) {
                        e.stopPropagation();
                    }
                });
            });
        };