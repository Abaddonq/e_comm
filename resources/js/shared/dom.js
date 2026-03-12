export function clearElement(el) {
    if (!el) {
        return;
    }

    while (el.firstChild) {
        el.removeChild(el.firstChild);
    }
}

export function onDomReady(callback) {
    document.addEventListener('DOMContentLoaded', callback);
}
