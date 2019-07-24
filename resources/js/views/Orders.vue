<template>
    <div class="card mb-4 mt-4">
        <div class="col-md-12 mb-4 mt-4">
            <b-table-order :params="params"></b-table-order>
        </div>
    </div>
</template>
<script>
export default {
    data: () => ({
        params: {
            orderAddress: {
                baseUrl: '/api/address_to_orders',
                headers: [
                    {
                        text: 'Клиент',
                        align: 'left',
                        sortable: true,
                        value: 'order_id',
                        selectText: 'clients_name',
                        TableGetIdName: 'orders',
                        selectApi: '/api/orders',
                        input: "select",
                    },
                    {
                        text: 'Адрес',
                        align: 'left',
                        sortable: true,
                        value: 'address_id',
                        selectText: 'city',
                        TableGetIdName: 'address',
                        selectApi: '/api/address',
                        input: "select",
                    },
                    { 
                        value: 'files', 
                        sortable: false,
                        input: "images",
                        visibility: 'd-none',
                    }
                ],
            },
            baseUrl: '/api/orders',
            user: null,
            headers: [
                {
                    text: 'Клиент',
                    align: 'left',
                    sortable: true,
                    value: 'clients_id',
                    selectText: 'clients_name',
                    TableGetIdName: 'clients_name',
                    selectApi: '/api/clients',
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
                    value: 'address', 
                    sortable: false,
                    input: "address",
                    visibility: 'd-none',
                }
            ],
            filter: [
                {
                    api: '/api/clients',
                    value: 'name'
                },
            ],
            searchValue: ['clients_name', 'order_start_date', 'order_end_date'],
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
    created () { 
        if(this.isLoggedUser.role == "moderator") {
            this.params.user = this.isLoggedUser.moderators.city_id;
        }
    }
}
</script>