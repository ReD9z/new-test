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
import 'babel-polyfill';

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
// Vue.use(VueFuse);


let token = window.localStorage.getItem('token');
if (token) {
    axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
}

/**
 * Vue components. 
*/

Vue.component('auth-component', require('./views/Auth.vue').default);
Vue.component('home-layout', require('./views/Layout.vue').default);
Vue.component('home-address', require('./views/Address.vue').default);
Vue.component('b-table', require('./components/tables/bTable.vue').default);
Vue.component('b-table-order', require('./components/tables/order/bTableOrder.vue').default);
Vue.component('b-table-order-create', require('./components/tables/order/bTableOrderCreate.vue').default);
Vue.component('b-table-address-order-edit', require('./components/tables/order/bTableAddressEdit.vue').default);
Vue.component('b-maps', require('./components/bMaps.vue').default);
Vue.component('b-table-task', require('./components/tables/task/bTableTask.vue').default);
Vue.component('b-table-address', require('./components/tables/address/bTableAddress.vue').default);
Vue.component('b-table-clients', require('./components/tables/clients/bTableClients.vue').default);
Vue.component('b-add-client', require('./components/bAddClient.vue').default);
Vue.component('b-task-manager', require('./components/tables/task/bTableTaskManager.vue').default);
Vue.component('b-address-images', require('./components/tables/address/bTableAddressImage.vue').default);

let router = new VueRouter({
    mode: 'history',
    routes: routers
});

/**
 * VueRouter redirect router auth
 */

router.beforeEach((to, from, next) => {
    if(to.meta.requiresAuth) {
        if (store.getters.isLoggedIn) {
            if (!to.meta.managerAuth && store.getters.isUserRole === 'manager') {
                if (to.path !== '/error') {
                    next('/error');
                } else {
                    next();
                }
            }
            else if (!to.meta.moderatorAuth && store.getters.isUserRole === 'moderator') {
                if (to.path !== '/error') {
                    next('/error');
                } else {
                    next();
                }
            }

            else if (!to.meta.installerAuth && store.getters.isUserRole === 'installer') {
                if (to.path !== '/error') {
                    next('/error');
                } else {
                    next();
                }
            }

            else if (!to.meta.clientAuth && store.getters.isUserRole === 'client') {
                if (to.path !== '/error') {
                    next('/error');
                } else {
                    next();
                }
            } else {
                next();
            }
        } else {
            next('/auth')
        }
    } else {
        next()
    }
}) 


/**
 * App config
 */

new Vue({
    el: '#app',
    created: function () {
        let vm = this;
        axios.interceptors.response.use(undefined, err => {
            return new Promise(function (resolve, reject) {
                if (err.response.status === 401 && err.response.config && !err.response.config.__isRetryRequest) {
                    vm.$router.push('/auth')
                    vm.$store.dispatch('logoutError')
                }
                throw err;
            });
        });
    },
    store,
    router: router
});
