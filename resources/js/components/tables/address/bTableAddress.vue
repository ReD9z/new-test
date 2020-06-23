<template>
<div>
    <v-toolbar color="#fff" fixed app clipped-righ>
        <v-toolbar-title>{{$route.meta.title}}</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn
            v-show="desserts.length > 0 && params.excel"
            color="#f2994a"
            class="white--text"
            large
            :loading="excelDownload"
            :disabled="excelDownload"
            @click='downloadExcel'
        >
            <v-icon left>vertical_align_bottom</v-icon>
            Скачать в Excel
        </v-btn>
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
                accept=".xls, .xlsx, .ods"
                @change="elementLoadToFile"
                multiple
            >
        </v-btn>
        <v-btn color="green" large class="mb-2 white--text" @click.stop="dialog = !dialog"><v-icon left>add</v-icon>Добавить адрес</v-btn>
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
                        <div v-if="param.input == 'text' && param.value != 'result'">
                            <v-text-field 
                                :data-vv-name="param.value" 
                                :data-vv-as="'`'+param.text+'`'" 
                                :error-messages="errors.collect(param.value)" 
                                v-validate="param.validate" 
                                v-model="editedItem[param.value]" 
                                :label="param.text" 
                                v-if="param.input !== 'images' && param.edit != true" 
                                xs12 
                                required
                            >
                            </v-text-field>
                        </div>
                        <div v-if="param.input == 'select'">
                            <v-autocomplete
                                v-if="param.value == 'city_id'"
                                :items="selectCity"
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
                            <v-autocomplete
                                v-if="param.value == 'area_id'"
                                :items="selectArea"
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
            <v-chip :items="chips" v-for="(item, key) in chips" :key="key" close @input="remove(item)">{{item['name']}}</v-chip>
        </div>
        <v-icon v-if="showRoles()">filter_list</v-icon>
        <v-menu v-if="showRoles()" :close-on-content-click="false" :nudge-width="200" offset-y bottom left>
            <template v-slot:activator="{ on }">
                <v-btn icon v-on="on">
                    <v-icon>more_vert</v-icon>
                </v-btn>
            </template>
            <v-card height="200px">
                <v-toolbar color="indigo" dark>
                    <v-toolbar-title>Города</v-toolbar-title>
                    <v-spacer></v-spacer>
                </v-toolbar>
                <v-layout row wrap>
                    <v-flex class="px-3" xs12>
                        <v-combobox
                            v-model="chips"
                            :items="selectCity"
                            item-text="name"
                            multiple
                            label="Города"
                        ></v-combobox>
                    </v-flex>
                </v-layout>
            </v-card>
        </v-menu>
    </v-toolbar>
        <v-data-table :rows-per-page-items='[25, 35, 45, {text: "Все", value: -1}]' :headers="params.headers" :items="desserts" :loading="loading" class="elevation-1">
        <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>
        <template v-slot:headers="props">
            <th
                v-for="header in props.headers"
                :key="header.text"
                :class="['column sortable', pagination.descending ? 'asc' : 'desc', header.value === pagination.sortBy ? 'active' : '' , 'text-xs-left', header.visibility]"
                @click="changeSort(header.value)"
            >{{ header.text }}<v-icon small>arrow_upward</v-icon></th>
            <th class="text-xs-left">
                Действия
            </th>
        </template>
        <template v-slot:items="props">
            <td v-for="(param, key) in params.headers" :key="key" :class="param.visibility">
                <v-flex v-if="param.input !== 'images' && param.value !== 'params'">
                    <v-flex v-if="param.selectText">{{props.item[param.TableGetIdName]}}</v-flex>
                    <v-flex v-else>
                        <span v-if="param.value == 'result' && props.item[param.value] === 'Занят'" class="red--text font-weight-bold">
                            {{props.item[param.value]}}
                        </span>
                        <span v-if="param.value == 'result' && props.item[param.value] === 'Свободен'" class="green--text font-weight-bold">
                            {{props.item[param.value]}}
                        </span>
                        <span v-if="param.value != 'result'">
                            {{props.item[param.value]}}
                        </span>
                    </v-flex>
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
    data: (vm) => ({
        search: '',
        dialog: false,
        loading: true,
        loadingExcel: false,
        excelDownload: false,
        files: [],
        deleteImage: false,
        desserts: [],
        editedIndex: -1,
        editedItem: {},
        defaultItem: {},
        selectCity: [],
        selectArea: [],
        areaArray: [],
        loadingSaveBtn: false,
        loaderSaveBtn: null,
        dateToday: vm.formatDate(new Date().toISOString().substr(0, 10)),
        formData: new FormData(),
        chips: [],
        picker: new Date().toISOString().substr(0, 10),
        valid: true,
        pagination: {
            sortBy: 'id'
        },
        selected: [],
        cityUser: null,
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
            val || this.close();
        },
        search: _.debounce(function () {
            this.initialize();
        }, 400),
        chips(val) {
            this.initialize();
        },
        'editedItem.city_id'(val) {
            this.selectArea = [];
            this.selectArea = this.areaArray.filter((item) => {return item.city_id == val});
        }
    },
    created() {
        this.initialize();
    },
    methods: {
        formatDate (date) {
            if (!date) return null
            const [year, month, day] = date.split('-')
            return `${day}.${month}.${year}`
        },
        roleUserCity() {
            if(this.isLoggedUser.moderators) {
                this.cityUser = this.isLoggedUser.moderators.addresses
                return this.cityUser;
            }
            if(this.isLoggedUser.managers) {
                let arr = [];
                arr.push({'city_id': this.isLoggedUser.managers.cities.id});
                return this.cityUser = arr;
            }
            if(!this.isLoggedUser.moderators || !this.isLoggedUser.managers) {
                return this.cityUser = null;
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
        filtered(data) {
            if(this.chips.length > 0) {
                let arr = [];
                this.chips.forEach((chip) => {
                    arr.push(chip['name']);
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
        initialize() {
            this.loading = true;
            axios({
                method: 'get',
                url: this.params.baseUrl,
                params: {
                    city: JSON.stringify(this.roleUserCity())
                }
            })
            .then(
                response => {
                    this.desserts = response.data.address;
                    this.selectCity = response.data.city;
                    this.areaArray = response.data.area;
                 
                    if(this.isLoggedUser.managers) {
                        this.selectArea = [];
                        this.editedItem.city_id = this.isLoggedUser.managers.city_id;
                        this.selectArea = this.areaArray.filter((item) => {return item.city_id == this.editedItem.city_id});
                        this.selectCity = this.selectCity.filter((item) => {return item.id == this.isLoggedUser.managers.city_id});
                    }
                    
                    this.filteredItems(this.desserts);
                    this.filtered(this.desserts);
 
                    this.loading = false;
                }
            ).catch(error => {
                console.log(error);
            })
        },
        downloadExcel() {
            this.excelDownload = true;
            if(this.desserts.length > 0) {
                let vm = this;
                let map = this.desserts.map((item)=> {
                    let date = null;
                    item.status.forEach(stats => {
                        let itemDateStart = vm.$moment(stats.orders.order_start_date).unix() * 1000;
                        let itemDateEnd = vm.$moment(stats.orders.order_end_date).unix() * 1000;
                        let dateToday = vm.$moment(vm.dateToday, 'DD-MM-YYYY').unix() * 1000;
                        if(dateToday >= itemDateStart && dateToday <= itemDateEnd) {
                            date = vm.$moment(stats.orders.order_end_date).format('DD.MM.YYYY');
                        } 
                    });
                    return {
                        "Город": item.city,
                        "Район": item.area,
                        "Улица": item.street,
                        "Номер дома": item.house_number,
                        "Количество подъездов": item.number_entrances,
                        "Управляющая компания": item.management_company,
                        "Статус": item.result != "Занят" ? item.result : item.result + " до " + date
                    }
                });

                let ws = XLSX.utils.json_to_sheet(map, {raw:true});
                if(!ws['!cols']) ws['!cols'] = [];
                for (let i = 0; i < 7; i++) {
                    ws['!cols'][i] = { wch: 28 };
                }
                let wb = XLSX.utils.book_new();
              
                XLSX.utils.book_append_sheet(wb, ws, "Адреса");
                XLSX.writeFile(wb, "Адреса.xlsx");
                this.excelDownload = false;
            }
        },
        parseDate (date) {
            if (!date) return null
            const [year, month, day] = date.split('-')
            return `${day}-${month}-${year}`
        },
        async loadExcel(file) {
            let files = file;
            this.formData.append('file', files);
            await axios.post(this.params.baseUrl +'/excel', this.formData, {
                headers: {'Content-Type': 'multipart/form-data'}
            })
            .then(
                response => {
                    this.$refs.excel.value = '';
                    this.loadingExcel = false;
                    this.initialize();
                }
            ).catch(error => {
                console.log(error);
            });
        },
        elementLoadToFile() {
            this.loadingExcel = true;
            let file = this.$refs.excel.files[0];
            this.loadExcel(file);
        },
        pickExcel() {
            this.$refs.excel.click();
        },
        editItem(item) {
            this.editedIndex = this.desserts.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialog = true;
        },
        deleteItem(item) {
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
            let vm = this;
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
                        this.loaderSaveBtn = null;
                        this.loadingSaveBtn = false;
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
    }
}
</script>