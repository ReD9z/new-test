<template>
    <div class="card mb-4 mt-4">
        <div class="col-md-12 mb-4 mt-4">
            <b-task-manager :params="params" v-show="roleUser(isLoggedUser.role, {admin: 'admin', moderator: 'moderator', manager: 'manager'})"></b-task-manager>
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
                    text: 'Дата звонка', 
                    input: "date",
                    sortable: true,
                    close: false,
                    value: 'task_date_completion',
                    tableValue: 'task_date_completion'
                },
                {
                    text: 'Город',
                    align: 'left',
                    sortable: true,
                    value: 'city_id',
                    childField: 'name',
                    tableValue: 'cities',
                    input: "select",
                    // validate: 'required'
                },       
                {
                    text: 'Наименование',
                    align: 'left',
                    sortable: true,
                    value: 'client_id',
                    childField: 'legal_name',
                    tableValue: 'clientsLegal',
                    input: "select",
                    // validate: 'required'
                },
                { 
                    text: 'Контактное лицо', 
                    sortable: true,
                    close: false,
                    tableValue: 'clients',
                    value: 'clients',
                    input: "text",
                    // validate: 'required'
                },
                { 
                    text: 'Телефон', 
                    sortable: true,
                    tableValue: 'phone',
                    close: false,
                    input: "text",
                    value: 'phone'
                },
                { 
                    text: 'Почта', 
                    sortable: true,
                    close: false,
                    tableValue: 'email',
                    input: "text",
                    value: 'email',
                    // validate: 'email'
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
                    text: 'Комментарий', 
                    input: "text",
                    tableValue: 'comment',
                    sortable: true,
                    value: 'comment',
                },
                {
                    input: "select",
                    value: "status",
                    tableValue: 'statusName',
                    childField: 'title',
                    text: 'Статус',
                    // validate: 'required',
                    sortable: true,
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