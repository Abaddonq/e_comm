import { postJson } from '../../shared/http';
import { registerGlobals } from '../../shared/globals';
import { showToast } from '../../shared/toast';

export function initProductDetailPage() {
    const productPage = document.querySelector('.product-page[data-product-id]');
    if (!productPage) {
        return;
    }

    let currentVariant = null;

    async function checkWishlistStatus() {
        try {
            const productId = Number(productPage.dataset.productId || 0);
            if (!productId) {
                return;
            }

            const data = await postJson('/wishlist/check', { product_id: productId });
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
            const data = await postJson('/wishlist/toggle', { product_id: productId });
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
            const data = await postJson('/cart/add', {
                variant_id: currentVariant.id,
                quantity,
            });

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

    registerGlobals({
        toggleWishlistDetail,
        selectVariant,
        changeQuantity,
        openTab,
        addToCartFromDetail,
    });

    checkWishlistStatus();
    initVariant();
}
