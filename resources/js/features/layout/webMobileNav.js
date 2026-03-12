export function initWebMobileNav() {
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
