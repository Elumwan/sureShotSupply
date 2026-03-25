import { useEffect, useState } from 'react';
import { Link } from '@inertiajs/react';

import AddToBagButton from '../Components/AddToBagButton';
import ProductImage from '../Components/ProductImage';
import Seo from '../Components/Seo';
import { formatPrice, stockStatus } from '../lib/storefront';
import SiteLayout from '../Layouts/SiteLayout';

function truncateDescription(description, fallback) {
    if (!description) {
        return fallback;
    }

    const trimmed = description.trim();

    if (trimmed.length <= 160) {
        return trimmed;
    }

    return `${trimmed.slice(0, 157).trimEnd()}...`;
}

function ProductDetail({ product }) {
    const images = product.full_images ?? [];
    const [selectedIndex, setSelectedIndex] = useState(0);
    const [quantity, setQuantity] = useState(1);
    const selectedImage = images[selectedIndex] ?? null;
    const maxQuantity = Math.max(product.stock_quantity ?? 0, 1);
    const description = truncateDescription(
        product.description,
        `${product.name} — available at SureShotSupply.`,
    );

    useEffect(() => {
        setQuantity((current) => Math.min(current, maxQuantity));
    }, [maxQuantity]);

    return (
        <main className="mx-auto max-w-7xl px-6 py-16 lg:px-10 lg:py-20">
            <Seo
                title={product.name}
                description={description}
                ogImage={product.primary_image}
                ogType="product"
            />
            <Link
                href="/shop"
                className="text-[10px] uppercase tracking-[0.22em] text-site-text-faint hover:text-site-amber"
            >
                ← All accessories
            </Link>

            <section className="mt-8 grid gap-10 lg:grid-cols-[1.05fr_0.95fr]">
                <div>
                    <ProductImage
                        src={selectedImage?.full ?? product.primary_image}
                        alt={product.name}
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
                                        alt={`${product.name} ${index + 1}`}
                                        className="aspect-square w-full"
                                    />
                                </button>
                            ))}
                        </div>
                    ) : null}
                </div>

                <div className="flex flex-col justify-between">
                    <div>
                        <p className="eyebrow">Permanent stock</p>
                        <h1 className="mt-4 text-4xl leading-tight sm:text-5xl">{product.name}</h1>
                        <p className="mt-6 text-sm uppercase tracking-[0.18em] text-site-text-faint">
                            {stockStatus(product.stock_quantity)}
                        </p>
                        <p className="mt-8 text-2xl font-light text-site-amber">{formatPrice(product.price)}</p>

                        {product.stock_quantity > 0 ? (
                            <div className="mt-8">
                                <p className="text-[10px] uppercase tracking-[0.2em] text-site-text-faint">
                                    Quantity
                                </p>
                                <div className="mt-3 inline-flex items-center border border-site-border">
                                    <button
                                        type="button"
                                        onClick={() => setQuantity((current) => Math.max(1, current - 1))}
                                        disabled={quantity <= 1}
                                        className="px-3 py-2 text-site-text-faint transition hover:text-site-text disabled:cursor-not-allowed disabled:opacity-40"
                                    >
                                        −
                                    </button>
                                    <span className="min-w-10 text-center text-sm text-site-text-muted">
                                        {quantity}
                                    </span>
                                    <button
                                        type="button"
                                        onClick={() => setQuantity((current) => Math.min(maxQuantity, current + 1))}
                                        disabled={quantity >= maxQuantity}
                                        className="px-3 py-2 text-site-text-faint transition hover:text-site-text disabled:cursor-not-allowed disabled:opacity-40"
                                    >
                                        +
                                    </button>
                                </div>
                            </div>
                        ) : null}

                        <AddToBagButton
                            type="product"
                            id={product.id}
                            quantity={quantity}
                            disabled={product.stock_quantity <= 0}
                            disabledLabel={product.stock_quantity <= 0 ? 'Out of Stock' : 'Unavailable'}
                            className="mt-8"
                        />
                    </div>

                    <div className="prose prose-invert mt-10 max-w-none prose-p:text-site-text-faint">
                        <p>{product.description || 'A dependable piece for daily carry.'}</p>
                    </div>
                </div>
            </section>
        </main>
    );
}

ProductDetail.layout = (page) => <SiteLayout>{page}</SiteLayout>;

export default ProductDetail;
