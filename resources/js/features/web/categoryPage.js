import { postJson } from '../../shared/http';
import { showToast } from '../../shared/toast';

export function initCategoryPage() {
    const categoryPage = document.querySelector('.category-page');
    if (!categoryPage) {
        return;
    }

    function toggleFilters() {
        const panel = document.getElementById('filterPanel');
        if (panel) {
            panel.classList.toggle('active');
        }
    }

    function removeFilter(param) {
        const url = new URL(window.location.href);
        url.searchParams.delete(param);
        window.location.href = url.toString();
    }

    async function quickAdd(productId, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        try {
            const data = await postJson('/cart/add', {
                product_id: productId,
                quantity: 1,
            });

            if (data.success) {
                const cartCount = document.getElementById('cart-count');
                if (cartCount) {
                    cartCount.textContent = data.cart_count;
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

    window.toggleFilters = toggleFilters;
    window.removeFilter = removeFilter;
    window.quickAdd = quickAdd;
}
