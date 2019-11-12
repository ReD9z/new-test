<template>
    <v-app id="inspire">
        <v-navigation-drawer class="blue darken-3" v-model="drawer" :mini-variant.sync="mini" hide-overlay stateless fixed app v-if="isLoggedIn">
            <v-toolbar flat class="transparent">
                <v-list class="pa-0">
                    <v-list-tile avatar>
                        <v-list-tile-avatar>
                            <v-img src="/images/atom-logo.png" lazy-src="/images/atom-logo.png"></v-img>
                        </v-list-tile-avatar>
                        <v-list-tile-content>
                            <v-list-tile-title></v-list-tile-title>
                        </v-list-tile-content>
                        <v-list-tile-action>
                            <v-btn icon @click.stop="mini = !mini" >
                                <v-icon class="white--text">chevron_left</v-icon>
                            </v-btn>
                        </v-list-tile-action>
                    </v-list-tile>
                </v-list>
            </v-toolbar>
            <v-list class="white--text pt-0 icon-menu-padding" dense>
                <v-divider></v-divider>
                <div v-for="(item, key) in items" :key="key">
                    <v-list-tile class="exit-style-menu" v-show="roleUser(isUserRole, item.role)" :to="{ name: item.src }" active-class="active" v-if="!item.wrapLink">	
                        <v-list-tile-action><v-icon class="white--text">{{ item.icon }}</v-icon></v-list-tile-action>	
                        <v-list-tile-content>	
                            <v-list-tile-title>{{ item.title }}</v-list-tile-title>	
                        </v-list-tile-content>	
                    </v-list-tile>   
                    <v-list-group v-show="roleUser(isUserRole, item.role)" :prepend-icon="item.icon" :value="hide" v-else>
                        <template v-slot:activator>
                            <v-list-tile>
                                <v-list-tile-title>{{item.title}}</v-list-tile-title>
                            </v-list-tile>
                        </template>
                        <div v-for="(wrapLink, key) in item.wrapLink" :key="key">
                            <v-list-tile v-show="roleUser(isUserRole, wrapLink.role)" active-class="active" :to="{ name: wrapLink.src }">
                                <v-list-tile-action>
                                    <v-icon class="white--text">{{ wrapLink.icon }}</v-icon>
                                </v-list-tile-action>
                                <v-list-tile-title>{{wrapLink.title}}</v-list-tile-title>
                            </v-list-tile>
                        </div>
                    </v-list-group>
                </div>
                <v-list-tile class="exit-style-menu" @click="exit()">
                    <v-list-tile-action>
                        <v-progress-circular
                            v-if="exitProgress"
                            size = "20"
                            width = "2"
                            indeterminate
                            color="amber"
                            >
                        </v-progress-circular>
                        <v-icon v-if="!exitProgress" class="white--text">exit_to_app</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                        <v-list-tile-title>Выход</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
        </v-navigation-drawer>
        <v-content>
            <v-container fluid fill-height>
                <v-layout justify-center align-center>
                    <v-flex xs12>
                        <router-view></router-view>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-content>
    </v-app>
</template>
<script>
export default {
    data: () => ({
        exitProgress: false,
        drawer: true,
        hide: "",
        items: [
            { 
                title: 'Продажи', icon: 'account_balance_wallet', wrapLink:
                [
                    { 
                        title: 'Клиенты', icon: 'how_to_reg', src: 'clients',
                        role: {admin: 'admin'}
                    },
                    { 
                        title: 'Адреса', icon: 'room', src: 'address',
                        role: {admin: 'admin', moderator: 'moderator'}
                    },
                    { 
                        title: 'Заказы', icon: 'credit_card', src: 'orders',
                        role: {admin: 'admin', moderator: 'moderator'}
                    },
                    { 
                        title: 'Задачи монтажникам', icon: 'list', src: 'tasks',
                        role: {admin: 'admin', moderator: 'moderator'}
                    },
                    { 
                        title: 'Задачи менеджерам', icon: 'assessment', src: 'tasksManager',
                        role: {admin: 'admin'}
                    }
                ],
                role: {admin: 'admin', moderator: 'moderator'}
            },
            { 
                title: 'Мои задачи', icon: 'list', src: 'tasksManager',
                role: {installer: 'manager'}
            },
            { 
                title: 'Мои задачи', icon: 'list', src: 'tasks',
                role: {installer: 'installer'}
            },
            { 
                title: 'Мои заказы', icon: 'credit_card', src: 'orders',
                role: {client: 'client'}
            },
            { 
                title: 'Мои клиенты', icon: 'how_to_reg', src: 'clients',
                role: {manager: 'manager'}
            },
            { 
                title: 'Мои адреса', icon: 'room', src: 'address',
                role: {manager: 'manager'}
            },
            { 
                title: 'Пользователи', icon: 'people', wrapLink:
                [
                    { 
                        title: 'Администраторы', icon: 'contacts', src: 'admins',
                        role: {admin: 'admin'}
                    },
                    { 
                        title: 'Модераторы', icon: 'security', src: 'moderators',
                        role: {admin: 'admin'}
                    },
                    { 
                        title: 'Менеджеры', icon: 'record_voice_over', src: 'managers', 
                        role: {admin: 'admin'}
                    },
                    { 
                        title: 'Монтажники', icon: 'build', src: 'installers',
                        role: {admin: 'admin', moderator: 'moderator'}
                    },
                ],
                role: {admin: 'admin', moderator: 'moderator'}
            },
            { 
                title: 'Настройки', icon: 'settings', wrapLink:
                [
                    { 
                        title: 'Города', icon: 'location_city', src: 'citiestoworks',
                        role: {admin: 'admin'}
                    },
                    { 
                        title: 'Районы', icon: 'home', src: 'areas',
                        role: {admin: 'admin'}
                    },
                    { 
                        title: 'Типы работы', icon: 'work', src: 'typestoworks',
                        role: {admin: 'admin'}
                    }
                ],
                role: {admin: 'admin'}
            },
        ],
        mini: true,
        right: null
    }),
    props: {
        source: String
    },
    computed: {
        isLoggedIn: function(){ 
            return this.$store.getters.isLoggedIn;
        },
        isLoggedUser: function(){ 
            return this.$store.getters.isLoggedUser;
        },
        isUserRole: function(){ 
            return this.$store.getters.isUserRole;
        }
    },
    methods: {
        roleUser(role, roleList) {
            const {admin, client, installer, moderator, manager} = roleList;
            if (role === admin || role === client || role === installer || role === manager || role === moderator) {
                return true;
            } else {
                return false;
            }
        },
        exit() {
            this.exitProgress = true;
            this.$store.dispatch('logout')
            .then(() => {
                this.exitProgress = false;
                this.$router.push('/auth')
            })
        }
    }
}
</script>