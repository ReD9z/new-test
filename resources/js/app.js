import axios from 'axios';
import VueRouter from 'vue-router';
import Vuex from 'vuex';
import store from './store.js';
import routers from './routes.js';
import Vue from 'vue';
import Vuetify from 'vuetify';
import ru from 'vuetify/es5/locale/ru';
import 'vuetify/dist/vuetify.min.css';
import VeeValidate, { Validator } from 'vee-validate';
import VeeValidateLaravel from 'vee-validate-laravel';
import ruVee from 'vee-validate/dist/locale/ru';

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
Validator.localize({ ru: ruVee })
Vue.use(VeeValidate, { inject: false, locale: 'ru' })
Vue.use(VeeValidateLaravel);

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
Vue.component('b-table-order', require('./components/tables/order/bTableOrder.vue').default);
Vue.component('b-table-order-create', require('./components/tables/order/bTableOrderCreate.vue').default);
Vue.component('b-table-address-order', require('./components/tables/order/bTableAddressOrder.vue').default);
Vue.component('b-table-address-order-edit', require('./components/tables/order/bTableAddressEdit.vue').default);
Vue.component('b-maps', require('./components/bMaps.vue').default);
Vue.component('b-table-task', require('./components/tables/task/bTableTask.vue').default);
Vue.component('b-table-address', require('./components/tables/address/bTableAddress.vue').default);

let router = new VueRouter({
    mode: 'history',
    routes: routers
});

/**
 * VueRouter redirect router auth
 */

router.beforeEach((to, from, next) => {
    if(to.matched.some(record => record.meta.requiresAuth)) {
        if (store.getters.isLoggedIn) {
            if(to.meta.adminAuth) {
                if (store.getters.isUserRole === 'admin') {
                    next()
                    return
                }
            }
            if (to.meta.moderatorAuth) {
                if (store.getters.isUserRole === 'moderator') {
                    next()
                    return
                }
            }
            if (to.meta.installerAuth) {
                if (store.getters.isUserRole === 'installer') {
                    next()
                    return
                }
            }
            if (to.meta.managerAuth) {
                if (store.getters.isUserRole === 'manager') {
                    next()
                    return
                }
            }
            if (to.meta.clientAuth) {
                if (store.getters.isUserRole === 'client') {
                    next()
                    return
                }
            }
            // next()
            // return
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
    store,
    router: router
});
