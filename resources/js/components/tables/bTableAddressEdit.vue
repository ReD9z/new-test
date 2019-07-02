<template>
<div>
    <v-toolbar color="#fff" fixed app clipped-righ>
        <v-toolbar-title>Заказ №{{order.id}}</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn color="green" large class="mb-2 white--text" to="orders"><v-icon left>chevron_left</v-icon>К списку заказов</v-btn>
    </v-toolbar>
    <v-card>
        <v-card-text>
            <v-form ref="forms" v-model="valid" lazy-validation>
                <v-flex v-for="(param, key) in params.headerOrders" :key="key" xs12>
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
                    <div v-if="param.input == 'dateStart'">
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
                                    :rules="param.validate"
                                    hint="YYYY-MM-DD формат"
                                    persistent-hint
                                    prepend-icon="event"
                                    :label="param.text"
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker v-model="editedItem[param.value]" no-title @input="param.close = false"></v-date-picker>
                        </v-menu>
                    </div>
                    <div v-if="param.input == 'dateEnd'">
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
                                    hint="YYYY-MM-DD формат"
                                    persistent-hint
                                    prepend-icon="event"
                                    :label="param.text"
                                    :rules="param.validate"
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker v-model="editedItem[param.value]" no-title @input="param.close = false"></v-date-picker>
                        </v-menu>
                    </div>
                </v-flex>
            </v-form>
        </v-card-text>
    </v-card>
    <v-toolbar flat color="#fff">
        <v-flex xs12 sm6 md3>
            <v-text-field v-model="search" append-icon="search" label="Поиск" v-show="params.search" single-line hide-details>
            </v-text-field>
        </v-flex>
        <v-spacer></v-spacer>
        <v-icon>filter_list</v-icon>
        <div>
            <v-chip :items="chips" v-for="(item, key) in chips" :key="key" close @input="remove(item)">{{item}}</v-chip>
        </div>
        <v-menu :close-on-content-click="false" :nudge-width="200" offset-y bottom left>
            <template v-slot:activator="{on}">
                <v-btn icon v-on="on">
                    <v-icon>more_vert</v-icon>
                </v-btn>
            </template>
            <v-card>
                <v-divider></v-divider>
                <v-list>
                    <v-list-tile v-for="(item, key) in chipsItem" :key="key">
                        <v-list-tile-action>
                            <v-checkbox v-model="chips" :label="item" :value="item"></v-checkbox>
                        </v-list-tile-action>
                    </v-list-tile>
                </v-list>
            </v-card>
        </v-menu>
    </v-toolbar>
    <v-data-table v-model="selected" select-all :headers="params.headers" :items="desserts" :search="search" :loading="loading" class="elevation-1">
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
                    <v-flex v-else>{{props.item[param.value]}}</v-flex>
                </v-flex>
            </td>
        </template>
        <template v-slot:no-data>
            <v-btn color="primary" @click="initialize">Сброс</v-btn>
        </template>
        <template v-slot:no-results>
            <v-alert :value="true" color="error" icon="warning">
                По запросу "{{ search }}" ничего не найдено.
            </v-alert>
        </template>
        <template v-if="selectedStatus" v-slot:footer>
            <td :colspan="params.headers.length">
                <div class="v-messages theme--light error--text">
                    <div class="v-messages__wrapper">
                        <div class="v-messages__message" style="">Выберите адрес!</div>
                    </div>
                </div>
            </td>
        </template>
    </v-data-table>
    <v-flex class="text-xs-center" mt-4>
        <v-btn color="info" large @click="save" :loading="loadingSaveBtn" :disabled="loadingSaveBtn">
            Сохранить
            <template v-slot:loaderSaveBtn>
                <span class="custom-loader">
                <v-icon light>cached</v-icon>
                </span>
            </template>
        </v-btn>
    </v-flex>
</div>
</template>
<script>
import XLSX from 'xlsx';
export default {
    data: () => ({
        search: '',
        loading: true,
        desserts: [],
        editedIndex: -1,
        editedItem: {},
        defaultItem: {},
        select: [],
        keywords: '',
        dateStart: null,
        dateEnd: null,
        loadingSaveBtn: false,
        loaderSaveBtn: null,
        selectedStatus: false,
        formData: new FormData(),
        chips: [],
        chipsItem: ['Фильтер1', 'Фильтер2'],
        valid: true,
        order: [],
        selected: []
    }),
    props: {
        params: Object,
        idRouteOrder: String
    },
    computed: {
        formTitle () {
            return this.editedIndex === -1 ? 'Добавить' : 'Редактировать'
        },
        computedDateFormatted () {
            return this.formatDate(this.date)
        }
    },
    watch: {
        dialog (val) {
            val || this.close()
        },
        dateStart(after, before) {
            this.initialize();
            this.selected = [];
        },
        dateEnd(after, before) {
            this.initialize();
            this.selected = [];
        }
    },
    created () {
        this.initialize();
        this.selectStatus();
        this.initializeOrder();
    },
    methods: {
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
        initialize() {
            axios({
                method: 'get',
                url: this.params.baseUrl
            })
            .then(
                response => {
                    this.desserts = response.data;
                    let vm = this;
                    this.desserts.map(function (item) {
                        if(item.status) {
                            let test = item.status.map(function (stats) {
                                let itemDateStart = vm.$moment(stats.orders.order_start_date, 'YYYY-MM-DD').unix() * 1000;
                                let itemDateEnd = vm.$moment(stats.orders.order_end_date, 'YYYY-MM-DD').unix() * 1000;

                                let dateStart = vm.$moment(vm.dateStart, 'YYYY-MM-DD').unix() * 1000;
                                let dateEnd = vm.$moment(vm.dateEnd, 'YYYY-MM-DD').unix() * 1000;
    
                                if(dateStart >= itemDateStart && dateEnd <= itemDateEnd) {
                                    item.result = 'Занято';
                                    item.data = stats;
                                    let index = vm.desserts.indexOf(item);
                                    Object.assign(vm.desserts[index], item);
                                } 
                            });
                        } else {
                            item.result = 'Свободно';
                            item.data = null;
                            let index = vm.desserts.indexOf(item);
                            Object.assign(vm.desserts[index], item);
                        }
                    });
                    this.loading = false;
                }
            ).catch(error => {
                console.log(error);
            })
        },
        initializeOrder() {
            axios({
                method: 'get',
                url: this.params.baseOrders+"/"+ this.idRouteOrder
            })
            .then(
                response => {
                    this.order = response.data;
                    this.editItem(response.data);
                    console.log(this);
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
            this.editedItem = Object.assign({}, item);
            // this.dialog = true;
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
        },
        save () {
            if (this.$refs.forms.validate() == false) {
                this.snackbar = true;
            } 
            if(this.selected.length <= 0) {
                this.selectedStatus = true;
            }     
            if(this.$refs.forms.validate() == true && this.selected.length > 0) {
                this.selectedStatus = false;
                this.loaderSaveBtn = true;
                this.loadingSaveBtn = true;
                axios({
                    method: 'post',
                        url: this.params.baseOrders,
                        data: {
                            client: this.editedItem,
                            address: this.selected.filter(item => item.result !== 'Занято'),
                            dateStart: this.dateStart,
                            dateEnd: this.dateEnd
                        }
                    })
                    .then(response => {
                        this.initialize();
                        this.loaderSaveBtn=null;
                        this.loadingSaveBtn=false;
                    }).catch(error => {
                        console.log(error);
                });
            }
        },
        remove(item) {
            this.chips.splice(this.chips.indexOf(item), 1)
            this.chips = [...this.chips]
        }
    }
}
</script>