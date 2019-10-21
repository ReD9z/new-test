<template>
    <v-card>
        <v-card-title>
            <span class="pa-2 headline">Добавить клиента</span>
        </v-card-title>
        <v-form ref="forms" v-model="valid" lazy-validation>
            <v-layout class="pa-3" wrap>
                <v-flex xs12 sm6>
                    <v-text-field 
                        :data-vv-as="'`'+'Имя'+'`'" 
                        data-vv-name="name" 
                        :error-messages="errors.collect('name')"
                        v-validate="required" 
                        v-model="editedItem.name" 
                        :label="Имя" 
                        xs12 
                        class="pa-2"
                        required
                    >
                    </v-text-field>
                    <v-text-field v-model="editedItem.name" label="Имя" xs12 required></v-text-field>
                    <v-text-field class="pa-2" v-model="editedItem.email" label="Email" required></v-text-field>
                </v-flex>
                <v-flex xs12 sm6>
                    <v-text-field class="pa-2" v-model="editedItem.phone" label="Телефон" xs12 required></v-text-field>
                    <v-text-field class="pa-2" v-model="editedItem.login" label="Логин" required></v-text-field>
                </v-flex>
                <v-flex xs12 sm6>
                    <v-text-field 
                        class="pa-2"
                        type="password" 
                        v-model="editedItem.password" 
                        label="Пароль" 
                        v-validate="editedItem.password ? {required:true, regex:/^\S*$/ , min:6} : ''"
                        data-vv-name="password" 
                        :error-messages="errors.collect('password')" 
                        :data-vv-as="'`'+'Пароль'+'`'"
                        xs12>
                    </v-text-field>
                    <v-text-field class="pa-2" v-model="editedItem.city_id" label="Город" required></v-text-field>
                </v-flex>
                <v-flex xs12 sm6>
                    <v-text-field class="pa-2" v-model="editedItem.legal_name" label="Юридическое название" xs12 required></v-text-field>
                    <v-text-field class="pa-2" v-model="editedItem.actual_title" label="Фактическое название" required></v-text-field>
                </v-flex>
                <v-flex xs12 sm6>
                    <v-text-field class="pa-2" v-model="editedItem.legal_address" label="Юридический адрес" xs12 required></v-text-field>
                    <v-text-field class="pa-2" v-model="editedItem.actual_address" label="Фактический адрес" required></v-text-field>
                </v-flex>
                <v-flex xs12 sm6>
                    <v-text-field class="pa-2" v-model="editedItem.bank_name" label="Название банка" xs12 required></v-text-field>
                    <v-text-field class="pa-2" v-model="editedItem.bik" label="БИК" required></v-text-field>
                </v-flex>
                <v-flex xs12 sm6>
                    <v-text-field class="pa-2" v-model="editedItem.cor_score" label="Кор. счёт" xs12 required></v-text-field>
                </v-flex>
                <v-flex xs12 sm6>
                    <v-text-field class="pa-2" v-model="editedItem.settlement_account" label="Расчётный счёт" required></v-text-field>
                </v-flex>
            </v-layout>
        </v-form>
        <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" flat @click="dialog = false">Закрыть</v-btn>
            <v-btn color="blue darken-1" flat @click="dialog = false">Сохранить</v-btn>
        </v-card-actions>
    </v-card>
</template>
<script>
export default {
    inject: ['$validator'],
    data: () => ({
        dialog: false,
        valid: true,
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
    methods: { 
         save () {
            this.$validator.validateAll().then(() => {
                if(this.$validator.errors.items.length == 0) {
                    axios({
                        method: 'post',
                        url: '/api/clients',
                        data: this.editedItem
                    })
                    .then(
                        response => {
                            // if (this.editedIndex > -1) {
                            //     this.initialize()
                            // } else {
                            //     this.initialize()
                            // }
                            // this.loaderSaveBtn = null;
                            // this.loadingSaveBtn = false;
                            // this.close();
                            // this.$refs.forms.reset();
                        }
                    ).catch(error => {
                        console.log(error);
                    })  
                } else {
                    this.snackbar = true
                }
            });
        },
    }
}
</script>    