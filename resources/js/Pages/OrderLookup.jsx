import { Link, useForm, usePage } from '@inertiajs/react';

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

function OrderLookup({ order = null }) {
    const { flash = {} } = usePage().props;
    const params = typeof window === 'undefined' ? new URLSearchParams() : new URLSearchParams(window.location.search);
    const subtotal = order ? order.items.reduce((sum, item) => sum + item.subtotal, 0) : 0;

    const form = useForm({
        reference: params.get('reference') ?? '',
        email: params.get('email') ?? '',
    });

    function submit(event) {
        event.preventDefault();
        form.post('/order-lookup');
    }

    return (
        <main className="mx-auto max-w-5xl px-6 py-16 lg:px-10 lg:py-20">
            <Seo
                title="Track Your Order"
                description="Enter your order reference and email to view your order status."
                robots="noindex, nofollow"
            />
            {!order ? (
                <>
                    <p className="eyebrow">Track your</p>
                    <h1 className="mt-4 font-serif text-4xl italic sm:text-5xl">Order</h1>
                    <p className="mt-6 max-w-2xl text-base leading-8 text-site-text-muted">
                        Enter your order reference and email address to view your order details.
                    </p>

                    <div className="panel-surface mt-10 p-8 sm:p-10">
                        {flash.error ? (
                            <div className="mb-8 border border-site-border-light bg-site-card px-4 py-3 text-sm text-site-text-muted">
                                {flash.error}
                            </div>
                        ) : null}

                        <form onSubmit={submit} className="space-y-6">
                            <div>
                                <label className="mb-2 block text-[10px] uppercase tracking-[0.2em] text-site-text-faint">
                                    Order Reference
                                </label>
                                <input
                                    type="text"
                                    value={form.data.reference}
                                    placeholder="SSS-000001"
                                    onChange={(event) => form.setData('reference', event.target.value)}
                                    className="w-full border border-site-border bg-site-card-alt px-4 py-3 text-site-text outline-none transition focus:border-site-amber"
                                />
                                {form.errors.reference ? (
                                    <p className="mt-2 text-sm text-site-amber">{form.errors.reference}</p>
                                ) : null}
                            </div>

                            <div>
                                <label className="mb-2 block text-[10px] uppercase tracking-[0.2em] text-site-text-faint">
                                    Email Address
                                </label>
                                <input
                                    type="email"
                                    value={form.data.email}
                                    onChange={(event) => form.setData('email', event.target.value)}
                                    className="w-full border border-site-border bg-site-card-alt px-4 py-3 text-site-text outline-none transition focus:border-site-amber"
                                />
                                {form.errors.email ? (
                                    <p className="mt-2 text-sm text-site-amber">{form.errors.email}</p>
                                ) : null}
                            </div>

                            <button type="submit" disabled={form.processing} className="ghost-button">
                                Look up order
                            </button>
                        </form>
                    </div>
                </>
            ) : (
                <>
                    <p className="eyebrow">Order</p>
                    <div className="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <h1 className="font-serif text-4xl italic text-site-amber sm:text-5xl">{order.reference}</h1>
                        <span
                            className={`inline-flex w-fit px-3 py-2 text-[10px] uppercase tracking-[0.2em] ${statusClasses(order.status)}`}
                        >
                            {order.status}
                        </span>
                    </div>
                    <p className="mt-4 text-sm text-site-text-muted">{order.created_at}</p>

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
                                    <p className="text-right text-sm text-site-text-muted">{formatAud(item.subtotal)}</p>
                                </div>
                            ))}

                            <div className="flex items-center justify-between px-6 py-4 text-sm text-site-text-muted">
                                <span>Subtotal</span>
                                <span>{formatAud(subtotal)}</span>
                            </div>
                            <div className="flex items-center justify-between px-6 py-4 text-sm text-site-text-muted">
                                <span>Shipping {order.shipping_rate_label ? `— ${order.shipping_rate_label}` : ''}</span>
                                <span>{formatAud(order.shipping_cost)}</span>
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

                    <Link href="/order-lookup" className="mt-8 inline-flex text-sm text-site-text-faint hover:text-site-amber">
                        Look up another order
                    </Link>
                </>
            )}
        </main>
    );
}

OrderLookup.layout = (page) => <SiteLayout>{page}</SiteLayout>;

export default OrderLookup;
