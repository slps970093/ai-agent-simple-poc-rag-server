import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { Quasar, Notify, Dialog, Loading } from 'quasar';
import { ZiggyVue } from 'ziggy-js';

import '@fortawesome/fontawesome-free/css/all.min.css';
import 'quasar/dist/quasar.css';

createInertiaApp({
    title: (title) => title ? `${title} - Comet Admin` : 'Comet Admin',
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(Quasar, {
                plugins: {
                    Notify,
                    Dialog,
                    Loading,
                },
                config: {
                    iconSet: {
                        type: { positive: 'fas fa-check', negative: 'fas fa-times', warning: 'fas fa-exclamation-triangle', info: 'fas fa-info-circle' },
                        arrowUp: 'fas fa-arrow-up',
                        arrowDown: 'fas fa-arrow-down',
                        arrowLeft: 'fas fa-arrow-left',
                        arrowRight: 'fas fa-arrow-right',
                        check: 'fas fa-check',
                        close: 'fas fa-times',
                        menu: 'fas fa-bars',
                        search: 'fas fa-search',
                        clear: 'fas fa-times',
                        firstPage: 'fas fa-angle-double-left',
                        prevPage: 'fas fa-angle-left',
                        nextPage: 'fas fa-angle-right',
                        lastPage: 'fas fa-angle-double-right',
                    },
                },
            })
            .mount(el);
    },
});
