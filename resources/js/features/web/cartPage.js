import { postJson } from '../../shared/http';
import { registerGlobals } from '../../shared/globals';
import { showToast } from '../../shared/toast';

export function initCartPage() {
    const cartPage = document.querySelector('.cart-page');
    if (!cartPage) {
        return;
    }

    async function updateQuantity(itemId, quantity) {
        try {
            const data = await postJson('/cart/update', {
                item_id: itemId,
                quantity: parseInt(quantity, 10),
            });

            if (data.success) {
                window.location.reload();
            }
        } catch (error) {
            console.error('Error:', error);
            showToast(window.__t['An error occurred'], 'error');
        }
    }

    async function removeItem(itemId) {
        if (!window.confirm(window.__t['Confirm Remove Item'])) {
            return;
        }

        try {
            const data = await postJson('/cart/remove', { item_id: itemId });
            if (data.success) {
                window.location.reload();
            }
        } catch (error) {
            console.error('Error:', error);
            showToast(window.__t['An error occurred'], 'error');
        }
    }

    registerGlobals({ updateQuantity, removeItem });
}
