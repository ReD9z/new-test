<template>
<div>
    <b-add-client :addClient.sync="addClient" :clientNew.sync="clientNew" v-on:input="$emit('addClient', $event.target.value)"></b-add-client>
    <v-toolbar color="#fff" fixed app clipped-righ>
        <v-toolbar-title>{{$route.meta.title}}</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn
            color="#f2994a"
            class="white--text"
            large
            :loading="loadingExcelTask"
            :disabled="loadingExcelTask"
            @click='loadExcelTask'
            v-show="params.excelTask"
        >
            <v-icon left>vertical_align_bottom</v-icon>
            Добавить Excel
            <input
                type="file"
                style="display: none"
                ref="excelTask"
                accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, .ods"
                @change="loadExecelTask"
            >
        </v-btn>
        <v-btn color="green" large class="mb-2 white--text" @click.stop="dialog = !dialog"><v-icon left>add</v-icon>Создать задачу</v-btn>
    </v-toolbar>
    <v-navigation-drawer v-model="dialog" right hide-overlay stateless fixed>
        <v-card  class="borderNone">
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
                            <v-autocomplete
                                :items="formFilds[param.tableValue]"
                                v-model="editedItem[param.value]"
                                :item-text="param.childField"
                                item-value="id"
                                :label="param.text"
                                :data-vv-as="'`'+param.text+'`'" 
                                :data-vv-name="param.value" 
                                :error-messages="errors.collect(param.value)" 
                                v-validate="param.validate"
                            >
                                <template v-if="param.value == 'client_id'" v-slot:append-outer>
                                    <v-btn
                                        color="primary"
                                        dark
                                        small
                                        icon
                                        @click.stop="addClient = !addClient"
                                    >
                                        <v-icon small>add</v-icon>
                                    </v-btn>
                                </template>
                            </v-autocomplete>
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
                                <v-date-picker locale="ru" :first-day-of-week="1" v-model="picker" no-title @input="param.close = false"></v-date-picker>
                            </v-menu>
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
        <div v-show="params.filter">
            <v-chip :items="chipsClients" v-for="item in chipsClients" :key="item.id + 'clients'" close @input="remove(item)">{{item.name}}</v-chip>
            <v-chip :items="chipsStatus" v-for="item in chipsStatus" :key="item.id + 'status'" close @input="removeStatus(item)">{{item.title}}</v-chip>
            <v-chip v-if="dateStartClient" close @input="resetDate()">Начало {{dateStartClient}}</v-chip>
            <v-chip v-if="dateEndClient" close @input="resetDate()">Конец {{dateEndClient}}</v-chip>
        </div>
        <v-icon v-show="params.filter">filter_list</v-icon>
        <v-menu v-show="params.filter" :close-on-content-click="false" :nudge-width="200" offset-y bottom left>
            <template v-slot:activator="{ on }" >
                <v-btn icon v-on="on" v-show="params.filter">
                    <v-icon>more_vert</v-icon>
                </v-btn>
            </template>
            <v-card>
                <v-toolbar color="indigo" dark>
                    <v-toolbar-title>Фильтры</v-toolbar-title>
                    <v-spacer></v-spacer>
                </v-toolbar>
                <v-layout row wrap>
                    <v-flex class="px-3" xs3>
                        <v-combobox
                            v-model="chipsStatus"
                            :items="statusFilter"
                            item-text="title"
                            multiple
                            label="Статус"
                        ></v-combobox>
                    </v-flex>
                    <v-flex class="px-3" xs4>
                        <v-combobox
                            v-model="chipsClients"
                            :items="clients"
                            item-text="name"
                            multiple
                            label="Клиенты"
                        ></v-combobox>
                    </v-flex>
                    <v-flex class="px-3" xs3>
                        <v-layout row wrap>
                            <v-flex pt-1 xs12>
                                <v-menu
                                    v-model="menu4"
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
                                            v-model="dateStartClient"
                                            hint="Формат дд.мм.гггг"
                                            persistent-hint
                                            prepend-icon="event"
                                            label="Начало"
                                            v-on="on"
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker locale="ru" :first-day-of-week="1" v-model="dateStartNew" no-title @input="menu4 = false"></v-date-picker>
                                </v-menu>
                            </v-flex>
                            <v-flex pb-4 xs12>
                                <v-menu
                                    v-model="menu5"
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
                                            v-model="dateEndClient"
                                            hint="Формат дд.мм.гггг"
                                            persistent-hint
                                            prepend-icon="event"
                                            label="Конец"
                                            v-on="on"
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker locale="ru" :first-day-of-week="1" v-model="dateEndNew" no-title @input="menu5 = false"></v-date-picker>
                                </v-menu>
                            </v-flex>
                        </v-layout>
                    </v-flex>
                    <v-flex class="px-3" text-right pt-2 xs2>
                        <v-btn flat icon @click="resetDate()">
                            <v-icon>close</v-icon>
                        </v-btn>                
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
                :class="['column sortable', pagination.descending ? 'asc' : 'desc', header.value === pagination.sortBy ? 'active' : '' , 'text-xs-left', header.visibility]"
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
                {{props.item[param.tableValue]}} 
            </td>
            <td class="justify-left layout">
                <v-icon small class="mr-2"  @click="editItem(props.item)">	
                    edit	
                </v-icon>
                <v-icon small class="mr-2"  @click="deleteItem(props.item)">
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
import Vue from 'vue'
import VeeValidate from 'vee-validate'

Vue.use(VeeValidate);

export default {
    $_veeValidate: {
        validator: 'new'
    },
    data: (vm) => ({
        search: '',
        clientNew: null,
        loadingExcelTask: false,
        dialog: false,
        addClient: false,
        chipsStatus: [],
        files: [],
        dateStartNew: null,
        dateEndNew: null,
        picker:null,
        desserts: [],
        editedIndex: -1,
        dateStartClient: vm.formatDate(new Date().toISOString().substr(0, 10)),
        dateEndClient: vm.formatDate(new Date().toISOString().substr(0, 10)),
        editedItem: {
            status: 1,
            manager_id: vm.$store.getters.isLoggedUser.managers ? vm.$store.getters.isLoggedUser.managers.id : null
        },
        defaultItem: { 
            status: 1,
            manager_id: vm.$store.getters.isLoggedUser.managers ? vm.$store.getters.isLoggedUser.managers.id : null
        },
        loadingSaveBtn: false,
        loaderSaveBtn: null,
        formData: new FormData(),
        chipsClients: [],
        clients: [],
        formFilds: [],
        valid: true,
        pagination: {
            sortBy: 'id'
        },
        statusFilter: [],
        loading: true,
        menu1: false,
        menu2: false,
        menu4: false,
        menu5: false,
        userId: null
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
        dialog(val) {
            val || this.close()
        },
        search: _.debounce(function () {
            this.initialize()
        }, 400),
        dateStartNew(val) {
            this.initialize();
            this.dateStartClient = this.formatDate(this.dateStartNew);
        },
        dateEndNew(val) {
            this.initialize();
            this.dateEndClient = this.formatDate(this.dateEndNew);
        },
        picker(val) {
            this.editedItem.task_date_completion = this.formatDate(this.picker);
        },
        chipsClients(val) {
            this.initialize();
        },
        chipsStatus(val) {
            this.initialize();
        },
        clientNew(newVal, oldVal) {
            this.initialize();
            if(this.clientNew) {
                this.editedItem['client_id'] = this.clientNew;
            }
        }
    },
    async created () {
        await this.initialize();
    },
    methods: {
        filteredStatus() {
            if(this.chipsStatus.length > 0) {
                let status = [];
            
                this.chipsStatus.forEach((chip) => {
                    status.push(chip.title);
                });

                var searchTerm = status.join('||').trim().toLowerCase(),
                useOr = 'and' == "or",
                AND_RegEx = "(?=.*" + searchTerm.replace(/ +/g, ")(?=.*") + ")",
                OR_RegEx = searchTerm.replace(/ +/g,"|"),
                regExExpression = useOr ? OR_RegEx : AND_RegEx,
                searchTest = new RegExp(regExExpression, "ig");
           
                this.desserts = this.desserts.filter(function(item) {
                    return searchTest.test([item.statusName]);
                });
            }
        },
        filteredClient() {
            if(this.chipsClients.length > 0) {
                let arr = [];
                this.chipsClients.forEach((chip) => {
                    arr.push(chip.name);
                });
                var searchTerm = arr.join('||').trim().toLowerCase(),
                useOr = 'and' == "or",
                AND_RegEx = "(?=.*" + searchTerm.replace(/ +/g, ")(?=.*") + ")",
                OR_RegEx = searchTerm.replace(/ +/g,"|"),
                regExExpression = useOr ? OR_RegEx : AND_RegEx,
                searchTest = new RegExp(regExExpression, "ig");
           
                this.desserts = this.desserts.filter(function(item) {
                    return searchTest.test([item.clients]);
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
                this.userId = this.isLoggedUser.managers.id;
                return this.isLoggedUser.managers.id;
            }
            if(!this.isLoggedUser.moderators || !this.isLoggedUser.installers || !this.isLoggedUser.managers) {
                return null;
            }
        },
        formatDate(date) {
            if (!date) return null

            const [year, month, day] = date.split('-')
            return `${day}.${month}.${year}`
        },
        resetDate() {
            this.dateStartNew = null;
            this.dateEndNew = null;
            this.dateStartClient = null;
            this.dateEndClient = null;
            this.chipsClients = [];
            this.chipsStatus = [];
            this.initialize();
        },
        refreshSearch() {
            this.dateStartNew = null;
            this.dateEndNew = null;
            this.dateStartClient = null;
            this.dateEndClient = null;
            this.loading = true;
            this.search = '';
            this.dateStartNew = null;
            this.dateEndNew = null;
            this.chipsClients = [];
            this.initialize();
        },
        changeSort(column) {
            if (this.pagination.sortBy === column) {
                this.pagination.descending = !this.pagination.descending;
            } else {
                this.pagination.sortBy = column;
                this.pagination.descending = false;
            }
        },
        initialize() {
            this.loading = true;
            axios({
                method: 'get',
                url: this.params.baseUrl,
                params: {
                    city: this.roleUserCity(),
                    user: this.roleUserId(),
                    dateStart: this.dateStartClient,
                    dateEnd: this.dateEndClient
                }
            })
            .then(
                response => {
                    this.desserts = response.data.tasks; 
                    this.statusFilter = response.data.statusName;
                    this.formFilds = response.data;
                    this.clients = response.data.clients;
                    
                    this.filteredItems(this.desserts);
                    this.filteredClient(this.desserts); 
                    this.filteredStatus(this.desserts);
                    this.loading = false;
                }
            ).catch(error => {
                console.log(error);
            })
        },
        loadExcelTask() {
            this.$refs.excelTask.click();
        },
        async loadExecelTask(file) {
            let vm = this;
            this.loadingExcelTask = true;
            this.dateStartNew = null;
            this.dateEndNew = null;
            this.dateStartClient = null;
            this.dateEndClient = null;
            let files = this.$refs.excelTask.files[0];
            this.formData.append('file', files);
            await axios({
                method: 'post',
                url: '/api/addExcelTask',
                data: this.formData,
                headers: {'Content-Type': 'multipart/form-data'},
                params: {
                    user: this.userId
                }
            })
            .then(
                response => {
                    this.$refs.excelTask.value = '';
                    this.loadingExcelTask = false;
                    this.initialize();
                }
            ).catch(
                error => {
                    console.log(error);
                    this.loadingExcelTask = false;
                }
            ); 
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
        close () {
            this.dialog = false;
            this.picker = null;
            setTimeout(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            }, 300)
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
            this.chipsClients.splice(this.chipsClients.indexOf(item), 1)
            this.chipsClients = [...this.chipsClients]
        },
        removeStatus(item) {
            this.chipsStatus.splice(this.chipsStatus.indexOf(item), 1)
            this.chipsStatus = [...this.chipsStatus]
        },
        roleUser(role, roleList) {
            const {admin, client, installer, moderator, manager} = roleList;
            if (role === admin || role === client || role === installer || role === manager || role === moderator) {
                return true;
            } else {
                return false;
            }
        },
    }
}
</script>