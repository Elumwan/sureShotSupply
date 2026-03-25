import { Link } from '@inertiajs/react';

import Seo from '../Components/Seo';
import SiteLayout from '../Layouts/SiteLayout';

function formatAud(cents) {
    return `$${((cents ?? 0) / 100).toFixed(2)} AUD`;
}

function statusClasses(status) {
    if (status === 'pending') {
        return 'border border-site-border-light bg-site-card-alt text-site-text-faint';
    }

    return 'border border-site-amber/30 bg-site-amber/10 text-site-amber';
}

function CheckoutSuccess({ order, processing = false }) {
    const subtotal = order ? order.items.reduce((sum, item) => sum + (item.price * item.quantity), 0) : 0;
    const shippingAmount = order ? Math.max((order.total ?? 0) - subtotal, 0) : 0;

    return (
        <>
            <Seo title="Order Confirmed" robots="noindex, nofollow" />
            <main className="mx-auto max-w-5xl px-6 py-16 lg:px-10 lg:py-20">
                <div className="panel-surface p-8 sm:p-12">
                    <p className="eyebrow">{processing ? 'Payment received' : 'Confirmed'}</p>
                    <div className="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <h1 className="font-serif text-4xl italic text-site-amber sm:text-5xl">
                            {order?.reference ?? 'Order confirmed'}
                        </h1>
                        {order ? (
                            <span
                                className={`inline-flex w-fit px-3 py-2 text-[10px] uppercase tracking-[0.2em] ${statusClasses(order.status)}`}
                            >
                                {order.status}
                            </span>
                        ) : null}
                    </div>
                    <p className="mt-6 max-w-2xl text-base leading-8 text-site-text-muted">
                        {processing
                            ? 'Your payment is still processing. We will update this order as soon as Stripe confirms it.'
                            : 'Thanks for your order. Your confirmation email is on the way.'}
                    </p>

                    {order ? (
                        <>
                            <p className="mt-4 text-sm text-site-text-muted">
                                {new Date(order.created_at).toLocaleDateString('en-AU', {
                                    day: 'numeric',
                                    month: 'long',
                                    year: 'numeric',
                                })}
                            </p>

                            <div className="panel-surface mt-10 overflow-hidden">
                                <div className="border-b border-site-border px-6 py-4 text-[10px] uppercase tracking-[0.2em] text-site-text-faint">
                                    Order details
                                </div>

                                <div className="divide-y divide-site-border">
                                    {order.items.map((item, index) => (
                                        <div key={`${item.name}-${index}`} className="flex items-start justify-between gap-4 px-6 py-5">
                                            <div>
                                                <p className="text-base text-site-text">{item.name}</p>
                                                {item.quantity > 1 ? (
                                                    <p className="mt-1 text-[10px] uppercase tracking-[0.2em] text-site-text-faint">
                                                        Qty {item.quantity}
                                                    </p>
                                                ) : null}
                                            </div>
                                            <p className="text-right text-sm text-site-text-muted">
                                                {formatAud(item.price * item.quantity)}
                                            </p>
                                        </div>
                                    ))}

                                    <div className="flex items-center justify-between px-6 py-4 text-sm text-site-text-muted">
                                        <span>Subtotal</span>
                                        <span>{formatAud(subtotal)}</span>
                                    </div>
                                    <div className="flex items-center justify-between px-6 py-4 text-sm text-site-text-muted">
                                        <span>Shipping {order.shipping_rate_label ? `— ${order.shipping_rate_label}` : ''}</span>
                                        <span>{formatAud(shippingAmount)}</span>
                                    </div>
                                    <div className="flex items-center justify-between px-6 py-5 text-base text-site-text">
                                        <span>Total</span>
                                        <span className="font-light text-site-amber">{formatAud(order.total)}</span>
                                    </div>
                                </div>
                            </div>

                            {order.shipping_address ? (
                                <div className="panel-surface mt-8 p-8">
                                    <p className="eyebrow">Ships to</p>
                                    <div className="mt-6 space-y-1 text-sm leading-7 text-site-text-muted">
                                        <p>{order.shipping_address.line1}</p>
                                        {order.shipping_address.line2 ? <p>{order.shipping_address.line2}</p> : null}
                                        <p>
                                            {[
                                                order.shipping_address.city,
                                                order.shipping_address.state,
                                                order.shipping_address.postal_code,
                                            ]
                                                .filter(Boolean)
                                                .join(', ')}
                                        </p>
                                        <p>{order.shipping_address.country}</p>
                                    </div>
                                </div>
                            ) : null}
                        </>
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
        </>
    );
}

CheckoutSuccess.layout = (page) => <SiteLayout>{page}</SiteLayout>;

export default CheckoutSuccess;
