import { Link } from '@inertiajs/react';

import MediaPlaceholder from '../Components/MediaPlaceholder';
import { formatPrice } from '../lib/storefront';
import SiteLayout from '../Layouts/SiteLayout';

function CameraDetail({ camera }) {
    return (
        <main className="mx-auto max-w-7xl px-6 py-16 lg:px-10 lg:py-20">
            <Link
                href="/cameras"
                className="text-[10px] uppercase tracking-[0.22em] text-site-text-faint hover:text-site-amber"
            >
                ← All cameras
            </Link>

            <section className="mt-8 grid gap-10 lg:grid-cols-[1.05fr_0.95fr]">
                <MediaPlaceholder label={camera.name} className="min-h-[420px] lg:min-h-[620px]" />

                <div className="flex flex-col justify-between">
                    <div>
                        <p className="text-[11px] uppercase tracking-[0.18em] text-site-text-faint">
                            {[camera.make, camera.model, camera.year, camera.type].filter(Boolean).join(' · ')}
                        </p>
                        <h1 className="mt-4 text-4xl leading-tight sm:text-5xl">{camera.name}</h1>
                        <div className="mt-6 inline-flex border border-site-border-light px-3 py-2 text-[10px] uppercase tracking-[0.2em] text-site-text-muted">
                            Condition: {camera.condition}
                        </div>
                        {camera.includes ? (
                            <p className="mt-6 text-sm leading-7 text-site-text-muted">
                                Includes: {camera.includes}
                            </p>
                        ) : null}
                        <p className="mt-8 text-2xl font-light text-site-amber">{formatPrice(camera.price)}</p>
                        <button type="button" className="ghost-button mt-8">
                            Add to bag
                        </button>
                    </div>

                    {camera.description ? (
                        <div className="prose prose-invert mt-10 max-w-none prose-p:text-site-text-faint">
                            <p>{camera.description}</p>
                        </div>
                    ) : null}
                </div>
            </section>
        </main>
    );
}

CameraDetail.layout = (page) => <SiteLayout>{page}</SiteLayout>;

export default CameraDetail;
