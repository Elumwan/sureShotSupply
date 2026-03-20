import { useEffect, useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';

import { itemIsInCart } from '../lib/storefront';

export default function AddToBagButton({
    type,
    id,
    quantity = 1,
    disabled = false,
    disabledLabel = 'Unavailable',
    className = '',
}) {
    const { cartItems = [] } = usePage().props;
    const [processing, setProcessing] = useState(false);
    const [added, setAdded] = useState(false);
    const inBag = itemIsInCart(cartItems, type, id);

    useEffect(() => {
        if (!added) {
            return undefined;
        }

        const timer = window.setTimeout(() => setAdded(false), 1400);

        return () => window.clearTimeout(timer);
    }, [added]);

    if (disabled) {
        return (
            <button type="button" disabled className={`ghost-button opacity-50 ${className}`.trim()}>
                {disabledLabel}
            </button>
        );
    }

    if (inBag) {
        return (
            <Link href="/cart" className={`ghost-button ${className}`.trim()}>
                In Bag
            </Link>
        );
    }

    return (
        <button
            type="button"
            disabled={processing}
            onClick={() => {
                setProcessing(true);

                router.post(
                    '/cart/add',
                    { type, id, quantity },
                    {
                        preserveScroll: true,
                        onSuccess: () => setAdded(true),
                        onFinish: () => setProcessing(false),
                    },
                );
            }}
            className={`ghost-button ${className}`.trim()}
        >
            {processing ? 'Adding...' : added ? 'Added' : 'Add to Bag'}
        </button>
    );
}
