import { registerGlobals } from '../../shared/globals';

export function initStockAdjustModal() {
    const adjustModal = document.getElementById('adjustModal');
    const adjustmentsContainer = document.getElementById('adjustmentsContainer');

    if (!adjustModal || !adjustmentsContainer) {
        return;
    }

    let adjustmentCount = 1;

    function showAdjustModal() {
        adjustModal.classList.remove('hidden');
    }

    function hideAdjustModal() {
        adjustModal.classList.add('hidden');
    }

    function addAdjustmentRow() {
        const row = document.createElement('div');
        row.className = 'grid grid-cols-1 md:grid-cols-4 gap-4 adjustment-row';
        row.innerHTML = `
        <div class="col-span-1">
            <input type="text" name="adjustments[${adjustmentCount}][sku]" placeholder="Enter SKU" required
                class="w-full rounded-md border-gray-300 shadow-sm min-h-[44px]">
        </div>
        <div class="col-span-1">
            <input type="number" name="adjustments[${adjustmentCount}][quantity]" placeholder="e.g. 10 or -5" required
                class="w-full rounded-md border-gray-300 shadow-sm min-h-[44px]">
        </div>
        <div class="col-span-2">
            <input type="text" name="adjustments[${adjustmentCount}][reason]" placeholder="Reason for adjustment" required
                class="w-full rounded-md border-gray-300 shadow-sm min-h-[44px]">
        </div>
    `;

        adjustmentsContainer.appendChild(row);
        adjustmentCount += 1;
    }

    registerGlobals({ showAdjustModal, hideAdjustModal, addAdjustmentRow });
}
