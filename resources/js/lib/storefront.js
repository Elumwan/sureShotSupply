export function formatPrice(cents) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
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
