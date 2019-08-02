<template>
    <v-layout justify-center align-center>
        <v-flex sm4>
            <v-form ref="form" v-model="valid" lazy-validation>
                <v-card>
                    <v-toolbar color="teal" dark>
                        <v-toolbar-title>Авторизация</v-toolbar-title>
                    </v-toolbar>
                    <v-text-field
                        class="mt-4 pl-4 pr-4"
                        solo
                        label="E-mail"
                        prepend-inner-icon="person"
                        :rules="emailRules"
                        v-model="user.email"
                        required 
                    ></v-text-field>
                    <v-text-field
                        class="pl-4 pr-4"
                        solo 
                        :rules="passwordRules"
                        label="Пароль"
                        type="password"
                        prepend-inner-icon="work"
                        v-model="user.password"
                        required
                    ></v-text-field>
                    {{validationErrors}}
                    <v-checkbox class="pl-4 pr-4" v-model="user.remember_me"  label="Запомнить?" :value="isActive" @click="remember()"></v-checkbox>
                    <v-btn class="mb-4 ml-4 mr-4" color="success" @click="login">
                        Войти
                    </v-btn>
                </v-card>
            </v-form>
        </v-flex>
    </v-layout>
</template>

<script>
    export default {
        data: () => ({
            isActive: false,
            valid: true,
            validationErrors: [],
            user: {
                email: "",
                password: "",
                remember_me: false
            },
            emailRules: [
                v => !!v || 'Поле "Email" обязательно',
                v => /.+@.+/.test(v) || 'Введите корректный "Email"'
            ],
            passwordRules: [
                v => !!v || 'Поле "Пароль" обязательно'
            ],
        }),
        methods: {
            remember() {
                this.isActive = !this.isActive;
            },
            login() {
                if (this.$refs.form.validate()) {
                    let email = this.user.email;
                    let password = this.user.password;
                    let remember_me = this.user.remember_me;
                    this.$store.dispatch('login', { email, password, remember_me })
                    .then((res) => this.$router.push('/'))
                    .catch((error) => this.validationErrors = error.response.data.errors)
                }
            }
        }
    }
</script>
