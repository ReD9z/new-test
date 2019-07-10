<template>
<div>
    <v-toolbar color="#fff" fixed app clipped-righ>
        <v-toolbar-title>Заказ №{{order.id}}</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn color="green" large class="mb-2 white--text" to="/orders"><v-icon left>chevron_left</v-icon>К списку заказов</v-btn>
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
                                    v-model="dateStart"
                                    :rules="param.validate"
                                    hint="YYYY-MM-DD формат"
                                    persistent-hint
                                    prepend-icon="event"
                                    :label="param.text"
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker v-model="dateStart" no-title @input="param.close = false"></v-date-picker>
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
                                    v-model="dateEnd"
                                    hint="YYYY-MM-DD формат"
                                    persistent-hint
                                    prepend-icon="event"
                                    :label="param.text"
                                    :rules="param.validate"
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker v-model="dateEnd" no-title @input="param.close = false"></v-date-picker>
                        </v-menu>
                    </div>
                </v-flex>
            </v-form>
        </v-card-text>
    </v-card>
    <v-navigation-drawer v-model="dialogImages" right temporary fixed width="700px">
        <v-card height="100%">
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
            <td>
                <v-flex>
                    <v-icon small class="mr-2" v-if="props.item.files !== null" @click="editPhotos(props.item)">
                        image
                    </v-icon>
                    <v-icon small class="mr-2" v-if="props.item.data !== null"  @click="deleteItem(props.item)">
                        delete
                    </v-icon>
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
        dialogImages: false,
        files: [],
        loadImages: false,
        loading: true,
        desserts: [],
        editedIndex: -1,
        editedItem: {},
        defaultItem: {},
        select: [],
        addOrderImages: [],
        keywords: '',
        deleteImage: false,
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
                                let itemDateStart = vm.$moment(stats.orders.order_start_date, 'YYYY-MM-DD').unix() * 1000;
                                let itemDateEnd = vm.$moment(stats.orders.order_end_date, 'YYYY-MM-DD').unix() * 1000;

                                let dateStart = vm.$moment(vm.dateStart, 'YYYY-MM-DD').unix() * 1000;
                                let dateEnd = vm.$moment(vm.dateEnd, 'YYYY-MM-DD').unix() * 1000;
    
                                if(dateStart >= itemDateStart && dateEnd <= itemDateEnd) {
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
                    this.loading = false;
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
            this.dateStart = item.order_start_date;
            this.dateEnd = item.order_end_date;
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
            // setTimeout(() => {
            //     this.editedItem = Object.assign({}, this.defaultItem)
            //     this.editedIndex = -1
            // }, 300)
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
                        method: 'put',
                        url: this.params.baseOrders,
                        data: {
                            order: this.editedItem,
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