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
            baseUrl: 'api/address_to_orders',
            fields: [
                {   
                    key: "order_id",
                    keyValue: "order_id",
                    label: "Заказ",
                    sort: true, 
                    edit: true,
                    type: "text",
                    input: "select",
                    selectApi: 'api/orders/allOrders',
                    selectKey: 'id',
                },
                {   
                    key: "address_id", 
                    keyValue: "address",  
                    label: "Адрес",
                    sort: true, 
                    edit: true,
                    type: "text",
                    input: "select",
                    selectApi: 'api/address/allAddress',
                    selectKey: 'city',
                },
                {   
                    key: "files", 
                    label: "Изображение",
                    sort: false, 
                    edit: true, 
                    type: "file",
                    input: "images"
                },
                {   
                    key: "status", 
                    label: "Статус",
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