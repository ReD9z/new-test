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
            fields: [
                {   
                    key: "client_id", 
                    keyValue: "clients",  
                    label: "Клиент",
                    sort: true, 
                    edit: true,
                    type: "text",
                    input: "select",
                    selectApi: 'api/clients/allClients',
                    selectKey: 'actual_title',
                },
                {   
                    key: "order_start_date", 
                    label: "Дата начала размещения",
                    sort: true, 
                    edit: true,
                    type: "datetime-local",
                    input: "input"
                },
                {   
                    key: "order_end_date", 
                    label: "Дата окончания размещения",
                    sort: true, 
                    edit: true,
                    type: "datetime-local",
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