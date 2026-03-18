import { Link } from '@inertiajs/react';

export default function Footer() {
    return (
        <footer className="border-t border-site-border bg-[#0e0e0e]">
            <div className="mx-auto flex max-w-7xl flex-col items-center justify-between gap-6 px-6 py-8 text-center lg:flex-row lg:px-10 lg:text-left">
                <Link href="/" className="text-sm font-light tracking-[0.08em] text-[#2e2e2e]">
                    SureShot<span className="text-site-amber/60">Supply</span>
                </Link>

                <div className="flex flex-col items-center gap-4 text-[10px] uppercase tracking-[0.22em] text-[#2e2e2e] sm:flex-row">
                    <Link href="/contact" className="hover:text-site-text-faint">
                        Contact
                    </Link>
                    <a href="#" className="hover:text-site-text-faint">
                        Returns
                    </a>
                    <a href="https://instagram.com" className="hover:text-site-text-faint">
                        Instagram
                    </a>
                </div>
            </div>
        </footer>
    );
}
