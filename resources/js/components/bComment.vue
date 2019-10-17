<template>
    <v-layout v-if="params.files_id == file.id" row wrap>
        <v-flex xs9>
            <v-text-field v-if="editShow ? true : false" v-model="params.comment" placeholder="Комментарий" required></v-text-field>
            <v-card-text v-if="editShow ? false : true" class="px-0"><v-icon small>textsms</v-icon> {{params.comment}}</v-card-text>
        </v-flex>
        <v-flex xs2>
            <v-layout row wrap>
                <v-flex xs6>
                    <v-btn v-if="editShow ? false : true" @click='edit()' icon>
                        <v-icon color="lighten-1" small>edit</v-icon>
                    </v-btn>
                    <v-btn  v-if="editShow ? true : false" @click='editComment(params.id, params.comment)' icon>
                        <v-icon color="lighten-1" small>save</v-icon>
                    </v-btn>
                </v-flex>
                <v-flex xs6>
                    <v-btn @click='removeComment(params)' icon>
                        <v-icon color="lighten-1" small >delete</v-icon>
                    </v-btn>
                </v-flex>
            </v-layout>
        </v-flex>
    </v-layout>
</template>
<script>
export default {
    props: {
        params: Object,
        file: Object,
        id: Number
    },
    data: () => ({
       editShow:false 
    }),
    methods: {
        edit() {
            this.editShow = true;
        },
        removeComment(item) {
            this.update = true;
            axios({
                method: 'delete',
                url: '/api/deleteComment',
                params: {
                    id: item.id,
                    idClient: this.id
                }
            })
            .then(
                response => {
                    
                }
            ).catch(error => {
                console.log(error);
            })
        },
        editComment(id, comment) {
            axios({
                method: 'put',
                url: '/api/editComment',
                params: {
                    id: id,
                    idClient: this.id,
                    comment: comment
                }
            })
            .then(
                response => {
                    this.editShow = false;
                }
            ).catch(error => {
                console.log(error);
            })
        },
    }, 
}
</script>