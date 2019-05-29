<template>
    <v-app id="inspire">
        <v-navigation-drawer fixed v-model="drawerRight" clipped app >
        </v-navigation-drawer>
        <v-toolbar color="blue-grey" dark fixed app clipped-righ>
        <v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>
        <v-toolbar-title>Toolbar</v-toolbar-title>
        <v-spacer></v-spacer>
        </v-toolbar>
        <v-navigation-drawer fixed v-model="drawer" app>
            <v-toolbar flat>
                <v-list dense>
                    <v-list-tile>
                        <v-list-tile-title class="title">
                            Application
                        </v-list-tile-title>
                    </v-list-tile>
                </v-list>
            </v-toolbar>
            <v-divider></v-divider>
            <v-list dense class="pt-0">
                <router-link :to="{ name: 'home' }">
                    <v-list-tile>
                        <v-list-tile-action>
                            <v-icon>dashboard</v-icon>
                        </v-list-tile-action>

                        <v-list-tile-content>
                            <v-list-tile-title>Главная</v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </router-link>
                <router-link :to="{ name: 'address' }">
                    <v-list-tile>
                        <v-list-tile-action>
                            <v-icon>dashboard</v-icon>
                        </v-list-tile-action>

                        <v-list-tile-content>
                            <v-list-tile-title>Адреса</v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </router-link>
            </v-list>
        </v-navigation-drawer>
        <v-navigation-drawer temporary v-model="left" fixed></v-navigation-drawer>
        <v-content>
            <v-container fluid fill-height>
                <v-layout justify-center align-center>
                <v-flex xs12>
                    <router-view></router-view>
                </v-flex>
                </v-layout>
            </v-container>
        </v-content>
        <v-footer color="blue-grey" class="white--text" app>
            <span>Vuetify</span>
            <v-spacer></v-spacer>
            <span>&copy; 2017</span>
        </v-footer>
    </v-app>
    <!-- <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card" v-if="isLoggedIn">
                    <nav class="navbar navbar-expand-lg navbar-light bg-light">
                        <router-link :to="{ name: 'home' }" class="navbar-brand">Navbar</router-link>
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar1" aria-controls="navbar1" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse" id="navbar1">
                            <ul class="navbar-nav mr-auto">
                                <li class="nav-item">
                                    <router-link :to="{ name: 'home' }" class="nav-item nav-link">Главная</router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link :to="{ name: 'address' }" class="nav-item nav-link">Адреса</router-link>
                                </li> 
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown1" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Пользователи</a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown1">
                                        <router-link :to="{ name: 'managers' }" class="nav-item nav-link">Менеджеры</router-link>
                                        <router-link :to="{ name: 'moderators' }" class="nav-item nav-link">Модераторы</router-link>
                                        <router-link :to="{ name: 'users' }" class="nav-item nav-link">Пользователи</router-link>
                                        <router-link :to="{ name: 'installers' }" class="nav-item nav-link">Монтажники</router-link>
                                        <router-link :to="{ name: 'roles' }" class="nav-item nav-link">Роли</router-link>
                                       
                                    </div>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown1" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Справочники</a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown1">
                                        <router-link :to="{ name: 'typestoworks' }" class="nav-item nav-link">Типы работы</router-link>
                                        <router-link :to="{ name: 'areas' }" class="nav-item nav-link">Районы</router-link>
                                        <router-link :to="{ name: 'citiestoworks' }" class="nav-item nav-link">Города работы</router-link>
                                    </div>
                                </li>
                                <router-link :to="{ name: 'clients' }" class="nav-item nav-link">Клиенты</router-link>
                                <router-link :to="{ name: 'orders' }" class="nav-item nav-link">Заказы</router-link>
                                <router-link :to="{ name: 'addresstoorders' }" class="nav-item nav-link">Адреса заказов</router-link>
                                <router-link :to="{ name: 'tasks' }" class="nav-item nav-link">Задачи</router-link>
                                <a @click="exit()" class="nav-item nav-link">Выход</a>
                            </ul>
                        </div>
                    </nav>
                </div>
                <router-view></router-view>
            </div>
         </div>
    </div> -->
</template>
<script>
export default {
    data: () => ({
        drawer: false,
        drawerRight: false,
        left: null
    }),
    props: {
        source: String
    },
    computed : {
        isLoggedIn: function(){ 
            return this.$store.getters.isLoggedIn;
        },
        isLoggedUser: function(){ 
            return this.$store.getters.isLoggedUser;
        }
    },
    methods: {
        exit() {
            this.$store.dispatch('logout')
            .then(() => {
                this.$router.push('/auth')
            })
        }
    },
}
</script>