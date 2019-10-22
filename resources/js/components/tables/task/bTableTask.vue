<template>
<div>
    <v-dialog
      v-model="addClient"
      max-width="700px"
    >
        <b-add-client></b-add-client>
    </v-dialog>
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
        <v-btn v-show="hideElem()" color="green" large class="mb-2 white--text" @click.stop="dialog = !dialog"><v-icon left>add</v-icon>Создать задачу</v-btn>
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
                        <div v-if="param.input == 'text' && param.value != 'comment'">
                            <v-text-field :data-vv-as="'`'+param.text+'`'" :data-vv-name="param.value" :error-messages="errors.collect(param.value)" v-validate="param.validate" v-model="editedItem[param.value]" :label="param.text" v-if="param.input !== 'images' && param.edit != true" xs12 required></v-text-field>
                        </div>
                        <div v-if="param.value == 'comment'" v-show="hideComment()">
                            <v-text-field :data-vv-as="'`'+param.text+'`'" :data-vv-name="param.value" :error-messages="errors.collect(param.value)" v-validate="param.validate" v-model="editedItem[param.value]" :label="param.text" v-if="param.input !== 'images' && param.edit != true" xs12 required></v-text-field>
                        </div>
                        <div v-if="param.input == 'select' && param.value != 'client_id'" v-show="hideElem()">
                            <div v-if="param.selectText == 'orderClient'">
                                <v-layout justify-space-around row>
                                    <v-autocomplete
                                        :items="orderDate"
                                        v-model="editedItem[param.value]"
                                        :item-text="param.selectText"
                                        item-value="id"
                                        :label="param.text"
                                        :data-vv-as="'`'+param.text+'`'" 
                                        :data-vv-name="param.value" 
                                        :error-messages="errors.collect(param.value)" 
                                        v-validate="param.validate"
                                    >
                                        <template v-slot:append-outer>
                                            <v-menu
                                                v-model="menu"
                                                :close-on-content-click="false"
                                                offset-x
                                            >
                                                <template v-slot:activator="{ on }">
                                                    <v-btn flat icon v-on="on">
                                                        <v-icon>date_range</v-icon>
                                                    </v-btn>
                                                </template>
                                                <v-card>
                                                    <v-layout pa-4 row wrap>
                                                        <v-flex xs12 lg5>
                                                            <v-menu
                                                                v-model="menu1"
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
                                                                <v-date-picker locale="ru" :first-day-of-week="1" v-model="dateStart" no-title @input="menu1 = false"></v-date-picker>
                                                            </v-menu>
                                                        </v-flex>
                                                        <v-flex xs12 lg5>
                                                            <v-menu
                                                                v-model="menu2"
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
                                                                <v-date-picker locale="ru" :first-day-of-week="1" v-model="dateEnd" no-title @input="menu2 = false"></v-date-picker>
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
                                                        <v-btn flat @click="menu = false">Закрыть</v-btn>
                                                        <v-btn color="primary" flat @click="dateFilter()">Применить</v-btn>
                                                    </v-card-actions>
                                                </v-card>
                                            </v-menu> 
                                        </template>
                                    </v-autocomplete>
                                </v-layout>
                            </div>
                            <div v-else>
                                <div v-for="item in select" :key="item[0]">
                                    <div v-if="item.url == param.selectApi">
                                        <v-layout justify-space-around row>
                                            <v-flex
                                                grow
                                                pa-1
                                            >
                                                <v-autocomplete
                                                    :items="item.data"
                                                    v-model="editedItem[param.value]"
                                                    :item-text="param.selectText"
                                                    item-value="id"
                                                    :label="param.text"
                                                    :data-vv-as="'`'+param.text+'`'" 
                                                    :data-vv-name="param.value" 
                                                    :error-messages="errors.collect(param.value)" 
                                                    v-validate="param.validate"
                                                >
                                                </v-autocomplete>
                                            </v-flex>
                                        </v-layout>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="param.input == 'select' && param.value == 'client_id'" v-show="hideElem()">
                            <div v-if="param.selectText == 'orderClient'">
                                <v-layout justify-space-around row>
                                    <v-autocomplete
                                        :items="orderDate"
                                        v-model="editedItem[param.value]"
                                        :item-text="param.selectText"
                                        item-value="id"
                                        :label="param.text"
                                        :data-vv-as="'`'+param.text+'`'" 
                                        :data-vv-name="param.value" 
                                        :error-messages="errors.collect(param.value)" 
                                        v-validate="param.validate"
                                    >
                                        <template v-slot:append-outer>
                                            <v-menu
                                                v-model="menu"
                                                :close-on-content-click="false"
                                                offset-x
                                            >
                                                <template v-slot:activator="{ on }">
                                                    <v-btn flat icon v-on="on">
                                                        <v-icon>date_range</v-icon>
                                                    </v-btn>
                                                </template>
                                                <v-card>
                                                    <v-layout pa-4 row wrap>
                                                        <v-flex xs12 lg5>
                                                            <v-menu
                                                                v-model="menu1"
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
                                                                <v-date-picker locale="ru" :first-day-of-week="1" v-model="dateStart" no-title @input="menu1 = false"></v-date-picker>
                                                            </v-menu>
                                                        </v-flex>
                                                        <v-flex xs12 lg5>
                                                            <v-menu
                                                                v-model="menu2"
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
                                                                <v-date-picker locale="ru" :first-day-of-week="1" v-model="dateEnd" no-title @input="menu2 = false"></v-date-picker>
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
                                                        <v-btn flat @click="menu = false">Закрыть</v-btn>
                                                        <v-btn color="primary" flat @click="dateFilter()">Применить</v-btn>
                                                    </v-card-actions>
                                                </v-card>
                                            </v-menu> 
                                        </template>
                                    </v-autocomplete>
                                </v-layout>
                            </div>
                            <div v-else>
                                <div v-for="item in select" :key="item[0]">
                                    <div v-if="item.url == param.selectApi">
                                        <v-layout wrap row>
                                            <v-flex
                                                xs10
                                            >
                                                <v-autocomplete
                                                    :items="item.data"
                                                    v-model="editedItem[param.value]"
                                                    :item-text="param.selectText"
                                                    item-value="id"
                                                    :label="param.text"
                                                    :data-vv-as="'`'+param.text+'`'" 
                                                    :data-vv-name="param.value" 
                                                    :error-messages="errors.collect(param.value)" 
                                                    v-validate="param.validate"
                                                >
                                                </v-autocomplete>
                                            </v-flex>
                                            <v-flex
                                                xs2
                                            >
                                                <v-btn
                                                    color="primary"
                                                    dark
                                                    block
                                                    small
                                                    icon
                                                    @click.stop="addClient = true"
                                                >
                                                    <v-icon small>add</v-icon>
                                                </v-btn>
                                            </v-flex>
                                        </v-layout>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="param.input == 'date'" v-show="hideElem()">
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
                                        @blur="editedItem[param.value] = parseDate(picker)"
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
                        <div v-if="param.input == 'password'">
                            <v-text-field :type="param.value" v-model="editedItem[param.value]" :label="param.text" v-if="param.input !== 'images' && param.edit != true" xs12></v-text-field>
                        </div>
                        <div v-if="param.value == 'status'">
                            <v-autocomplete
                                :items="param.data"
                                v-model="editedItem[param.value]"
                                item-text="text"
                                item-value="status"
                                :label="param.text"
                                :data-vv-as="'`'+param.text+'`'" 
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
                            <v-flex v-for="(file, key) in editedItem.files" :key="key" xs4 d-flex>
                                <v-card flat tile class="d-flex pr-1 pb-1">
                                    <v-img :src="'/storage/' + file.url" :lazy-src="'/storage/' + file.url" aspect-ratio="1" class="grey lighten-2">
                                        <template v-slot:placeholder>
                                            <v-layout fill-height align-center justify-center ma-0>
                                                <v-progress-circular indeterminate color="grey lighten-5"></v-progress-circular>
                                            </v-layout>
                                        </template>
                                        <template>
                                            <v-layout fill-height right top ma-0>
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
        <div v-show="params.filter">
            <v-chip :items="chips" v-for="(item, key) in chips" :key="key" close @input="remove(item)">{{item}}</v-chip>
            <v-chip v-for="(item, key) in chipsStatus" :key="'K'+ key" close @input="removeStatus(item)">{{item}}</v-chip>
            <v-chip v-if="dateStartClient" close @input="resetDate()">Начало {{dateStartClient}}</v-chip>
            <v-chip v-if="dateEndClient" close @input="resetDate()">Конец {{dateEndClient}}</v-chip>
        </div>
        <v-icon v-if="showRoles()" v-show="params.filter">filter_list</v-icon>
        <v-menu v-if="showRoles()" v-show="params.filter" :close-on-content-click="false" :nudge-width="200" offset-y bottom left>
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
                            multiple
                            label="Статус"
                        ></v-combobox>
                    </v-flex>
                    <v-flex class="px-3" xs4>
                        <v-combobox
                            v-model="chips"
                            :items="chipsItem"
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
                :class="['column sortable', pagination.descending ? 'desc' : 'asc', header.value === pagination.sortBy ? 'active' : '' , 'text-xs-left', header.visibility]"
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
                <v-flex v-if="param.input !== 'images'">
                    <v-flex v-if="param.value === 'orders_id'">
                        <v-flex v-if="param.selectText">
                            <a v-if="hideElem()" :href="'/orders-address/'+props.item['orders_id']">{{props.item[param.TableGetIdName]}}</a>
                            <span v-else>{{props.item[param.TableGetIdName]}}</span>
                        </v-flex>
                    </v-flex>
                    <v-flex v-else>
                        <v-flex v-if="param.value === 'status'">{{props.item[param.title]}}</v-flex>
                        <v-flex v-else-if="param.selectText">{{props.item[param.TableGetIdName]}}</v-flex>
                        <v-flex v-else>{{props.item[param.value]}}</v-flex>
                    </v-flex>
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
        statusFilter: ['Завершена', 'В работе'],
        search: '',
        dialog: false,
        addClient: false,
        dialogImages: false,
        loading: true,
        loadingExcel: false,
        chipsStatus: [],
        files: [],
        dateStartNew: null,
        dateEndNew: null,
        deleteImage: false,
        desserts: [],
        editedIndex: -1,
        dateStartClient: vm.formatDate(new Date().toISOString().substr(0, 10)),
        dateEndClient: vm.formatDate(new Date().toISOString().substr(0, 10)),
        loadImages: false,
        editedItem: {},
        defaultItem: {},
        select: [],
        loadingSaveBtn: false,
        loaderSaveBtn: null,
        formData: new FormData(),
        chips: [],
        chipsItem: [],
        picker: new Date().toISOString().substr(0, 10),
        valid: true,
        pagination: {
            sortBy: 'id'
        },
        selected: [],
        menu: false,
        dateStartFormatted: null,
        dateEndFormatted: null,
        dateStart: null,
        dateEnd: null,
        menu1: false,
        menu2: false,
        menu4: false,
        menu5: false,
        orderDate: [],
        orderFull: []
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
        dateStartNew(val) {
            this.dateStartClient = this.formatDate(this.dateStartNew);
            this.initialize();
        },
        dateEndNew(val) {
            this.dateEndClient = this.formatDate(this.dateEndNew);
            this.initialize();
        },
        chips(val) {
            this.initialize();
        },
        chipsStatus() {
            this.initialize();
        }
    },
    async created () {
        await this.initialize();
        await this.selectStatus();
        await this.getFiltered();
    },
    methods: {
        async getFiltered() {
            if(this.params.filter) {
                await this.params.filter.forEach((item) => {
                    axios({
                        method: 'get',
                        url: item.api,
                        params: {
                            city: this.roleUserCity(),
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
                })
            }
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
                    return searchTest.test([item.statusName]); 
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
                    return searchTest.test([item.client]);
                });
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
        showRoles() {
            if(this.isLoggedUser.moderators || this.isLoggedUser.managers) {
                return false;
            }
            if(!this.isLoggedUser.moderators || !this.isLoggedUser.managers) {
                return true;
            }
        },
        hideElem() {
            if(this.isLoggedUser.installers || this.isLoggedUser.managers) {
                return false;
            }
            if(!this.isLoggedUser.installers || !this.isLoggedUser.managers) {
                return true;
            }
        },
        hideComment() {
            if(this.isLoggedUser.managers) {
                return false;
            }
            if(!this.isLoggedUser.installers || !this.isLoggedUser.managers) {
                return true;
            }
        },
        formatDate (date) {
            if (!date) return null

            const [year, month, day] = date.split('-')
            return `${day}.${month}.${year}`
        },
        dateFilter () {
            let vm = this;
            this.orderDate = this.orderFull;
            this.menu = false;
            let orderData = [];
            if(this.dateStart == null || this.dateEnd == null) {
                this.orderDate = this.orderFull;
            } else {
                this.orderDate.map(function (item) {
                    let itemDateStart = vm.$moment(item.order_start_date, 'DD.MM.YYYY').unix() * 1000;
                    let itemDateEnd = vm.$moment(item.order_start_date, 'DD.MM.YYYY').unix() * 1000;
                    let dateStart = vm.$moment(vm.formatDate(vm.dateStartNew), 'DD.MM.YYYY').unix() * 1000;
                    let dateEnd = vm.$moment(vm.formatDate(vm.dateEndNew), 'DD.MM.YYYY').unix() * 1000;
            
                    if(dateStart >= itemDateStart && dateEnd <= itemDateEnd || dateStart <= itemDateStart && dateEnd >= itemDateEnd) {
                        orderData.push(item);
                    } 
                });
                this.orderDate = orderData;
            } 
        },
        resetDate() {
            this.dateStartNew = null;
            this.dateEndNew = null;
            this.dateStartClient = null;
            this.dateEndClient = null;
            this.initialize();
            this.chips = [];
            this.orderDate = this.orderFull;
            this.chipsStatus = [];
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
            this.loading = true;
            this.search = '';
            this.dateStartNew = null;
            this.dateEndNew = null;
            this.chips = [];
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
        initialize () {
            axios({
                method: 'get',
                url: this.params.baseUrl,
                params: {
                    city: this.roleUserCity(),
                    user: this.roleUserId()
                }
            })
            .then(
                response => {
                    this.desserts = response.data;
                    // console.log(response.data);
                    let vm = this;
                    if(this.params.baseUrl == '/api/tasks') {
                        if(this.dateStart && this.dateEnd) {
                            this.desserts = this.desserts.filter(function (item) {
                                let itemDateStart = vm.$moment(item.task_date_completion, 'DD.MM.YYYY').unix() * 1000;
                                let dateStart = vm.$moment(vm.formatDate(vm.dateStart), 'DD.MM.YYYY').unix() * 1000;
                                let dateEnd = vm.$moment(vm.formatDate(vm.dateEnd), 'DD.MM.YYYY').unix() * 1000;
                                if(itemDateStart >= dateStart && itemDateStart <= dateEnd) {
                                    return item;
                                } 
                            });
                        } 
                    } 
                    if(!this.params.baseUrl == '/api/tasks') {
                        if(this.dateStartClient && this.dateEndClient) {
                            this.desserts = this.desserts.filter(function (item) {
                                let itemDateStart = vm.$moment(item.task_date_completion, 'DD.MM.YYYY').unix() * 1000;
                                let dateStart = vm.$moment(vm.formatDate(vm.dateStartNew), 'DD.MM.YYYY').unix() * 1000;
                                let dateEnd = vm.$moment(vm.formatDate(vm.dateEndNew), 'DD.MM.YYYY').unix() * 1000;
                                if(itemDateStart >= dateStart && itemDateStart <= dateEnd) {
                                    return item;
                                } 
                            });
                        }
                    }
                    
                    this.filteredItems(this.desserts);
                    this.filtered(this.desserts); 
                    this.filteredStatus(this.desserts);
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
            return `${day}.${month}.${year}`
        },
        selectStatus() {
            this.params.headers.forEach(element => {
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
                                if(element.selectText == 'orderClient') {
                                    this.orderDate = res.data;
                                    this.orderFull = res.data;
                                }else {
                                    this.select.push(
                                        {
                                            data: res.data,
                                            url: element.selectApi
                                        }
                                    );
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
                            images: res.data.files,
                            city: this.isLoggedUser.installers.city_id
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
        downloadExcel() {
            if(this.desserts.length > 0) {
                let map = this.desserts.map((item)=> {
                    let address = [];
                    item.orderAddresses.map((item2, key) => {
                        address += "г." + item2.address.cities.name 
                        address += ", " + item2.address.areas.name + ","
                        address += " ул. " + item2.address.street + ","
                        address += " дом " + item2.address.house_number + ","
                        address += " количество подъездов " + item2.address.number_entrances + ","
                        address += " управляющая компания " + item2.address.management_company + " ";
                    });

                    return {
                        "Название организации клиента" : item.orders,
                        "Дата выполнения задачи"  : item.task_date_completion,
                        "Тип работы"  : item.types,
                        "Комментарий" : item.comment,
                        "Адреса": address
                    }
                });
                
                let ws = XLSX.utils.json_to_sheet(map, {raw:true});
                if(!ws['!cols']) ws['!cols'] = [];
                for (let i = 0; i < 5; i++) {
                    ws['!cols'][i] = { wch: 28 };
                }
                let wb = XLSX.utils.book_new();
              
                XLSX.utils.book_append_sheet(wb, ws, "Задачи");
                XLSX.writeFile(wb, "Задачи.xlsx");
            }
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
        removeImg(data) {
            this.loadImages = true;
            axios.post('/api/files/remove', data)
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
                        // this.desserts.splice(index, 1);
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
        resetFilesLoad() {
            this.files = [];
            this.$refs.images.value = '';
            this.formData.delete('file[]');
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
        },
        dataAdd() {
            if(this.isLoggedUser.managers) {
                // this.editedItem['city_id'] = this.isLoggedUser.managers.city_id;
            }
            if(this.isLoggedUser.moderators) {
                // this.editedItem['city_id'] = this.isLoggedUser.moderators.city_id;
                // this.editedItem['moderator_id'] = this.isLoggedUser.moderators.id;
            }
        }
    },
    mounted() {
        this.dataAdd();
    }
}
</script>