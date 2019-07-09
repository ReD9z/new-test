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
                    value: 'name' 
                },
                { 
                    text: 'Email', 
                    input: "text",
                    sortable: true,
                    value: 'email' 
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
                    selectApi: 'api/cities_to_works',
                    input: "select",
                },
                { 
                    text: 'Юридическое название', 
                    input: "text",
                    sortable: true,
                    value: 'legal_name' 
                },
                { 
                    text: 'Фактическое название', 
                    input: "text",
                    sortable: true,
                    value: 'actual_title' 
                },
                { 
                    text: 'Юридический адрес', 
                    input: "text",
                    sortable: true,
                    value: 'legal_address' 
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
            search: true,
            pagination: true,
            excel: false
        }
    })
}
</script>