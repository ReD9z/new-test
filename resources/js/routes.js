let routers =  [
    {
        path: '/',
        name: 'home',
        component: require('./views/Home.vue').default,
        meta: { 
            requiresAuth: true
        }
    },
    {
        path: '/auth',
        name: 'auth',
        component: require('./views/Auth.vue').default
    },
    {
        path: '/address',
        name: 'address',
        component: require('./views/Address.vue').default
    },
    {
        path: '/users',
        name: 'users',
        component: require('./views/Users.vue').default
    },
    {
        path: '/moderators',
        name: 'moderators',
        component: require('./views/Moderators.vue').default
    },
    {
        path: '/citiestoworks',
        name: 'citiestoworks',
        component: require('./views/CitiesToWorks.vue').default
    },
    {
        path: '/addresstoorders',
        name: 'addresstoorders',
        component: require('./views/AddressToOrders.vue').default
    },
    {
        path: '/areas',
        name: 'areas',
        component: require('./views/Areas.vue').default
    },
    {
        path: '/clients',
        name: 'clients',
        component: require('./views/Clients.vue').default
    },
    {
        path: '/installers',
        name: 'installers',
        component: require('./views/Installers.vue').default
    },
    {
        path: '/managers',
        name: 'managers',
        component: require('./views/Managers.vue').default
    },
    {
        path: '/orders',
        name: 'orders',
        component: require('./views/Orders.vue').default
    },
    {
        path: '/tasks',
        name: 'tasks',
        component: require('./views/Tasks.vue').default
    },
    {
        path: '/typestoworks',
        name: 'typestoworks',
        component: require('./views/TypesToWorks.vue').default
    },
];

export default routers;