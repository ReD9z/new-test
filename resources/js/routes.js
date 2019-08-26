let routers =  [
    {
        path: '/',
        name: 'home',
        component: require('./views/Address.vue').default,
        meta: { 
            title: 'Рабочий стол',     
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
        meta: {
            title: 'Адреса', 
        },
        component: require('./views/Address.vue').default
    },
    {
        path: '/tasks',
        name: 'tasks',
        meta: {
            title: 'Задачи',
        },
        component: require('./views/Tasks.vue').default
    },
    {
        path: '/users',
        name: 'users',
        meta: {
            title: 'Администраторы',
        },
        component: require('./views/Users.vue').default
    },
    {
        path: '/moderators',
        name: 'moderators',
        meta: {
            title: 'Модераторы',
        },
        component: require('./views/Moderators.vue').default
    },
    {
        path: '/citiestoworks',
        name: 'citiestoworks',
        meta: {
            title: 'Города работы',
        },
        component: require('./views/CitiesToWorks.vue').default
    },
    {
        path: '/areas',
        name: 'areas',
        meta: {
            title: 'Районы',
        },
        component: require('./views/Areas.vue').default
    },
    {
        path: '/clients',
        name: 'clients',
        meta: {
            title: 'Клиенты',
        },
        component: require('./views/Clients.vue').default
    },
    {
        path: '/installers',
        name: 'installers',
        meta: {
            title: 'Монтажники',
        },
        component: require('./views/Installers.vue').default
    },
    {
        path: '/managers',
        name: 'managers',
        meta: {
            title: 'Менеджеры',
        },
        component: require('./views/Managers.vue').default
    },
    {
        path: '/orders',
        name: 'orders',
        meta: {
            title: 'Заказы',
        },
        component: require('./views/Orders.vue').default
    },
    {
        path: '/orders-create',
        name: 'orders-create',
        meta: {
            title: 'Создать заказ',
        },
        component: require('./views/OrdersCreate.vue').default
    },
    {
        path: '/orders-address/:id',
        name: 'orders-address',
        props: true,
        meta: {
            title: 'Адреса заказов',
        },
        component: require('./views/OrdersToAddress.vue').default
    },
    {
        path: '/typestoworks',
        name: 'typestoworks',
        meta: {
            title: 'Типы заказов',
        },
        component: require('./views/TypesToWorks.vue').default
    },
];

export default routers;