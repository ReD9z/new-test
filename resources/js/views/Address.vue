<template>
    <div class="card mb-4 mt-4">
        <div class="col-md-12 mb-4 mt-4">
            <b-table-address :params="params"></b-table-address>
        </div>
    </div>
</template>

<script>
export default {
    data: () => ({
        params: {
            baseUrl: '/api/address',
            user: null,
            headers: [
                // {
                //     text: 'Город',
                //     align: 'left',
                //     sortable: true,
                //     value: 'city_id',
                //     selectText: 'name',
                //     TableGetIdName: 'city',
                //     selectApi: '/api/cities_to_works',
                //     input: "select",
                // },                                                                                                                
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
                }
            ],
            searchValue: ['area', 'street', 'house_number', 'number_entrances', 'management_company'],
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
    created () { 
        if(this.isLoggedUser.role == "moderator") {
            this.params.user = this.isLoggedUser.moderators.city_id;
            this.params.headers.unshift(
                { 
                    text: 'Город', 
                    value: 'city' 
                },   
                {
                    text: 'Город',
                    align: 'left',
                    sortable: true,
                    value: 'city_id',
                    show: this.isLoggedUser.moderators.city_id,
                    input: "hidden",
                    visibility: 'd-none'
                }
               
            );
        } else {
            this.params.headers.unshift({
                text: 'Город',
                align: 'left',
                sortable: true,
                value: 'city_id',
                selectText: 'name',
                TableGetIdName: 'city',
                selectApi: '/api/cities_to_works',
                input: "select"
            });
        }
    }
}
</script>