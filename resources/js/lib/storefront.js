export function formatPrice(cents) {
    return new Intl.NumberFormat('en-AU', {
        style: 'currency',
        currency: 'AUD',
    }).format((cents ?? 0) / 100);
}

export function cameraMeta(camera) {
    return [camera.make, camera.year, camera.condition].filter(Boolean).join(' · ');
}

export function stockStatus(quantity) {
    if (quantity <= 0) {
        return 'Out of stock';
    }

    if (quantity <= 5) {
        return 'Low stock';
    }

    return 'In stock';
}

export function itemIsInCart(cartItems, type, id) {
    return (cartItems ?? []).some((item) => item.type === type && item.id === id);
}
