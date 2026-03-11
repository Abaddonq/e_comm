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

function showToast(message, type = 'success') {
    const existing = document.querySelector('.toast-notification');
    if (existing) {
        existing.remove();
    }

    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;

    const svgNs = 'http://www.w3.org/2000/svg';
    const icon = document.createElementNS(svgNs, 'svg');
    icon.setAttribute('width', '20');
    icon.setAttribute('height', '20');
    icon.setAttribute('fill', 'none');
    icon.setAttribute('stroke', 'currentColor');
    icon.setAttribute('viewBox', '0 0 24 24');

    const path = document.createElementNS(svgNs, 'path');
    path.setAttribute('stroke-linecap', 'round');
    path.setAttribute('stroke-linejoin', 'round');
    path.setAttribute('stroke-width', '2');
    path.setAttribute('d', type === 'success' ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12');
    icon.appendChild(path);

    const messageEl = document.createElement('span');
    messageEl.textContent = String(message ?? '');

    toast.appendChild(icon);
    toast.appendChild(messageEl);
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'toastSlideIn 0.3s ease reverse';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function toggleWishlist(productId, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        return;
    }

    const btn = document.getElementById(`wishlist-btn-${productId}`);

    fetch('/wishlist/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.content,
        },
        body: JSON.stringify({ product_id: productId }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                if (btn) {
                    btn.classList.toggle('active');
                }
                showToast(data.is_added ? window.__t['Product added to wishlist'] : window.__t['Product removed from wishlist'], 'success');
            } else if (data.error) {
                showToast(data.error, 'error');
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            showToast(window.__t['An error occurred'], 'error');
        });
}

window.showToast = showToast;
window.toggleWishlist = toggleWishlist;

function initHomeQuickAdd() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.product-quick-add');
        if (!btn) {
            return;
        }

        const variantId = btn.dataset.variantId;
        if (!variantId) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();

        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.content,
            },
            body: JSON.stringify({
                variant_id: variantId,
                quantity: 1,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (!data.success) {
                    return;
                }

                const cartCount = document.getElementById('cart-count');
                if (cartCount) {
                    cartCount.textContent = data.cart_count;
                }

                btn.style.background = '#22c55e';
                btn.style.color = 'white';

                setTimeout(() => {
                    btn.style.background = '';
                    btn.style.color = '';
                }, 1000);
            })
            .catch((error) => {
                console.error('Error:', error);
            });
    });
}

document.addEventListener('DOMContentLoaded', initHomeQuickAdd);

function initProductDetailPage() {
    const productPage = document.querySelector('.product-page[data-product-id]');
    if (!productPage) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        return;
    }

    let currentVariant = null;

    async function checkWishlistStatus() {
        try {
            const productId = Number(productPage.dataset.productId || 0);
            if (!productId) {
                return;
            }

            const response = await fetch('/wishlist/check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.content,
                },
                body: JSON.stringify({ product_id: productId }),
            });

            const data = await response.json();
            if (data.is_wishlisted) {
                const btn = document.getElementById('wishlistBtnDetail');
                const text = document.getElementById('wishlistText');
                if (btn) {
                    btn.classList.add('active');
                }
                if (text) {
                    text.textContent = window.__t['In Wishlist'];
                }
            }
        } catch (error) {
            console.error('Error checking wishlist:', error);
        }
    }

    function initVariant() {
        const firstVariant = document.querySelector('.variant-option');
        if (firstVariant) {
            selectVariant(firstVariant);
        }
    }

    function selectVariant(element) {
        if (!element) {
            return;
        }

        document.querySelectorAll('.variant-option').forEach((opt) => opt.classList.remove('selected'));
        element.classList.add('selected');

        currentVariant = {
            id: element.dataset.id,
            price: parseFloat(element.dataset.price),
            stock: parseInt(element.dataset.stock, 10),
            sku: element.dataset.sku,
        };

        const priceNode = document.querySelector('.product-price');
        if (priceNode) {
            priceNode.innerHTML = `₺${currentVariant.price.toLocaleString('tr-TR', { minimumFractionDigits: 2 })}`;
        }

        const stockStatus = document.querySelector('.stock-status');
        const addToCartBtn = document.getElementById('addToCartBtn');
        const quantityInput = document.getElementById('quantity');

        if (!stockStatus || !addToCartBtn) {
            return;
        }

        if (currentVariant.stock > 0) {
            stockStatus.className = 'stock-status in-stock';
            stockStatus.innerHTML = `<span class="stock-dot"></span>${window.__t['In Stock']} (${currentVariant.stock} ${window.__t['pieces']})`;
            addToCartBtn.disabled = false;
            addToCartBtn.textContent = window.__t['Add to Cart'];
            if (quantityInput) {
                quantityInput.max = String(currentVariant.stock);
            }
        } else {
            stockStatus.className = 'stock-status out-of-stock';
            stockStatus.innerHTML = `<span class="stock-dot"></span>${window.__t['Out of Stock']}`;
            addToCartBtn.disabled = true;
            addToCartBtn.textContent = window.__t['Out of Stock'];
        }
    }

    function changeImage(url, element) {
        const mainImage = document.getElementById('mainImage');
        if (mainImage && url) {
            mainImage.src = url;
        }

        document.querySelectorAll('.thumbnail-item').forEach((item) => item.classList.remove('active'));
        if (element) {
            element.classList.add('active');
        }
    }

    function changeQuantity(delta) {
        const input = document.getElementById('quantity');
        if (!input) {
            return;
        }

        let value = parseInt(input.value, 10) + delta;
        const max = parseInt(input.max, 10) || 99;

        if (value < 1) {
            value = 1;
        }

        if (value > max) {
            value = max;
        }

        input.value = String(value);
    }

    function openTab(tabName, triggerButton) {
        document.querySelectorAll('.tab-content').forEach((content) => content.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach((btn) => btn.classList.remove('active'));

        const tab = document.getElementById(tabName);
        if (tab) {
            tab.classList.add('active');
        }

        if (triggerButton) {
            triggerButton.classList.add('active');
        }
    }

    async function toggleWishlistDetail(productId) {
        try {
            const response = await fetch('/wishlist/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.content,
                },
                body: JSON.stringify({ product_id: productId }),
            });

            const data = await response.json();
            if (data.success) {
                const btn = document.getElementById('wishlistBtnDetail');
                const text = document.getElementById('wishlistText');

                if (data.is_added) {
                    if (btn) {
                        btn.classList.add('active');
                    }
                    if (text) {
                        text.textContent = window.__t['In Wishlist'];
                    }
                    showToast(window.__t['Product added to wishlist'], 'success');
                } else {
                    if (btn) {
                        btn.classList.remove('active');
                    }
                    if (text) {
                        text.textContent = window.__t['Add to Wishlist'];
                    }
                    showToast(window.__t['Product removed from wishlist'], 'success');
                }
            } else if (data.error) {
                showToast(data.error, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast(window.__t['An error occurred'], 'error');
        }
    }

    async function addToCartFromDetail() {
        if (!currentVariant) {
            initVariant();
        }

        if (!currentVariant || currentVariant.stock <= 0) {
            showToast(window.__t['Product not in stock'], 'error');
            return;
        }

        const quantityInput = document.getElementById('quantity');
        const quantity = quantityInput ? parseInt(quantityInput.value, 10) : 1;

        try {
            const response = await fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.content,
                },
                body: JSON.stringify({
                    variant_id: currentVariant.id,
                    quantity,
                }),
            });

            const data = await response.json();
            if (data.success) {
                const cartCount = document.getElementById('cart-count');
                const addToCartBtn = document.getElementById('addToCartBtn');

                if (cartCount) {
                    cartCount.textContent = data.cart_count;
                }

                if (addToCartBtn) {
                    addToCartBtn.textContent = window.__t['Added to Cart!'];
                    setTimeout(() => {
                        addToCartBtn.textContent = window.__t['Add to Cart'];
                    }, 2000);
                }

                showToast(window.__t['Product added to cart'], 'success');
            } else {
                showToast(data.error || window.__t['Add to cart failed'], 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast(window.__t['An error occurred'], 'error');
        }
    }

    document.querySelectorAll('.thumbnail-item').forEach((item) => {
        item.addEventListener('click', () => {
            changeImage(item.dataset.image, item);
        });
    });

    window.toggleWishlistDetail = toggleWishlistDetail;
    window.selectVariant = selectVariant;
    window.changeQuantity = changeQuantity;
    window.openTab = openTab;
    window.addToCartFromDetail = addToCartFromDetail;

    checkWishlistStatus();
    initVariant();
}

document.addEventListener('DOMContentLoaded', initProductDetailPage);
