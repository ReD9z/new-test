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
            :loading="loadingExcel"
            :disabled="loadingExcel"
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
                accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
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
            <v-chip :items="chips" v-for="(item, key) in chips" :key="key" close @input="remove(item)">{{item}}</v-chip>
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
                            :items="chipsItem"
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
                :class="['column sortable', pagination.descending ? 'desc' : 'asc', header.value === pagination.sortBy ? 'active' : '' , 'text-xs-left', header.visibility]"
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
        <template v-slot:no-results>
            <v-alert :value="true" color="error" icon="warning">
                По запросу "{{ search }}" ничего не найдено.
            </v-alert>
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
        files: [],
        deleteImage: false,
        desserts: [],
        editedIndex: -1,
        geo: [],
        editedItem: {},
        defaultItem: {},
        selectCity: [],
        selectArea: [],
        loadingSaveBtn: false,
        loaderSaveBtn: null,
        dateToday: vm.formatDate(new Date().toISOString().substr(0, 10)),
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
        testNew: [],
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
            this.selectStatus();
        },
        geo(newItem, oldItem) {
            if(newItem.length > 0) {
                this.testNew = newItem[0] + ", " + newItem[1];
            }
            if(oldItem.length > 0) {
                this.testNew = oldItem[0] + ", " + oldItem[1];
            }
        }
    },
    created () {
        this.initialize();
        this.selectStatus();
        this.getFiltered();

        let scriptYandexMap = document.createElement('script');
        scriptYandexMap.setAttribute('src', 'https://api-maps.yandex.ru/2.1/?apikey=4fa86d95-9009-491e-9dbc-762df5da5650&lang=ru_RU');
        document.head.appendChild(scriptYandexMap);
    },
    methods: {
        formatDate (date) {
            if (!date) return null

            const [year, month, day] = date.split('-')
            return `${day}.${month}.${year}`
        },
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
        showRoles() {
            if(this.isLoggedUser.moderators || this.isLoggedUser.managers) {
                return false;
            }
            if(!this.isLoggedUser.moderators || !this.isLoggedUser.managers) {
                return true;
            }
        },
        async getFiltered() {
            this.chipsItem = [];
            await this.params.filter.forEach((item) => {
                if(item.api) {
                    axios({
                        method: 'get',
                        url: item.api
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
                    let vm = this;
                    if(!this.dateToday) {
                        this.initialize();
                    } else {
                        this.desserts.forEach(function (item) {
                            if(item.status.length > 0) {
                                return item.status.filter(function (stats) {
                                    let itemDateStart = vm.$moment(stats.orders.order_start_date).unix() * 1000;
                                    let itemDateEnd = vm.$moment(stats.orders.order_end_date).unix() * 1000;
                                    let dateToday = vm.$moment(vm.dateToday, 'DD-MM-YYYY').unix() * 1000;
                                    if(dateToday >= itemDateStart && dateToday <= itemDateEnd) {
                                        item.result = 'Занят';
                                        item.data = stats;
                                        item.files = stats.files;
                                        let index = vm.desserts.indexOf(item);
                                        Object.assign(vm.desserts[index], item);
                                    } 
                                });
                            } 
                        });
                        this.filteredItems(this.desserts);
                        this.filtered(this.desserts);
                        this.loading = false;
                    }
                }
            ).catch(error => {
                console.log(error);
            })
        },
        downloadExcel() {
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
            }
        },
        pickImages () {
            this.$refs.images.click();
        },
        parseDate (date) {
            if (!date) return null
            const [year, month, day] = date.split('-')
            return `${day}-${month}-${year}`
        },
        selectStatus() {
            this.params.headers.forEach(element => {
                if(element.selectApi != undefined) {
                    axios({
                        method: 'get',
                        url: element.selectApi,
                        params: {
                            city: this.roleUserCity(),
                            areaCity: this.editedItem.city_id,
                        }
                    })
                    .then(
                        res => {
                            if(res) {
                                if (element.value == 'city_id') {
                                    this.selectCity = res.data;
                                }

                                if (element.value == 'area_id') {
                                    this.selectArea = res.data;
                                }
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
        dataAdd() {
            if(this.isLoggedUser.managers) {
                this.editedItem['city_id'] = this.isLoggedUser.managers.city_id;
            }
            if(this.isLoggedUser.moderators) {
                this.editedItem['city_id'] = this.isLoggedUser.moderators.city_id;
            }
        },
        close () {
            this.dialog = false
            this.dialogImages = false
            setTimeout(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.dataAdd();
                this.editedIndex = -1
            }, 300)
            this.$validator.reset()
        },
        async save () {
            let vm = this;
            this.loaderSaveBtn = true;
            this.loadingSaveBtn = true;
            await this.selectCity.filter(res => 
                res.id == this.editedItem.city_id ? this.editedItem.city = res.name : null
            );
            var geocoder = ymaps.geocode(this.editedItem.city + ", " + this.editedItem.street + ", " + this.editedItem.house_number);

            await geocoder.then(res => {
                let geo = res.geoObjects.get(0).geometry.getCoordinates();
                vm.editedItem.coordinates = geo[0] + ", " + geo[1];   
            });
            
            await this.$validator.validateAll().then(() => {
                if(this.$validator.errors.items.length == 0) {
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
        this.dataAdd();
    }
}
</script>