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
            baseUrl: 'api/admins',
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
                    validate: 'required|email'
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
                    value: 'login',
                    validate: 'required'
                },
                {
                    text: "Пароль",
                    input: "password",
                    sortable: false,
                    value: 'password',
                    visibility: 'd-none',
                }
            ],
            searchValue: ['name','email','phone','login'],
        }
    })
}
</script>