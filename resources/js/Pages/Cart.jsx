import { Link, router, usePage } from '@inertiajs/react';
import { EmbeddedCheckout, EmbeddedCheckoutProvider } from '@stripe/react-stripe-js';
import { loadStripe } from '@stripe/stripe-js';
import { useState } from 'react';

import ProductImage from '../Components/ProductImage';
import { formatPrice } from '../lib/storefront';
import SiteLayout from '../Layouts/SiteLayout';

const publishableKey = import.meta.env.VITE_STRIPE_PUBLISHABLE_KEY ?? '';
const stripePromise = publishableKey ? loadStripe(publishableKey) : null;

function Cart({ items = [], total = 0 }) {
    const { flash = {} } = usePage().props;
    const [checkoutStarted, setCheckoutStarted] = useState(false);
    const [clientSecret, setClientSecret] = useState('');
    const [sessionId, setSessionId] = useState('');
    const [checkoutLoading, setCheckoutLoading] = useState(false);
    const [checkoutError, setCheckoutError] = useState('');

    async function startCheckout() {
        setCheckoutStarted(true);
        setCheckoutLoading(true);
        setCheckoutError('');

        try {
            const response = await fetch('/checkout/session', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-XSRF-TOKEN': decodeURIComponent(
                        document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] ?? '',
                    ),
                },
                body: JSON.stringify({}),
            });

            const payload = await response.json();

            if (!response.ok) {
                throw new Error(payload.message ?? 'Unable to start checkout right now.');
            }

            setClientSecret(payload.clientSecret ?? '');
            setSessionId(payload.sessionId ?? '');
        } catch (error) {
            setCheckoutError(error.message ?? 'Unable to start checkout right now.');
        } finally {
            setCheckoutLoading(false);
        }
    }

    function resetCheckout() {
        setCheckoutStarted(false);
        setClientSecret('');
        setSessionId('');
        setCheckoutLoading(false);
        setCheckoutError('');
    }

    async function handleShippingDetailsChange(event) {
        const country = event?.shippingDetails?.address?.country;

        if (!sessionId || !country) {
            return {
                type: 'reject',
                errorMessage: 'Sorry, we could not calculate shipping for this address. Please try again.',
            };
        }

        try {
            const response = await fetch('/checkout/shipping-rate', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-XSRF-TOKEN': decodeURIComponent(
                        document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] ?? '',
                    ),
                },
                body: JSON.stringify({
                    session_id: sessionId,
                    country,
                }),
            });

            const payload = await response.json();

            if (!response.ok || payload.success !== true) {
                return {
                    type: 'reject',
                    errorMessage: 'Sorry, we could not calculate shipping for this address. Please try again.',
                };
            }

            return { type: 'accept' };
        } catch {
            return {
                type: 'reject',
                errorMessage: 'Sorry, we could not calculate shipping for this address. Please try again.',
            };
        }
    }

    if (items.length === 0) {
        return (
            <main className="mx-auto max-w-4xl px-6 py-16 lg:px-10 lg:py-20">
                <p className="eyebrow">Your</p>
                <h1 className="mt-4 text-4xl sm:text-5xl">Bag</h1>
                <div className="mt-10 panel-surface p-10 text-center">
                    <p className="text-xl text-site-text-muted">Your bag is empty.</p>
                    <Link href="/shop" className="mt-6 inline-flex text-sm text-site-amber hover:text-site-text">
                        Return to the shop
                    </Link>
                </div>
            </main>
        );
    }

    return (
        <main className="mx-auto max-w-7xl px-6 py-16 lg:px-10 lg:py-20">
            <p className="eyebrow">Your</p>
            <h1 className="mt-4 text-4xl sm:text-5xl">Bag</h1>

            {flash.error ? (
                <div className="mt-6 border border-site-border-light bg-site-card px-4 py-3 text-sm text-site-text-muted">
                    {flash.error}
                </div>
            ) : null}

            <section className="mt-10 grid gap-8 lg:grid-cols-[1.25fr_0.75fr]">
                <div className="space-y-4">
                    {items.map((item) => {
                        const isProduct = item.type === 'product';
                        const model = item.model;

                        return (
                            <div
                                key={`${item.type}-${item.id}`}
                                className="panel-surface flex flex-col gap-5 p-5 sm:flex-row sm:items-center"
                            >
                                <div className="w-full shrink-0 sm:w-28">
                                    <ProductImage
                                        src={model.primary_image}
                                        alt={model.name}
                                        className="aspect-square min-h-[112px] border-0"
                                    />
                                </div>

                                <div className="min-w-0 flex-1">
                                    <div className="flex items-start justify-between gap-4">
                                        <div>
                                            <h2 className="text-2xl leading-tight">{model.name}</h2>
                                            <p className="mt-2 text-[11px] uppercase tracking-[0.18em] text-site-text-faint">
                                                {isProduct
                                                    ? 'Accessory'
                                                    : [model.condition, model.type].filter(Boolean).join(' · ')}
                                            </p>
                                        </div>

                                        <button
                                            type="button"
                                            onClick={() =>
                                                router.post(
                                                    '/cart/remove',
                                                    { type: item.type, id: item.id },
                                                    { preserveScroll: true },
                                                )
                                            }
                                            className="text-lg text-site-text-faint hover:text-site-amber"
                                        >
                                            ×
                                        </button>
                                    </div>

                                    <div className="mt-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                        {isProduct ? (
                                            <div className="inline-flex items-center border border-site-border">
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        router.post(
                                                            '/cart/update',
                                                            {
                                                                type: item.type,
                                                                id: item.id,
                                                                quantity: Math.max(0, item.quantity - 1),
                                                            },
                                                            { preserveScroll: true },
                                                        )
                                                    }
                                                    className="px-3 py-2 text-site-text-faint hover:text-site-text"
                                                >
                                                    −
                                                </button>
                                                <span className="min-w-10 text-center text-sm text-site-text-muted">
                                                    {item.quantity}
                                                </span>
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        router.post(
                                                            '/cart/update',
                                                            {
                                                                type: item.type,
                                                                id: item.id,
                                                                quantity: Math.min(
                                                                    item.quantity + 1,
                                                                    model.stock_quantity,
                                                                ),
                                                            },
                                                            { preserveScroll: true },
                                                        )
                                                    }
                                                    className="px-3 py-2 text-site-text-faint hover:text-site-text"
                                                >
                                                    +
                                                </button>
                                            </div>
                                        ) : (
                                            <span className="text-sm text-site-text-faint">Quantity: 1</span>
                                        )}

                                        <p className="text-lg font-light text-site-amber">
                                            {formatPrice(item.subtotal)}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                </div>

                <aside className="panel-surface h-fit p-6 sm:p-8">
                    <p className="eyebrow">Order summary</p>
                    <div className="mt-8 space-y-4 text-sm text-site-text-muted">
                        <div className="flex items-center justify-between">
                            <span>Subtotal</span>
                            <span>{formatPrice(total)}</span>
                        </div>
                        <div className="flex items-center justify-between">
                            <span>Shipping</span>
                            <span>Calculated at checkout</span>
                        </div>
                        <div className="flex items-center justify-between border-t border-site-border pt-4 text-base text-site-text">
                            <span>Total</span>
                            <span className="font-light text-site-amber">{formatPrice(total)}</span>
                        </div>
                    </div>

                    {!checkoutStarted ? (
                        <button type="button" onClick={startCheckout} className="ghost-button mt-8 w-full">
                            Proceed to checkout
                        </button>
                    ) : null}

                    {checkoutStarted ? (
                        <button
                            type="button"
                            onClick={resetCheckout}
                            className="mt-4 inline-flex text-sm text-site-text-faint hover:text-site-amber"
                        >
                            Back
                        </button>
                    ) : null}

                    {checkoutLoading ? (
                        <p className="mt-6 text-sm text-site-text-muted">Preparing secure checkout…</p>
                    ) : null}

                    {checkoutError ? (
                        <p className="mt-6 text-sm text-site-text-muted">{checkoutError}</p>
                    ) : null}

                    {checkoutStarted && !checkoutLoading && !clientSecret && !publishableKey ? (
                        <p className="mt-6 text-sm text-site-text-muted">
                            Stripe checkout is not configured in this environment.
                        </p>
                    ) : null}

                    {clientSecret && stripePromise ? (
                        <div className="mt-8">
                            <EmbeddedCheckoutProvider
                                stripe={stripePromise}
                                options={{
                                    clientSecret,
                                    onShippingDetailsChange: handleShippingDetailsChange,
                                }}
                            >
                                <EmbeddedCheckout />
                            </EmbeddedCheckoutProvider>
                        </div>
                    ) : null}

                    <Link href="/shop" className="mt-4 inline-flex text-sm text-site-text-faint hover:text-site-amber">
                        Continue shopping
                    </Link>
                </aside>
            </section>
        </main>
    );
}

Cart.layout = (page) => <SiteLayout>{page}</SiteLayout>;

export default Cart;
