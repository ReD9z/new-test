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
            <v-btn color="green" v-show="hideElem()" large class="mb-2 white--text" @click.stop="dialog = !dialog"><v-icon left>add</v-icon>Создать задачу</v-btn>
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
                            <div v-if="param.input == 'text'" v-show="roleUser(param.role, {admin: 'admin', moderator: 'moderator'})">
                                <v-text-field 
                                    :data-vv-as="'`'+param.text+'`'" 
                                    :data-vv-name="param.value" 
                                    :error-messages="errors.collect(param.value)" 
                                    v-validate="param.validate" 
                                    v-model="editedItem[param.value]" 
                                    :label="param.text" 
                                    v-if="param.edit != true" 
                                    xs12 
                                    required>
                                </v-text-field>
                            </div>
                            <div v-if="param.input == 'select'">
                                <v-autocomplete
                                    :items="formFilds[param.tableValue]"
                                    v-model="editedItem[param.value]"
                                    :item-text="param.childField"
                                    v-show="roleUser(param.role, {admin: 'admin', moderator: 'moderator'})"
                                    item-value="id"
                                    :label="param.text"
                                    :data-vv-as="'`'+param.text+'`'" 
                                    :data-vv-name="param.value" 
                                    :error-messages="errors.collect(param.value)" 
                                    v-validate="param.validate"
                                >
                                    <template v-if="param.value == 'orders_id'" v-slot:append-outer>
                                        <v-menu
                                            v-model="orderDate"
                                            :close-on-content-click="false"
                                            offset-x
                                        >
                                            <template v-slot:activator="{ on }">
                                                <v-btn v-show="!hideOrdersSelectDate()" flat icon v-on="on">
                                                    <v-icon>date_range</v-icon>
                                                </v-btn>
                                                <v-btn v-show="hideOrdersSelectDate()"  @click="resetDate()" flat icon>
                                                    <v-icon>close</v-icon>
                                                </v-btn>
                                            </template>
                                            <v-card>
                                                <v-layout pa-4 row wrap>
                                                    <v-flex xs12 lg5>
                                                        <v-menu
                                                            v-model="dateStartShow"
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
                                                                    label="Начало"
                                                                    v-on="on"
                                                                ></v-text-field>
                                                            </template>
                                                            <v-date-picker locale="ru" :first-day-of-week="1" v-model="dateStart" no-title @input="dateStartShow = false"></v-date-picker>
                                                        </v-menu>
                                                    </v-flex>
                                                    <v-flex xs12 lg5>
                                                        <v-menu
                                                            v-model="dateEndShow"
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
                                                                    label="Конец"
                                                                    v-on="on"
                                                                ></v-text-field>
                                                            </template>
                                                            <v-date-picker locale="ru" :first-day-of-week="1" v-model="dateEnd" no-title @input="dateEndShow = false"></v-date-picker>
                                                        </v-menu>
                                                    </v-flex>
                                                    <v-flex xs12 lg2>
                                                        <v-btn flat icon @click="resetDate()">
                                                            <v-icon>close</v-icon>
                                                        </v-btn>                
                                                    </v-flex>
                                                </v-layout>
                                                <v-card-actions>
                                                    <v-spacer></v-spacer>
                                                    <v-btn flat @click="orderDate = false">Закрыть</v-btn>
                                                    <v-btn color="primary" flat @click="orderFilter()">Применить</v-btn>
                                                </v-card-actions>
                                            </v-card>
                                        </v-menu> 
                                    </template>
                                </v-autocomplete>
                            </div>
                            <div v-if="param.input == 'date'" v-show="roleUser(param.role, {admin: 'admin', moderator: 'moderator'})">
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
                                            hint="Формат дд.мм.гггг"
                                            persistent-hint
                                            @blur="editedItem[param.value] = formatDate(picker)"
                                            prepend-icon="event"
                                            :label="param.text"
                                            :data-vv-as="'`'+param.text+'`'" 
                                            :data-vv-name="param.value" 
                                            :error-messages="errors.collect(param.value)" 
                                            v-validate="param.validate"
                                            v-on="on"
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker locale="ru" :first-day-of-week="1" v-model="picker" no-title :value="editedItem[param.value]" @input="param.close = false"></v-date-picker>
                                </v-menu>
                            </div>
                            <div v-if="param.input == 'textarea'" v-show="roleUser(param.role, {admin: 'admin', moderator: 'moderator', installer: 'installer'})">
                                <v-textarea
                                    rows='1'
                                    :data-vv-as="'`'+param.text+'`'" 
                                    :data-vv-name="param.value" 
                                    :error-messages="errors.collect(param.value)" 
                                    v-validate="param.validate" 
                                    v-model="editedItem[param.value]" 
                                    :label="param.text" 
                                    v-if="param.edit != true" 
                                    xs12 
                                    required>
                                </v-textarea>
                            </div>
                        </v-flex>
                        <div class="text-xs-center">
                            <v-btn color="info" @click="save" :loading="loadingSaveBtn" :disabled="loadingSaveBtn">
                                Сохранить
                                <template v-slot:loadingSaveBtn>
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
        </v-toolbar>
        <v-data-table :rows-per-page-items='[25, 35, 45, {text: "Все", value: -1}]' :pagination.sync="pagination" item-key="name" :headers="params.headers" :items="desserts" :loading="loading" class="elevation-1">
            <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>
            <template v-slot:headers="props">
                <th
                    v-for="header in props.headers"
                    :key="header.text"
                    :class="[
                        'column sortable',
                        pagination.descending ? 'asc' : 'desc', 
                        header.value === pagination.sortBy ? 'active' : '' , 
                        'text-xs-left', 
                        header.visibility
                    ]"
                    @click="changeSort(header.value)"
                >
                    {{ header.text }}
                    <v-icon small>arrow_upward</v-icon>
                </th>
                <th class="text-xs-left">
                    Действия
                </th>
            </template>
            <template v-slot:items="props">
                <td v-for="(param, key) in params.headers" :key="key" :class="param.visibility">
                    <v-flex v-if="param.value == 'orders_id'">
                        <a :href="'/orders-address/'+props.item['orders_id']">{{props.item[param.tableValue]}}</a>
                    </v-flex>
                    <v-flex v-else>
                        {{props.item[param.tableValue]}}
                    </v-flex> 
                </td>
                <td class="justify-left layout">
                    <v-icon small class="mr-2" v-show="hideElem()" @click="editItem(props.item)">	
                        edit	
                    </v-icon>
                    <v-icon small class="mr-2" v-show="hideElem()" @click="deleteItem(props.item)">
                        delete
                    </v-icon>
                    <v-icon class="mr-2" v-show="!hideElem()" @click="editItem(props.item)">
                        assignment_turned_in
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
import Vue from 'vue'
import VeeValidate from 'vee-validate'
import XLSX from 'xlsx';

Vue.use(VeeValidate);

export default {
    $_veeValidate: {
        validator: 'new'
    },
    data: (vm) => ({
        search: '',
        orderDate: false,
        dialog: false,
        loading: true,
        valid: true,
        desserts: [],
        loadingSaveBtn: false,
        editedIndex: -1,
        formFilds: {},
        dateStartFormatted: null,
        dateEndFormatted: null,
        dateStart: null,
        dateEnd: null,
        dateStartShow: false,
        dateEndShow: false,
        picker: new Date().toISOString().substr(0, 10),
        loadingExcel:false,
        pagination: {
            sortBy: 'id'
        },
        editedItem: {
            status: 1,
        },
        defaultItem: { 
            status: 1
        } 
    }),
    props: {
        params: Object
    },
    computed: {
        formTitle () {
            return this.editedIndex === -1 ? 'Создание задачи' : 'Редактирование задачи'
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
        dateStart(val) {
            this.dateStartFormatted = this.formatDate(this.dateStart);
        },
        dateEnd(val) {
            this.dateEndFormatted = this.formatDate(this.dateEnd);
        }, 
        picker(val) {
            this.editedItem.task_date_completion = this.formatDate(this.picker);
        },
    },
    async created () {
        await this.initialize();
    },
    methods: {
        changeSort (column) {
            if (this.pagination.sortBy === column) {
                this.pagination.descending = !this.pagination.descending
            } else {
                this.pagination.sortBy = column
                this.pagination.descending = false
            }
        },
        hideOrdersSelectDate() {
            if (this.dateStart || this.dateEnd) {
                return true;
            }else {
                return false;
            }
        },
        orderFilter() {
            this.initialize();
            this.orderDate = false;
        },
        hideElem() {
            if(this.isLoggedUser.installers) {
                return false;
            }
            if(!this.isLoggedUser.installers) {
                return true;
            }
        },
        roleUser(role, roleList) {
            if(role) {
                const {admin, client, installer, moderator, manager} = roleList;
                if (role === admin || role === client || role === installer || role === manager || role === moderator) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        },
        roleUserCity() {
            if(this.isLoggedUser.moderators) {
                return this.isLoggedUser.moderators.city_id;
            }
            if(this.isLoggedUser.managers) {
                return this.isLoggedUser.managers.city_id;
            }
            if(!this.isLoggedUser.moderators || !this.isLoggedUser.managers) {
                return null;
            }
        },
        roleUserId() {
            if(this.isLoggedUser.moderators) {
                return this.isLoggedUser.moderators.id;
            }
            if(this.isLoggedUser.installers) {
                return this.isLoggedUser.installers.id;
            }
            if(this.isLoggedUser.managers) {
                return this.isLoggedUser.managers.id;
            }
            if(!this.isLoggedUser.moderators || !this.isLoggedUser.installers || !this.isLoggedUser.managers) {
                return null;
            }
        },
        filteredItems(data) {
            this.desserts = data;
            let searchTerm = this.search.trim().toLowerCase(),
            useOr = this.search == "or",
            AND_RegEx = "(?=.*" + searchTerm.replace(/ +/g, ")(?=.*") + ")",
            OR_RegEx = searchTerm.replace(/ +/g,"|"),
            regExExpression = useOr ? OR_RegEx : AND_RegEx,
            search = new RegExp(regExExpression, "ig");
            let thisSearch = this.params.searchValue;
            return this.desserts = this.desserts.filter(function(item) {
                let arr = [];
                thisSearch.forEach(function(val) {
                    arr.push(item[val]);
                })
                return search.test(arr.join(" ")); 
            });
        },
        downloadExcel() {
            if(this.desserts.length > 0) {
                let map = this.desserts.map((item)=> {
                    return {
                        "Название организации клиента": item.orders,
                        "Дата выполнения задачи": item.task_date_completion,
                        "Тип работы": item.types,
                        "Комментарий": item.comment,
                        "Адреса": item.orderAddress
                    }
                });
                
                let ws = XLSX.utils.json_to_sheet(map, {raw:true});

                if(!ws['!cols']) ws['!cols'] = [];
                for (let i = 0; i < 6; i++) {
                    ws['!cols'][i] = {wch: 28};
                }
                let wb = XLSX.utils.book_new();
              
                XLSX.utils.book_append_sheet(wb, ws, "Задачи");
                XLSX.writeFile(wb, "Задачи.xlsx");
            }
        },
        initialize() {
            this.loading = true;
            axios({
                method: 'get',
                url: this.params.baseUrl,
                params: {
                    dateStart: this.dateStartFormatted,
                    dateEnd: this.dateEndFormatted,
                    city: this.roleUserCity(),
                    user: this.roleUserId(),
                    cityOrder: this.roleUserCity(),   
                }
            })
            .then(
                response => {
                    this.desserts = response.data.tasks;
                    this.formFilds = response.data;
                    this.filteredItems(this.desserts);
                    this.loading = false;
                }
            ).catch(error => {
                console.log(error);
            })
        },
        resetDate() {
            this.dateStartFormatted = null;
            this.dateEndFormatted = null;
            this.dateStart = null;
            this.dateEnd = null;
            this.initialize();
        },
        refreshSearch() {
            this.loading = true;
            this.search = '';
            this.initialize();
        },
        editItem(item) {
            this.editedIndex = this.desserts.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.formFilds.orders.push(this.editedItem.order);
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
        close() {
            this.dialog = false;
            this.picker = null;
            setTimeout(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            }, 300);
            this.$validator.reset();
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
                            this.initialize();
                            this.loadingSaveBtn = false;
                            this.dialog = false;
                        }
                    ).catch(error => {
                        console.log(error);
                    })  
                } else {
                    this.snackbar = true
                }
            });
        },
        formatDate (date) {
            if (!date) return null

            const [year, month, day] = date.split('-')
            return `${day}.${month}.${year}`
        },
        remove(item) {
            this.chips.splice(this.chips.indexOf(item), 1)
            this.chips = [...this.chips]
        }
    }
}
</script>