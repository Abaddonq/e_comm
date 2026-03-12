export function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]');
}

export async function getJson(url, options = {}) {
    const response = await fetch(url, {
        ...options,
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...(options.headers || {}),
        },
    });

    return response.json();
}

export async function postJson(url, body, options = {}) {
    const csrfToken = getCsrfToken();

    const response = await fetch(url, {
        method: 'POST',
        ...options,
        headers: {
            'Content-Type': 'application/json',
            ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken.content } : {}),
            ...(options.headers || {}),
        },
        body: JSON.stringify(body),
    });

    return response.json();
}
