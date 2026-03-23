import { Link } from '@inertiajs/react';

import CameraCard from '../Components/CameraCard';
import FeaturedCameraCard from '../Components/FeaturedCameraCard';
import Seo from '../Components/Seo';
import SectionHeader from '../Components/SectionHeader';
import SiteLayout from '../Layouts/SiteLayout';

function Cameras({ featuredCamera, cameras }) {
    const items = cameras.data ?? [];

    return (
        <main className="mx-auto max-w-7xl px-6 py-16 lg:px-10 lg:py-20">
            <Seo
                title="Cameras"
                description="Browse our hand-picked selection of second-hand cameras."
            />
            <SectionHeader
                eyebrow="Available now"
                title="Cameras"
                description="A rotating edit of film and digital bodies, chosen for feel, reliability, and the way they want to be carried."
            />

            {featuredCamera ? (
                <div className="mt-10">
                    <FeaturedCameraCard camera={featuredCamera} />
                </div>
            ) : null}

            {items.length ? (
                <>
                    <div className="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                        {items.map((camera) => (
                            <CameraCard key={camera.id} camera={camera} />
                        ))}
                    </div>

                    <div className="mt-10 flex flex-wrap gap-3">
                        {cameras.links.map((link) => (
                            <Link
                                key={`${link.label}-${link.url}`}
                                href={link.url || '#'}
                                preserveScroll
                                className={`border px-4 py-2 text-[10px] uppercase tracking-[0.2em] ${
                                    link.active
                                        ? 'border-site-amber text-site-amber'
                                        : 'border-site-border text-site-text-faint'
                                } ${!link.url ? 'pointer-events-none opacity-40' : 'hover:text-site-text'}`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                </>
            ) : (
                <div className="panel-surface mt-10 px-8 py-14 text-center">
                    <p className="eyebrow">Nothing live</p>
                    <p className="mt-4 text-lg text-site-text-muted">
                        There are no available cameras at the moment. Check back soon.
                    </p>
                </div>
            )}
        </main>
    );
}

Cameras.layout = (page) => <SiteLayout>{page}</SiteLayout>;

export default Cameras;
