<template>
    <div class="card mb-4 mt-4">
        <div class="col-md-12 mb-4 mt-4">
            <v-card>
                <v-toolbar color="white" flat>
                    <v-toolbar-title>Нет такой страницы! <router-link :to="linkRouterRole()">Перейти на главную</router-link></v-toolbar-title>
                </v-toolbar>
            </v-card>
        </div>
    </div>
</template>
<script>
export default { 
computed: {
    isLoggedUser: function(){ 
            return this.$store.getters.isLoggedUser;
        }
    },
    methods: { 
        linkRouterRole() { 
            if(this.isLoggedUser.clients) {
                return "/orders";
            }
            if(this.isLoggedUser.managers) {
                return "/tasksManager";
            }
            if(this.isLoggedUser.installers) {
                return "/tasks";
            }
            else {
                return "/";
            }
           
        }
    }
}
</script>