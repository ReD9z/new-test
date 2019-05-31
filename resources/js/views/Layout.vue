<template>
    <v-app id="inspire">
        <v-navigation-drawer fixed :width="180" v-model="drawer" app>
            <v-toolbar flat> 
                <v-list dense>
                    <v-list-tile>
                        <v-list-tile-title class="title">
                            Application
                        </v-list-tile-title>
                    </v-list-tile>
                </v-list>
            </v-toolbar>
            <v-list class="pt-0" dense>
                <v-btn color="blue-grey" class="white--text" block>
                    <router-link :to="{ name: 'home' }" style="color: #fff; text-decoration:none;">Главная <v-icon right dark>home</v-icon></router-link>
                </v-btn>
                <v-btn color="blue-grey" class="white--text" block>
                    <router-link :to="{ name: 'address' }" style="color: #fff; text-decoration:none;">Адреса <v-icon right dark>home</v-icon></router-link>
                </v-btn>
                <v-btn color="blue-grey" class="white--text" block>
                    <router-link :to="{ name: 'orders' }" style="color: #fff; text-decoration:none;">Заказы <v-icon right dark>home</v-icon></router-link>
                </v-btn>
                <v-btn color="blue-grey" class="white--text" block>
                    <router-link :to="{ name: 'clients' }" style="color: #fff; text-decoration:none;">Клиенты <v-icon right dark>home</v-icon></router-link>
                </v-btn>
                <v-menu offset-y>
                    <template v-slot:activator="{ on }">
                        <v-btn
                        color="blue-grey" 
                        class="white--text" 
                        block
                        v-on="on"
                        >
                        Пользователи
                        <v-icon right dark>home</v-icon>
                        </v-btn>
                    </template>
                    <v-list>
                        <v-list-tile>
                            <router-link :to="{ name: 'managers' }">Менеджеры</router-link>
                        </v-list-tile>
                        <v-list-tile>
                            <router-link :to="{ name: 'moderators' }">Модераторы</router-link>
                        </v-list-tile>
                        <v-list-tile>
                            <router-link :to="{ name: 'users' }">Пользователи</router-link>
                        </v-list-tile>
                        <v-list-tile>
                            <router-link :to="{ name: 'installers' }">Монтажники</router-link>
                        </v-list-tile>
                        <v-list-tile>
                            <router-link :to="{ name: 'roles' }">Роли</router-link>
                        </v-list-tile>
                    </v-list>
                </v-menu>
                 <v-menu offset-y>
                    <template v-slot:activator="{ on }">
                        <v-btn color="blue-grey" class="white--text" block v-on="on" >
                        Справочники
                        <v-icon right dark>home</v-icon>
                        </v-btn>
                    </template>
                    <v-list>
                        <v-list-tile><router-link :to="{ name: 'typestoworks' }">Типы работы</router-link></v-list-tile>
                        <v-list-tile><router-link :to="{ name: 'areas' }">Районы</router-link></v-list-tile>
                        <v-list-tile><router-link :to="{ name: 'citiestoworks' }">Города работы</router-link></v-list-tile>
                    </v-list>
                </v-menu>
                <v-btn color="blue-grey" class="white--text" block>
                    <router-link :to="{ name: 'addresstoorders' }" style="color: #fff; text-decoration:none;">Адреса заказов
                        <v-icon right dark>home</v-icon>
                    </router-link>
                </v-btn>
                <v-btn color="blue-grey" class="white--text" block>
                    <router-link :to="{ name: 'tasks' }" style="color: #fff; text-decoration:none;">Задачи
                        <v-icon right dark>home</v-icon>
                    </router-link>
                </v-btn>
                <v-btn color="blue-grey" class="white--text" @click="exit()" block>
                    Выход
                    <v-icon right dark>home</v-icon>
                </v-btn>
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
        drawer: true,
        drawerRight: true,
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