<template>
<v-flex>
    <v-toolbar color="#fff" fixed app clipped-righ>
        <v-toolbar-title>Заказ №{{order.id}}</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn color="info" large @click="save" :loading="loadingSaveBtn" :disabled="loadingSaveBtn">
            Сохранить
            <template v-slot:loaderSaveBtn>
                <span class="custom-loader">
                <v-icon light>cached</v-icon>
                </span>
            </template>
        </v-btn>
        <v-btn color="green" large class="mb-2 white--text" to="/orders"><v-icon left>chevron_left</v-icon>К списку заказов</v-btn>
    </v-toolbar>
    <v-card>
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
    <v-navigation-drawer v-model="dialogImages" right temporary fixed width="700px">
        <v-card>
            <v-toolbar color="pink" dark>
                <v-toolbar-title>Изображения</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-icon right dark @click='pickImages'>control_point</v-icon>
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
                            <v-flex v-for="(file, key) in addOrderImages.files" :key="key" xs4 d-flex>
                                <v-card flat tile class="d-flex pr-1 pb-1">
                                    <v-img :src="'/storage/' + file.url" :lazy-src="'/storage/' + file.url" aspect-ratio="1" class="grey lighten-2">
                                        <template v-slot:placeholder>
                                            <v-layout fill-height align-center justify-center ma-0 >
                                                <v-progress-circular indeterminate color="grey lighten-5"></v-progress-circular>
                                            </v-layout>
                                        </template>
                                        <template>
                                            <v-layout fill-height right top ma-0 >
                                                <v-btn icon class="white--text" :loading="deleteImage" :disabled="loadImages" @click='removeImg(file)'>
                                                    <v-icon>close</v-icon>
                                                </v-btn>
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
            <v-chip :items="chips" v-for="(item, key) in chips" :key="key" close @input="remove(item)">{{item.data}}</v-chip>
        </div>
        <v-icon>filter_list</v-icon>
        <v-menu :close-on-content-click="false" :nudge-width="200" offset-y bottom left>
            <template v-slot:activator="{ on }">
                <v-btn icon v-on="on">
                    <v-icon>more_vert</v-icon>
                </v-btn>
            </template>
            <v-card>
                <v-divider></v-divider>
                <v-list v-for="(items, keys) in params.filter" :key="`A-${keys}`">
                    <v-subheader>
                        {{items.title}}
                    </v-subheader>
                    <div v-for="(item, key) in chipsItem" :key="`A-${key}`">
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
            <td v-for="(param, key) in params.headers" :key="`A-${key}`" :class="param.visibility">
                <v-flex v-if="param.input !== 'images' && param.value !== 'params'">
                    <v-flex v-if="param.selectText">
                        {{props.item[param.TableGetIdName]}}
                    </v-flex>
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
            <td class="justify-center layout px-0">
                <v-icon small class="mr-2" v-if="props.item.files !== null" @click="editPhotos(props.item)">
                    image
                </v-icon>
                <v-icon small class="mr-2" v-if="props.item.data !== null"  @click="deleteItem(props.item)">
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
export default {
    inject: ['$validator'],
    data: vm => ({
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
        chips(val) {
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
        formatDate (date) {
            if (!date) return null

            const [year, month, day] = date.split('-')
            return `${day}-${month}-${year}`
        },
        parseDate (date) {
            if (!date) return null

            const [year, month, day] = date.split('-')
            return `${day.padStart(2, '0')}-${month.padStart(2, '0')}-${year}`
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
                } 
                // else {
                //     item.data.filter((items) => {
                //         this.chipsItem.push({
                //             data: items[item.value],
                //             text: item.text,
                //             input: item.input
                //         });
                //     });
                // }
            });
        },
        filtered(data) {
            this.desserts = data;
            if(this.chips.length > 0) {
                let arr = [];
                this.chips.forEach((chip) => {
                    arr.push(chip.data);
                });
                var searchTerm = arr.join('||').trim().toLowerCase(),
                useOr = 'and' == "or",
                AND_RegEx = "(?=.*" + searchTerm.replace(/ +/g, ")(?=.*") + ")",
                OR_RegEx = searchTerm.replace(/ +/g,"|"),
                regExExpression = useOr ? OR_RegEx : AND_RegEx,
                searchTest = new RegExp(regExExpression, "ig");
                let array = [];
                // console.log(searchTerm);
                this.desserts = this.desserts.filter(function(item) {
                    // console.log(item.city.trim().toLowerCase() == searchTest);
                    // console.log(item.city.trim().toLowerCase());
                    // console.log(item.city.trim().toLowerCase() == arr.join('||').trim().toLowerCase());
                    // if(arr.join('||').trim().toLowerCase()) {
                    //     array.push(item);
                    // }
                    // console.log(arr.join('||').trim().toLowerCase());
                    // searchTest.test([item.city, item.result].join('||').trim().toLowerCase());
                    // console.log(searchTest.test([item.city, item.result].join('||').trim().toLowerCase()));
                    // console.log([item.city, item.result].join(' ').trim().toLowerCase());
                    // console.log("12");
                    // console.log(arr.join(' ').trim().toLowerCase());
                    // if(searchTest.test([item.city].join(' ').trim().toLowerCase())) {
                        // array.push(item);
                        // console.log(item);
                    // }
                    // console.log(searchTest.test([item.result, item.city].join(' ').trim().toLowerCase()));
                    return searchTest.test([item.city]); 
                // console.log(searchTest.test([item.city]));
                });
                // this.desserts = array;
            }
            // // if(this.chips.length > 0) {
            //     let vm = this;
            //     let array = [];
            //     this.chips.forEach((chip) => {
            //         vm.desserts.filter(function(item) {
            //             if(chip.data == item['result']) {
            //                 array.push(item);
            //             }
            //         });
            //     });
            //     return this.desserts = array;
            // else {
            //     return this.desserts = data;
            // }
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
                        data: this.addOrderImages,
                        params: {
                            images: res.data.files
                        }
                    })
                    .then(
                        response => {
                            Object.assign(this.addOrderImages, response.data);
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
            axios.post('/api/files/remove', data)
            .then(
                res => {
                    this.addOrderImages.files.splice(this.addOrderImages.files.indexOf(data.id), 1);
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
                    if(!this.dateStartFormatted && !this.dateEndFormatted) {
                        this.initialize();
                    } else {
                        this.desserts.map(function (item) {
                            if(item.status.length > 0) {
                                return item.status.map(function (stats) {
                                    let itemDateStart = vm.$moment(stats.orders.order_start_date).unix() * 1000;
                                    let itemDateEnd = vm.$moment(stats.orders.order_end_date).unix() * 1000;
                                    let dateStart = vm.$moment(vm.dateStartFormatted, 'DD-MM-YYYY').unix() * 1000;
                                    let dateEnd = vm.$moment(vm.dateEndFormatted, 'DD-MM-YYYY').unix() * 1000;
    
                                    if(itemDateStart >= dateStart && itemDateEnd <= dateEnd || itemDateStart <= dateStart && itemDateEnd >= dateEnd) {
                                        item.result = 'Занято';
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
                    axios.get(element.selectApi)
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
            this.editedItem = Object.assign({}, item);
            let vm = this;
            this.dateStart = this.$moment(vm.order.order_start_date, "DD-MM-YYYY").format("YYYY-MM-DD");
            this.dateEnd =  this.$moment(vm.order.order_end_date, "DD-MM-YYYY").format("YYYY-MM-DD");
        },
        editPhotos(item) {
            this.addOrderImages = Object.assign({}, item.data);
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
            this.$validator.validateAll().then(() => {
                if(this.$validator.errors.items.length == 0) {
                    this.loaderSaveBtn = true;
                    this.loadingSaveBtn = true;
                    axios({
                        method: 'put',
                        url: this.params.baseOrders,
                        data: {
                            order: this.editedItem,
                            address: this.selected.filter(item => item.result !== 'Занято'),
                            dateStart: this.dateStartFormatted,
                            dateEnd: this.dateEndFormatted
                        }
                    })
                    .then(response => {
                        this.initialize();
                        this.loaderSaveBtn=null;
                        this.loadingSaveBtn = false;
                        this.forceRerender();  
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