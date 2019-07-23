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
            baseUrl: 'api/tasks',
            headers: [
                {
                    text: 'Заказ',
                    align: 'left',
                    sortable: true,
                    value: 'orders_id',
                    selectText: 'actual_title',
                    TableGetIdName: 'orders',
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
                    text: 'Дата начала размещения', 
                    input: "date",
                    sortable: true,
                    close: false,
                    value: 'task_date_completion' 
                },
                { 
                    text: 'Комментарий', 
                    input: "text",
                    sortable: true,
                    value: 'comment',
                }
            ],
            searchValue: ['actual_title', 'installers','types','task_date_completion','comment'],
            search: true,
            pagination: true,
            excel: false
        }
    })
}
</script>