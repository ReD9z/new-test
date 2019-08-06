<template>
    <v-app id="inspire">
        <v-navigation-drawer v-model="drawer" :mini-variant.sync="mini" hide-overlay stateless fixed app v-if="isLoggedIn">
            <v-toolbar flat class="transparent">
                <v-list class="pa-0">
                    <v-list-tile avatar>
                        <v-list-tile-avatar>
                           <v-icon size="38px">account_circle</v-icon>
                        </v-list-tile-avatar>
                        <v-list-tile-content>
                            <v-list-tile-title></v-list-tile-title>
                        </v-list-tile-content>
                        <v-list-tile-action>
                            <v-btn icon @click.stop="mini = !mini" >
                                <v-icon>chevron_left</v-icon>
                            </v-btn>
                        </v-list-tile-action>
                    </v-list-tile>
                </v-list>
            </v-toolbar>
            <v-list class="pt-0" dense>
                <v-divider></v-divider>
                <div v-for="(item, key) in items" :key="key">
                    <v-list-tile v-show="roleUser(isUserRole, item.role)" :to="{ name: item.src }" active-class="active" v-if="!item.wrapLink">
                        <v-list-tile-action><v-icon>{{ item.icon }}</v-icon></v-list-tile-action>
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
                                    <v-icon>{{ wrapLink.icon }}</v-icon>
                                </v-list-tile-action>
                                <v-list-tile-title>{{wrapLink.title}}</v-list-tile-title>
                            </v-list-tile>
                        </div>
                    </v-list-group>
                </div>
                <v-list-tile @click="exit()">
                    <v-list-tile-action><v-icon>exit_to_app</v-icon></v-list-tile-action>
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
        drawer: true,
        hide: "",
        items: [
            { 
                title: 'Продажи', icon: 'account_balance_wallet', wrapLink:
                [
                    { 
                        title: 'Заказы', icon: 'question_answer', src: 'orders',
                        role: {admin: 'admin', moderator: 'moderator', client: 'client'}
                    },
                    { 
                        title: 'Адреса', icon: 'question_answer', src: 'address',
                        role: {admin: 'admin', moderator: 'moderator'}
                    },
                    { 
                        title: 'Задачи', icon: 'question_answer', src: 'tasks',
                        role: {admin: 'admin', installer: 'installer'}
                    },
                    { 
                        title: 'Клиенты', icon: 'question_answer', src: 'clients',
                        role: {manager: 'manager', admin: 'admin'}
                    },
                ],
                role: {admin: 'admin', moderator: 'moderator', client: 'client', manager: 'manager'}
            },
            { 
                title: 'Пользователи', icon: 'people', wrapLink:
                [
                    { 
                        title: 'Менеджеры', icon: 'question_answer', src: 'managers', 
                        role: {admin: 'admin'}
                    },
                    { 
                        title: 'Монтажники', icon: 'question_answer', src: 'installers',
                        role: {admin: 'admin', moderator: 'moderator'}
                    },
                    { 
                        title: 'Модераторы', icon: 'question_answer', src: 'moderators',
                        role: {admin: 'admin'}
                    },
                    { 
                        title: 'Администраторы', icon: 'question_answer', src: 'users',
                        role: {admin: 'admin'}
                    }
                ],
                role: {admin: 'admin', moderator: 'moderator'}
            },
            { 
                title: 'Настройки', icon: 'settings', wrapLink:
                [
                    { 
                        title: 'Типы работы', icon: 'question_answer', src: 'typestoworks',
                        role: {admin: 'admin'}
                    },
                    { 
                        title: 'Районы', icon: 'question_answer', src: 'areas',
                        role: {admin: 'admin'}
                    },
                    { 
                        title: 'Города работы', icon: 'question_answer', src: 'citiestoworks',
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
            if (role === roleList.admin || role === roleList.client || role === roleList.installer || role === roleList.manager || role === roleList.moderator) {
                return true;
            } else {
                return false;
            }
        },
        exit() {
            this.$store.dispatch('logout')
            .then(() => {
                this.$router.push('/auth')
            })
        }
    }
}
</script>