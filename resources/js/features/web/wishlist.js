import { postJson } from '../../shared/http';
import { registerGlobals } from '../../shared/globals';
import { showToast } from '../../shared/toast';

export async function toggleWishlist(productId, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    const btn = document.getElementById(`wishlist-btn-${productId}`);

    try {
        const data = await postJson('/wishlist/toggle', { product_id: productId });

        if (data.success) {
            if (btn) {
                btn.classList.toggle('active');
            }
            showToast(data.is_added ? window.__t['Product added to wishlist'] : window.__t['Product removed from wishlist'], 'success');
        } else if (data.error) {
            showToast(data.error, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast(window.__t['An error occurred'], 'error');
    }
}

export function exposeWishlistGlobals() {
    registerGlobals({ toggleWishlist });
}
