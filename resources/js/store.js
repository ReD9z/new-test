let store = {
    state: {
        status: '',
        token: localStorage.getItem('token') || '',
        user:  localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : '',
        data: [],
        pagination: {}
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
        data_list(state, data) {
            state.data = data;
        },
        data_edit(state, data) {
            state.data = data;
        },
        data_push(state, data) {
            state.data.push(data);
        },
        data_push_excel(state, data) {
            data.forEach(element => {
                state.data.push(element);
            });
        },
        data_edit_new(state, data) {
            state.data = state.data.map((item) => {
                return item.id === data.id ? data : item;
            })
        },
        data_delete(state, id) {
            state.data.splice(state.data.indexOf(id), 1);
        },
        pagination(state, data) {
            let paginate = {
                first: data.links.first,
                last: data.links.last,
                next: data.links.next,
                prev: data.links.prev,
                last_page: data.meta.last_page,
                path: data.meta.path+"/?page=",
                currentPage: data.meta.current_page,
                per_page: data.meta.per_page,
                from: data.meta.from,
                to: data.meta.to,
                urlCurrent: data.meta.path + "/?page=" + data.meta.current_page
            }
            // перенести вызов в другое место в list
            state.pagination = paginate; 
        }
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
        call({ commit }, data) {
            return new Promise((resolve, reject) => {
                axios({
                    method: data.method,
                    url: data.url
                })
                .then(
                    response => {
                        console.log(response);
                        commit('data_list', response.data);
                           
                        resolve(response);
                    }
                ).catch(error => {
                    reject(error);
                })
            })
        },
        add({ commit }, data) {
            axios({
                method: data.method,
                url: data.url,
                data: data.list,
                params: {
                    images: data.images
                }
            })
            .then(
                response => {
                    console.log(response);
                    let array = response.data;
                    commit('data_push', array);
                }
            ).catch(error => {
                console.log(error);
            })
        },
        addExcel({ commit }, data) {
            axios({
                method: data.method,
                url: data.url +'/excel',
                data: data.list
            })
            .then(
                response => {
                    let array = response.data.data;
                    commit('data_push_excel', array);
                }
            ).catch(error => {
                console.log(error);
            })
        },
        save({ commit }, data) {
            axios({
                method: data.method,
                url: data.url,
                data: data.list,
                params: { 
                    images: data.images
                }
            })
            .then(
                response => {
                    // console.log(data.images);
                    // console.log(response.data);
                    commit('data_edit_new', response.data);
                }
            ).catch(error => {
                console.log(error);
            })
        },
        remove({ commit }, data) {
            axios({
                method: data.method,
                url: data.url,
            })
            .then(
                response => {
                    let id = response.data.id;
                    commit('data_delete', id);
                }
            ).catch(error => {
                console.log(error);
            })
        }
    },
    getters : {
        isLoggedIn: state => !!state.token,
        isLoggedUser: state => state.user,
        authStatus: state => state.status,
        dataCall: state => state.data,
        paginate: state => state.pagination
    }
};

export default store;