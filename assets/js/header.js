document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const searchResults = document.getElementById("searchResults");
    const userProfile = document.getElementById("userProfile");
    const profileDropdown = document.getElementById("profileDropdown");
    const overlay = document.getElementById("overlay");
    let searchTimeout;

    // Search functionality
    searchInput.addEventListener("input", function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            searchResults.innerHTML = '';
            searchResults.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`api/search.php?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.results.length > 0) {
                        searchResults.innerHTML = data.results.map(item => `
                            <div class="search-result-item" data-type="${item.type}" data-id="${item.id}">
                                <img src="${item.thumbnail || 'assets/images/default-thumbnail.png'}" 
                                     alt="${item.name}" class="result-thumbnail">
                                <div class="result-info">
                                    <div class="result-name">${item.name}</div>
                                    ${item.artist ? `<div class="result-artist">${item.artist}</div>` : ''}
                                    <div class="result-type">${item.type}</div>
                                </div>
                            </div>
                        `).join('');
                        searchResults.style.display = 'block';
                    } else {
                        searchResults.innerHTML = '<div class="no-results">No results found</div>';
                        searchResults.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                });
        }, 300);
    });

    // User profile dropdown
    if (userProfile) {
        userProfile.addEventListener("click", function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle("show");
        });
    }

    // Close dropdowns when clicking outside
    document.addEventListener("click", function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.remove("show");
        }
        if (userProfile && !userProfile.contains(e.target)) {
            profileDropdown?.classList.remove("show");
        }
    });

    // Prevent form submission on enter
    document.getElementById("searchForm").addEventListener("submit", function(e) {
        e.preventDefault();
        window.location.href = `../search.php?query=${encodeURIComponent(searchInput.value)}`;
    });

    // Handle result item clicks
    searchResults.addEventListener('click', function(e) {
        const resultItem = e.target.closest('.search-result-item');
        if (resultItem) {
            const type = resultItem.dataset.type;
            const id = resultItem.dataset.id;
            window.location.href = `${type}.php?id=${id}`;
        }
    });
});

// Search results fetch function
async function fetchSearchResults(query) {
    try {
        const response = await fetch(`../includes/search_ajax.php?query=${encodeURIComponent(query)}`);
        const data = await response.json();
        
        if (data.error) throw new Error(data.error);
        
        updateSearchResults(data, query);
    } catch (error) {
        console.error('Search error:', error);
        showErrorResults();
    }
}