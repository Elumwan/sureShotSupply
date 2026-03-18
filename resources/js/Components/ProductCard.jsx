import { Link } from '@inertiajs/react';

import { formatPrice } from '../lib/storefront';
import ProductImage from './ProductImage';

export default function ProductCard({ product, showButton = false }) {
    return (
        <div className="panel-surface overflow-hidden">
            <Link href={`/shop/${product.slug}`} className="group block">
                <ProductImage
                    src={product.primary_image}
                    alt={product.name}
                    className="min-h-[220px] border-0"
                />
                <div className="p-6">
                    <div className="mb-4 inline-flex h-9 w-9 items-center justify-center rounded-full border border-site-border-light text-site-amber">
                        <span className="text-sm">+</span>
                    </div>
                    <h3 className="text-2xl leading-tight">{product.name}</h3>
                    <p className="mt-4 text-sm leading-7 text-site-text-faint">
                        {product.description || 'Everyday carry essential for deliberate photographers.'}
                    </p>
                    <p className="mt-5 text-lg font-light text-site-amber">{formatPrice(product.price)}</p>
                </div>
            </Link>

            {showButton ? (
                <div className="px-6 pb-6">
                    <button type="button" className="ghost-button w-full">
                        Add to bag
                    </button>
                </div>
            ) : null}
        </div>
    );
}
