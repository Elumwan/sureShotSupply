import { Disclosure } from '@headlessui/react';
import { Link } from '@inertiajs/react';

const links = [
    { label: 'Shop', href: '/shop' },
    { label: 'Cameras', href: '/cameras' },
    { label: 'Accessories', href: '/shop' },
    { label: 'Contact', href: '/contact' },
];

export default function Nav() {
    return (
        <Disclosure
            as="nav"
            className="fixed inset-x-0 top-0 z-50 border-b border-site-border bg-site-bg/95 backdrop-blur-sm"
        >
            {({ open }) => (
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

                        <div className="hidden text-[10px] uppercase tracking-[0.2em] text-site-text-faint md:block">
                            Bag — 0
                        </div>

                        <div className="md:hidden">
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
                                    className="text-[10px] uppercase tracking-[0.22em] text-site-text-muted"
                                >
                                    {link.label}
                                </Link>
                            ))}
                            <div className="pt-2 text-[10px] uppercase tracking-[0.22em] text-site-text-faint">
                                Bag — 0
                            </div>
                        </div>
                    </Disclosure.Panel>
                </>
            )}
        </Disclosure>
    );
}
