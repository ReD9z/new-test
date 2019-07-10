let routers =  [
    {
        path: '/',
        name: 'home',
        component: require('./views/Home.vue').default,
        meta: { 
            title: 'Рабочий стол',
            requiresAuth: true,
            adminAuth: true,
            moderatorAuth: true,
            installerAuth: true,
            managerAuth: true,
            clientAuth: true       
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
            adminAuth: true,
            moderatorAuth: true,
            installerAuth: false,
            managerAuth: false,
            clientAuth: false   
        },
        component: require('./views/Address.vue').default
    },
    {
        path: '/clients_managers',
        name: 'clients_managers',
        meta: {
            title: 'Клиенты',
            adminAuth: false,
            moderatorAuth: false,
            installerAuth: false,
            managerAuth: true,
            clientAuth: false
        },
        component: require('./views/ClientsManagers.vue').default
    },
    {
        path: '/tasks',
        name: 'tasks',
        meta: {
            title: 'Задачи',
            adminAuth: true,
            moderatorAuth: true,
            installerAuth: true,
            managerAuth: false,
            clientAuth: false
        },
        component: require('./views/Tasks.vue').default
    },
    {
        path: '/users',
        name: 'users',
        meta: {
            title: 'Администраторы',
            adminAuth: true,
            moderatorAuth: false,
            installerAuth: false,
            managerAuth: false,
            clientAuth: false  
        },
        component: require('./views/Users.vue').default
    },
    {
        path: '/moderators',
        name: 'moderators',
        meta: {
            title: 'Модераторы',
            adminAuth: true,
            moderatorAuth: false,
            installerAuth: false,
            managerAuth: false,
            clientAuth: false   
        },
        component: require('./views/Moderators.vue').default
    },
    {
        path: '/citiestoworks',
        name: 'citiestoworks',
        meta: {
            title: 'Города работы',
            adminAuth: true,
            moderatorAuth: false,
            installerAuth: false,
            managerAuth: false,
            clientAuth: false   
        },
        component: require('./views/CitiesToWorks.vue').default
    },
    {
        path: '/areas',
        name: 'areas',
        meta: {
            title: 'Районы',
            adminAuth: true,
            moderatorAuth: false,
            installerAuth: false,
            managerAuth: false,
            clientAuth: false  
        },
        component: require('./views/Areas.vue').default
    },
    {
        path: '/clients',
        name: 'clients',
        meta: {
            title: 'Клиенты',
            adminAuth: true,
            moderatorAuth: false,
            installerAuth: false,
            managerAuth: false,
            clientAuth: true  
        },
        component: require('./views/Clients.vue').default
    },
    {
        path: '/installers',
        name: 'installers',
        meta: {
            title: 'Монтажники',
            adminAuth: true,
            moderatorAuth: true,
            installerAuth: false,
            managerAuth: false,
            clientAuth: false  
        },
        component: require('./views/Installers.vue').default
    },
    {
        path: '/managers',
        name: 'managers',
        meta: {
            title: 'Менеджеры',
            adminAuth: true,
            moderatorAuth: false,
            installerAuth: false,
            managerAuth: false,
            clientAuth: false   
        },
        component: require('./views/Managers.vue').default
    },
    {
        path: '/orders',
        name: 'orders',
        meta: {
            title: 'Заказы',
            adminAuth: true,
            moderatorAuth: true,
            installerAuth: false,
            managerAuth:false,
            clientAuth: true   
        },
        component: require('./views/Orders.vue').default
    },
    {
        path: '/orders-create',
        name: 'orders-create',
        meta: {
            title: 'Создать заказ',
            adminAuth: true,
            moderatorAuth: true,
            installerAuth: false,
            managerAuth: false,
            clientAuth: false  
        },
        component: require('./views/OrdersCreate.vue').default
    },
    {
        path: '/orders-address/:id',
        name: 'orders-address',
        props: true,
        meta: {
            title: 'Адреса заказов',
            adminAuth: true,
            moderatorAuth: true,
            installerAuth: false,
            managerAuth: false,
            clientAuth: false 
        },
        component: require('./views/OrdersToAddress.vue').default
    },
    {
        path: '/typestoworks',
        name: 'typestoworks',
        meta: {
            title: 'Типы заказов',
            adminAuth: true,
            moderatorAuth: false,
            installerAuth: false,
            managerAuth: false,
            clientAuth: false   
        },
        component: require('./views/TypesToWorks.vue').default
    },
];

export default routers;