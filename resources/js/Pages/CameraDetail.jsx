import { useState } from 'react';
import { Link } from '@inertiajs/react';

import AddToBagButton from '../Components/AddToBagButton';
import ProductImage from '../Components/ProductImage';
import { formatPrice } from '../lib/storefront';
import SiteLayout from '../Layouts/SiteLayout';

function CameraDetail({ camera }) {
    const images = camera.full_images ?? [];
    const [selectedIndex, setSelectedIndex] = useState(0);
    const selectedImage = images[selectedIndex] ?? null;

    return (
        <main className="mx-auto max-w-7xl px-6 py-16 lg:px-10 lg:py-20">
            <Link
                href="/cameras"
                className="text-[10px] uppercase tracking-[0.22em] text-site-text-faint hover:text-site-amber"
            >
                ← All cameras
            </Link>

            <section className="mt-8 grid gap-10 lg:grid-cols-[1.05fr_0.95fr]">
                <div>
                    <ProductImage
                        src={selectedImage?.full ?? camera.featured_image ?? camera.primary_image}
                        alt={camera.name}
                        className="min-h-[420px] border border-site-border lg:min-h-[620px]"
                    />

                    {images.length > 1 ? (
                        <div className="mt-4 grid grid-cols-4 gap-3 sm:grid-cols-5">
                            {images.map((image, index) => (
                                <button
                                    key={`${image.thumb}-${index}`}
                                    type="button"
                                    onClick={() => setSelectedIndex(index)}
                                    className={`overflow-hidden border ${
                                        index === selectedIndex
                                            ? 'border-site-amber'
                                            : 'border-site-border'
                                    }`}
                                >
                                    <ProductImage
                                        src={image.thumb}
                                        alt={`${camera.name} ${index + 1}`}
                                        className="aspect-[4/3] w-full"
                                    />
                                </button>
                            ))}
                        </div>
                    ) : null}
                </div>

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
                        <AddToBagButton
                            type="camera"
                            id={camera.id}
                            disabled={camera.status !== 'available'}
                            disabledLabel={camera.status === 'sold' ? 'Sold' : 'Unavailable'}
                            className="mt-8"
                        />
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
