<template>
    <div class="card mb-4 mt-4">
        <div class="col-md-12 mb-4 mt-4">
            <b-table-address-order-edit :params="params" :idRouteOrder="$route.params.id" v-show="roleUser(isLoggedUser.role, {admin: 'admin', moderator: 'moderator', client: 'client'})"></b-table-address-order-edit>
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
                    selectText: 'name',
                    TableGetIdName: 'name',
                    selectApi: '/api/clients',
                    input: "select",
                    validate: 'required'
                },
                { 
                    text: 'Дата начала размещения', 
                    input: "dateStart",
                    sortable: true,
                    close: false,
                    value: 'order_start_date',
                    validate: 'required'
                },
                { 
                    text: 'Дата конца размещения', 
                    input: "dateEnd",
                    sortable: true,
                    close: false,
                    value: 'order_end_date',
                    validate: 'required'
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
                    selectApi: '/api/areas',
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
                },
                { 
                    value: 'files', 
                    sortable: false,
                    input: "images",
                    visibility: 'd-none',
                }
            ],
            filter: [
                {
                    title: 'Город',
                    api: '/api/cities_to_works',
                    value: 'name',
                    input: 'city'
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