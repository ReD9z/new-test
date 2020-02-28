<template>
    <div class="card mb-4 mt-4">
        <div class="col-md-12 mb-4 mt-4">
            <b-task-manager :params="params" v-show="roleUser(isLoggedUser.role, {admin: 'admin', moderator: 'moderator', installer: 'installer', manager: 'manager'})"></b-task-manager>
        </div>
    </div>
</template>
<script>
export default {
    data: () => ({
        params: {
            baseUrl: '/api/managerTask',
            excelTask: true,
            headers: [
                {
                    text: 'ФИО клиента',
                    align: 'left',
                    sortable: true,
                    value: 'client_id',
                    childField: 'legal_name',
                    tableValue: 'clients',
                    input: "select",
                    validate: 'required'
                },
                { 
                    text: 'Дата создания клинта', 
                    sortable: true,
                    close: false,
                    tableValue: 'created_at',
                    value: 'created_at'
                },
                { 
                    text: 'Email клиента', 
                    sortable: true,
                    close: false,
                    tableValue: 'email',
                    value: 'email'
                },
                { 
                    text: 'Телефон клиента', 
                    sortable: true,
                    tableValue: 'phone',
                    close: false,
                    value: 'phone'
                },
                {
                    text: 'Менеджер',
                    align: 'left',
                    sortable: true,
                    value: 'manager_id',
                    childField: 'name',
                    tableValue: 'managers',
                    input: "select"
                },
                { 
                    text: 'Дата звонка', 
                    input: "date",
                    sortable: true,
                    close: false,
                    value: 'task_date_completion',
                    tableValue: 'task_date_completion'
                },
                { 
                    text: 'Дата последнего звонка', 
                    sortable: true,
                    close: false,
                    tableValue: 'task_date_ended',
                    value: 'task_date_ended'
                },
                {
                    input: "select",
                    value: "status",
                    tableValue: 'statusName',
                    childField: 'title',
                    text: 'Статус',
                    validate: 'required',
                    sortable: true,
                    role: null
                },
                { 
                    text: 'Комментарий', 
                    input: "text",
                    tableValue: 'comment',
                    sortable: true,
                    value: 'comment',
                },
                { 
                    text: 'Результат выполнения', 
                    input: "text",
                    tableValue: 'result',
                    sortable: true,
                    value: 'result'
                }
            ],
            filter: true,
            searchValue: ['clients', 'managers', 'task_date_completion', 'comment', 'statusName', 'result'],
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