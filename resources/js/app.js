import './bootstrap';

import Alpine from 'alpinejs';

import { onDomReady } from './shared/dom';
import { exposeToastGlobally } from './shared/toast';
import { initWebSearchModal } from './features/layout/webSearchModal';
import { initWebMobileNav } from './features/layout/webMobileNav';
import { initHomeHeroBackground } from './features/layout/homeHeroBackground';
import { initWebLayoutChrome } from './features/layout/webLayoutChrome';
import { exposeWishlistGlobals } from './features/web/wishlist';
import { initHomeQuickAdd } from './features/web/homeQuickAdd';
import { initProductDetailPage } from './features/web/productDetailPage';
import { initCategoryPage } from './features/web/categoryPage';
import { initCartPage } from './features/web/cartPage';
import { initCheckoutPage } from './features/web/checkoutPage';
import { initProfilePage } from './features/web/profilePage';

window.Alpine = Alpine;
Alpine.start();

exposeToastGlobally();
exposeWishlistGlobals();

onDomReady(initWebSearchModal);
onDomReady(initWebMobileNav);
onDomReady(initHomeHeroBackground);
onDomReady(initWebLayoutChrome);
onDomReady(initHomeQuickAdd);
onDomReady(initProductDetailPage);
onDomReady(initCategoryPage);
onDomReady(initCartPage);
onDomReady(initCheckoutPage);
onDomReady(initProfilePage);
