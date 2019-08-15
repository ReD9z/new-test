<template>
    <div class="card mb-4 mt-4">
        <div class="col-md-12 mb-4 mt-4">
            <b-table :params="params"></b-table>
        </div>
    </div>
</template>
<script>
export default {
    data: () => ({
        params: {
            baseUrl: 'api/clients',
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
                    validate: 'required:email'
                },
                { 
                    text: 'Телефон', 
                    input: "text",
                    sortable: true,
                    value: 'phone',
                    validate: 'required'
                },
                { 
                    text: 'Логин', 
                    input: "text",
                    sortable: true,
                    validate: 'required',
                    value: 'login' 
                },
                {
                    text: "Пароль",
                    input: "password",
                    sortable: false,
                    value: 'password',
                    visibility: 'd-none',
                },
                // {
                //     text: 'Город',
                //     align: 'left',
                //     sortable: true,
                //     value: 'city_id',
                //     selectText: 'name',
                //     TableGetIdName: 'city',
                //     selectApi: 'api/cities_to_works',
                //     input: "select",
                // },
                { 
                    text: 'Юридическое название', 
                    input: "text",
                    sortable: true,
                    value: 'legal_name', 
                    validate: 'required'
                },
                { 
                    text: 'Фактическое название', 
                    input: "text",
                    sortable: true,
                    value: 'actual_title',
                    validate: 'required'
                },
                { 
                    text: 'Юридический адрес', 
                    input: "text",
                    sortable: true,
                    value: 'legal_address',
                    validate: 'required'
                },
                { 
                    text: 'Фактический адрес', 
                    input: "text",
                    sortable: true,
                    validate: 'required',
                    value: 'actual_address' 
                },
                { 
                    text: 'Название банка', 
                    input: "text",
                    sortable: true,
                    validate: 'required',
                    value: 'bank_name' 
                },
                { 
                    text: 'БИК', 
                    input: "text",
                    sortable: true,
                    validate: 'required',
                    value: 'bik' 
                },
                { 
                    text: 'Кор. счёт', 
                    input: "text",
                    sortable: true,
                    validate: 'required',
                    value: 'cor_score' 
                },
                { 
                    text: 'Расчётный счёт', 
                    input: "text",
                    sortable: true,
                    validate: 'required',
                    value: 'settlement_account' 
                }
            ],
            searchValue: ['name', 'email', 'phone', 'login', 'legal_name', 'actual_title', 'legal_address', 'actual_address', 'bank_name', 'bik', 'cor_score', 'settlement_account'],
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
    created () { 
        if(this.isLoggedUser.role == "manager") {
            this.params.user = this.isLoggedUser.managers.city_id;
            this.params.headers.unshift(
                { 
                    text: 'Город', 
                    value: 'city' 
                }, 
                {
                    // text: 'Город',
                    align: 'left',
                    sortable: true,
                    value: 'city_id',
                    show: this.isLoggedUser.managers.city_id,
                    input: "hidden",
                    visibility: 'd-none'
                }
               
            );
        } else {
            this.params.headers.unshift({
                text: 'Город',
                align: 'left',
                sortable: true,
                value: 'city_id',
                selectText: 'name',
                TableGetIdName: 'city',
                selectApi: '/api/cities_to_works',
                input: "select",
                validate: 'required',
            });
        }
    }
}
</script>