<template>
    <v-layout justify-center align-center>
        <v-flex sm6>
            <v-card>
                <v-toolbar color="teal" dark>
                    <v-toolbar-title>Авторизация</v-toolbar-title>
                </v-toolbar>
                <v-text-field
                    class="mt-4 pl-4 pr-4"
                    solo
                    label="E-mail"
                    prepend-inner-icon="person"
                    v-model="user.email"
                    required 
                    autofocus
                ></v-text-field>
                <v-text-field
                    class="pl-4 pr-4"
                    solo
                    label="Пароль"
                    prepend-inner-icon="work"
                    v-model="user.password"
                    required
                ></v-text-field>
                <v-checkbox class="pl-4 pr-4" v-model="user.remember_me"  label="Запомнить?" :value="isActive" @click="remember()"></v-checkbox>
                <v-btn class="mb-4 ml-4 mr-4" color="success" @click="login" >
                    Войти
                </v-btn>
            </v-card>
        </v-flex>
    </v-layout>
</template>

<script>
    export default {
        data: () => ({
            isActive: false,
            user: {
                email: "",
                password: "",
                remember_me: false
            },
        }),
        methods: {
            remember() {
                this.isActive = !this.isActive;
            },
            login() {
                let email = this.user.email;
                let password = this.user.password;
                let remember_me = this.user.remember_me;
                this.$store.dispatch('login', { email, password, remember_me })
                .then(() => this.$router.push('/'))
                .catch(err => console.log(err))
            }
        }
    }
</script>
