import { Link } from '@inertiajs/react';

import SiteLayout from '../Layouts/SiteLayout';

function CheckoutCancel() {
    return (
        <main className="mx-auto max-w-4xl px-6 py-16 lg:px-10 lg:py-20">
            <div className="panel-surface p-8 sm:p-12">
                <p className="eyebrow">Checkout</p>
                <h1 className="mt-4 text-4xl sm:text-5xl">Payment cancelled</h1>
                <p className="mt-6 max-w-2xl text-base leading-8 text-site-text-muted">
                    No problem. Your bag has been preserved, and you can return to checkout whenever you are ready.
                </p>
                <Link href="/cart" className="ghost-button mt-8 inline-flex">
                    Return to bag
                </Link>
            </div>
        </main>
    );
}

CheckoutCancel.layout = (page) => <SiteLayout>{page}</SiteLayout>;

export default CheckoutCancel;
