<template>
<div>
    <v-toolbar color="#fff" fixed app clipped-righ>
        <v-toolbar-title>{{$route.meta.title}}</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn
            color="#f2994a"
            class="white--text"
            large
            :loading="loadingExcel"
            :disabled="loadingExcel"
            @click='pickExcel'
            v-show="params.excel"
        >
            <v-icon left>vertical_align_bottom</v-icon>
            Добавить Excel
            <input
                type="file"
                style="display: none"
                ref="excel"
                accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                @change="elementLoadToFile"
                multiple
            >
        </v-btn>
        <v-btn color="green" large class="mb-2 white--text" @click.stop="dialog = !dialog"><v-icon left>add</v-icon>Добавить адрес</v-btn>
    </v-toolbar>
    <v-navigation-drawer v-model="dialog" right temporary fixed>
        <v-card>
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
                            <v-text-field :data-vv-name="param.value" :data-vv-as="'`'+param.text+'`'" :error-messages="errors.collect(param.value)" v-validate="param.validate" v-model="editedItem[param.value]" :label="param.text" v-if="param.input !== 'images' && param.edit != true" xs12 required></v-text-field>
                        </div>
                        <div v-if="param.input == 'select'">
                            <div v-for="item in select" :key="item[0]">
                                <div v-if="item.url == param.selectApi">
                                    <v-autocomplete
                                        :items="item.data"
                                        v-model="editedItem[param.value]"
                                        :item-text="param.selectText"
                                        :data-vv-as="'`'+param.text+'`'" 
                                        item-value="id"
                                        :label="param.text"
                                        :data-vv-name="param.value" 
                                        :error-messages="errors.collect(param.value)" 
                                        v-validate="param.validate"
                                        >
                                    </v-autocomplete>
                                </div>
                            </div>
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
            <v-text-field v-model="search" append-icon="search" label="Поиск" v-show="params.search" single-line hide-details>
            </v-text-field>
        </v-flex>
        <v-spacer></v-spacer>
        <div>
            <v-chip :items="chips" v-for="(item, key) in chips" :key="key" close @input="remove(item)">{{item}}</v-chip>
        </div>
        <v-icon v-if="!isLoggedUser.managers">filter_list</v-icon>
        <v-menu v-if="!isLoggedUser.managers" :close-on-content-click="false" :nudge-width="200" offset-y bottom left>
            <template v-slot:activator="{ on }">
                <v-btn icon v-on="on">
                    <v-icon>more_vert</v-icon>
                </v-btn>
            </template>
            <v-card height="200px">
                <v-toolbar color="indigo" dark>
                    <v-toolbar-title>Клиенты</v-toolbar-title>
                    <v-spacer></v-spacer>
                </v-toolbar>
                <v-layout row wrap>
                    <v-flex class="px-3" xs12>
                        <v-combobox
                            v-model="chips"
                            :items="chipsItem"
                            multiple
                            label="Клиенты"
                        ></v-combobox>
                    </v-flex>
                </v-layout>
            </v-card>
        </v-menu>
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
        loading: true,
        loadingExcel: false,
        files: [],
        deleteImage: false,
        desserts: [],
        editedIndex: -1,
        editedItem: {},
        defaultItem: {},
        select: [],
        loadingSaveBtn: false,
        loaderSaveBtn: null,
        formData: new FormData(),
        chips: [],
        chipsItem: [],
        picker: new Date().toISOString().substr(0, 10),
        valid: true,
        pagination: {
            sortBy: 'id'
        },
        selected: [],
        cityUser: null
    }),
    props: {
        params: Object
    },
    computed: {
        formTitle () {
            return this.editedIndex === -1 ? 'Добавление адреса' : 'Редактирование адреса'
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
        }, 400),
        chips(val) {
            this.initialize();
        }
    },
    created () {
        this.initialize();
        this.selectStatus();
        this.getFiltered();
    },
    methods: {
        roleUserCity() {
            return !this.isLoggedUser.managers ? this.cityUser = null : this.cityUser = this.isLoggedUser.managers.city_id
        },
        async getFiltered() {
            this.chipsItem = [];
            await this.params.filter.forEach((item) => {
                if(item.api) {
                    axios({
                        method: 'get',
                        url: item.api,
                    })
                    .then(
                        response => {
                            let data = response.data;
                            data.filter((items) => {
                                this.chipsItem.push(items[item.value]);
                            });
                        }
                    ).catch(error => {
                        console.log(error);
                    })
                } else {
                    item.data.filter((items) => {
                        this.chipsItem.push(items[item.value]);
                    });
                }
            });
        },
        filtered(data) {
            if(this.chips.length > 0) {
                let arr = [];
                this.chips.forEach((chip) => {
                    arr.push(chip);
                });
                var searchTerm = arr.join('||').trim().toLowerCase(),
                useOr = 'and' == "or",
                AND_RegEx = "(?=.*" + searchTerm.replace(/ +/g, ")(?=.*") + ")",
                OR_RegEx = searchTerm.replace(/ +/g,"|"),
                regExExpression = useOr ? OR_RegEx : AND_RegEx,
                searchTest = new RegExp(regExExpression, "ig");
           
                this.desserts = this.desserts.filter(function(item) {
                    return searchTest.test([item.city]);
                });
            }
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
            this.chips = [];
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
                    city: this.roleUserCity()
                }
            })
            .then(
                response => {
                    this.desserts = response.data;
                    this.filteredItems(this.desserts);
                    this.filtered(this.desserts);
                    this.loading = false;
                }
            ).catch(error => {
                console.log(error);
            })
        },
        pickImages () {
            this.$refs.images.click();
        },
        parseDate (date) {
            if (!date) return null
            const [year, month, day] = date.split('-')
            return `${month}-${day}-${year}`
        },
        selectStatus() {
            this.select = [];
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
        elementLoadToFileImage() {
            this.loadImages = true;
            this.files = this.$refs.images.files;
            Array.from(this.files).forEach(files => {
                this.formData.append('file[]', files);
            });
            axios.post('api/files', this.formData, {
                headers: {'Content-Type': 'multipart/form-data'}
            })
            .then(
                res => {
                    axios({
                        method: 'put',
                        url: this.params.baseUrl,
                        data: this.editedItem,
                        params: {
                            images: res.data.files
                        }
                    })
                    .then(
                        response => {
                            Object.assign(this.editedItem, response.data);
                            this.loadImages = false;
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
        async loadExecel(file) {
            let vm = this;
            await file.forEach(function (item) {
                axios({
                    method: 'post',
                    url: vm.params.baseUrl +'/excel',
                    data: item
                })
                .then(
                    response => {
                        let array = response.data.data;
                        if(array != undefined) {
                            vm.desserts.push(array);
                        }
                        setTimeout(() => (vm.loadingExcel = false), 1000);
                        vm.$refs.excel.value = '';
                    }
                ).catch(error => {
                    console.log(error);
                });
            })
            await this.getFiltered();
            await this.selectStatus();
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

                vm.loadExecel(excelRows);
                file.value = '';
            };
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
        resetFilesLoad() {
            this.files = [];
            this.$refs.images.value = '';
            this.formData.delete('file[]');
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
        save () {
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
                        method: method,
                        url: this.params.baseUrl,
                        data: this.editedItem
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
                        console.log(error);
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
        }
    },
    mounted() {
        if(this.isLoggedUser.managers) {
            this.editedItem['city_id'] = this.isLoggedUser.managers.city_id;
        }
    }
}
</script>