import { Link } from '@inertiajs/react';

import { formatPrice } from '../lib/storefront';
import ProductImage from './ProductImage';

export default function CameraCard({ camera }) {
    return (
        <Link href={`/cameras/${camera.slug}`} className="group panel-surface overflow-hidden">
            <ProductImage
                src={camera.primary_image}
                alt={camera.name}
                className="min-h-[240px] border-0 transition duration-300 group-hover:scale-[1.01]"
            />

            <div className="p-6">
                <h3 className="text-2xl leading-tight">{camera.name}</h3>
                <p className="mt-3 text-[11px] uppercase tracking-[0.18em] text-site-text-faint">
                    {[camera.condition, camera.type].filter(Boolean).join(' · ')}
                </p>
                <p className="mt-5 text-lg font-light text-site-amber">{formatPrice(camera.price)}</p>
            </div>
        </Link>
    );
}
