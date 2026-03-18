import { Link } from '@inertiajs/react';

export default function Hero({ heroImage, heroTitle, heroSubtitle }) {
    const style = heroImage
        ? {
              backgroundImage: `url(${heroImage})`,
              backgroundSize: 'cover',
              backgroundPosition: 'center',
          }
        : undefined;

    return (
        <section className="relative isolate overflow-hidden border-b border-site-border" style={style}>
            <div className={`absolute inset-0 ${heroImage ? 'bg-[#0a0a0a]/70' : 'bg-site-bg'}`} />
            <div className="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(196,146,74,0.14),transparent_35%),linear-gradient(180deg,rgba(20,20,20,0.2)_0%,rgba(20,20,20,0.9)_100%)]" />

            {!heroImage && (
                <div className="absolute inset-0 bg-[linear-gradient(120deg,rgba(255,255,255,0.02),transparent_30%),radial-gradient(circle_at_20%_20%,rgba(196,146,74,0.08),transparent_25%),radial-gradient(circle_at_80%_30%,rgba(255,255,255,0.04),transparent_22%)]" />
            )}

            <div className="relative mx-auto flex min-h-[360px] max-w-7xl items-end px-6 py-16 sm:min-h-[420px] lg:min-h-[460px] lg:px-10 lg:py-20">
                <div className="max-w-3xl">
                    <p className="eyebrow">SureShotSupply</p>
                    <h1 className="mt-5 max-w-2xl text-4xl leading-tight text-site-text sm:text-5xl lg:text-6xl">
                        {heroTitle}
                    </h1>
                    <p className="mt-5 max-w-2xl text-base font-light leading-7 text-site-text-muted sm:text-lg">
                        {heroSubtitle}
                    </p>
                    <Link
                        href="/cameras"
                        className="mt-8 inline-flex items-center gap-3 text-[10px] uppercase tracking-[0.24em] text-site-text hover:text-site-amber"
                    >
                        Explore cameras
                        <span className="block h-px w-12 bg-site-amber" />
                    </Link>
                </div>
            </div>
        </section>
    );
}
