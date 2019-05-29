<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 mt-4 mb-4">
                <div class="card">
                    <div class="card-header">Авторизация</div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">Email</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" v-model="user.email" required autofocus>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">Пароль</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" v-model="user.password" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" v-model="user.remember_me" :value="isActive" @click="remember()">
                                    <label class="form-check-label" for="remember">
                                        Запомнить
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" @click="login" class="btn btn-primary">
                                    Войти
                                </button>
                                <a class="btn btn-link" href="/">
                                    Забыли ваш пароль?
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
