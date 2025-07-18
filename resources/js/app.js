
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { fas } from '@fortawesome/free-solid-svg-icons'
import { fab } from '@fortawesome/free-brands-svg-icons'
import { far } from '@fortawesome/free-regular-svg-icons'
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

library.add(far)
library.add(fab)
library.add(fas)

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const options = {
  timeout: 3000,
  position: 'top-right',
  closeOnClick: true,
  pauseOnHover: true,
};
createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(Toast,) 
        // 👈 Registrar componente globalmente
        vueApp.component('font-awesome-icon', FontAwesomeIcon)

        vueApp.mount(el);
        return vueApp;
    },
    progress: {
        color: '#4B5563',
    },
});
