import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import { HelmetProvider } from 'react-helmet-async';

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.jsx', { eager: true });

        return pages[`./Pages/${name}.jsx`];
    },
    setup({ el, App, props }) {
        createRoot(el).render(
            <HelmetProvider>
                <App {...props} />
            </HelmetProvider>,
        );
    },
    progress: {
        color: '#111827',
    },
});
