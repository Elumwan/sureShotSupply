import { Disclosure } from '@headlessui/react';
import { Link, usePage } from '@inertiajs/react';

const links = [
    { label: 'Shop', href: '/shop' },
    { label: 'Cameras', href: '/cameras' },
    { label: 'Accessories', href: '/shop' },
    { label: 'Contact', href: '/contact' },
];

export default function Nav() {
    const { cartCount = 0 } = usePage().props;

    return (
        <Disclosure
            as="nav"
            className="fixed inset-x-0 top-0 z-50 border-b border-site-border bg-site-bg/95 backdrop-blur-sm"
        >
            {({ open, close }) => (
                <>
                    <div className="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-10">
                        <Link href="/" className="text-lg font-light tracking-[0.08em] text-site-text">
                            SureShot<span className="text-site-amber">Supply</span>
                        </Link>

                        <div className="hidden items-center gap-8 md:flex">
                            {links.map((link) => (
                                <Link
                                    key={link.label}
                                    href={link.href}
                                    className="text-[10px] uppercase tracking-[0.2em] text-site-text-faint hover:text-site-text"
                                >
                                    {link.label}
                                </Link>
                            ))}
                        </div>

                        <Link
                            href="/cart"
                            className="hidden text-[10px] uppercase tracking-[0.2em] text-site-text-faint hover:text-site-text md:block"
                        >
                            Bag — {cartCount}
                        </Link>

                        <div className="flex items-center gap-2 md:hidden">
                            <Link
                                href="/cart"
                                onClick={() => close()}
                                className="inline-flex items-center gap-2 px-2 py-1 text-site-text-faint transition hover:text-site-text"
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    strokeWidth="1.5"
                                    className="h-5 w-5"
                                    aria-hidden="true"
                                >
                                    <path d="M6.75 8.25h10.5l-.84 9.24a2.25 2.25 0 0 1-2.24 2.01H9.83a2.25 2.25 0 0 1-2.24-2.01l-.84-9.24Z" />
                                    <path d="M9 9V7.5a3 3 0 1 1 6 0V9" />
                                </svg>
                                <span
                                    className={`text-[10px] uppercase tracking-[0.2em] ${
                                        cartCount > 0 ? 'text-site-amber' : 'text-site-text-faint'
                                    }`}
                                >
                                    {cartCount}
                                </span>
                            </Link>
                            <Disclosure.Button className="flex flex-col gap-1.5 p-2 text-site-text-faint transition hover:text-site-text">
                                <span
                                    className={`h-px w-5 bg-current transition ${open ? 'translate-y-[7px] rotate-45' : ''}`}
                                />
                                <span
                                    className={`h-px w-5 bg-current transition ${open ? 'opacity-0' : ''}`}
                                />
                                <span
                                    className={`h-px w-5 bg-current transition ${open ? '-translate-y-[7px] -rotate-45' : ''}`}
                                />
                            </Disclosure.Button>
                        </div>
                    </div>

                    <Disclosure.Panel className="border-t border-site-border bg-site-card md:hidden">
                        <div className="flex flex-col gap-5 px-6 py-6">
                            {links.map((link) => (
                                <Link
                                    key={link.label}
                                    href={link.href}
                                    onClick={() => close()}
                                    className="text-[10px] uppercase tracking-[0.22em] text-site-text-muted"
                                >
                                    {link.label}
                                </Link>
                            ))}
                        </div>
                    </Disclosure.Panel>
                </>
            )}
        </Disclosure>
    );
}
