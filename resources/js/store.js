import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        status: '',
        token: localStorage.getItem('token') || '',
        user:  localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : '',
    },
    mutations: {
        auth_request(state){
            state.status = 'loading';
        },
        auth_success (state, { token, user }){
            state.status = 'success';
            state.token = token;
            state.user = user;
        },
        logout(state){
            state.status = '';
            state.token = '';
        },
        auth_error(state){
            state.status = 'error';
        },
    },
    actions: {
        login({commit}, user) {
            return new Promise((resolve, reject) => {
                commit('auth_request');
                axios.post('/api/login', user)
                .then(response => { 
                    const token = response.data.access_token;
                    const user = response.data.user;
                    localStorage.setItem('token', token);
                    localStorage.setItem('user', JSON.stringify(user));
                    axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
                    commit('auth_success', { token, user });
                    resolve(response);
                })
                .catch(error => {
                    commit('auth_error');
                    localStorage.removeItem('token');
                    reject(error);
                });
            })
        },
        logout({commit}){
            return new Promise((resolve, reject) => {
                axios.get('/api/logout')
                .then(response => {
                    commit('logout');
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    delete axios.defaults.headers.common['Authorization'];
                    resolve(response);
                })
                .catch(error => {
                    commit('auth_error');
                    localStorage.removeItem('token');
                    reject(error);
                });
            })
        },
        logoutError({ commit }) {
            return new Promise((resolve, reject) => {
                commit('logout');
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                delete axios.defaults.headers.common['Authorization'];
            })
        },
    },
    getters: {
        isLoggedIn: state => !!state.token,
        isLoggedUser: state => state.user,
        isAuthStatus: state => state.status,
        isUserRole: state => state.user.role
    }
})