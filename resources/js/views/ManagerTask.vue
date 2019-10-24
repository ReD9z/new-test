<template>
    <div class="card mb-4 mt-4">
        <div class="col-md-12 mb-4 mt-4">
            <b-table-task :params="params" v-show="roleUser(isLoggedUser.role, {admin: 'admin', moderator: 'moderator', installer: 'installer', manager: 'manager'})"></b-table-task>
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
                    selectText: 'name',
                    TableGetIdName: 'client',
                    selectApi: '/api/clients',
                    input: "select",
                    validate: 'required'
                },
                { 
                    text: 'Дата создания клинта', 
                    sortable: true,
                    close: false,
                    value: 'created_at',
                },
                { 
                    text: 'Email клиента', 
                    sortable: true,
                    close: false,
                    value: 'email'
                },
                { 
                    text: 'Телефон клиента', 
                    sortable: true,
                    close: false,
                    value: 'phone'
                },
                {
                    text: 'Менеджер',
                    align: 'left',
                    sortable: true,
                    value: 'manager_id',
                    selectText: 'name',
                    TableGetIdName: 'managers',
                    selectApi: '/api/managers',
                    input: "select",
                    validate: 'required'
                },
                { 
                    text: 'Дата звонка', 
                    input: "date",
                    sortable: true,
                    close: false,
                    value: 'task_date_completion'
                },
                { 
                    text: 'Дата последнего звонка', 
                    sortable: true,
                    close: false,
                    value: 'task_date_ended'
                },
                {
                    data: [
                        {status: 1, text: 'В работе'},
                        {status: 2, text: 'Завершена'}
                    ],
                    input: "status",
                    value: "status",
                    title: "statusName",
                    text: 'Статус',
                    validate: 'required',
                    sortable: true,
                },
                { 
                    text: 'Описание задачи', 
                    input: "text",
                    sortable: true,
                    value: 'comment',
                    validate: 'required'
                },
                { 
                    text: 'Результат выполнения', 
                    input: "text",
                    sortable: true,
                    value: 'result',
                }
            ],
            filter: [
                {
                    title: 'Клиенты',
                    api: '/api/clients',
                    value: 'name',
                    input: 'client'
                }
            ],
            searchValue: ['client', 'managers', 'task_date_completion', 'comment', 'statusName', 'result'],
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