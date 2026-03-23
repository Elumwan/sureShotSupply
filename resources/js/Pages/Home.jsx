import { usePage } from '@inertiajs/react';

import CameraCard from '../Components/CameraCard';
import FeaturedCameraCard from '../Components/FeaturedCameraCard';
import Hero from '../Components/Hero';
import ProductCard from '../Components/ProductCard';
import Seo from '../Components/Seo';
import SectionHeader from '../Components/SectionHeader';
import SiteLayout, { useSiteSettings } from '../Layouts/SiteLayout';

function Home({ featuredCamera, cameras, products }) {
    const siteSettings = useSiteSettings();
    const { seo = {} } = usePage().props;
    const moreCameras = (cameras ?? []).filter((camera) => camera.slug !== featuredCamera?.slug);

    return (
        <>
            <Seo
                title={null}
                description={seo.default_description}
                ogImage={seo.og_image}
            />
            <Hero
                heroImage={siteSettings.heroImage}
                heroTitle={siteSettings.heroTitle}
                heroSubtitle={siteSettings.heroSubtitle}
            />

            <main>
                {featuredCamera ? (
                    <section className="mx-auto max-w-7xl px-6 py-16 lg:px-10 lg:py-20">
                        <SectionHeader
                            eyebrow="Currently available"
                            title="Featured camera"
                            href="/cameras"
                            linkLabel="View all cameras →"
                        />

                        <div className="mt-10">
                            <FeaturedCameraCard camera={featuredCamera} />
                        </div>
                    </section>
                ) : null}

                <section className="mx-auto max-w-7xl px-6 py-16 lg:px-10 lg:py-20">
                    <SectionHeader eyebrow="More cameras" title="A small run of available bodies." />

                    <div className="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                        {moreCameras.map((camera) => (
                            <CameraCard key={camera.id} camera={camera} />
                        ))}
                    </div>
                </section>

                <section className="mx-auto max-w-7xl px-6 py-16 lg:px-10 lg:py-20">
                    <SectionHeader
                        eyebrow="Always in stock"
                        title="Accessories"
                        href="/shop"
                        linkLabel="View all →"
                    />

                        <div className="mt-10 grid gap-6 lg:grid-cols-3">
                            {products.map((product) => (
                                <ProductCard key={product.id} product={product} showButton />
                            ))}
                        </div>
                    </section>
            </main>
        </>
    );
}

Home.layout = (page) => <SiteLayout>{page}</SiteLayout>;

export default Home;
