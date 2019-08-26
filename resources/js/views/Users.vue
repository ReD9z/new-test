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
            baseUrl: 'api/admins',
            headers: [
                { 
                    text: 'Имя', 
                    input: "text",
                    sortable: true,
                    value: 'name',
                    validate: 'required'
                },
                { 
                    text: 'Email', 
                    input: "text",
                    sortable: true,
                    value: 'email',
                    validate: 'required|email'
                },
                { 
                    text: 'Телефон', 
                    input: "text",
                    sortable: true,
                    value: 'phone',
                    validate: 'required'
                },
                { 
                    text: 'Логин', 
                    input: "text",
                    sortable: true,
                    value: 'login',
                    validate: 'required'
                },
                {
                    text: "Пароль",
                    input: "password",
                    sortable: false,
                    value: 'password',
                    visibility: 'd-none',
                }
            ],
            searchValue: ['name','email','phone','login'],
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