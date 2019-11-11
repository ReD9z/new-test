<template>
    <div class="card mb-4 mt-4">
        <div class="col-md-12 mb-4 mt-4">
            <b-table-address :params="params" v-show="roleUser(isLoggedUser.role, {admin: 'admin', moderator: 'moderator', manager: 'manager'})"></b-table-address>
        </div>
    </div>
</template>

<script>
export default {
    data: () => ({
        params: {
            baseUrl: '/api/address',
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
                    validate: 'required'
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
                    validate: 'required'
                },
                { 
                    text: 'Улица', 
                    input: "text",
                    value: 'street',
                    validate: 'required'
                },
                { 
                    text: 'Номер дома', 
                    input: "text",
                    value: 'house_number',
                    validate: 'required'
                },
                { 
                    text: 'Количество подъездов', 
                    input: "text",
                    validate: 'required|numeric',
                    value: 'number_entrances' 
                },
                { 
                    text: 'Управляющая компания', 
                    input: "text",
                    validate: 'required',
                    value: 'management_company' 
                },
                { 
                    text: 'Статус', 
                    input: "text",
                    value: 'result' 
                }
            ],
            searchValue: ['area', 'street', 'house_number', 'number_entrances', 'management_company', 'city'],
            search: true,
            pagination: true,
            excel: true,
            filter: [
                {
                    title: 'Адреса',
                    api: '/api/cities_to_works',
                    value: 'name',
                    input: 'city'
                }
            ],
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