import { Link } from '@inertiajs/react';

import { formatPrice } from '../lib/storefront';
import ProductImage from './ProductImage';

export default function CameraCard({ camera }) {
    return (
        <Link
            href={`/cameras/${camera.slug}`}
            className="group overflow-hidden border border-site-border bg-site-card transition duration-300 hover:border-site-border-light"
        >
            <ProductImage
                src={camera.primary_image}
                alt={camera.name}
                className="min-h-[240px] border-0 transition duration-300 group-hover:scale-[1.01]"
            />

            <div className="border-t border-site-border px-5 py-5">
                <h3 className="font-serif text-2xl italic leading-tight text-site-text">{camera.name}</h3>
                <p className="mt-3 text-lg font-light text-site-amber">{formatPrice(camera.price)}</p>
                <p className="mt-3 text-[11px] uppercase tracking-[0.18em] text-site-text-faint">
                    {[camera.condition, camera.type].filter(Boolean).join(' · ')}
                </p>
            </div>
        </Link>
    );
}
