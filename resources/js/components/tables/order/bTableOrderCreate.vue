<template>
<div>
    <v-toolbar color="#fff" fixed app clipped-righ>
        <v-toolbar-title>{{$route.meta.title}}</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn color="info" large @click="save" :loading="loadingSaveBtn" :disabled="loadingSaveBtn">
            Сохранить
            <template v-slot:loaderSaveBtn>
                <span class="custom-loader">
                <v-icon light>cached</v-icon>
                </span>
            </template>
        </v-btn>
        <v-btn color="green" large class="mb-2 white--text" to="orders"><v-icon left>chevron_left</v-icon>К списку заказов</v-btn>
    </v-toolbar>
    <v-card>
        <v-card-text>
            <v-form ref="forms" v-model="valid" lazy-validation>
                <v-layout row wrap>
                    <v-flex v-for="(param, key) in params.headerOrders" :key="`Y-${key}`" xs12>
                        <div v-if="param.input == 'text'" xs12>
                            <v-text-field v-validate="param.validate" v-model="editedItem[param.value]" :label="param.text" v-if="param.input !== 'images' && param.edit != true" xs12 required></v-text-field>
                        </div>
                        <div v-if="param.input == 'select'" xs12>
                            <div v-for="item in select" :key="item[0]">
                                <div v-if="item.url == param.selectApi">
                                    <v-autocomplete
                                        :items="item.data"
                                        v-model="editedItem[param.value]"
                                        :item-text="param.selectText"
                                        :data-vv-as="'`'+param.text+'`'" 
                                        :data-vv-name="param.value" 
                                        :error-messages="errors.collect(param.value)" 
                                        v-validate="param.validate"
                                        item-value="id"
                                        :label="param.text"
                                        >
                                    </v-autocomplete>
                                </div>
                            </div>
                        </div>
                    </v-flex>
                    <v-flex v-for="(param, key) in params.headerOrders" v-if="param.input == 'dateStart'" :key="`C-${key}`" xs12 lg6>
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
                                    v-model="dateStartFormatted"
                                    hint="Формат дд.мм.гггг"
                                    persistent-hint
                                    prepend-icon="event"
                                    :label="param.text"
                                    :data-vv-as="'`'+param.text+'`'" 
                                    :data-vv-name="param.value" 
                                    :error-messages="errors.collect(param.value)"
                                    v-validate="param.validate" 
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker  locale="ru" :first-day-of-week="1" v-model="dateStart" no-title @input="param.close = false"></v-date-picker>
                        </v-menu>
                    </v-flex>
                    <v-flex v-for="(param, key) in params.headerOrders" v-if="param.input == 'dateEnd'" :key="`B-${key}`" xs12 lg6>
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
                                    v-model="dateEndFormatted"
                                    hint="Формат дд.мм.гггг"
                                    persistent-hint
                                    prepend-icon="event"
                                    :label="param.text"
                                    v-on="on"
                                    :data-vv-as="'`'+param.text+'`'" 
                                    :data-vv-name="param.value" 
                                    :error-messages="errors.collect(param.value)" 
                                    v-validate="param.validate"
                                ></v-text-field>
                            </template>
                            <v-date-picker  locale="ru" :first-day-of-week="1" v-model="dateEnd" no-title @input="param.close = false"></v-date-picker>
                        </v-menu>
                    </v-flex>
                </v-layout>
            </v-form>
        </v-card-text>
    </v-card>
    <v-toolbar flat color="#fff">
        <v-flex xs12 sm6 md3>
            <v-text-field v-model="search" append-icon="search" label="Поиск" v-show="params.search" single-line hide-details></v-text-field>
        </v-flex>
        <v-spacer></v-spacer>
        <v-icon>filter_list</v-icon>
        <div>
            <v-chip :items="chips" v-for="(item, key) in chips" :key="key" close @input="remove(item)">{{item.data}}</v-chip>
        </div>
        <v-menu :close-on-content-click="false" :nudge-width="200" offset-y bottom left>
            <template v-slot:activator="{on}">
                <v-btn icon v-on="on">
                    <v-icon>more_vert</v-icon>
                </v-btn>
            </template>
            <v-card>
                <v-divider></v-divider>
                <v-list v-for="items in params.filter" :key="items.value">
                    <v-subheader>
                        {{items.title}}
                    </v-subheader>
                    <div v-for="(item, key) in chipsItem" :key="key">
                        <v-list-tile v-if="item.api && items.api == item.api">
                            <v-list-tile-action>
                                <v-checkbox v-model="chips" :label="item.data" :value="item"></v-checkbox>
                            </v-list-tile-action>
                        </v-list-tile>
                        <v-list-tile v-if="item.text && items.text == item.text">
                            <v-list-tile-action>
                                <v-checkbox v-model="chips" :label="item.data" :value="item"></v-checkbox>
                            </v-list-tile-action>
                        </v-list-tile>
                      </div>
                </v-list>
            </v-card>
        </v-menu>
    </v-toolbar>
    <v-data-table :rows-per-page-items='[25, 35, 45, {text: "Все", value: -1}]' v-model="selected" select-all :headers="params.headers" :items="desserts" :loading="loading" class="elevation-1">
        <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>
        <template v-slot:items="props">
            <td>
                <v-checkbox
                    v-show="props.item.result !== 'Занято'"
                    v-model="props.selected"
                    primary
                    hide-details
                >
                </v-checkbox>
            </td>
            <td v-for="(param, key) in params.headers" :key="key" :class="param.visibility">
                <v-flex v-if="param.input !== 'images' && param.value !== 'params'">
                    <v-flex v-if="param.selectText">{{props.item[param.TableGetIdName]}}</v-flex>
                    <v-flex v-else>
                        <span v-if="param.value == 'result' && props.item[param.value] === 'Занято'" style="font-weight: bold; color:red">
                            {{props.item[param.value]}}
                        </span>
                        <span v-if="param.value == 'result' && props.item[param.value] === 'Свободен'" style="font-weight: bold; color:green">
                            {{props.item[param.value]}}
                        </span>
                        <span v-if="param.value != 'result'">
                            {{props.item[param.value]}}
                        </span>
                    </v-flex>
                </v-flex>
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
    <!-- <v-flex class="text-xs-center" mt-4>
        <v-btn color="info" large @click="save" :loading="loadingSaveBtn" :disabled="loadingSaveBtn">
            Сохранить
            <template v-slot:loaderSaveBtn>
                <span class="custom-loader">
                <v-icon light>cached</v-icon>
                </span>
            </template>
        </v-btn>
    </v-flex> -->
</div>
</template>
<script>
import XLSX from 'xlsx';

export default {
    inject: ['$validator'],
    data: vm => ({
        search: '',
        loading: true,
        desserts: [],
        editedIndex: -1,
        editedItem: {},
        dateStartFormatted: vm.formatDate(vm.getDate()),
        dateEndFormatted: vm.formatDate(new Date().toISOString().substr(0, 10)),
        defaultItem: {},
        select: [],
        keywords: '',
        dateStart: vm.getDate(),
        dateEnd: new Date().toISOString().substr(0, 10),
        loadingSaveBtn: false,
        loaderSaveBtn: null,
        selectedStatus: false,
        formData: new FormData(),
        chips: [],
        chipsItem: [],
        valid: true,
        order: [],
        selected: []
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
        },
        dateStart(val) {
            this.dateStartFormatted = this.formatDate(this.dateStart);
            this.initialize();
            this.selected = [];
        },
        dateEnd(val) {
            this.dateEndFormatted = this.formatDate(this.dateEnd);
            this.initialize();
            this.selected = [];
        },
        search : _.debounce(function () {
            this.initialize()
        }, 300),
        chips(val) {
            this.initialize();
        }
    },
    created () {
        this.initialize();
        this.selectStatus();
        this.initializeOrder();
        this.getFiltered();
    },
    methods: {
        getDate() {
            let next_week_start = this.$moment().isoWeekday(1);
            return next_week_start.format("YYYY-MM-DD");
        },
        formatDate (date) {
            if (!date) return null

            const [year, month, day] = date.split('-')
            return `${day}-${month}-${year}`
        },
        toggleAll () {
            if (this.selected.length) this.selected = []
            else this.selected = this.desserts.slice();
        },
        changeSort (column) {
            if (this.pagination.sortBy === column) {
                this.pagination.descending = !this.pagination.descending
            } else {
                this.pagination.sortBy = column
                this.pagination.descending = false
            }
        },
        async getFiltered() {
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
                                this.chipsItem.push({
                                    data: items[item.value],
                                    api: item.api,
                                    input: item.input
                                });
                            });
                        }
                    ).catch(error => {
                        console.log(error);
                    })
                } else {
                    item.data.filter((items) => {
                        this.chipsItem.push({
                            data: items[item.value],
                            text: item.text,
                            input: item.input
                        });
                    });
                }
            });
        },
        filtered(data) {
            if(this.chips.length > 0) {
                let vm = this;
                let array = [];
                this.chips.forEach((chip) => {
                    vm.desserts.filter(function(item) {
                        if(chip.data === item[chip.input]) {
                            array.push(item);
                        }
                    });
                });
                return this.desserts = array;
            } else {
                return this.desserts = data;
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
        initialize() {
            axios({
                method: 'get',
                url: this.params.baseUrl,
                params: {
                    user: this.params.user
                }
            })
            .then(
                response => {
                    this.desserts = response.data;
                    let vm = this;
                    this.desserts.map(function (item) {
                        if(item.status) {
                            let test = item.status.map(function (stats) {
                                let itemDateStart = vm.$moment(stats.orders.order_start_date, 'DD-MM-YYYY').unix() * 1000;
                                let itemDateEnd = vm.$moment(stats.orders.order_end_date, 'DD-MM-YYYY').unix() * 1000;

                                let dateStart = vm.$moment(vm.dateStart, 'DD-MM-YYYY').unix() * 1000;
                                let dateEnd = vm.$moment(vm.dateEnd, 'DD-MM-YYYY').unix() * 1000;

                                if(dateStart >= itemDateStart && dateEnd <= itemDateEnd || dateStart <= itemDateStart && dateEnd >= itemDateEnd) {
                                    item.result = 'Занято';
                                    item.data = stats;
                                    item.files = stats.files;
                                    let index = vm.desserts.indexOf(item);
                                    Object.assign(vm.desserts[index], item);
                                } 
                            });
                        } else {
                            item.result = 'Свободно';
                            item.data = null;
                            item.files = null;
                            let index = vm.desserts.indexOf(item);
                            Object.assign(vm.desserts[index], item);
                        }
                    });
                    this.filteredItems(response.data);
                    this.filtered(this.desserts);
                    this.loading = false;
                }
            ).catch(error => {
                console.log(error);
            })
        },
        initializeOrder() {
            axios({
                method: 'get',
                url: this.params.baseOrders
            })
            .then(
                response => {
                    this.order = response.data;
                    this.loading = false;
                }
            ).catch(error => {
                console.log(error);
            })
        },
        parseDate (date) {
            if (!date) return null
            const [year, month, day] = date.split('-')
            return `${month}-${day}-${year}`
        },
        selectStatus() {
            this.params.headerOrders.forEach(element => {
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
        deleteItem (item) {
            if (confirm('Вы уверены, что хотите удалить этот элемент?')) {
                axios({
                    method: 'delete',
                    url: this.params.baseOrders+'/'+item.data.orders.id,
                })
                .then(
                    response => {
                        this.initialize();
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
        save () {
            this.$validator.validateAll().then(() => {
                if(this.$validator.errors.items.length == 0) {
                    this.loaderSaveBtn = true;
                    this.loadingSaveBtn = true;
                  
                    axios({
                        method: 'post',
                        url: this.params.baseOrders,
                        data: {
                            order: this.editedItem,
                            address: this.selected.filter(item => item.result !== 'Занято'),
                            dateStart: this.dateStart,
                            dateEnd: this.dateEnd
                        }
                    })
                    .then(
                        response => {
                            this.$router.push(`orders-address/${response.data.id}`);
                        }
                    ).catch(error => {
                        console.log(error);
                    })  
                } else {
                    this.snackbar = true
                }
            });  
        },
        refreshSearch() {
            this.chips = [];
            this.loading = true;
            this.search = '';
            this.initialize();
        },
        remove(item) {
            this.chips.splice(this.chips.indexOf(item), 1)
            this.chips = [...this.chips]
        }
    },
    mounted() {
   
    }
}
</script>