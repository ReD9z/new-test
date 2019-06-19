import axios from 'axios';
import VueRouter from 'vue-router';
import Vuex from 'vuex';
import store from './store.js';
import routers from './routes.js';
import Vue from 'vue';
import Vuetify from 'vuetify';
import ru from 'vuetify/es5/locale/ru'
import 'vuetify/dist/vuetify.min.css';

Vue.use(Vuetify, {
    lang: {
        locales: { ru },
        current: 'ru'
    }
});


require('./bootstrap');
Vue.use(require('vue-moment'));
window.Vue = require('vue');
Vue.use(VueRouter);
Vue.use(Vuex);

let token = window.localStorage.getItem('token');
if (token) {
    axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
}

/**
 * Vue components. 
*/

Vue.component('auth-component', require('./views/Auth.vue').default);
Vue.component('home-component', require('./views/Home.vue').default);
Vue.component('home-layout', require('./views/Layout.vue').default);
Vue.component('home-address', require('./views/Address.vue').default);
Vue.component('b-table', require('./components/tables/bTable.vue').default);
Vue.component('b-table-order', require('./components/tables/bTableOrder.vue').default);

/**
 * VueRouter components
 */

let router = new VueRouter({
    mode: 'history',
    routes: routers
});

/**
 * VueRouter redirect router
 */

router.beforeEach((to, from, next) => {
    if(to.matched.some(record => record.meta.requiresAuth)) {
        if (store.state.token) {
            next()
            return
        }
        next('/auth')
    } else {
        next()
    }
}) 

/**
 * App config
 */

const app = new Vue({
    el: '#app',
    store: new Vuex.Store(store),
    router: router
});
