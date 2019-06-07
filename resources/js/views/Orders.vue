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
            baseUrl: 'api/orders',
            headers: [
                {
                    text: 'Клиент',
                    align: 'left',
                    sortable: true,
                    value: 'client_id',
                    selectText: 'actual_title',
                    TableGetIdName: 'actual_title',
                    selectApi: 'api/clients',
                    input: "select",
                },
                { 
                    text: 'Дата начала размещения', 
                    input: "date",
                    sortable: true,
                    close: false,
                    value: 'order_start_date' 
                },
                { 
                    text: 'Дата конца размещения', 
                    input: "date",
                    sortable: true,
                    close: false,
                    value: 'order_end_date' 
                },
                { 
                    text: 'Параметры',
                    sortable: false,
                    edit: true,
                }
            ],
            filters: [

            ],
            search: true,
            pagination: true,
            excel: false
        }
    })
}
</script>