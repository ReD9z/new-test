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
            headers: [
                {
                    text: 'Заказ',
                    align: 'left',
                    sortable: true,
                    value: 'order_id',
                    selectText: 'actual_title',
                    TableGetIdName: 'orders',
                    selectApi: 'api/orders',
                    input: "select",
                },
                {
                    text: 'Адрес',
                    align: 'left',
                    sortable: true,
                    value: 'address_id',
                    selectText: 'city',
                    TableGetIdName: 'address',
                    selectApi: 'api/address',
                    input: "select",
                },
                { 
                    value: 'files', 
                    sortable: false,
                    input: "images",
                    visibility: 'd-none',
                }
            ],
            filters: [],
            search: true,
            pagination: true,
            excel: false
        }
    })
}
</script>