let routers =  [
    {
        path: '/',
        name: 'home',
        component: require('./views/Address.vue').default,
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
            requiresAuth: true,
            adminAuth: true,
            moderatorAuth: true,
            installerAuth: false,
            managerAuth: true,
            clientAuth: false  
        },
        component: require('./views/Address.vue').default
    },
    {
        path: '/tasks',
        name: 'tasks',
        meta: {
            title: 'Задачи монтажникам',
            requiresAuth: true,
            adminAuth: true,
            moderatorAuth: true,
            installerAuth: true,
            managerAuth: false,
            clientAuth: false 
        },
        component: require('./views/Tasks.vue').default
    },
    {
        path: '/tasksManager',
        name: 'tasksManager',
        meta: {
            title: 'Задачи менеджерам',
            requiresAuth: true,
            adminAuth: true,
            moderatorAuth: false,
            installerAuth: false,
            managerAuth: true,
            clientAuth: false
        },
        component: require('./views/ManagerTask.vue').default
    },
    {
        path: '/error',
        name: 'error',
        meta: {
            title: 'Ошибка',
            requiresAuth: true,
            adminAuth: true,
            moderatorAuth: true,
            installerAuth: true,
            managerAuth: true,
            clientAuth: true 
        },
        component: require('./views/Error.vue').default
    },
    {
        path: "*",  component: require('./views/Error404.vue').default
    },
    {
        path: '/admins',
        name: 'admins',
        meta: {
            title: 'Администраторы',
            requiresAuth: true,
            adminAuth: true,
            moderatorAuth: false,
            installerAuth: false,
            managerAuth: false,
            clientAuth: false     
        },
        component: require('./views/Admins.vue').default
    },
    {
        path: '/moderators',
        name: 'moderators',
        meta: {
            title: 'Модераторы',
            requiresAuth: true,
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
            requiresAuth: true,
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
            requiresAuth: true,
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
            requiresAuth: true,
            adminAuth: true,
            moderatorAuth: false,
            installerAuth: false,
            managerAuth: true,
            clientAuth: true   
        },
        component: require('./views/Clients.vue').default
    },
    {
        path: '/installers',
        name: 'installers',
        meta: {
            title: 'Монтажники',
            requiresAuth: true,
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
            requiresAuth: true,
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
            requiresAuth: true,
            adminAuth: true,
            moderatorAuth: true,
            installerAuth: false,
            managerAuth: false,
            clientAuth: true 
        },
        component: require('./views/Orders.vue').default
    },
    {
        path: '/orders-create',
        name: 'orders-create',
        meta: {
            title: 'Создать заказ',
            requiresAuth: true,
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
            requiresAuth: true,
            adminAuth: true,
            moderatorAuth: true,
            installerAuth: false,
            managerAuth: false,
            clientAuth: true
        },
        component: require('./views/OrdersToAddress.vue').default
    },
    {
        path: '/typestoworks',
        name: 'typestoworks',
        meta: {
            title: 'Типы заказов',
            requiresAuth: true,
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