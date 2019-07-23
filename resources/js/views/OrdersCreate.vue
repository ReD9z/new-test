<template>
    <div class="card mb-4 mt-4">
        <div class="col-md-12 mb-4 mt-4">
            <b-table-order-create :params="params"></b-table-order-create>
        </div>
    </div>
</template>
<script>
export default {
    data: () => ({
        params: {
            baseUrl: '/api/address',
            baseOrders: '/api/orders',
            headerOrders: [
                {
                    text: 'Клиент',
                    align: 'left',
                    sortable: true,
                    value: 'clients_id',
                    selectText: 'actual_title',
                    TableGetIdName: 'actual_title',
                    selectApi: 'api/clients',
                    input: "select",
                    validate: [
                        v => !!v || 'Поле "Клиент" обязательно',
                    ],
                },
                { 
                    text: 'Дата начала размещения', 
                    input: "dateStart",
                    sortable: true,
                    close: false,
                    value: 'order_start_date',
                    validate: [
                        v => !!v || 'Поле "Дата начала размещения" обязательно',
                    ],
                },
                { 
                    text: 'Дата конца размещения', 
                    input: "dateEnd",
                    sortable: true,
                    close: false,
                    value: 'order_end_date',
                    validate: [
                        v => !!v || 'Поле "Дата конца размещения" обязательно',
                    ],
                }
            ],
            searchValue: ['city', 'street', 'management_company', 'number_entrances', 'area', 'house_number', 'result'],
            headers: [
                {
                    text: 'Город',
                    align: 'left',
                    sortable: true,
                    value: 'city_id',
                    selectText: 'name',
                    TableGetIdName: 'city',
                    selectApi: '/api/cities_to_works',
                    input: "select",
                },
                {
                    text: 'Район',
                    align: 'left',
                    sortable: true,
                    value: 'area_id',
                    selectText: 'name',
                    TableGetIdName: 'area',
                    selectApi: 'api/areas',
                    input: "select",
                },
                { 
                    text: 'Улица', 
                    input: "text",
                    value: 'street' 
                },
                { 
                    text: 'Номер дома', 
                    input: "text",
                    value: 'house_number' 
                },
                { 
                    text: 'Количество подъездов', 
                    input: "text",
                    value: 'number_entrances' 
                },
                { 
                    text: 'Управляющая компания', 
                    input: "text",
                    value: 'management_company' 
                },
                { 
                    text: 'Статус', 
                    input: "text",
                    value: 'result' 
                }
            ],
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