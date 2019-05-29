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
            fields: [
                {   
                    key: "name", 
                    label: "Имя",
                    sort: true, 
                    edit: true,
                    type: "text",
                    input: "input"
                },
                {   
                    key: "email", 
                    label: "Email",
                    sort: true, 
                    edit: true,
                    type: "text",
                    input: "input"
                },
                {   
                    key: "password", 
                    label: "Пароль",
                    sort: false, 
                    edit: false,
                    type: "password",
                    input: "input"
                }
            ],
            search: true,
            pagination: true,
            excel: false
        }
    })
}
</script>