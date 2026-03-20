import { Link } from '@inertiajs/react';

import { formatPrice } from '../lib/storefront';
import SiteLayout from '../Layouts/SiteLayout';

function CheckoutSuccess({ order, processing = false }) {
    return (
        <main className="mx-auto max-w-4xl px-6 py-16 lg:px-10 lg:py-20">
            <div className="panel-surface p-8 sm:p-12">
                <p className="eyebrow">{processing ? 'Payment received' : 'Confirmed'}</p>
                <h1 className="mt-4 text-4xl sm:text-5xl">Order confirmed</h1>
                <p className="mt-6 max-w-2xl text-base leading-8 text-site-text-muted">
                    {processing
                        ? 'Your payment is still processing. We will update this order as soon as Stripe confirms it.'
                        : 'Thanks for your order. A confirmation email will be added in a future sprint.'}
                </p>

                {order ? (
                    <div className="mt-10 border border-site-border p-6">
                        <div className="flex flex-col gap-3 text-sm text-site-text-muted sm:flex-row sm:items-center sm:justify-between">
                            <span>Order #{order.id}</span>
                            <span className="uppercase tracking-[0.16em] text-site-text-faint">{order.status}</span>
                        </div>
                        <p className="mt-5 text-xl font-light text-site-amber">{formatPrice(order.total)}</p>
                    </div>
                ) : null}

                <div className="mt-8 flex flex-col gap-3 sm:flex-row">
                    <Link href="/shop" className="ghost-button">
                        Continue shopping
                    </Link>
                    <Link href="/cameras" className="ghost-button">
                        View cameras
                    </Link>
                </div>
            </div>
        </main>
    );
}

CheckoutSuccess.layout = (page) => <SiteLayout>{page}</SiteLayout>;

export default CheckoutSuccess;
