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
            baseUrl: 'api/users',
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
                    text: 'Роль', 
                    input: "text",
                    sortable: true,
                    value: 'role' 
                },
                // Сброс пароля
                // Роль пользователя
                {
                    text: 'Пароль', 
                    input: "text",
                    sortable: true,
                    value: 'password' 
                },
                { 
                    text: 'Параметры',
                    sortable: false,
                    edit: true,
                }
            ]
        }
    })
}
</script>