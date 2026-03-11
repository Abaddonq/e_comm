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

function initWebMobileNav() {
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileNav = document.getElementById('mobileNav');
    const mobileNavOverlay = document.getElementById('mobileNavOverlay');
    const mobileNavClose = document.getElementById('mobileNavClose');
    const mobileSearchBtn = document.getElementById('mobileSearchBtn');

    if (!mobileNav || !mobileNavOverlay || !mobileMenuBtn) {
        return;
    }

    function openMobileNav() {
        mobileNav.classList.add('active');
        mobileNavOverlay.classList.add('active');
        mobileNav.setAttribute('aria-hidden', 'false');
        mobileMenuBtn.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileNav() {
        mobileNav.classList.remove('active');
        mobileNavOverlay.classList.remove('active');
        mobileNav.setAttribute('aria-hidden', 'true');
        mobileMenuBtn.setAttribute('aria-expanded', 'false');

        const searchModalEl = document.getElementById('searchModal');
        if (!searchModalEl || !searchModalEl.classList.contains('active')) {
            document.body.style.overflow = '';
        }
    }

    window.__closeMobileNav = closeMobileNav;

    mobileMenuBtn.addEventListener('click', openMobileNav);

    if (mobileNavClose) {
        mobileNavClose.addEventListener('click', closeMobileNav);
    }

    mobileNavOverlay.addEventListener('click', closeMobileNav);

    if (mobileSearchBtn) {
        mobileSearchBtn.addEventListener('click', () => {
            closeMobileNav();
            if (typeof window.__openSearchModal === 'function') {
                window.__openSearchModal();
            }
        });
    }

    mobileNav.querySelectorAll('a.mobile-nav-link').forEach((link) => {
        link.addEventListener('click', closeMobileNav);
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 1024) {
            closeMobileNav();
        }
    });
}

document.addEventListener('DOMContentLoaded', initWebMobileNav);

function initHomeHeroBackground() {
    const heroBg = document.querySelector('.hero-bg');
    const heroImage = document.querySelector('.hero-bg-image');

    if (!heroBg || !heroImage) {
        return;
    }

    const markHeroReady = () => heroBg.classList.add('is-loaded');

    if (heroImage.complete && heroImage.naturalWidth > 0) {
        markHeroReady();
        return;
    }

    heroImage.addEventListener('load', markHeroReady, { once: true });
    heroImage.addEventListener('error', markHeroReady, { once: true });
}

document.addEventListener('DOMContentLoaded', initHomeHeroBackground);

function initWebLayoutChrome() {
    function hidePageLoader() {
        const loader = document.getElementById('pageLoader');
        if (loader) {
            loader.classList.add('hidden');
            setTimeout(() => {
                loader.remove();
            }, 300);
        }
    }

    const header = document.getElementById('header');
    const heroSection = document.querySelector('.hero');

    if (header && !heroSection) {
        header.classList.add('scrolled');
    }

    window.addEventListener('scroll', () => {
        if (!header) {
            return;
        }

        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else if (heroSection) {
            header.classList.remove('scrolled');
        }
    });

    const userIcon = document.querySelector('.header-user-icon');
    const userDropdown = document.querySelector('.user-dropdown');
    const userIconLink = userIcon ? userIcon.querySelector('.header-icon') : null;
    const hasDropdownMenu = Boolean(userDropdown);

    if (userIcon && userDropdown) {
        userIcon.addEventListener('mouseenter', () => {
            if (window.innerWidth > 768) {
                userDropdown.style.display = 'block';
            }
        });

        userIcon.addEventListener('mouseleave', () => {
            if (window.innerWidth > 768) {
                userDropdown.style.display = 'none';
            }
        });

        if (userIconLink) {
            userIconLink.addEventListener('click', (e) => {
                if (window.innerWidth <= 768 && hasDropdownMenu) {
                    e.preventDefault();
                    userDropdown.style.display = userDropdown.style.display === 'block' ? 'none' : 'block';
                }
            });
        }

        document.addEventListener('click', (e) => {
            if (!userIcon.contains(e.target)) {
                userDropdown.style.display = 'none';
            }
        });
    }

    hidePageLoader();
    window.addEventListener('load', hidePageLoader);
}

document.addEventListener('DOMContentLoaded', initWebLayoutChrome);
