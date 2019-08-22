<template>
<div>
    <v-toolbar color="#fff" fixed app clipped-righ>
        <v-toolbar-title>{{$route.meta.title}}</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn color="green" large class="mb-2 white--text" @click.stop="dialog = !dialog"><v-icon left>add</v-icon>Создать</v-btn>
    </v-toolbar>
    <v-navigation-drawer v-model="dialog" right temporary fixed>
        <v-card height="100%">
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
                            <div v-if="!isLoggedUser.managers">
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
                        </div>
                        <div v-if="param.input == 'password'">
                            <v-text-field :type="param.value" v-model="editedItem[param.value]" :label="param.text" v-if="param.input !== 'images' && param.edit != true" xs12></v-text-field>
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
    <v-toolbar flat color="#fff">
        <v-flex xs12 sm6 md3>
            <v-text-field v-model="search" append-icon="search" label="Поиск" v-show="params.search" single-line hide-details></v-text-field>
        </v-flex>
    </v-toolbar>
    <v-data-table :rows-per-page-items='[25, 35, 45, {text: "Все", value: -1}]' :pagination.sync="pagination" item-key="name" :headers="params.headers" :items="desserts" :loading="loading" class="elevation-1">
        <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>
        <template v-slot:headers="props">
            <th
                v-for="header in props.headers"
                :key="header.text"
                :class="['column sortable', pagination.descending ? 'desc' : 'asc', header.value === pagination.sortBy ? 'active' : '' , 'text-xs-left', header.visibility]"
                @click="changeSort(header.value)"
            >{{ header.text }}<v-icon small>arrow_upward</v-icon></th>
            <th class="text-xs-left">
                Действия
            </th>
        </template>
        <template v-slot:items="props">
            <td v-for="(param, key) in params.headers" :key="key" :class="param.visibility">
                <v-flex v-if="param.input !== 'images'">
                    <v-flex v-if="param.selectText">{{props.item[param.TableGetIdName]}}</v-flex>
                    <v-flex v-else>{{props.item[param.value]}}</v-flex>
                </v-flex>
            </td>
            <td class="justify-left layout">
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
</div>
</template>
<script>
import XLSX from 'xlsx';
export default {
    inject: ['$validator'],
    data: () => ({
        search: '',
        dialog: false,
        dialogImages: false,
        loading: true,
        loadingExcel: false,
        files: [],
        desserts: [],
        editedIndex: -1,
        editedItem: {},
        defaultItem: {},
        select: [],
        loadingSaveBtn: false,
        loaderSaveBtn: null,
        formData: new FormData(),
        valid: true,
        pagination: {
            sortBy: 'id'
        },
        selected: [],
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
        initialize () {
            axios({
                method: 'get',
                url: this.params.baseUrl,
                params: {
                    city: !this.isLoggedUser.managers ? null : this.isLoggedUser.managers.city_id
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
        selectStatus() {
            this.params.headers.forEach(element => {
                if(element.selectApi != undefined) {
                    axios.get(element.selectApi)
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
        editPhotos(item) {
            this.editedIndex = this.desserts.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialogImages = true;
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
            this.dialogImages = false
            setTimeout(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
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
        }
    },
    mounted() {
        if(this.isLoggedUser.managers) {
            this.editedItem['city_id'] = this.isLoggedUser.managers.city_id;
        }
    }
}
</script>