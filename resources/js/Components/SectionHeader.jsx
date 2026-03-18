import { Link } from '@inertiajs/react';

export default function SectionHeader({ eyebrow, title, href, linkLabel, description }) {
    return (
        <div className="flex flex-col gap-4 border-b border-site-border pb-6 md:flex-row md:items-end md:justify-between">
            <div className="max-w-2xl">
                <p className="eyebrow">{eyebrow}</p>
                <h2 className="mt-3 text-3xl sm:text-4xl">{title}</h2>
                {description ? (
                    <p className="mt-3 text-sm leading-7 text-site-text-faint sm:text-base">
                        {description}
                    </p>
                ) : null}
            </div>

            {href && linkLabel ? (
                <Link
                    href={href}
                    className="text-[10px] uppercase tracking-[0.22em] text-site-text-faint hover:text-site-amber"
                >
                    {linkLabel}
                </Link>
            ) : null}
        </div>
    );
}
