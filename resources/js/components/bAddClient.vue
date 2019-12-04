<template>
<v-dialog v-model="dialog" max-width="700px">
    <v-card>
        <v-card-title>
            <span class="pa-2 headline">Добавить клиента</span>
        </v-card-title>
        <v-form ref="forms2" v-model="valid" lazy-validation>
            <v-layout class="pa-3" wrap>
                <v-flex xs12 sm6>
                    <v-text-field
                        data-vv-as="`Имя`" 
                        data-vv-name="name" 
                        :error-messages="errors.collect('name')"
                        v-validate="required" 
                        v-model="editedItem.name" 
                        label="Имя" 
                        xs12 
                        class="pa-2"
                        required
                    >
                    </v-text-field>
                    <v-text-field 
                        class="pa-2"
                        :data-vv-as="'`Email`'" 
                        :data-vv-name="'email'" 
                        :error-messages="errors.collect('email')"
                        v-validate="'email'" 
                        v-model="editedItem.email" 
                        label="Email" 
                        xs12 required>
                    </v-text-field>
                </v-flex>
                <v-flex xs12 sm6>
                    <v-text-field 
                        data-vv-as="`Телефон`" 
                        data-vv-name="phone" 
                        :error-messages="errors.collect('phone')"
                        v-model="editedItem.phone" 
                        label="Телефон" 
                        xs12 
                        class="pa-2"
                        required
                    >
                    </v-text-field>
                    <v-text-field 
                        data-vv-as="`Логин`" 
                        data-vv-name="login" 
                        :error-messages="errors.collect('login')"
                        v-model="editedItem.login" 
                        label="Логин" 
                        class="pa-2"
                        required
                    >
                    </v-text-field>
                </v-flex>
                <v-flex xs12 sm6>
                    <v-text-field 
                        data-vv-as="`Пароль`" 
                        type="password"
                        data-vv-name="password" 
                        :error-messages="errors.collect('password')"
                        v-model="editedItem.password" 
                        label="Пароль" 
                        xs12 
                        class="pa-2"
                        required
                    >
                    </v-text-field>     
                    <v-autocomplete
                        :items="select[0]"
                        v-model="editedItem.city_id"
                        item-text="name"
                        item-value="id"
                        class="pa-2"
                        label="Город"
                        data-vv-as="`Город`" 
                        data-vv-name="city_id" 
                        :error-messages="errors.collect('city_id')"
                    >
                    </v-autocomplete>
                </v-flex>
                <v-flex xs12 sm6>
                    <v-text-field 
                        data-vv-as="`Юридическое название'`" 
                        data-vv-name="legal_name" 
                        :error-messages="errors.collect('legal_name')"
                        v-model="editedItem.legal_name" 
                        label="Юридическое название" 
                        xs12 
                        class="pa-2"
                        required
                    >
                    </v-text-field>
                    <v-text-field 
                        data-vv-as="`Фактическое название`" 
                        data-vv-name="actual_title" 
                        :error-messages="errors.collect('actual_title')"
                        v-model="editedItem.actual_title" 
                        label="Фактическое название" 
                        class="pa-2"
                        required
                    >
                    </v-text-field>
                </v-flex>
                <v-flex xs12 sm6>
                    <v-text-field 
                        data-vv-as="`Юридический адрес`" 
                        data-vv-name="legal_address" 
                        :error-messages="errors.collect('legal_address')"
                        v-model="editedItem.legal_address" 
                        label="Юридический адрес" 
                        xs12 
                        class="pa-2"
                        required
                    >
                    </v-text-field>
                    <v-text-field 
                        data-vv-as="`Фактический адрес`" 
                        data-vv-name="actual_address" 
                        :error-messages="errors.collect('actual_address')"
                        v-model="editedItem.actual_address" 
                        label="Фактический адрес" 
                        class="pa-2"
                        required
                    >
                    </v-text-field>
                </v-flex>
                <v-flex xs12 sm6>
                    <v-text-field 
                        data-vv-as="`Название банка`" 
                        data-vv-name="bank_name" 
                        :error-messages="errors.collect('bank_name')"
                        v-model="editedItem.bank_name" 
                        label="Название банка" 
                        xs12 
                        class="pa-2"
                        required
                    >
                    </v-text-field>
                    <v-text-field 
                        data-vv-as="`БИК`" 
                        data-vv-name="bik" 
                        :error-messages="errors.collect('bik')"
                        v-model="editedItem.bik" 
                        label="БИК" 
                        class="pa-2"
                        required
                    >
                    </v-text-field>
                </v-flex>
                <v-flex xs12 sm6>
                    <v-text-field 
                        data-vv-as="`Кор. счёт`" 
                        data-vv-name="cor_score" 
                        :error-messages="errors.collect('cor_score')"
                        v-model="editedItem.cor_score" 
                        label="Кор. счёт" 
                        xs12 
                        class="pa-2"
                        required
                    >
                    </v-text-field>
                </v-flex>
                <v-flex xs12 sm6>
                    <v-text-field 
                        data-vv-as="`Расчётный счёт`" 
                        data-vv-name="settlement_account" 
                        :error-messages="errors.collect('settlement_account')"
                        v-model="editedItem.settlement_account" 
                        label="Расчётный счёт" 
                        class="pa-2"
                        required
                    >
                    </v-text-field>
                </v-flex>
            </v-layout>
        </v-form>
        <v-card-actions class="pa-2">
            <v-spacer></v-spacer>
            <div class="text-xs-center pa-3">
                <v-btn color="info" @click="save()" :loading="loadingSaveBtn" :disabled="loadingSaveBtn">
                    Сохранить
                    <template v-slot:loaderSaveBtn>
                        <span class="custom-loader">
                            <v-icon light>cached</v-icon>
                        </span>
                    </template>
                </v-btn>
            </div>
        </v-card-actions>
    </v-card>
</v-dialog>
</template>
<script>
import Vue from 'vue';
import VeeValidate from 'vee-validate';

export default {
    $_veeValidate: {
        validator: 'new'
    },
    props: {
        addClient: Boolean,
        clientNew: Object 
    },
    watch: {
        addClient(newVal, oldVal) {
            if(newVal) {
                this.dialog = newVal;
            }else {
                this.dialog = oldVal;
            }
        }
    },
    data: () => ({
        dialog: false,
        required: "required",
        valid: true,
        select: [],
        client: [],
        loadingSaveBtn: false,
        loaderSaveBtn: null,
        editedItem: {
            name: '',
            email: '',
            phone: '',
            login: '',
            password: '',
            city_id: '',
            legal_name: '',
            actual_title: '',
            legal_address: '',
            actual_address: '',
            bank_name: '',
            bik: '',
            cor_score: '',
            settlement_account: ''
        }
    }),
    async created () {
        await this.citySelect();
    },
    methods: { 
        save () {
            this.$emit('update:addClient', true);
            this.$validator.validateAll().then(() => {
                if(this.$validator.errors.items.length == 0) {
                    this.loaderSaveBtn = true;
                    this.loadingSaveBtn = true;
                    axios({
                        method: 'post',
                        url: '/api/clients',
                        data: this.editedItem
                    })
                    .then(
                        async response => {
                            this.loaderSaveBtn = null;
                            this.loadingSaveBtn = false;
                            await this.$refs.forms2.reset();
                            await this.$validator.reset();
                            await this.$emit('update:addClient', false)
                            await this.$emit('update:clientNew', response.data)
                            console.log(this);
                            this.dialog = false;
                            // console.log(this.clientArr);
                        }
                    ).catch(error => {
                        if(error.response.status == 422) {
                            if(error.response.data.errors.email) {
                                const error = {
                                    field: "email",
                                    msg: 'Такой email уже есть!',
                                    rule: 'required', 
                                    scope: null,
                                    regenerate: () => 'some string', 
                                    vmId: 88,
                                    id: "2"
                                };
                                this.errors.items.push(error);
                                this.loaderSaveBtn = null;
                                this.loadingSaveBtn = false;
                            }
                        }
                    })  
                } else {
                    this.snackbar = true
                }
            });
        },
        citySelect() {
            axios({
                method: 'get',
                url: '/api/cities_to_works',
            })
            .then(
                res => {
                    if(res) {
                        this.select.push(res.data);
                    }
                }
            ).catch(
                error => {
                    console.log(error);
                }
            ); 
        },
    }
}
</script>    