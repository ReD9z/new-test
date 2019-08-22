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
            baseUrl: '/api/managers',
            headers: [
                { 
                    text: 'Имя', 
                    input: "text",
                    sortable: true,
                    validate: 'required',
                    value: 'name'
                },
                { 
                    text: 'Email', 
                    input: "text",
                    sortable: true,
                    validate: 'required|email',
                    value: 'email' 
                },
                { 
                    text: 'Телефон', 
                    input: "text",
                    sortable: true,
                    validate: 'required',
                    value: 'phone' 
                },
                { 
                    text: 'Логин', 
                    input: "text",
                    sortable: true,
                    validate: 'required',
                    value: 'login' 
                },
                {
                    text: 'Город',
                    align: 'left',
                    sortable: true,
                    value: 'city_id',
                    selectText: 'name',
                    TableGetIdName: 'city',
                    selectApi: '/api/cities_to_works',
                    validate: 'required',
                    input: "select",
                },
                {
                    text: 'Модератор',
                    align: 'left',
                    sortable: true,
                    value: 'moderator_id',
                    selectText: 'name',
                    TableGetIdName: 'moderator',
                    validate: 'required',
                    selectApi: '/api/moderators',
                    input: "select",
                },
                {
                    text: "Пароль",
                    input: "password",
                    sortable: false,
                    value: 'password',
                    visibility: 'd-none',
                }
            ],
            searchValue: ['name','email','phone','login','city','moderator'],
            search: true,
            pagination: true,
            excel: false
        }
    })
}
</script>