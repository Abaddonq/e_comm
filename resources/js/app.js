import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

function initWebSearchModal() {
    const searchModal = document.getElementById('searchModal');
    const searchOpenBtn = document.getElementById('searchOpenBtn');
    const searchCloseBtn = document.getElementById('searchCloseBtn');
    const searchInput = document.getElementById('searchInput');
    const suggestionsList = document.getElementById('suggestionsList');
    const searchLoading = document.getElementById('searchLoading');
    const recentSearches = document.getElementById('recentSearches');
    const recentList = document.getElementById('recentList');
    const clearRecentSearches = document.getElementById('clearRecentSearches');

    if (!searchModal || !searchInput || !suggestionsList || !searchLoading || !recentSearches || !recentList) {
        return;
    }

    const RECENT_SEARCHES_KEY = 'recentSearches';
    const MAX_RECENT = 5;
    let searchTimeout;

    function getRecentSearches() {
        try {
            return JSON.parse(localStorage.getItem(RECENT_SEARCHES_KEY)) || [];
        } catch {
            return [];
        }
    }

    function saveRecentSearch(query) {
        if (!query.trim()) return;
        let recent = getRecentSearches();
        recent = recent.filter((s) => s.toLowerCase() !== query.toLowerCase());
        recent.unshift(query);
        recent = recent.slice(0, MAX_RECENT);
        localStorage.setItem(RECENT_SEARCHES_KEY, JSON.stringify(recent));
        showRecentSearches();
    }

    function clearRecentSearchesList() {
        localStorage.removeItem(RECENT_SEARCHES_KEY);
        showRecentSearches();
    }

    function clearElement(el) {
        if (!el) return;
        while (el.firstChild) {
            el.removeChild(el.firstChild);
        }
    }

    function createSuggestionItem(product) {
        const item = document.createElement('a');
        item.className = 'suggestion-item';

        const slug = product && product.slug ? String(product.slug) : '';
        item.setAttribute('href', `/products/${encodeURIComponent(slug)}`);

        const imageUrl = product && product.image ? String(product.image) : '';
        if (imageUrl) {
            const image = document.createElement('img');
            image.className = 'suggestion-image';
            image.setAttribute('src', imageUrl);
            image.setAttribute('alt', product && product.title ? String(product.title) : window.__t['No Image']);
            item.appendChild(image);
        } else {
            const imagePlaceholder = document.createElement('div');
            imagePlaceholder.className = 'suggestion-image';
            item.appendChild(imagePlaceholder);
        }

        const info = document.createElement('div');
        info.className = 'suggestion-info';

        const title = document.createElement('div');
        title.className = 'suggestion-title';
        title.textContent = product && product.title ? String(product.title) : '';
        info.appendChild(title);

        if (product && product.price) {
            const price = document.createElement('div');
            price.className = 'suggestion-price';
            price.textContent = `₺${String(product.price)}`;
            info.appendChild(price);
        }

        item.appendChild(info);
        return item;
    }

    function renderNoResults(query) {
        clearElement(suggestionsList);
        const noResults = document.createElement('div');
        noResults.style.padding = '20px';
        noResults.style.textAlign = 'center';
        noResults.style.color = '#666';
        noResults.textContent = `"${String(query ?? '')}" ${window.__t['No search results for']}`;
        suggestionsList.appendChild(noResults);
    }

    function showRecentSearches() {
        const recent = getRecentSearches();
        if (recent.length > 0) {
            recentSearches.style.display = 'block';
            clearElement(recentList);

            recent.forEach((term) => {
                const recentItem = document.createElement('span');
                recentItem.className = 'recent-item';
                recentItem.textContent = String(term);
                recentItem.addEventListener('click', () => {
                    searchInput.value = String(term);
                    performSearch();
                });
                recentList.appendChild(recentItem);
            });
        } else {
            recentSearches.style.display = 'none';
            clearElement(recentList);
        }
    }

    function openSearchModal() {
        searchModal.classList.add('active');
        document.body.style.overflow = 'hidden';
        setTimeout(() => searchInput.focus(), 100);
        showRecentSearches();
        clearElement(suggestionsList);
    }

    function closeSearchModal() {
        searchModal.classList.remove('active');
        document.body.style.overflow = '';
        searchInput.value = '';
        clearElement(suggestionsList);
        recentSearches.style.display = 'none';
    }

    async function fetchSuggestions(query) {
        const response = await fetch(`/search/suggestions?q=${encodeURIComponent(query)}`, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        return response.json();
    }

    function performSearch() {
        const query = searchInput.value.trim();

        clearTimeout(searchTimeout);

        if (query.length < 2) {
            clearElement(suggestionsList);
            recentSearches.style.display = getRecentSearches().length > 0 ? 'block' : 'none';
            return;
        }

        recentSearches.style.display = 'none';
        searchLoading.style.display = 'flex';
        clearElement(suggestionsList);

        searchTimeout = setTimeout(async () => {
            try {
                const data = await fetchSuggestions(query);
                searchLoading.style.display = 'none';

                if (data.products && data.products.length > 0) {
                    clearElement(suggestionsList);
                    data.products.forEach((product) => {
                        suggestionsList.appendChild(createSuggestionItem(product));
                    });
                } else {
                    renderNoResults(query);
                }
            } catch (error) {
                console.error('Search error:', error);
                searchLoading.style.display = 'none';
            }
        }, 300);
    }

    if (searchOpenBtn) {
        searchOpenBtn.addEventListener('click', openSearchModal);
    }

    if (searchCloseBtn) {
        searchCloseBtn.addEventListener('click', closeSearchModal);
    }

    searchModal.addEventListener('click', (e) => {
        if (e.target === searchModal) {
            closeSearchModal();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (searchModal.classList.contains('active')) {
                closeSearchModal();
            }

            if (typeof window.__closeMobileNav === 'function') {
                window.__closeMobileNav();
            }
        }

        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            openSearchModal();
        }
    });

    searchInput.addEventListener('input', performSearch);
    searchInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && searchInput.value.trim()) {
            saveRecentSearch(searchInput.value.trim());
            const firstSuggestion = suggestionsList.querySelector('a.suggestion-item');
            if (firstSuggestion) {
                window.location.href = firstSuggestion.getAttribute('href');
            }
        }
    });

    if (clearRecentSearches) {
        clearRecentSearches.addEventListener('click', clearRecentSearchesList);
    }

    window.__openSearchModal = openSearchModal;
}

document.addEventListener('DOMContentLoaded', initWebSearchModal);
