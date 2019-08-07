<template>
    <div class="card mb-4 mt-4">
        <div class="col-md-12 mb-4 mt-4">
            <b-table-task :params="params"></b-table-task>
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
                    selectText: 'order_name',
                    TableGetIdName: 'order_name',
                    selectApi: 'api/orders',
                    input: "select",
                },
                {
                    text: 'Монтажник',
                    align: 'left',
                    sortable: true,
                    value: 'installer_id',
                    selectText: 'name',
                    TableGetIdName: 'installers',
                    selectApi: 'api/installers',
                    input: "select",
                },
                {
                    text: 'Тип работы',
                    align: 'left',
                    sortable: true,
                    value: 'types_to_works_id',
                    selectText: 'title',
                    TableGetIdName: 'types',
                    selectApi: 'api/types_to_work',
                    input: "select",
                },
                { 
                    text: 'Статус', 
                    input: "text",
                    sortable: true,
                    value: 'status',
                },
                { 
                    text: 'Комментарий', 
                    input: "text",
                    sortable: true,
                    value: 'comment',
                },
                { 
                    text: 'Дата выполнения', 
                    input: "date",
                    sortable: true,
                    close: false,
                    value: 'task_date_completion' 
                },
            ],
            searchValue: ['order_name', 'installers', 'types', 'task_date_completion', 'comment'],
            search: true,
            pagination: true,
            excel: true
        }
    })
}
</script>