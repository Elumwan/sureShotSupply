import { Link } from '@inertiajs/react';

import { cameraMeta, formatPrice } from '../lib/storefront';
import AddToBagButton from './AddToBagButton';
import ProductImage from './ProductImage';

export default function FeaturedCameraCard({ camera }) {
    if (!camera) {
        return null;
    }

    const featuredImage = camera.featured_image ?? camera.full_images?.[0]?.featured ?? null;

    return (
        <div className="panel-surface grid overflow-hidden lg:grid-cols-[1.15fr_0.85fr]">
            <ProductImage
                src={featuredImage}
                alt={camera.name}
                className="min-h-[280px] border-0 lg:min-h-[420px]"
            />

            <div className="flex flex-col justify-between bg-site-card-alt p-8 sm:p-10">
                <div>
                    <p className="eyebrow">Just listed</p>
                    <h3 className="mt-4 text-3xl leading-tight sm:text-4xl">{camera.name}</h3>
                    <p className="mt-4 text-sm uppercase tracking-[0.16em] text-site-text-faint">
                        {cameraMeta(camera)}
                    </p>
                    {camera.includes ? (
                        <p className="mt-4 text-sm leading-7 text-site-text-muted">
                            Includes: {camera.includes}
                        </p>
                    ) : null}
                    {camera.description ? (
                        <p className="mt-6 text-sm leading-7 text-site-text-faint">
                            {camera.description}
                        </p>
                    ) : null}
                </div>

                <div className="mt-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <p className="text-xl font-light text-site-amber">{formatPrice(camera.price)}</p>
                    <div className="flex flex-col gap-3 sm:flex-row">
                        <Link href={`/cameras/${camera.slug}`} className="ghost-button">
                            View listing
                        </Link>
                        <AddToBagButton
                            type="camera"
                            id={camera.id}
                            disabled={camera.status !== 'available'}
                            disabledLabel={camera.status === 'sold' ? 'Sold' : 'Unavailable'}
                        />
                    </div>
                </div>
            </div>
        </div>
    );
}
