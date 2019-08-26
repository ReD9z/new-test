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
            baseUrl: 'api/types_to_work',
            headers: [
                {
                    text: 'Название',
                    align: 'left',
                    sortable: true,
                    input: "text",
                    value: 'title',
                    validate: 'required'
                }
            ],
            searchValue: ['title'],
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