<template>
<div>
    <v-toolbar color="#fff" fixed app clipped-righ>
        <v-toolbar-title>Атом</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn
            color="blue-grey"
            class="white--text"
            :loading="loadingExcel"
            :disabled="loadingExcel"
            @click='pickExcel'
            v-show="params.excel"
        >
            Добавить Excel
            <v-icon right dark>cloud_upload</v-icon>
            <input
                type="file"
                style="display: none"
                ref="excel"
                accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                @change="elementLoadToFile"
                multiple
            >
        </v-btn>
        <v-btn color="green" dark class="mb-2" @click.stop="dialog = !dialog">Добавить</v-btn>
    </v-toolbar>
    <v-navigation-drawer v-model="dialog" right temporary fixed>
        <v-card height="100%">
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
                                        hint="DD-MM-YYYY формат"
                                        persistent-hint
                                        @blur="editedItem[param.value] = parseDate(picker)"
                                        prepend-icon="event"
                                        :label="param.text"
                                        v-on="on"
                                    ></v-text-field>
                                </template>
                                <v-date-picker v-model="picker" no-title :value="editedItem[param.value]" @input="param.close = false"></v-date-picker>
                            </v-menu>
                        </div>
                        <div v-if="param.input == 'password'">
                            <v-text-field :type="param.value" v-model="editedItem[param.value]" :label="param.text" v-if="param.input !== 'images' && param.edit != true" xs12></v-text-field>
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
            <template v-slot:activator="{ on }">
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
    <v-data-table :headers="params.headers" :items="desserts" :search="search" :loading="loading" class="elevation-1">
        <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>
        <template v-slot:items="props">
            <td v-for="(param, key) in params.headers" :key="key">
                <v-flex v-if="param.input !== 'images'">
                    <v-flex v-if="param.selectText">{{props.item[param.TableGetIdName]}}</v-flex>
                    <v-flex v-else>{{props.item[param.value]}}</v-flex>
                </v-flex>
                <v-flex v-if="param.edit">
                    <v-icon v-if="props.item.files" small class="mr-2" @click="editPhotos(props.item)">
                        image
                    </v-icon>
                    <v-icon small class="mr-2" @click="editItem(props.item)">
                        edit
                    </v-icon>
                    <v-icon small @click="deleteItem(props.item)">
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
    </v-data-table>
</div>
</template>
<script>
import XLSX from 'xlsx';
export default {
    data: () => ({
        search: '',
        dialog: false,
        dialogImages: false,
        loading: true,
        loadingExcel: false,
        files: [],
        deleteImage: false,
        desserts: [],
        editedIndex: -1,
        loadImages: false,
        editedItem: {},
        defaultItem: {},
        select: [],
        loadingSaveBtn: false,
        loaderSaveBtn: null,
        formData: new FormData(),
        chips: [],
        chipsItem: ['Фильтер1', 'Фильтер2'],
        picker: new Date().toISOString().substr(0, 10),
        pagination: {
            sortBy: 'id'
        },
        valid: true
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
        }
    },
    created () {
        this.initialize();
        this.selectStatus();
    },
    methods: {
        initialize () {
            axios({
                method: 'get',
                url: this.params.baseUrl
            })
            .then(
                response => {
                    this.desserts = response.data;
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
            this.params.headers.forEach(element => {
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
                axios({
                    method: 'post',
                    url: vm.params.baseUrl +'/excel',
                    data: excelRows
                })
                .then(
                    response => {
                        let array = Array.from(response.data.data);
                        array.forEach(element => {
                            vm.desserts.push(element);
                        });
                        setTimeout(() => ( vm.loadingExcel = false), 1000);
                    }
                ).catch(error => {
                    console.log(error);
                });
               
                file.value = '';
            };
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
        pickExcel () {
            this.$refs.excel.click();
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
            if (this.$refs.forms.validate() == false) {
                this.snackbar = true
            } 
            else {
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
                            Object.assign(this.desserts[this.editedIndex], this.editedItem);
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
            }
        },
        remove(item) {
            this.chips.splice(this.chips.indexOf(item), 1)
            this.chips = [...this.chips]
        }
    }
}
</script>