<template>
<v-flex>
    <v-toolbar color="#fff" fixed app clipped-righ>
        <v-toolbar-title>Заказ №{{order.id}}</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn v-if="hideElem()" color="green" large class="mb-2 white--text" @click="save" :loading="loadingSaveBtn" :disabled="loadingSaveBtn">
            <v-icon left>add</v-icon> Сохранить
            <template v-slot:loaderSaveBtn>
                <span class="custom-loader">
                <v-icon light>cached</v-icon>
                </span>
            </template>
        </v-btn>
        <v-btn color="info" large class="mb-2 white--text" to="/orders"><v-icon left>chevron_left</v-icon>К списку заказов</v-btn>
    </v-toolbar>
    <v-card v-if="hideElem()">
        <v-card-text>
            <v-form ref="forms" v-model="valid" lazy-validation>
                <v-layout row wrap>
                    <v-flex v-for="(param, key) in params.headerOrders" :key="`Y-${key}`" xs12>
                        <div v-if="param.input == 'text'" xs12>
                            <v-text-field v-model="editedItem[param.value]" :rules="param.validate" :label="param.text" v-if="param.input !== 'images' && param.edit != true" xs12 required></v-text-field>
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
                    <v-flex v-for="(param, key) in params.headerOrders" v-if="param.input == 'dateStart'"  :key="`T-${key}`" xs12 lg6>
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
                            <v-date-picker locale="ru" :first-day-of-week="1" v-model="dateStart" no-title @input="param.close = false"></v-date-picker>
                        </v-menu>
                    </v-flex>
                    <v-flex v-for="(param, key) in params.headerOrders" v-if="param.input == 'dateEnd'"  :key="`M-${key}`" xs12 lg6>
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
                                    :data-vv-as="'`'+param.text+'`'" 
                                    :data-vv-name="param.value" 
                                    :error-messages="errors.collect(param.value)" 
                                    v-validate="param.validate"
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker locale="ru" :first-day-of-week="1" v-model="dateEnd" no-title @input="param.close = false"></v-date-picker>
                        </v-menu>
                    </v-flex>
                </v-layout>
            </v-form>
        </v-card-text>
    </v-card>
    <v-navigation-drawer v-model="dialogImages" right hide-overlay stateless fixed width="700px">
        <v-card class="borderNone">
            <v-toolbar color="pink" dark>
                <v-toolbar-title>Изображения</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-icon v-if="hideElem()" right dark @click='pickImages'>control_point</v-icon>
                <input type="file" ref="images" name='file' accept="image/*" style="display: none" @change="elementLoadToFileImage" multiple>
                <v-btn icon @click="close">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-progress-linear value="15" :indeterminate="true" v-show="loadImages" color="blue" class="ma-0"></v-progress-linear>
            <v-card-text>
                <v-flex v-for="(param, key) in params.headers" :key="key" xs12>
                    <v-flex xs12 v-if="param.input == 'images'">
                        <v-layout row wrap>
                            <v-flex v-for="(file, key) in addOrderImages" :key="key" xs4 d-flex>
                                <v-card flat tile class="d-flex pr-1 pb-1">
                                    <v-img :src="file.url" :lazy-src="file.url" aspect-ratio="1" class="grey lighten-2">
                                        <template>
                                            <v-layout v-show="file.entrances" fill-width align-center column ma-0>
                                                <v-btn>
                                                    Номер подъезда {{file.entrances ? file.entrances.number : null}}
                                                </v-btn>
                                            </v-layout>
                                        </template>
                                        <template v-slot:placeholder>
                                            <v-layout left align-end ma-0>
                                                <v-progress-circular indeterminate color="grey lighten-5"></v-progress-circular>
                                            </v-layout>
                                        </template>
                                        <template>
                                            <v-layout right align-start ma-0>
                                                <v-btn icon class="white--text" :loading="deleteImage" :disabled="loadImages" @click='removeImg(file)'>
                                                    <v-icon>close</v-icon>
                                                </v-btn>
                                            </v-layout>
                                        </template>
                                        <template>
                                            <v-layout>
                                                <v-dialog v-model="dialogImg" width="600px">
                                                    <template v-slot:activator="{ on }">
                                                        <v-btn icon class="white--text" v-on="on" @click='filesImg(file)'><v-icon>search</v-icon></v-btn>
                                                    </template>
                                                    <v-card v-if="imgBig">
                                                        <v-img :src="imgBig" :lazy-src="imgBig" aspect-ratio="1" class="grey lighten-2"></v-img>
                                                    </v-card>
                                                </v-dialog>
                                            </v-layout>
                                        </template>
                                    </v-img>
                                </v-card>
                            </v-flex>
                        </v-layout>
                    </v-flex>
                </v-flex>
            </v-card-text>
        </v-card>
    </v-navigation-drawer>
    <v-toolbar flat color="#fff">
        <v-flex xs12 sm6 md3>
            <v-text-field v-model="search" append-icon="search" label="Поиск" v-show="params.search" single-line hide-details></v-text-field>
        </v-flex>
        <v-spacer></v-spacer>
        <div>
            <v-chip v-for="(item, key) in chips" :key="key" close @input="remove(item)">{{item}}</v-chip>
            <v-chip v-for="(item, key) in chipsStatus" :key="'K'+ key" close @input="removeStatus(item)">{{item}}</v-chip>
        </div>
        <v-icon>filter_list</v-icon>
        <v-menu :close-on-content-click="false" :nudge-width="500" offset-y bottom left>
            <template v-slot:activator="{ on }">
                <v-btn icon v-on="on">
                    <v-icon>more_vert</v-icon>
                </v-btn>
            </template>
            <v-card height="200px">
                <v-toolbar color="indigo" dark>
                    <v-toolbar-title>Фильтры</v-toolbar-title>
                    <v-spacer></v-spacer>
                </v-toolbar>
                <v-layout row wrap>
                    <v-flex class="px-3" xs6>
                        <v-combobox
                            v-model="chips"
                            :items="chipsItem"
                            multiple
                            label="Город"
                        ></v-combobox>
                    </v-flex>
                    <v-flex class="px-3" xs6>
                        <v-combobox
                            v-model="chipsStatus"
                            :items="statusFilter"
                            multiple
                            label="Статус"
                        ></v-combobox>
                    </v-flex>
                </v-layout>
            </v-card>
        </v-menu>
    </v-toolbar>
    <v-data-table :rows-per-page-items='[25, 35, 45, {text: "Все", value: -1}]' v-model="selected" select-all :pagination.sync="pagination" item-key="name" :headers="params.headers" :items="desserts" :loading="loading" class="elevation-1">
        <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>
        <template v-slot:headers="props">
            <th v-if="hideElem()">
                <v-checkbox
                    primary
                    hide-details
                    @click.native="toggleAll"
                    :input-value="props.all"
                    :indeterminate="props.indeterminate"
                    >
                </v-checkbox>
            </th>
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
            <td v-if="hideElem()">
                <v-checkbox
                    v-show="props.item.resultStatus !== 'Занят'"
                    v-model="props.selected"
                    primary
                    hide-details
                >
                </v-checkbox>
            </td>
            <td v-for="(param, key) in params.headers" :key="`A-${key}`" :class="param.visibility">
                <v-flex v-if="param.input !== 'images' && param.value !== 'params'">
                    <v-flex v-if="param.selectText">
                        {{props.item[param.TableGetIdName]}}
                    </v-flex>
                    <v-flex v-else>
                        <span v-if="param.value == 'resultStatus' && props.item[param.value] === 'Занят'" style="font-weight: bold; color:red">
                            {{props.item[param.value]}}
                        </span>
                        <span v-if="param.value == 'resultStatus' && props.item[param.value] === 'Свободен'" style="font-weight: bold; color:green">
                            {{props.item[param.value]}}
                        </span>
                        <span v-if="param.value != 'resultStatus'">
                            {{props.item[param.value]}}
                        </span>
                    </v-flex>
                </v-flex>
            </td>
            <td class="justify-center layout px-0">
                <v-icon small class="mr-2" v-if="props.item.files !== null" @click="editPhotos(props.item)">
                    image
                </v-icon>
                <v-icon small class="mr-2" v-if="props.item.data !== null && hideElem()"  @click="deleteItem(props.item)">
                    delete
                </v-icon>
            </td>
        </template>
        <template v-slot:no-data>
            <v-btn color="primary" @click="refreshSearch">Сброс</v-btn>
        </template>
    </v-data-table>
    <b-maps :items="desserts" v-if="renderComponent"></b-maps>
</v-flex>
</template>
<script>
import XLSX from 'xlsx';
import { constants } from 'crypto';
export default {
    inject: ['$validator'],
    data: vm => ({
        imgBig: '',
        statusFilter: ['Занят', 'Свободен'],
        chipsStatus: [],
        dialogImg: false,
        search: '',
        dialogImages: false,
        files: [],
        loadImages: false,
        loading: true,
        desserts: [],
        renderComponent: true,
        editedIndex: -1,
        editedItem: {},
        defaultItem: {},
        select: [],
        addOrderImages: [],
        dateStartFormatted: null,
        dateEndFormatted: null,
        keywords: '',
        deleteImage: false,
        dateStart: null,
        dateEnd: null,
        loadingSaveBtn: false,
        loaderSaveBtn: null,
        formData: new FormData(),
        chips: [],
        chipsItem: [],
        pagination: {
            sortBy: 'id'
        },
        valid: true,
        order: [],
        cityUser: null,
        selected: [],
        addOrder: []
    }),
    props: {
        params: Object,
        idRouteOrder: String
    },
    computed: {
        formTitle () {
            return this.editedIndex === -1 ? 'Добавить' : 'Редактировать'
        },
        isLoggedUser: function(){ 
            return this.$store.getters.isLoggedUser
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
            this.initialize();
        }, 300),
        chips() {
            this.initialize();
        },
        chipsStatus() {
            this.initialize();
        }
    },
    async created () {
        await this.initializeOrder();
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
        showRoles() {
            if(this.isLoggedUser.moderators || this.isLoggedUser.managers) {
                return false;
            }
            if(!this.isLoggedUser.moderators || !this.isLoggedUser.managers) {
                return true;
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
        filesImg(file) {
            this.imgBig = '';
            this.imgBig = file.url;
        },
        formatDate (date) {
            if (!date) return null

            const [year, month, day] = date.split('-')
            return `${day}.${month}.${year}`
        },
        parseDate (date) {
            if (!date) return null

            const [year, month, day] = date.split('-')
            return `${day.padStart(2, '0')}-${month.padStart(2, '0')}-${year}`
        },
        async getFiltered() {
            this.chipsItem = [];
            await this.params.filter.forEach((item) => {
                axios({
                    method: 'get',
                    url: item.api,
                    params: {
                        city: this.roleUserCity()
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
            });
        },
        filteredStatus(data) {
            if(this.chipsStatus.length > 0) {
                let arr = [];
                this.chipsStatus.forEach((chip) => {
                    arr.push(chip);
                });
                var searchTerm = arr.join('||').trim().toLowerCase(),
                useOr = 'and' == "or",
                AND_RegEx = "(?=.*" + searchTerm.replace(/ +/g, ")(?=.*") + ")",
                OR_RegEx = searchTerm.replace(/ +/g,"|"),
                regExExpression = useOr ? OR_RegEx : AND_RegEx,
                searchTest = new RegExp(regExExpression, "ig");
                let array = [];
           
                this.desserts = this.desserts.filter(function(item) {
                    return searchTest.test([item.result]); 
                });
            }
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
            useOr = 'and' == "or",
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
        forceRerender() {
            this.renderComponent = false;

            this.$nextTick().then(() => {
                this.renderComponent = true;
            });
        },
        toggleAll() {
            if (this.selected.length) this.selected = []
            else this.selected = this.desserts.slice();
        },
        elementLoadToFileImage() {
            this.loadImages = true;
            this.files = this.$refs.images.files;
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
                        url: '/api/address_to_orders',
                        data: this.addOrder,
                        params: {
                            images: res.data.files
                        }
                    })
                    .then(
                        response => {
                            Object.assign(this.addOrderImages, response.data.files);
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
        refreshSearch() {
            this.chips = [];
            this.loading = true;
            this.search = '';
            this.initialize();
        },
        resetFilesLoad() {
            this.files = [];
            this.$refs.images.value = '';
            this.formData.delete('file[]');
        },
        pickImages() {
            this.$refs.images.click();
        },
        changeSort(column) {
            if (this.pagination.sortBy === column) {
                this.pagination.descending = !this.pagination.descending
            } else {
                this.pagination.sortBy = column
                this.pagination.descending = false
            }
        },
        removeImg(data) {
            this.loadImages = true;
            const index = Array.from(this.addOrderImages).indexOf(data.id);
            axios.post('/api/files/remove', data)
            .then(
                res => {
                    this.addOrderImages.splice(index, 1);
                    this.loadImages = false;
                }
            ).catch(
                error => {
                    console.log(error);
                }
            );
        },
        initialize() {
            this.loading = true;
            console.log(this.order);
            axios({
                method: 'get',
                url: this.params.baseUrl,
                params: {
                    city: this.roleUserCity(),
                    user: this.isLoggedUser.clients ? true : null,
                    order: this.$route.params.id,
                }
            })
            .then(
                response => {
                    this.desserts = response.data.address;
                    let vm = this;
                    if(!this.dateStartFormatted && !this.dateEndFormatted) {
                        this.initialize();
                    } else {
                        this.desserts.forEach(function (item) {
                            if(item.status.length > 0) {
                                return item.status.filter(function (stats) {
                                    let itemDateStart = vm.$moment(stats.orders.order_start_date).unix() * 1000;
                                    let itemDateEnd = vm.$moment(stats.orders.order_end_date).unix() * 1000;
                                    let dateStart = vm.$moment(vm.dateStartFormatted, 'DD-MM-YYYY').unix() * 1000;
                                    let dateEnd = vm.$moment(vm.dateEndFormatted, 'DD-MM-YYYY').unix() * 1000;
                                    if((itemDateStart >= dateStart) && (itemDateStart <= dateEnd) || (itemDateEnd >= dateStart) && (itemDateEnd <= dateEnd)) {
                                        item.resultStatus = 'Занят';
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
                        this.filteredStatus(this.desserts); 
                        this.loading = false;
                    }
                }
            ).catch(error => {
                console.log(error);
            })
        },
        initializeOrder() {
            axios({
                method: 'get',
                url: this.params.baseOrders + "/" + this.idRouteOrder
            })
            .then(
                response => {
                    this.order = response.data;
                    this.editItem(response.data);
                }
            ).catch(error => {
                console.log(error);
            })
        },
        selectStatus() {
            this.params.headerOrders.forEach(element => {
                if(element.selectApi != undefined) {
                    axios({
                        method: 'get',
                        url: element.selectApi,
                        params: {
                            city: this.roleUserCity()
                        }
                    })
                    .then(
                        res => {
                            if(res) {
                                this.select.push({data: res.data, url: element.selectApi});
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
            let vm = this;
            this.editedItem = Object.assign({}, item);
            this.dateStart = this.$moment(vm.order.order_start_date, "DD-MM-YYYY").format("YYYY-MM-DD");
            this.dateEnd =  this.$moment(vm.order.order_end_date, "DD-MM-YYYY").format("YYYY-MM-DD");
        },
        editPhotos(item) {
            this.addOrderImages =  item.images;
            this.addOrder = Object.assign({}, item.data);
            this.dialogImages = true;
        },
        deleteItem (item) {
            if (confirm('Вы уверены, что хотите удалить этот элемент?')) {
                axios({
                    method: 'delete',
                    url: '/api/address_to_orders/' + item.data.id,
                })
                .then(
                    response => {
                        this.forceRerender();   
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
            this.$validator.reset()
        },
        save() {
            let address = this.selected.filter(item => item.resultStatus === "Свободен");
            this.$validator.validateAll().then(() => {
                if(this.$validator.errors.items.length == 0) {
                    this.loaderSaveBtn = true;
                    this.loadingSaveBtn = true;
                    axios({
                        method: 'put',
                        url: this.params.baseOrders,
                        data: {
                            order: this.editedItem,
                            address: address,
                            dateStart: this.dateStartFormatted,
                            dateEnd: this.dateEndFormatted
                        }
                    })
                    .then(response => {
                        this.initialize();
                        this.loaderSaveBtn = null;
                        this.loadingSaveBtn = false;
                        this.forceRerender(); 
                        this.selected = [];
                    }).catch(error => {
                        console.log(error);
                    });
                } else {
                    this.snackbar = true
                }
            }); 
        },
        remove(item) {
            this.chips.splice(this.chips.indexOf(item), 1)
            this.chips = [...this.chips]
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
        }
    }
}
</script>