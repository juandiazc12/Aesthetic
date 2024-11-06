import { createInertiaApp } from '@inertiajs/react'
import { createRoot } from 'react-dom/client'
import Layout from "@/Layouts/Layout.tsx";

createInertiaApp({
    title: (title) =>
        title ? `${title} - Laravel Inertia React` : "Laravel Inertia React",
    resolve: (name) => {
        const pagesJsx = import.meta.glob("./Pages/**/*.jsx", { eager: true });
        const pagesTsx = import.meta.glob("./Pages/**/*.tsx", { eager: true });
        let page = pagesJsx[`./Pages/${name}.jsx`] || pagesTsx[`./Pages/${name}.tsx`];
        page.default.layout =
            page.default.layout || ((page) => <Layout children={page} />);
        return page;
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
    progress: {
        color: "#3d69ff",
        showSpinner: true,
    },
});
