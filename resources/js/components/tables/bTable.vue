<template>
<div>
    <v-toolbar color="#fff" fixed app clipped-righ>
        <v-toolbar-title>Toolbar</v-toolbar-title>
        <v-spacer></v-spacer>
         <v-btn
            color="blue-grey"
            class="white--text"
            :loading="loadingExcel"
            :disabled="loadingExcel"
            @click='pickExcel'
        >
        Добавить Excel
        <v-icon right dark>cloud_upload</v-icon>
        <input
            type="file"
            style="display: none"
            ref="excel"
            accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
            @change="elementLoadToFile"
            multiple
        >
        </v-btn>
        <v-dialog v-model="dialog" max-width="500px">
            <template v-slot:activator="{ on }">
                <v-btn color="green" dark class="mb-2" v-on="on">Добавить</v-btn>
            </template>
            <v-card>
                <v-card-title>
                    <span class="headline">{{ formTitle }}</span>
                </v-card-title>
                <v-card-text>
                    <v-container grid-list-md>
                    <v-layout wrap>
                        <v-flex xs12 sm6 md4>
                            <v-text-field v-model="editedItem.title" label="Название"></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm6 md4>
                            <v-text-field v-model="editedItem.text" label="Текст"></v-text-field>
                        </v-flex>
                        <v-flex xs12>
                            <v-btn
                                color="blue-grey"
                                class="white--text"
                                @click='pickImages'
                            >
                                Добавить изображения
                                <v-icon right dark>cloud_upload</v-icon>
                                <input
                                    type="file"
                                    ref="images"
                                    name='file'
                                    accept="image/*"
                                    style="display: none"
                                    @change="elementLoadToFileImage"
                                    multiple
                                >
                            </v-btn>
                        </v-flex> 
                    </v-layout>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" flat @click="close">Закрыть</v-btn>
                    <v-btn color="blue darken-1" flat @click="save">Сохранить</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-toolbar>
    <v-toolbar flat color="#fff">
        <v-flex xs12 sm6 md3>
        <v-text-field
            v-model="search"
            append-icon="search"
            label="Search"
            single-line
            hide-details
        ></v-text-field>
        </v-flex>
        <v-spacer></v-spacer>
        <v-btn icon>
            <v-icon>more_vert</v-icon>
        </v-btn>
    </v-toolbar>
    <v-data-table :headers="headers" :items="desserts" :search="search" :loading="loading" class="elevation-1">
        <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>
        <template v-slot:items="props">
            <td>{{ props.item.title }}</td>
            <td class="text-xs-right">{{ props.item.text}}</td>
            <td class="text-xs-right">
                <div class="" v-for="img in props.item.files" :key="img.id">
                    <img :src="'/storage/' + img.url" alt="" style="width:200px">
                    <button type="button" class="close" aria-label="Close" v-on:click="removeImg(img)">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </td>
            <td class="justify-center layout px-0">
            <v-icon small class="mr-2" @click="editItem(props.item)">
                edit
            </v-icon>
            <v-icon small @click="deleteItem(props.item)">
                delete
            </v-icon>
        </td>
        </template>
        <template v-slot:no-data>
            <v-btn color="primary" @click="initialize">Сброс</v-btn>
        </template>
        <template v-slot:no-results>
            <v-alert :value="true" color="error" icon="warning">
            Your search for "{{ search }}" found no results.
            </v-alert>
        </template>
        <template v-slot:pageText="props">
            {{ props.pageStart }} - {{ props.pageStop }} из {{ props.itemsLength }}
        </template>
    </v-data-table>
</div>
</template>
<script>
import XLSX from 'xlsx';
export default {
    data: () => ({
        search: '',
        dialog: false,
        loading: true,
        loadingExcel: false,
        headers: [
            {
            text: 'Название',
            align: 'left',
            sortable: true,
            value: 'title'
            },
            { text: 'Текст', value: 'text' },
            { text: 'Изображения', value: 'files', sortable: false},
            { text: 'Параметры', value: 'name', sortable: false }
        ],
        files: [],
        desserts: [],
        editedIndex: -1,
        editedItem: {
            title: '',
            text: '',
            files: []
        },
        defaultItem: {
            title: '',
            text: '',
            files: []
        },
        formData: new FormData(),
    }),
    props: {
        params: Object
    },
    computed: {
        formTitle () {
            return this.editedIndex === -1 ? 'Добавить' : 'Редактировать'
        }
    },
    watch: {
        dialog (val) {
            val || this.close()
        }
    },
    created () {
        this.initialize();
    },
    methods: {
        initialize () {
            axios({
                method: 'get',
                url: this.params.baseUrl
            })
            .then(
                response => {
                    this.desserts = response.data;
                    this.loading = false;
                }
            ).catch(error => {
                console.log(error);
            })
        },
        pickImages () {
            this.$refs.images.click();
        },
        elementLoadToFileImage() {
            this.files = this.$refs.images.files;
        },
        elementLoadToFile() {
            this.loadingExcel = true;
            let file = this.$refs.excel.files[0];
            let reader = new FileReader();
            let vm = this;
            reader.readAsBinaryString(file);
            reader.onload = function (e) {
                let workbook = XLSX.read(e.target.result, {
                    type: 'binary'
                });
                let firstSheet = workbook.SheetNames[0];
                let excelRows = XLSX.utils.sheet_to_json(workbook.Sheets[firstSheet]);
                axios({
                    method: 'post',
                    url: vm.params.baseUrl +'/excel',
                    data: excelRows
                })
                .then(
                    response => {
                        let array = Array.from(response.data.data);
                        array.forEach(element => {
                            vm.desserts.push(element);
                        });
                        setTimeout(() => ( vm.loadingExcel = false), 1000);
                    }
                ).catch(error => {
                    console.log(error);
                });
               
                file.value = '';
            };
        },
        removeImg(data) {
            axios.post('api/files/remove', data)
            .then(
                res => {
                    axios({
                        method: 'delete',
                        url: this.params.baseUrl,
                        data: res,
                        params: { 
                            images: data.id
                        }
                    })
                    .then(
                        response => {
                           this.initialize();
                        }
                    ).catch(error => {
                        console.log(error);
                    })
                }
            ).catch(
                error => {
                    console.log(error);
                }
            );
        },
        pickExcel () {
            this.$refs.excel.click();
        },
        editItem (item) {
            this.editedIndex = this.desserts.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialog = true;
        },
        deleteItem (item) {
            const index = this.desserts.indexOf(item);
            if (confirm('Are you sure you want to delete this item?')) {
                axios({
                    method: 'delete',
                    url: this.params.baseUrl+'/'+item.id,
                })
                .then(
                    response => {
                        this.desserts.splice(index, 1);
                    }
                ).catch(error => {
                    console.log(error);
                })
            }
        },
        close () {
            this.dialog = false
            setTimeout(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            }, 300)
        },
        resetFilesLoad() {
            this.files = [];
            this.$refs.images.value = '';
            this.formData.delete('file[]');
        },
        save () {
            let method = null;
            if (this.editedIndex > -1) {
                method = 'put'
            } else {
                method = 'post'
            }
            if(this.files.length > 0) {
                Array.from(this.files).forEach(files => {
                    this.formData.append('file[]', files);
                });
                axios.post('api/files', this.formData, {
                    headers: {'Content-Type': 'multipart/form-data'}
                })
                .then(
                    res => {
                        axios({
                            method: method,
                            url: this.params.baseUrl,
                            data: this.editedItem,
                            params: {
                                images: res.data.files
                            }
                        })
                        .then(
                            response => {
                                this.initialize();
                                this.close();
                                this.resetFilesLoad();
                            }
                        ).catch(error => {
                            console.log(error);
                        })
                    }
                ).catch(
                    error => {
                        console.log(error);
                    }
                );
            } else {
                axios({
                    method: method,
                    url: this.params.baseUrl,
                    data: this.editedItem
                })
                .then(
                    response => {
                        if (this.editedIndex > -1) {
                            Object.assign(this.desserts[this.editedIndex], this.editedItem);
                        } else {
                            this.desserts.push(this.editedItem);
                        }
                        this.close();
                    }
                ).catch(error => {
                    console.log(error);
                })
            }
        },
    }
}
</script>