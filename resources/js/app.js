import Vue from 'vue'
import VueRouter from 'vue-router'
import {routes} from './routes.js'

import Sortable from 'vue-sortable'

Vue.use(Sortable);
Vue.use(VueRouter);

import App from './views/App'
import auth from './auth/index.js'

const router = new VueRouter({
    // mode: 'history',
    routes,
});

window.axios = require('axios');
window.axios.defaults.baseURL = '/api/';
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

router.beforeEach((to, from, next) => {
    if (to.meta.auth) {
        if (auth.check()) {
            next()
        } else {
            router.push('/login')
        }
    } else {
        if (to.name == 'login' && auth.check()) {
            router.push('/')
        } else {
            next()
        }
    }
});

const app = new Vue({
    el: '#app',
    components: { App },
    router,
});