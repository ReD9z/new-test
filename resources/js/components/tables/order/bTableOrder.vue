<template>
<div>
    <v-toolbar color="#fff" fixed app clipped-righ>
        <v-toolbar-title>{{$route.meta.title}}</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn color="green" v-show="hideElem()" large class="mb-2 white--text" to="orders-create"><v-icon left>add</v-icon>Создать заказ</v-btn>
    </v-toolbar>
    <v-navigation-drawer v-model="dialog" right hide-overlay stateless fixed>
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
                            <v-text-field v-model="editedItem[param.value]" :rules="param.validate" :label="param.text" v-if="param.input !== 'images' && param.edit != true" xs12 required></v-text-field>
                        </div>
                        <div v-if="param.input == 'select'">
                            <div v-for="item in select" :key="item[0]">
                                <div v-if="item.url == param.selectApi">
                                    <v-autocomplete
                                        :items="item.data"
                                        v-model="editedItem[param.value]"
                                        :item-text="param.selectText"
                                        item-value="id"
                                        :label="param.text"
                                    >
                                    </v-autocomplete>
                                </div>
                            </div>
                        </div>
                        <div v-if="param.input == 'date'">
                            <v-menu
                                v-model="param.close"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                lazy
                                transition="scale-transition"
                                offset-y
                                full-width
                                max-width="290px"
                                min-width="290px"
                            >
                                <template v-slot:activator="{ on }">
                                    <v-text-field
                                        v-model="editedItem[param.value]"
                                        hint="DD-MM-YYYY формат"
                                        persistent-hint
                                        @blur="editedItem[param.value] = parseDate(picker)"
                                        prepend-icon="event"
                                        :label="param.text"
                                        v-on="on"
                                    ></v-text-field>
                                </template>
                                <v-date-picker :first-day-of-week="1" v-model="picker" :rules="param.validate" no-title :value="editedItem[param.value]" @input="param.close = false"></v-date-picker>
                            </v-menu>
                        </div>
                        <div v-if="param.input == 'password'">
                            <v-text-field :type="param.value" :rules="param.validate" v-model="editedItem[param.value]" :label="param.text" v-if="param.input !== 'images' && param.edit != true" xs12></v-text-field>
                        </div>
                        <div v-if="param.value == 'status'">
                            <v-combobox
                                v-model="editedItem[param.value]"
                                :items="param.status"
                                :label="param.text"
                                >
                            </v-combobox>
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
        <v-spacer></v-spacer>
        <div>
            <v-chip :items="chips" v-for="(item, key) in chips" :key="key" close @input="remove(item)">{{item}}</v-chip>
        </div>
        <v-icon v-show="hideElem()">filter_list</v-icon>
        <v-menu :close-on-content-click="false" :nudge-width="200" offset-y bottom left>
            <template v-slot:activator="{ on }">
                <v-btn icon v-on="on" v-show="hideElem()">
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
            <th v-for="header in props.headers"
                :key="header.text"
                :class="['column sortable', pagination.descending ? 'desc' : 'asc', header.value === pagination.sortBy ? 'active' : '' , 'text-xs-left', header.visibility]"
                @click="changeSort(header.value)"
            >
                {{ header.text }}<v-icon small>arrow_upward</v-icon>
            </th>
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
                <v-icon small class="mr-2" @click="redirectEdit(props.item.id)">
                    edit
                </v-icon>
                <v-icon v-show="hideElem()" small class="mr-2" @click="deleteItem(props.item)">
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
    data: () => ({
        search: '',
        dialog: false,
        dialogImages: false,
        listAddressOrder: false,
        loading: true,
        loadingExcel: false,
        files: [],
        idOrder: 0,
        deleteImage: false,
        desserts: [],
        editedIndex: -1,
        loadImages: false,
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
        cityUser: null,
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
        }
    },
    watch: {
        dialog (val) {
            val || this.close();
        },
        search: _.debounce(function () {
            this.initialize();
        }, 400),
        chips(val) {
            this.initialize();
        }
    },
    async created () {
        await this.initialize();
        await this.selectStatus();
        await this.getFiltered();
    },
    methods: {
        roleUserCity() {
            if(this.isLoggedUser.moderators) {
                return this.cityUser = this.isLoggedUser.moderators.city_id;
            }
            if(this.isLoggedUser.managers) {
                return this.cityUser = this.isLoggedUser.managers.city_id;
            }
            if(!this.isLoggedUser.moderators || !this.isLoggedUser.managers) {
                return this.cityUser = null;
            }
        },
        roleUserId() {
            if(this.isLoggedUser.clients) {
                return this.userId = this.isLoggedUser.clients.id;
            }
            if(!this.isLoggedUser.clients) {
                return this.userId = null;
            }
        },
        hideElem() {
            if(this.isLoggedUser.clients) {
                return false;
            }
            if(!this.isLoggedUser.clients) {
                return true;
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
        editAddress(data) {
            this.listAddressOrder = true;
            this.idOrder = data.id;
        },
        async getFiltered() {
            await this.params.filter.forEach((item) => {
                axios({
                    method: 'get',
                    url: item.api,
                    params: {
                        city: this.roleUserCity(),
                    }
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
            })
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
                    return searchTest.test([item.clients_name]);
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
                    city: this.roleUserCity(),
                    client: this.roleUserId()
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
        redirectEdit(id) {
            this.$router.push(`orders-address/${id}`);
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
                        // Обновлять при сохранении select с адресами
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
        removeImg(data) {
            this.loadImages = true;
            axios.post('api/files/remove', data)
            .then(
                res => {
                    this.editedItem.files.splice(this.editedItem.files.indexOf(data.id), 1);
                    this.loadImages = false;
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
        resetFilesLoad() {
            this.files = [];
            this.$refs.images.value = '';
            this.formData.delete('file[]');
        },
        save () {
            if (this.$refs.forms.validate() == false) {
                this.snackbar = true
            } 
            else {
                this.loaderSaveBtn = true;
                this.loadingSaveBtn = true;
                let method = null;
                if (this.editedIndex > -1) {
                    method = 'put';
                } else {
                    method = 'post';
                }
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
            }
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
    }
}
</script>