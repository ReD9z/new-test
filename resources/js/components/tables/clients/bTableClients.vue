<template>
<div>
    <v-toolbar color="#fff" fixed app clipped-righ>
        <v-toolbar-title>{{$route.meta.title}}</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn color="green" large class="mb-2 white--text" @click.stop="dialog = !dialog"><v-icon left>add</v-icon>Создать</v-btn>
    </v-toolbar>
    <v-navigation-drawer v-model="dialog" right hide-overlay stateless fixed>
        <v-card class="borderNone">
            <v-toolbar color="pink" dark>
                <v-toolbar-title>{{ formTitle }}</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn icon @click="close">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-card-text>
                <v-form ref="forms" v-model="valid" lazy-validation>
                    <v-flex v-for="(param, key) in params.headers" :key="key" xs12>
                        <div v-if="param.input == 'text'">
                            <v-text-field :data-vv-as="'`'+param.text+'`'" :data-vv-name="param.value" :error-messages="errors.collect(param.value)" v-validate="param.validate" v-model="editedItem[param.value]" :label="param.text" v-if="param.input !== 'images' && param.edit != true" xs12 required></v-text-field>
                        </div>
                        <div v-if="param.input == 'select'">
                            <div v-for="item in select" :key="item[0]">
                                <div v-if="item.url == param.selectApi">
                                    <v-autocomplete
                                        :items="item.data"
                                        v-model="editedItem[param.value]"
                                        :item-text="param.selectText"
                                        :data-vv-name="param.value" 
                                        :error-messages="errors.collect(param.value)" 
                                        v-validate="param.validate"
                                        item-value="id"
                                        :label="param.text"
                                        :data-vv-as="'`'+param.text+'`'"
                                        >
                                    </v-autocomplete>
                                </div>
                            </div>
                        </div>
                        <div v-if="param.input == 'password'">
                            <v-text-field 
                                :type="param.value" 
                                v-model="editedItem[param.value]" 
                                :label="param.text" 
                                v-validate="editedItem[param.value] ? {required:true, regex:/^\S*$/ , min:6} : ''"
                                :data-vv-name="param.value" 
                                :error-messages="errors.collect(param.value)" 
                                :data-vv-as="'`'+param.text+'`'"
                                xs12>
                            </v-text-field>
                        </div>
                    </v-flex>
                    <div class="text-xs-center">
                        <v-btn color="info" @click="save" :loading="loadingSaveBtn" :disabled="loadingSaveBtn">
                            Сохранить
                            <template v-slot:loaderSaveBtn>
                                <span class="custom-loader">
                                    <v-icon light>cached</v-icon>
                                </span>
                            </template>
                        </v-btn>
                    </div>
                </v-form>
            </v-card-text>
        </v-card>
    </v-navigation-drawer>
    <v-navigation-drawer v-model="dialogFiles" right hide-overlay stateless fixed width="400px">
        <v-card class="borderNone">
            <v-toolbar color="pink" dark>
                <v-text-field color="#fff" v-model="comment" placeholder="Комментарий" required></v-text-field>
                <v-spacer></v-spacer>
                <v-icon black @click='pickFiles'>control_point</v-icon>
                <input type="file" ref="filesClient" name='file' style="display: none" @change="elementLoadToFile" multiple>
                <v-btn icon @click="close">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-progress-linear value="15" :indeterminate="true" v-show="loadFiles" color="blue" class="ma-0"></v-progress-linear>
                <v-flex v-for="(file, key) in addClientFiles.files" :key="key" xs12>
                    <v-card class="borderNone">
                        <v-list two-line subheader>
                            <v-list-tile>
                                <v-list-tile-avatar>
                                    <v-icon class="blue white--text">cloud</v-icon>
                                </v-list-tile-avatar>
                                <v-list-tile-content>
                                    <v-list-tile-title><a :href="'/storage/' + file.url" download>{{file.url.replace('upload/', '')}}</a></v-list-tile-title>
                                </v-list-tile-content>
                                <v-list-tile-action>
                                    <v-btn  @click='removeFile(file)' :disabled="deleteFile" icon ripple>
                                        <v-icon color="grey lighten-1">close</v-icon>
                                    </v-btn>
                                </v-list-tile-action>
                            </v-list-tile>
                            <v-expansion-panel>
                                <v-expansion-panel-content>
                                    <template v-slot:header>
                                        <div><v-icon small>comment</v-icon> Комментарии</div>
                                        <v-spacer></v-spacer>
                                    </template>
                                    <v-card class="mt-0 ml-4 mr-2 mb-2">
                                        <v-text-field
                                            v-model="newComment"
                                            persistent-hint
                                            placeholder="Комментарий" 
                                            required
                                        >
                                            <template v-slot:append-outer>
                                                <v-btn @click='addComment(file.id)' icon>
                                                    <v-icon color="lighten-1">add</v-icon>
                                                </v-btn>
                                            </template>
                                        </v-text-field>
                                        <div v-for="(value, keys) in addClientFiles.comments" :key="keys">
                                            <v-layout v-if="value.files_id == file.id">
                                                <v-flex xs9>
                                                    <v-textarea rows="1" class="pb-0 pt-0 pr-0 pl-0" v-model="value.comment" placeholder="Комментарий" required></v-textarea>
                                                </v-flex>
                                                <v-flex xs2>
                                                    <v-layout row wrap>
                                                        <v-flex xs6>
                                                            <v-btn @click='editComment(value.id, value.comment)' icon>
                                                                <v-icon color="lighten-1" small>save</v-icon>
                                                            </v-btn>
                                                        </v-flex>
                                                        <v-flex xs6>
                                                            <v-btn @click='removeComment(value.id)' icon>
                                                                <v-icon color="lighten-1" small >delete</v-icon>
                                                            </v-btn>
                                                        </v-flex>
                                                    </v-layout>
                                                </v-flex>
                                            </v-layout>
                                        </div>
                                    </v-card>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-list>
                    </v-card>
                </v-flex>
        </v-card>
    </v-navigation-drawer>
    <v-toolbar flat color="#fff">
        <v-flex xs12 sm6 md3>
            <v-text-field v-model="search" append-icon="search" label="Поиск" v-show="params.search" single-line hide-details></v-text-field>
        </v-flex>
    </v-toolbar>
    <v-data-table :rows-per-page-items='[25, 35, 45, {text: "Все", value: -1}]' :pagination.sync="pagination" :headers="params.headers" :items="desserts">
        <template v-slot:items="props">
            <td v-for="(param, key) in params.headers" :key="key" :class="param.visibility">
                <v-flex v-if="param.input !== 'images'">
                    <v-flex v-if="param.selectText">{{props.item[param.TableGetIdName]}}</v-flex>
                    <v-flex v-else>{{props.item[param.value]}}</v-flex>
                </v-flex>
            </td>
            <td class="justify-left layout">
                <v-icon small class="mr-2" @click="editFiles(props.item)">
                    attach_file
                </v-icon>
                <v-icon small class="mr-2" @click="editItem(props.item)">	
                    edit	
                </v-icon>
                <v-icon small class="mr-2" @click="deleteItem(props.item)">
                    delete
                </v-icon>
            </td>
        </template>
        <template v-slot:no-data>
            <v-btn color="primary" @click="refreshSearch">Сброс</v-btn>
        </template>
    </v-data-table>
    <div class="text-xs-center pt-2">
        <v-pagination v-model="pagination.page" :length="pages" :total-visible="7"></v-pagination>
    </div>
</div>
</template>
<script>
import XLSX from 'xlsx';
export default {
    inject: ['$validator'],
    data: () => ({
        search: '',
        dialog: false,
        dialogFiles: false,
        loading: true,
        loadFiles: false,
        loadingExcel: false,
        comment: '',
        files: [],
        newComment: '',
        desserts: [],
        editedIndex: -1,
        editedItem: {},
        defaultItem: {},
        select: [],
        loadingSaveBtn: false,
        loaderSaveBtn: null,
        formData: new FormData(),
        valid: true,
        files: null,
        dialogPass: false,
        pagination: {
            sortBy: 'id'
        },
        addClientFiles: [],
        selected: [],
        cityUser: null,
        deleteFile: false,
        userId: null
    }),
    props: {
        params: Object
    },
    computed: {
        formTitle () {
            return this.editedIndex === -1 ? 'Добавить' : 'Редактировать'
        },
        computedDateFormatted () {
            return this.formatDate(this.date)
        },
        isLoggedUser: function(){ 
            return this.$store.getters.isLoggedUser;
        },
        pages() {
            this.pagination.totalItems = this.desserts.length;
            if (this.pagination.rowsPerPage == null ||
                this.pagination.totalItems == null
            ) return 0
            return Math.ceil(this.pagination.totalItems / this.pagination.rowsPerPage)
        }
    },
    watch: {
        dialog (val) {
            val || this.close()
        },
        search: _.debounce(function () {
            this.initialize()
        }, 400)
    },
    async created () {
        await this.initialize();
        await this.selectStatus();
    },
    methods: {
        editShow() {
            return true;
        },
        roleUserCity() {
            if(this.isLoggedUser.moderators) {
               return this.cityUser = this.isLoggedUser.moderators.addresses;
            }
            if(this.isLoggedUser.managers) {
                let arr = [];
                arr.push({'city_id': this.isLoggedUser.managers.city_id});
                return this.cityUser = arr;
            }
            if(!this.isLoggedUser.moderators || !this.isLoggedUser.managers) {
                return this.cityUser = null;
            }
        },
        getModeratorId() {
            if(this.isLoggedUser.moderators) {
                return this.isLoggedUser.moderators.id;
            } 
            else {
                return null
            }
        },
        getManagerId() {
            if(this.isLoggedUser.managers) {
                return this.isLoggedUser.managers.id;
            } 
            else {
                return null
            }
        },
        roleUserId() {
            if(this.isLoggedUser.moderators) {
                return this.userId = this.isLoggedUser.moderators.id;
            }
            if(this.isLoggedUser.managers) {
                return this.userId = this.isLoggedUser.managers.moderator_id;
            }
            if(!this.isLoggedUser.moderators || !this.isLoggedUser.managers) {
                return this.userId = null;
            }
        },
        showRoles() {
            if(this.isLoggedUser.moderators || this.isLoggedUser.managers) {
                return false;
            }
            if(!this.isLoggedUser.moderators || !this.isLoggedUser.managers) {
                return true;
            }
        },
        pickFiles() {
            this.$refs.filesClient.click();
        },
        filteredItems(data) {
            this.desserts = data;
            let searchTerm = this.search.trim().toLowerCase(),
            useOr = this.search == "or",
            AND_RegEx = "(?=.*" + searchTerm.replace(/ +/g, ")(?=.*") + ")",
            OR_RegEx = searchTerm.replace(/ +/g,"|"),
            regExExpression = useOr ? OR_RegEx : AND_RegEx,
            searchTest = new RegExp(regExExpression, "ig");
            let thisSearch = this.params.searchValue;
            return this.desserts = this.desserts.filter(function(item) {
                let arr = [];
                thisSearch.forEach(function(val) {
                    arr.push(item[val]);
                })
                return searchTest.test(arr.join(" ")); 
            });
        },
        refreshSearch() {
            this.loading = true;
            this.search = '';
            this.initialize();
        },
        toggleAll () {
            if (this.selected.length) this.selected = []
            else this.selected = this.desserts.slice()
        },
        changeSort (column) {
            if (this.pagination.sortBy === column) {
                this.pagination.descending = !this.pagination.descending
            } else {
                this.pagination.sortBy = column
                this.pagination.descending = false
            }
        },
        initialize() {
            axios({
                method: 'get',
                url: this.params.baseUrl,
                params: {
                    // user: this.roleUserId(),
                    // city: JSON.stringify(this.roleUserCity()),
                    moderator: this.getModeratorId(),
                    manager: this.getManagerId()
                }
            })
            .then(
                response => {
                    this.desserts = response.data;
                    this.filteredItems(this.desserts);
                    this.loading = false;
                }
            ).catch(error => {
                console.log(error);
            })
        },
        showEdit(val) {
            console.log(this);
        },
        elementLoadToFile() {
            this.loadFiles = true;
            this.files = this.$refs.filesClient.files;
            Array.from(this.files).forEach(files => {
                this.formData.append('file[]', files);
            });
            axios.post('/api/files', this.formData, {
                headers: {'Content-Type': 'multipart/form-data'}
            })
            .then(
                res => {
                    axios({
                        method: 'put',
                        url: '/api/clientsFiles',
                        data: this.addClientFiles,
                        params: {
                            fileClient: res.data.files,
                            comment: this.comment,
                            idClient: this.editedItem.id
                        }
                    })
                    .then(
                        response => {
                            this.comment = '';
                            let data = response.data;
                            this.editFiles(data);
                            // this.addClientFiles = Object.assign(this.addClientFiles, data);
                            this.loadFiles = false;
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
        },
        addComment(idFile) {
            axios({
                method: 'post',
                url: '/api/clientsComment',
                params: {
                    fileId: idFile,
                    comment: this.newComment,
                    idClient: this.editedItem.id
                }
            })
            .then(
                response => {
                    this.newComment = '';
                    let data = response.data;
                    this.editFiles(data);
                }
            ).catch(error => {
                console.log(error);
            })
        },
        removeComment(id) {
            axios({
                method: 'delete',
                url: '/api/deleteComment',
                params: {
                    id: id,
                    idClient: this.editedItem.id
                }
            })
            .then(
                response => {
                    let data = response.data;
                    this.editFiles(data);
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
                    idClient: this.editedItem.id,
                    comment: comment
                }
            })
            .then(
                response => {
                    let data = response.data;
                    this.editFiles(data);
                }
            ).catch(error => {
                console.log(error);
            })
        },
        removeFile(data) {
            this.deleteFile = true;
            axios.post('/api/files/remove', data)
            .then(
                res => {
                    this.addClientFiles.files.splice(this.addClientFiles.files.indexOf(data.id), 1);
                    this.deleteFile = false;
                }
            ).catch(
                error => {
                    console.log(error);
                }
            );
        },
        resetFilesLoad() {
            this.files = [];
            this.$refs.filesClient.value = '';
            this.formData.delete('file[]');
        },
        selectStatus() {
            this.params.headers.forEach(element => {
                if(element.selectApi != undefined) {
                    axios({
                        method: 'get',
                        url: element.selectApi,
                        params: {
                            user: this.roleUserId(),
                            city: JSON.stringify(this.roleUserCity()),
                            moderator: this.getModeratorId(),
                            manager: this.getManagerId()
                        }
                    })
                    .then(
                        res => {
                            if(res) {
                                this.select.push(
                                    {
                                        data: res.data,
                                        url: element.selectApi
                                    }
                                );
                            }
                        }
                    ).catch(
                        error => {
                            console.log(error);
                        }
                    ); 
                }
            });
        },
        editItem (item) {
            this.editedIndex = this.desserts.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialog = true;
        },
        editFiles(item) {
            this.addClientFiles = Object.assign({}, item);
            this.editedItem = Object.assign({}, item);
            this.dialogFiles = true;
        },
        deleteItem (item) {
            const index = this.desserts.indexOf(item);
            if (confirm('Вы уверены, что хотите удалить этот элемент?')) {
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
            this.dialogFiles = false
            setTimeout(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.dataAdd()
                this.editedIndex = -1
            }, 300)
            this.$validator.reset()
        },
        save() {
            this.$validator.validateAll().then(() => {
                if(this.$validator.errors.items.length == 0) {
                    this.loaderSaveBtn = true;
                    this.loadingSaveBtn = true;
                    let method = null;
                    if (this.editedIndex > -1) {
                        method = 'put'
                    } else {
                        method = 'post'
                    }
                    axios({
                        url: this.params.baseUrl,
                        method: method,
                        data: this.editedItem,
                    })
                    .then(
                        response => {
                            if (this.editedIndex > -1) {
                                Object.assign(this.desserts[this.editedIndex], response.data);
                            } else {
                                this.desserts.push(response.data);
                            }
                            this.loaderSaveBtn = null;
                            this.loadingSaveBtn = false;
                            this.close();
                            this.$refs.forms.reset();
                        }
                    ).catch(error => {
                        if(error.response.status == 422) {
                            if(error.response.data.errors.email) {
                                const error = {
                                    field: "email",
                                    msg: 'Такой email уже есть!',
                                    rule: 'required', 
                                    scope: null,
                                    regenerate: () => 'some string', 
                                    vmId: 4,
                                    id: "2"
                                };
                                this.errors.items.push(error);
                                this.loaderSaveBtn = null;
                                this.loadingSaveBtn = false;
                            }
                        }
                    })  
                } else {
                    this.snackbar = true
                }
            });
        },
        remove(item) {
            this.chips.splice(this.chips.indexOf(item), 1)
            this.chips = [...this.chips]
        },
        roleUser(role, roleList) {
            const {admin, client, installer, moderator, manager} = roleList;
            if (role === admin || role === client || role === installer || role === manager || role === moderator) {
                return true;
            } else {
                return false;
            }
        },
        dataAdd() {
            if(this.isLoggedUser.managers) {
                this.editedItem['city_id'] = this.isLoggedUser.managers.city_id;
                this.editedItem['moderator_id'] = this.isLoggedUser.managers.moderator_id;
                this.editedItem['manager_id'] = this.isLoggedUser.managers.id;
            }
            if(this.isLoggedUser.moderators) {
                this.editedItem['city_id'] = this.isLoggedUser.moderators.city_id;
                this.editedItem['moderator_id'] = this.isLoggedUser.moderators.id;
            }
        },
    },
    mounted() {
        this.dataAdd();
    }
}
</script>