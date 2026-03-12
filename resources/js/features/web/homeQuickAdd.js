import { postJson } from '../../shared/http';

export function initHomeQuickAdd() {
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.product-quick-add');
        if (!btn) {
            return;
        }

        const variantId = btn.dataset.variantId;
        if (!variantId) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();

        try {
            const data = await postJson('/cart/add', {
                variant_id: variantId,
                quantity: 1,
            });

            if (!data.success) {
                return;
            }

            const cartCount = document.getElementById('cart-count');
            if (cartCount) {
                cartCount.textContent = data.cart_count;
            }

            btn.classList.add('product-quick-add-success');

            setTimeout(() => {
                btn.classList.remove('product-quick-add-success');
            }, 1000);
        } catch (error) {
            console.error('Error:', error);
        }
    });
}
