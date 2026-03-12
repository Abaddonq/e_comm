export function initHomeHeroBackground() {
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
