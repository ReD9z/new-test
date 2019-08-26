<template>
    <div class="card mb-4 mt-4">
        <div class="col-md-12 mb-4 mt-4">
            <b-table :params="params" v-show="roleUser(isLoggedUser.role, {admin: 'admin'})"></b-table>
        </div>
    </div>
</template>
<script>
export default {
    data: () => ({
        params: {
            baseUrl: '/api/cities_to_works',
            headers: [
                {
                    text: 'Название',
                    align: 'left',
                    sortable: true,
                    value: 'name',
                    validate: 'required',
                    input: "text"
                }
            ],
            searchValue: ['name'],
            search: true,
            pagination: true,
            excel: false
        },
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
    })
}
</script>