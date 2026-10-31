import { createInertiaApp } from '@inertiajs/react';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

void createInertiaApp({
    title: (title) => {
        if (!title) {
            return appName;
        }

        return title.includes(appName) ? title : `${title} - ${appName}`;
    },
    // Setiap halaman situs membawa header/footer-nya sendiri.
    layout: () => null,
    strictMode: true,
    progress: {
        color: '#2F5BE8',
    },
});
