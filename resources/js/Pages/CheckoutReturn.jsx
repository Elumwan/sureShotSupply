import { Link, router } from '@inertiajs/react';
import { useEffect, useState } from 'react';

import Seo from '../Components/Seo';
import SiteLayout from '../Layouts/SiteLayout';

function CheckoutReturn() {
    const [status, setStatus] = useState('loading');
    const [message, setMessage] = useState('Checking your payment status...');

    useEffect(() => {
        const sessionId = new URLSearchParams(window.location.search).get('session_id');

        if (!sessionId) {
            setStatus('open');
            setMessage('We could not find a checkout session to verify.');
            return;
        }

        async function checkSessionStatus() {
            try {
                const response = await fetch(
                    `/checkout/session-status?session_id=${encodeURIComponent(sessionId)}`,
                    {
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    },
                );
                const payload = await response.json();

                if (!response.ok) {
                    throw new Error(payload.message ?? 'Unable to verify payment status.');
                }

                if (payload.status === 'complete') {
                    router.visit(`/checkout/success?session_id=${encodeURIComponent(sessionId)}`);
                    return;
                }

                setStatus(payload.status ?? 'open');
                setMessage('Your payment was not completed. You can return to your bag and try again.');
            } catch (error) {
                setStatus('open');
                setMessage(error.message ?? 'Unable to verify payment status.');
            }
        }

        checkSessionStatus();
    }, []);

    return (
        <>
            <Seo title="Processing Order" robots="noindex, nofollow" />
            <main className="mx-auto max-w-4xl px-6 py-16 lg:px-10 lg:py-20">
                <div className="panel-surface p-8 sm:p-12">
                    <p className="eyebrow">{status === 'loading' ? 'Processing' : 'Checkout'}</p>
                    <h1 className="mt-4 text-4xl sm:text-5xl">
                        {status === 'loading' ? 'Checking payment' : 'Checkout incomplete'}
                    </h1>
                    <p className="mt-6 max-w-2xl text-base leading-8 text-site-text-muted">{message}</p>

                    {status !== 'loading' ? (
                        <Link href="/cart" className="mt-8 inline-flex text-sm text-site-text-faint hover:text-site-amber">
                            Return to bag
                        </Link>
                    ) : null}
                </div>
            </main>
        </>
    );
}

CheckoutReturn.layout = (page) => <SiteLayout>{page}</SiteLayout>;

export default CheckoutReturn;
