<template>
    <div class="card mb-4 mt-4">
        <div class="col-md-12 mb-4 mt-4">
            <b-address-moderator :params="params" v-show="roleUser(isLoggedUser.role, {admin: 'admin'})"></b-address-moderator>
        </div>
    </div>
</template>
<script>
export default {
    data: () => ({
        params: {
            baseUrl: '/api/moderators',
            headers: [
                { 
                    text: 'Имя', 
                    input: "text",
                    sortable: true,
                    validate: 'required',
                    value: 'name' 
                },
                { 
                    text: 'Email', 
                    input: "text",
                    sortable: true,
                    validate: 'required',
                    value: 'email' 
                },
                { 
                    text: 'Телефон', 
                    input: "text",
                    sortable: true,
                    validate: 'required',
                    value: 'phone' 
                },
                { 
                    text: 'Логин', 
                    input: "text",
                    sortable: true,
                    validate: 'required',
                    value: 'login' 
                },
                {
                    text: 'Город',
                    align: 'left',
                    sortable: true,
                    validate: 'required',
                    value: 'address',
                    selectText: 'name',
                    TableGetIdName: 'address',
                    input: "select",
                },
                {
                    text: "Пароль",
                    input: "password",
                    sortable: false,
                    value: 'password',
                    visibility: 'd-none',
                }
            ],
            searchValue: ['name','email','phone','login','city'],
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