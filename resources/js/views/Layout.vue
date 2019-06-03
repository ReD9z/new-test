<template>
    <v-app id="inspire">
        <v-navigation-drawer
            v-model="drawer"
            :mini-variant.sync="mini"
            hide-overlay
            stateless
            fixed
            app  
            v-if="isLoggedIn"
        >
            <v-toolbar flat class="transparent">
                <v-list class="pa-0">
                    <v-list-tile avatar>
                        <v-list-tile-avatar>
                            <img src="https://randomuser.me/api/portraits/men/85.jpg">
                        </v-list-tile-avatar>
                        <v-list-tile-content>
                            <v-list-tile-title>{{isLoggedUser.name}}</v-list-tile-title>
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
                    <v-list-tile :to="{ name: item.src }" active-class="active" v-if="!item.wrapLink">
                        <v-list-tile-action><v-icon>{{ item.icon }}</v-icon></v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>{{ item.title }}</v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>   
                    <v-list-group prepend-icon="account_circle" :value="hide" v-else>
                        <template v-slot:activator>
                            <v-list-tile>
                                <v-list-tile-title>{{item.title}}</v-list-tile-title>
                            </v-list-tile>
                        </template>
                        <div v-for="(wrapLink, key) in item.wrapLink" :key="key">
                            <v-list-tile active-class="active" :to="{ name: wrapLink.src }" >
                                <v-list-tile-action>
                                    <v-icon>{{ item.icon }}</v-icon>
                                </v-list-tile-action>
                                <v-list-tile-title>{{wrapLink.title}}</v-list-tile-title>
                            </v-list-tile>
                        </div>
                    </v-list-group>
                </div>
                <v-list-tile @click="exit()">
                    <v-list-tile-action><v-icon>home</v-icon></v-list-tile-action>
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
            { title: 'Главная', icon: 'dashboard', src: 'home'},
            { title: 'Адреса', icon: 'question_answer', src: 'address'},
            { title: 'Заказы', icon: 'question_answer', src: 'orders'},
            { title: 'Клиенты', icon: 'question_answer', src: 'clients'},
            { title: 'Адреса заказов', icon: 'question_answer', src: 'addresstoorders'},
            { title: 'Задачи', icon: 'question_answer', src: 'tasks'},
            { title: 'Пользователи', icon: 'home', wrapLink:
                [
                    { title: 'Менеджеры', icon: 'question_answer', src: 'managers'},
                    { title: 'Модераторы', icon: 'question_answer', src: 'moderators'},
                    { title: 'Пользователи', icon: 'question_answer', src: 'users'},
                    { title: 'Монтажники', icon: 'question_answer',src: 'installers'},
                    { title: 'Роли', icon: 'question_answer', src: 'roles'},
                ] 
            },
            { title: 'Справочники', icon: 'home', wrapLink:
                [
                    { title: 'Типы работы', icon: 'question_answer', src: 'typestoworks'},
                    { title: 'Районы', icon: 'question_answer', src: 'areas'},
                    { title: 'Города работы', icon: 'question_answer', src: 'citiestoworks'}
                ] 
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
        }
    },
    methods: {
        exit() {
            this.$store.dispatch('logout')
            .then(() => {
                this.$router.push('/auth')
            })
        }
    }
}
</script>