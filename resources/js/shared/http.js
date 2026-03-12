export function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]');
}

export async function requestJson(url, options = {}) {
    const csrfToken = getCsrfToken();
    const method = (options.method || 'GET').toUpperCase();
    const hasJsonBody = Object.prototype.hasOwnProperty.call(options, 'body');

    const headers = {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...(method !== 'GET' && csrfToken ? { 'X-CSRF-TOKEN': csrfToken.content } : {}),
        ...(hasJsonBody ? { 'Content-Type': 'application/json' } : {}),
        ...(options.headers || {}),
    };

    const response = await fetch(url, {
        ...options,
        method,
        headers,
        ...(hasJsonBody ? { body: JSON.stringify(options.body) } : {}),
    });

    return response.json();
}

export async function getJson(url, options = {}) {
    return requestJson(url, { ...options, method: 'GET' });
}

export async function postJson(url, body, options = {}) {
    return requestJson(url, { ...options, method: 'POST', body });
}

export async function putJson(url, body, options = {}) {
    return requestJson(url, { ...options, method: 'PUT', body });
}

export async function deleteJson(url, body = undefined, options = {}) {
    return requestJson(url, {
        ...options,
        method: 'DELETE',
        ...(body === undefined ? {} : { body }),
    });
}
