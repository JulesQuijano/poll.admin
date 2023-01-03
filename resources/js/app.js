import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import {createInertiaApp, Link} from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

// Fontawesome
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from  '@fortawesome/vue-fontawesome';
import { faArrowLeft,
    faArrowRight,
    faFingerprint,
    faUpload,
    faTrashAlt,
    faFileCsv,
    faRetweet,
    faCalendarPlus,
    faPen,
    faPeopleGroup,
    faListCheck,
    faRectangleXmark,
    faLeftLong,
    faUserAlt} from '@fortawesome/free-solid-svg-icons';
library.add(faArrowLeft,
    faArrowRight,
    faFingerprint,
    faUpload,
    faTrashAlt,
    faFileCsv,
    faRetweet,
    faCalendarPlus,
    faPen,
    faPeopleGroup,
    faListCheck,
    faRectangleXmark,
    faLeftLong,
    faUserAlt);

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .component('Link',Link)
            .component('font-awesome-icon', FontAwesomeIcon)
            .mount(el);
    },
});

InertiaProgress.init({ color: '#e0e593', showSpinner: true });
