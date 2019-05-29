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
            fields: [
                {   
                    key: "orders_id",
                    keyValue: "orders_id",
                    label: "Заказ",
                    sort: true, 
                    edit: true,
                    type: "text",
                    input: "select",
                    selectApi: 'api/orders/allOrders',
                    selectKey: 'id',
                },
                {   
                    key: "installer_id", 
                    keyValue: "installers",
                    label: "Монтажник",
                    sort: true, 
                    edit: true,
                    type: "text",
                    input: "select",
                    selectApi: 'api/installers/allInstallers',
                    selectKey: 'name',
                },
                {   
                    key: "types_to_works_id",
                    keyValue: "types", 
                    label: "Тип работы",
                    sort: true, 
                    edit: true,
                    type: "text",
                    input: "select",
                    selectApi: 'api/types_to_work/allTypesToWorks',
                    selectKey: 'title',
                },
                {   
                    key: "task_date_completion", 
                    label: "Дата выполнения",
                    sort: true, 
                    edit: true,
                    type: "datetime-local",
                    input: "input"
                },
                {   
                    key: "comment", 
                    label: "Комментарий",
                    sort: true, 
                    edit: true,
                    type: "text",
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