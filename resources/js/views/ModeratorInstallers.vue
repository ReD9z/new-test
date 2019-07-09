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
            baseUrl: '',
            headers: [
                { 
                    text: 'Имя', 
                    input: "text",
                    sortable: true,
                    value: 'name' 
                },
                { 
                    text: 'Email', 
                    input: "text",
                    sortable: true,
                    value: 'email' 
                },
                { 
                    text: 'Телефон', 
                    input: "text",
                    sortable: true,
                    value: 'phone' 
                },
                { 
                    text: 'Логин', 
                    input: "text",
                    sortable: true,
                    value: 'login' 
                },
                {
                    text: "Пароль",
                    input: "password",
                    sortable: false,
                    value: 'password',
                    visibility: 'd-none',
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
        this.params.baseUrl = '/api/moderatorsInstallers/' + this.isLoggedUser.moderators.id;
        let data = {
            text: 'Город',
            align: 'left',
            sortable: true,
            value: 'city_id',
            // selectText: 'name',
            // type: 'hidden',
            // selectApi: '/api/cities_to_works/' + this.isLoggedUser.moderators.city_id,
            input: "hidden",
            visibility: 'd-none',
        };
        this.params.headers.push(data);
    }
}
</script>