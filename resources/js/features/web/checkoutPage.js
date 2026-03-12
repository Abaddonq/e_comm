export function initCheckoutPage() {
    const checkoutPage = document.querySelector('.checkout-page');
    if (!checkoutPage) {
        return;
    }

    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const cardFields = document.getElementById('card-fields');

    function toggleCardFields() {
        if (!cardFields) {
            return;
        }

        const selected = document.querySelector('input[name="payment_method"]:checked');
        const showCardFields = selected && selected.value === 'iyzico';
        cardFields.style.display = showCardFields ? 'block' : 'none';
        cardFields.classList.toggle('card-fields-hidden', !showCardFields);
    }

    function syncSelectableCards(groupSelector) {
        const group = document.querySelector(`[data-selectable-group="${groupSelector}"]`);
        if (!group) {
            return;
        }

        group.querySelectorAll('.option-card').forEach((card) => {
            const input = card.querySelector('input[type="radio"]');
            card.classList.toggle('active', Boolean(input && input.checked));
        });
    }

    paymentMethods.forEach((method) => {
        method.addEventListener('change', () => {
            toggleCardFields();
            syncSelectableCards('payment-method');
        });
    });

    const addressInputs = document.querySelectorAll('input[name="address_id"]');
    addressInputs.forEach((input) => {
        input.addEventListener('change', () => {
            syncSelectableCards('address');
        });
    });

    toggleCardFields();
    syncSelectableCards('payment-method');
    syncSelectableCards('address');
}
