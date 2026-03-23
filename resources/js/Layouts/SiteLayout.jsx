import { createContext, useContext } from 'react';
import { usePage } from '@inertiajs/react';

import Footer from '../Components/Footer';
import Nav from '../Components/Nav';
import Seo from '../Components/Seo';

const SiteSettingsContext = createContext({});

export function useSiteSettings() {
    return useContext(SiteSettingsContext);
}

export default function SiteLayout({ children }) {
    const { siteSettings = {} } = usePage().props;

    return (
        <SiteSettingsContext.Provider value={siteSettings}>
            <div className="min-h-screen bg-site-bg text-site-text">
                <Seo />
                <Nav />
                <div className="pt-20">{children}</div>
                <Footer />
            </div>
        </SiteSettingsContext.Provider>
    );
}
