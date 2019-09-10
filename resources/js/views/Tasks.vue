<template>
    <div class="card mb-4 mt-4">
        <div class="col-md-12 mb-4 mt-4">
            <b-table-task :params="params" v-show="roleUser(isLoggedUser.role, {admin: 'admin', moderator: 'moderator', installer: 'installer'})"></b-table-task>
        </div>
    </div>
</template>
<script>
export default {
    data: () => ({
        params: {
            baseUrl: 'api/tasks',
            headers: [
                {
                    text: 'Заказ',
                    align: 'left',
                    sortable: true,
                    value: 'orders_id',
                    selectText: 'orderClient',
                    TableGetIdName: 'orderClient',
                    selectApi: '/api/orders',
                    input: "select",
                    validate: 'required'
                },
                {
                    text: 'Монтажник',
                    align: 'left',
                    sortable: true,
                    value: 'installer_id',
                    selectText: 'name',
                    TableGetIdName: 'installers',
                    selectApi: '/api/installers',
                    input: "select",
                    validate: 'required'
                },
                { 
                    text: 'Дата выполнения', 
                    input: "date",
                    sortable: true,
                    close: false,
                    value: 'task_date_completion',
                    validate: 'required'
                },
                {
                    text: 'Тип работы',
                    align: 'left',
                    sortable: true,
                    value: 'types_to_works_id',
                    selectText: 'title',
                    TableGetIdName: 'types',
                    selectApi: '/api/types_to_work',
                    input: "select",
                    validate: 'required'
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
                    text: 'Комментарий', 
                    input: "text",
                    sortable: true,
                    value: 'comment',
                    validate: 'required'
                }
            ],
            searchValue: ['orderClient', 'installers', 'types', 'task_date_completion', 'comment', 'statusName'],
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