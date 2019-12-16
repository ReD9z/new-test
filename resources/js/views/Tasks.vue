<template>
    <div class="card mb-4 mt-4">
        <div class="col-md-12 mb-4 mt-4">
            <b-table-task :params="params" v-show="roleUser(isLoggedUser.role, {admin: 'admin', moderator: 'moderator', installer: 'installer'})"></b-table-task>
        </div>
    </div>
</template>
<script>
export default {
    data: (vm) => ({
        params: {
            baseUrl: '/api/tasks',
            excelTask: false,
            headers: [
                {
                    input: "select",
                    text: 'Заказ',
                    align: 'left',
                    sortable: true,
                    value: 'orders_id',
                    tableValue: 'orders',
                    childField: 'orderClient',
                    validate: 'required',
                    role: vm.$store.getters.isUserRole
                },
                {
                    input: "select",
                    text: 'Монтажник',
                    align: 'left',
                    sortable: true,
                    value: 'installer_id',
                    tableValue: 'installers',
                    childField: 'name',
                    validate: 'required',
                    role: vm.$store.getters.isUserRole
                },
                { 
                    input: "date",
                    text: 'Дата выполнения', 
                    sortable: true,
                    close: false,
                    value: 'task_date_completion',
                    tableValue: 'task_date_completion',
                    validate: 'required',
                    role: vm.$store.getters.isUserRole
                },
                {
                    input: "select",
                    text: 'Тип работы',
                    align: 'left',
                    sortable: true,
                    value: 'types_to_works_id',
                    tableValue: 'types',
                    childField: 'title',
                    validate: 'required',
                    role: vm.$store.getters.isUserRole
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
                    tableValue: "comment",
                    input: "textarea",
                    sortable: true,
                    value: 'comment',
                    validate: 'required',
                    role: vm.$store.getters.isUserRole
                }
            ],
            filter: false,
            searchValue: ['orders', 'installers', 'types', 'task_date_completion', 'comment', 'statusName'],
            search: true,
            pagination: true,
            excel: true
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