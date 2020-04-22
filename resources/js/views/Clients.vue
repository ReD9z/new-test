<template>
    <div class="card mb-4 mt-4">
        <div class="col-md-12 mb-4 mt-4">
            <b-table-clients :params="params" v-show="roleUser(isLoggedUser.role, {admin: 'admin', manager: 'manager', moderator:'moderator'})"></b-table-clients>
        </div>
    </div>
</template>
<script>
export default {
    data: () => ({
        params: {
            baseUrl: '/api/clients',
            headers: [
                { 
                    text: 'Имя', 
                    input: "text",
                    sortable: true,
                    value: 'name',
                    validate: 'required'
                },
                { 
                    text: 'Email', 
                    input: "text",
                    sortable: true,
                    value: 'email',
                    validate: 'email'
                },
                { 
                    text: 'Телефон', 
                    input: "text",
                    sortable: true,
                    value: 'phone'
                },
                { 
                    text: 'Логин', 
                    input: "text",
                    sortable: true,
                    value: 'login'
                },
                {
                    text: "Пароль",
                    input: "password",
                    sortable: false,
                    value: 'password',
                    visibility: 'd-none',
                },
                {
                    text: 'Город',
                    align: 'left',
                    sortable: true,
                    value: 'city_id',
                    selectText: 'name',
                    TableGetIdName: 'city',
                    selectApi: '/api/cities_to_works',
                    input: "select",
                },
                {
                    text: 'Модератор',
                    align: 'left',
                    sortable: true,
                    value: 'moderator_id',
                    selectText: 'name',
                    TableGetIdName: 'moderator',
                    selectApi: '/api/moderators',
                    input: "select",
                },
                {
                    text: 'Менеджер',
                    align: 'left',
                    sortable: true,
                    value: 'manager_id',
                    selectText: 'name',
                    TableGetIdName: 'manager',
                    selectApi: '/api/managers',
                    input: "select",
                },
                { 
                    text: 'Юридическое название', 
                    input: "text",
                    sortable: true,
                    value: 'legal_name',
                },
                { 
                    text: 'Фактическое название', 
                    input: "text",
                    sortable: true,
                    value: 'actual_title',
                },
                { 
                    text: 'Юридический адрес', 
                    input: "text",
                    sortable: true,
                    value: 'legal_address',
                },
                { 
                    text: 'Фактический адрес', 
                    input: "text",
                    sortable: true,
                    value: 'actual_address' 
                },
                { 
                    text: 'Название банка', 
                    input: "text",
                    sortable: true,
                    value: 'bank_name' 
                },
                { 
                    text: 'БИК', 
                    input: "text",
                    sortable: true,
                    value: 'bik'
                },
                { 
                    text: 'Кор. счёт', 
                    input: "text",
                    sortable: true,
                    value: 'cor_score' 
                },
                { 
                    text: 'Расчётный счёт', 
                    input: "text",
                    sortable: true,
                    value: 'settlement_account' 
                }
            ],
            searchValue: ['name', 'email', 'phone', 'city', 'login', 'legal_name', 'actual_title', 'legal_address', 'actual_address', 'bank_name', 'bik', 'cor_score', 'settlement_account'],
            search: true,
            pagination: true,
            excel: false
        }
    }),
    computed: {
        isLoggedUser: function(){ 
            return this.$store.getters.isLoggedUser;
        }
    },
    methods: {
        roleUser(role, roleList) {
            const {admin, client, installer, moderator, manager} = roleList;
            if (role === admin || role === client || role === installer || role === manager || role === moderator) {
                return true;
            } else {
                return false;
            }
        }
    }
}
</script>