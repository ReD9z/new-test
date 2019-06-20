<template>
<div>
    <v-toolbar color="#fff" fixed app clipped-righ>
        <v-toolbar-title>{{$route.meta.title}}</v-toolbar-title>
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
                            <v-flex v-for="(file, key) in editedItem.files" :key="key" xs4 d-flex>
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
    <v-data-table v-model="selected" :pagination.sync="pagination" select-all :headers="params.headers" :items="desserts" :search="search" :loading="loading" class="elevation-1">
        <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>
        <template v-slot:headers="props">
            <tr>
                <th>
                    <v-checkbox :input-value="props.all" :indeterminate="props.indeterminate" primary hide-details @click.stop="toggleAll"></v-checkbox>
                </th>
                <th
                    v-for="header in props.headers"
                    :key="header.text"
                    :class="['column sortable', pagination.descending ? 'desc' : 'asc', header.value === pagination.sortBy ? 'active' : '' , 'text-xs-left', header.visibility]"
                    @click="changeSort(header.value)">
                    {{ header.text }}<v-icon small>arrow_upward</v-icon>
                </th>
                <th class="text-xs-left">
                    Парамеры  
                </th>
            </tr>
        </template>
        <template v-slot:items="props">
            <tr :active="props.selected" @click="props.selected = !props.selected">
                <td>
                    <v-checkbox :input-value="props.selected" primary hide-details ></v-checkbox>
                </td>
                <td v-for="(param, key) in params.headers" :key="key" :class="param.visibility">
                    <v-flex v-if="param.input !== 'images'">
                        <v-flex v-if="param.selectText">{{props.item[param.TableGetIdName]}}</v-flex>
                        <v-flex v-else>{{props.item[param.value]}}</v-flex>
                    </v-flex>
                </td>
                <td>
                    <v-flex>
                        <v-icon v-if="props.item.files" small class="mr-2" @click="editPhotos(props.item)">
                            image
                        </v-icon>
                        <!-- <v-icon small @click="deleteItem(props.item)">
                            delete
                        </v-icon> -->
                    </v-flex>
                </td>
            </tr>
        </template>
        <template v-slot:no-data>
            <v-btn color="primary" @click="initialize">Сброс</v-btn>
        </template>
        <template v-slot:no-results>
            <v-alert :value="true" color="error" icon="warning">
                По запросу "{{ search }}" ничего не найдено.
            </v-alert>
        </template>
    </v-data-table>
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
</div>
</template>
<script>
import XLSX from 'xlsx';
export default {
    data: () => ({
        search: '',
        dialogImages: false,
        loading: true,
        files: [],
        deleteImage: false,
        desserts: [],
        editedIndex: -1,
        loadImages: false,
        editedItem: {},
        defaultItem: {},
        select: [],
        keywords: '',
        dateStart: null,
        dateEnd: null,
        loadingSaveBtn: false,
        loaderSaveBtn: null,
        formData: new FormData(),
        chips: [],
        chipsItem: ['Фильтер1', 'Фильтер2'],
        valid: true,
        order: [],
        pagination: {
            sortBy: 'id'
        },
        selected: [],
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
        }
    },
    watch: {
        dialog (val) {
            val || this.close()
        },
        dateStart(after, before) {
            this.initialize();
        },
        dateEnd(after, before) {
            this.initialize();
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
                                // console.log(stats);
                                // console.log(stats);
                                let itemDateStart = vm.$moment(stats.orders.order_start_date, 'YYYY-MM-DD').unix() * 1000;
                                let itemDateEnd = vm.$moment(stats.orders.order_end_date, 'YYYY-MM-DD').unix() * 1000;

                                let dateStart = vm.$moment(vm.dateStart, 'YYYY-MM-DD').unix() * 1000;
                                let dateEnd = vm.$moment(vm.dateEnd, 'YYYY-MM-DD').unix() * 1000;
    
                                if(dateStart >= itemDateStart && dateEnd <= itemDateEnd) {
                                    item.result = 'Занято';
                                    let index = vm.desserts.indexOf(item);
                                    Object.assign(vm.desserts[index], item);
                                    vm.selected.push(item);
                                } 
                                else {
                                    item.result = 'Свободно';
                                    let index = vm.desserts.indexOf(item);
                                    Object.assign(vm.desserts[index], item);
                                    vm.selected.splice(vm.selected.indexOf(item), 1)
                                }
                            });
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
        pickImages () {
            this.$refs.images.click();
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
        },
        resetFilesLoad() {
            this.files = [];
            this.$refs.images.value = '';
            this.formData.delete('file[]');
        },
        save () {
            // console.log(this.selected);
            // console.log(this.editedItem);
            axios({
                method: 'post',
                url: this.params.baseOrders,
                data: {
                    client: this.editedItem,
                    address: this.selected,
                    dateStart: this.dateStart,
                    dateEnd: this.dateEnd
                }
            })
            .then(
                response => {
                    this.initialize();
                    // if (this.editedIndex > -1) {
                    //     Object.assign(this.desserts[this.editedIndex], this.editedItem);
                    // } else {
                    //     this.desserts.push(response.data);
                    // }
                    this.loaderSaveBtn = null;
                    this.loadingSaveBtn = false;
                    // this.close();
                    this.$refs.forms.reset();
                }
            ).catch(error => {
                console.log(error);
            });
        // if (this.$refs.forms.validate() == false) {
            //     this.snackbar = true
            // } 
            // else {
            //     this.loaderSaveBtn = true;
            //     this.loadingSaveBtn = true;
            //     let method = null;
            //     if (this.editedIndex > -1) {
            //         method = 'put'
            //     } else {
            //         method = 'post'
            //     }
            //     axios({
            //         method: method,
            //         url: this.params.baseUrl,
            //         data: this.editedItem
            //     })
            //     .then(
            //         response => {
            //             if (this.editedIndex > -1) {
            //                 Object.assign(this.desserts[this.editedIndex], this.editedItem);
            //             } else {
            //                 this.desserts.push(response.data);
            //             }
            //             this.loaderSaveBtn = null;
            //             this.loadingSaveBtn = false;
            //             this.close();
            //             this.$refs.forms.reset();
            //         }
            //     ).catch(error => {
            //         console.log(error);
            //     })  
            // }
        },
        remove(item) {
            this.chips.splice(this.chips.indexOf(item), 1)
            this.chips = [...this.chips]
        }
    }
}
</script>