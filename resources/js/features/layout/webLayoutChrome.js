export function initWebLayoutChrome() {
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
        const openDropdown = () => {
            userDropdown.classList.add('active');
            userDropdown.setAttribute('aria-hidden', 'false');
        };

        const closeDropdown = () => {
            userDropdown.classList.remove('active');
            userDropdown.setAttribute('aria-hidden', 'true');
        };

        userIcon.addEventListener('mouseenter', () => {
            if (window.innerWidth > 768) {
                openDropdown();
            }
        });

        userIcon.addEventListener('mouseleave', () => {
            if (window.innerWidth > 768) {
                closeDropdown();
            }
        });

        if (userIconLink) {
            userIconLink.addEventListener('click', (e) => {
                if (window.innerWidth <= 768 && hasDropdownMenu) {
                    e.preventDefault();
                    if (userDropdown.classList.contains('active')) {
                        closeDropdown();
                    } else {
                        openDropdown();
                    }
                }
            });
        }

        document.addEventListener('click', (e) => {
            if (!userIcon.contains(e.target)) {
                closeDropdown();
            }
        });
    }

    hidePageLoader();
    window.addEventListener('load', hidePageLoader);
}
