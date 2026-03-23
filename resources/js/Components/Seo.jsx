import { usePage } from '@inertiajs/react';
import { Helmet } from 'react-helmet-async';

function normalizeTwitterHandle(handle) {
    if (!handle) {
        return null;
    }

    return handle.startsWith('@') ? handle : `@${handle}`;
}

export default function Seo({
    title,
    description,
    ogImage,
    canonical,
    ogType = 'website',
    robots = 'index, follow',
}) {
    const page = usePage();
    const { seo = {} } = page.props;
    const siteName = seo.site_name ?? 'SureShotSupply';
    const resolvedTitle = title ?? seo.default_title ?? siteName;
    const resolvedDescription = description ?? seo.default_description ?? '';
    const resolvedOgImage = ogImage ?? seo.og_image ?? null;
    const resolvedCanonical = canonical
        ?? (typeof window !== 'undefined'
            ? new URL(page.url ?? '/', window.location.origin).toString()
            : page.url ?? '/');
    const documentTitle = title ? `${title} | ${siteName}` : resolvedTitle;
    const twitterHandle = normalizeTwitterHandle(seo.twitter_handle);

    return (
        <Helmet>
            <title>{documentTitle}</title>
            <meta name="description" content={resolvedDescription} />
            <meta name="robots" content={robots} />
            <link rel="canonical" href={resolvedCanonical} />

            <meta property="og:type" content={ogType} />
            <meta property="og:title" content={documentTitle} />
            <meta property="og:description" content={resolvedDescription} />
            <meta property="og:url" content={resolvedCanonical} />
            <meta property="og:site_name" content={siteName} />
            {resolvedOgImage ? <meta property="og:image" content={resolvedOgImage} /> : null}

            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:title" content={documentTitle} />
            <meta name="twitter:description" content={resolvedDescription} />
            {resolvedOgImage ? <meta name="twitter:image" content={resolvedOgImage} /> : null}
            {twitterHandle ? <meta name="twitter:site" content={twitterHandle} /> : null}
        </Helmet>
    );
}
