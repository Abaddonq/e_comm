import { showToast } from '../../shared/toast';

export function initProfilePage() {
    const profilePage = document.querySelector('.profile-page');
    if (!profilePage) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        return;
    }

    const profileUpdateUrl = profilePage.dataset.profileUpdateUrl;
    const passwordUpdateUrl = profilePage.dataset.passwordUpdateUrl;
    const destroyAccountUrl = profilePage.dataset.destroyAccountUrl;
    const addressStoreUrl = profilePage.dataset.addressStoreUrl;
    const homeUrl = profilePage.dataset.homeUrl;
    const wishlistToggleUrl = profilePage.dataset.wishlistToggleUrl;

    const addressesRaw = profilePage.dataset.addresses || '[]';
    let addresses = [];
    try {
        addresses = JSON.parse(addressesRaw);
    } catch (error) {
        console.error('Failed to parse addresses JSON:', error);
    }

    const newAddressLabel = profilePage.dataset.newAddressLabel || 'New Address';
    const editAddressLabel = profilePage.dataset.editAddressLabel || 'Edit Address';
    const confirmDeleteAddressText = profilePage.dataset.confirmDeleteAddress || 'Are you sure?';
    const confirmDeleteAccountText = profilePage.dataset.confirmDeleteAccount || 'Are you sure?';

    function switchTab(tab) {
        const url = new URL(window.location.href);
        url.searchParams.set('tab', tab);
        window.location.href = url.toString();
    }

    async function quickAdd(productId, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        try {
            const response = await fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.content,
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1,
                }),
            });

            const data = await response.json();
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

    async function removeFromWishlist(productId) {
        try {
            const response = await fetch(wishlistToggleUrl || '/wishlist/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.content,
                },
                body: JSON.stringify({ product_id: productId }),
            });

            const data = await response.json();
            if (data.success) {
                showToast(window.__t['Product removed from wishlist'], 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast(window.__t['An error occurred'], 'error');
        }
    }

    const profileForm = document.getElementById('profileForm');
    if (profileForm && profileUpdateUrl) {
        profileForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);

            try {
                const response = await fetch(profileUpdateUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken.content,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(Object.fromEntries(formData)),
                });

                const data = await response.json();
                const alert = document.getElementById('profileAlert');

                if (data.success && alert) {
                    alert.textContent = data.message;
                    alert.style.display = 'block';
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    }

    const passwordForm = document.getElementById('passwordForm');
    if (passwordForm && passwordUpdateUrl) {
        passwordForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const successAlert = document.getElementById('passwordAlert');
            const errorAlert = document.getElementById('passwordError');

            if (successAlert) {
                successAlert.style.display = 'none';
            }
            if (errorAlert) {
                errorAlert.style.display = 'none';
            }

            try {
                const response = await fetch(passwordUpdateUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken.content,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(Object.fromEntries(formData)),
                });

                const data = await response.json();

                if (data.success) {
                    if (successAlert) {
                        successAlert.textContent = data.message;
                        successAlert.style.display = 'block';
                    }
                    e.target.reset();
                } else if (errorAlert) {
                    errorAlert.textContent = data.errors?.current_password?.[0] || window.__t['An error occurred'];
                    errorAlert.style.display = 'block';
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    }

    function openModal() {
        const addressModal = document.getElementById('addressModal');
        const modalTitle = document.getElementById('modalTitle');
        const addressForm = document.getElementById('addressForm');
        const addressId = document.getElementById('addressId');

        if (addressModal) {
            addressModal.classList.add('active');
        }
        if (modalTitle) {
            modalTitle.textContent = newAddressLabel;
        }
        if (addressForm) {
            addressForm.reset();
        }
        if (addressId) {
            addressId.value = '';
        }
    }

    function closeModal() {
        const addressModal = document.getElementById('addressModal');
        if (addressModal) {
            addressModal.classList.remove('active');
        }
    }

    const addressForm = document.getElementById('addressForm');
    if (addressForm) {
        addressForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const addressId = document.getElementById('addressId').value;
            const isEdit = addressId !== '';

            const payload = {
                full_name: document.getElementById('fullName').value,
                phone: document.getElementById('phone').value,
                address_line1: document.getElementById('addressLine1').value,
                address_line2: document.getElementById('addressLine2').value,
                city: document.getElementById('city').value,
                state: document.getElementById('state').value,
                postal_code: document.getElementById('postalCode').value,
                country: document.getElementById('country').value,
                is_default: document.getElementById('isDefault').checked,
            };

            const url = isEdit ? `/addresses/${addressId}` : addressStoreUrl || '/addresses';

            try {
                const response = await fetch(url, {
                    method: isEdit ? 'PUT' : 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken.content,
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    closeModal();
                    window.location.reload();
                } else {
                    let errorMessage = result.message || window.__t['An error occurred'];
                    if (result.errors) {
                        errorMessage = Object.values(result.errors).flat().join('\n');
                    }
                    window.alert(errorMessage);
                }
            } catch (error) {
                console.error('Error:', error);
                window.alert(`${window.__t['An error occurred']}: ${error.message}`);
            }
        });
    }

    function editAddress(id) {
        const address = addresses.find((item) => Number(item.id) === Number(id));
        if (!address) {
            return;
        }

        document.getElementById('addressId').value = address.id;
        document.getElementById('modalTitle').textContent = editAddressLabel;
        document.getElementById('fullName').value = address.full_name;
        document.getElementById('phone').value = address.phone;
        document.getElementById('addressLine1').value = address.address_line1;
        document.getElementById('addressLine2').value = address.address_line2 || '';
        document.getElementById('city').value = address.city;
        document.getElementById('state').value = address.state || '';
        document.getElementById('postalCode').value = address.postal_code;
        document.getElementById('country').value = address.country;
        document.getElementById('isDefault').checked = Boolean(address.is_default);

        const addressModal = document.getElementById('addressModal');
        if (addressModal) {
            addressModal.classList.add('active');
        }
    }

    async function deleteAddress(id) {
        if (!window.confirm(confirmDeleteAddressText)) {
            return;
        }

        try {
            const response = await fetch(`/addresses/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken.content,
                    Accept: 'application/json',
                },
            });

            const data = await response.json();

            if (response.ok && data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message || window.__t['An error occurred'], 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast(window.__t['An error occurred'], 'error');
        }
    }

    async function setDefaultAddress(id) {
        try {
            const response = await fetch(`/addresses/${id}/set-default`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken.content,
                },
            });

            const data = await response.json();

            if (data.success) {
                window.location.reload();
            } else {
                window.alert(data.message || window.__t['An error occurred']);
            }
        } catch (error) {
            console.error('Error:', error);
            window.alert(window.__t['An error occurred']);
        }
    }

    const addressModal = document.getElementById('addressModal');
    if (addressModal) {
        addressModal.addEventListener('click', (e) => {
            if (e.target === e.currentTarget) {
                closeModal();
            }
        });
    }

    const deleteAccountForm = document.getElementById('deleteAccountForm');
    if (deleteAccountForm && destroyAccountUrl) {
        deleteAccountForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);
            const errorAlert = document.getElementById('deleteAccountError');
            const successAlert = document.getElementById('deleteAccountSuccess');

            if (errorAlert) {
                errorAlert.style.display = 'none';
            }
            if (successAlert) {
                successAlert.style.display = 'none';
            }

            if (!window.confirm(confirmDeleteAccountText)) {
                return;
            }

            try {
                const response = await fetch(destroyAccountUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken.content,
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                    },
                    body: JSON.stringify(Object.fromEntries(formData)),
                });

                const data = await response.json();

                if (data.success) {
                    if (successAlert) {
                        successAlert.textContent = data.message;
                        successAlert.style.display = 'block';
                    }
                    setTimeout(() => {
                        window.location.href = homeUrl || '/';
                    }, 2000);
                } else if (errorAlert) {
                    errorAlert.textContent = data.message;
                    errorAlert.style.display = 'block';
                }
            } catch (error) {
                console.error('Error:', error);
                if (errorAlert) {
                    errorAlert.textContent = window.__t['An error occurred'];
                    errorAlert.style.display = 'block';
                }
            }
        });
    }

    window.switchTab = switchTab;
    window.quickAdd = quickAdd;
    window.removeFromWishlist = removeFromWishlist;
    window.openModal = openModal;
    window.closeModal = closeModal;
    window.editAddress = editAddress;
    window.deleteAddress = deleteAddress;
    window.setDefaultAddress = setDefaultAddress;
}
