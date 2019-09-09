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
                        v-model="user.email"
                        v-validate="'required|email'"
                        data-vv-name="email"
                        :error-messages="errors.collect('email')"
                        :data-vv-as="'`E-mail`'" 
                        required 
                    ></v-text-field>
                    <v-text-field
                        class="pl-4 pr-4"
                        solo 
                        label="Пароль"
                        type="password"
                        prepend-inner-icon="work"
                        v-model="user.password"
                        v-validate="'required'"
                        data-vv-name="password"
                        :data-vv-as="'`Пароль`'"       
                        :error-messages="errors.collect('password')"
                        required
                    ></v-text-field>
                    <v-checkbox class="pl-4 pr-4" v-model="user.remember_me"  label="Запомнить?" :value="isActive" @click="remember()"></v-checkbox>
                    <v-btn class="mb-4 ml-4 mr-4" color="success" @click="login" :loading="loading" :disabled="loading">
                        Войти
                        <template v-slot:loading>
                            <span class="custom-loader">
                            <v-icon light>cached</v-icon>
                            </span>
                        </template>
                    </v-btn>
                </v-card>
            </v-form>
        </v-flex>
    </v-layout>
</template>

<script>
    export default {
        inject: ['$validator'],
        data: () => ({
            isActive: false,
            valid: true,
            validationErrors: null,
            loading: false,
            user: {
                email: "",
                password: "",
                remember_me: false
            }
        }),
        methods: {
            remember() {
                this.isActive = !this.isActive;
            },
            login() {
                // генерация excel файла с переходами строк в адресе, 
                // Скрыть удаление адресов и районов если они в связях с адресом
                // доделать права пользователя на категории 
                this.$validator.validateAll().then(() => {
                    if(this.$validator.errors.items.length == 0) {
                        this.loading = true;
                        let email = this.user.email;
                        let password = this.user.password;
                        let remember_me = this.user.remember_me;
                        this.$store.dispatch('login', { email, password, remember_me })
                        .then((res) => {
                            this.loading = false
                            this.$router.push('/')
                            this.validationErrors=null
                        })
                        .catch((error) => {
                            if(error.response.status == 401) {
                                this.errors.items.push(
                                    {
                                        field: "email",
                                        id: "1",
                                        msg: 'Неверный e-mail',
                                        regenerate: () => 'some string', 
                                        rule: 'required', 
                                        scope: null,
                                        vmId: 4
                                    },
                                    {
                                        field: "password",
                                        id: "2",
                                        msg: 'Неверный пароль',
                                        rule: 'required', 
                                        scope: null,
                                        regenerate: () => 'some string', 
                                        vmId: 4
                                    }
                                );
                            }
                            this.loading = false
                        });
                    }
                });
            }
        }
    }
</script>
