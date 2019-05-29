<template>
    <nav aria-label="Page navigation example" :class="[paginate.last_page > 1 ? '' : paginationShow]">
        <ul class="pagination">
            <li class="page-item" v-on:click="page(paginate.prev)" v-bind:class="[!paginate.prev ? 'disabled' : '']">
                <button class="page-link" type="button">Назад</button>
            </li>
            <li class="page-item" v-on:click="page(paginate.first)" v-bind:class="[!paginate.prev ? 'd-none' : '']">
                <button class="page-link" type="button">1</button>
            </li>
            <li class="page-item" v-on:click="page(paginate.prev)" v-bind:class="[paginate.currentPage-1 == 1 || !paginate.prev ? 'd-none' : '']">
                <button class="page-link" type="button">{{paginate.currentPage-1}}</button>
            </li>
            <li class="page-item" v-on:click="page(paginate.urlCurrent)" v-bind:class="[paginate.currentPage ? activeItem : '']">
                <button class="page-link" type="button">{{paginate.currentPage}}</button>
            </li>
            <li class="page-item" v-on:click="page(paginate.next)" v-bind:class="[paginate.currentPage+1 == paginate.last_page || !paginate.next ? 'd-none' : '']">
                <button class="page-link" type="button">{{paginate.currentPage+1}}</button>
            </li>
            <li class="page-item" v-on:click="page(paginate.last)" v-bind:class="[!paginate.next ? 'd-none' : '']">
                <button class="page-link" type="button">{{paginate.last_page}}</button>
            </li>
            <li class="page-item" v-on:click="page(paginate.next)" v-bind:class="[!paginate.next ? 'disabled' : '']">
                <button class="page-link" type="button">Вперед</button>
            </li>
        </ul>
    </nav>
</template>
<script>
export default {
    props: {
        params: Object,
        sorting: Object
    },
    data: () => ({
        activeItem: 'active',
        paginationShow: "d-none",
    }),
    computed : {
        paginate: function(){ 
            return this.$store.getters.paginate;
        },
        spinner: function() {
            return this.$store.getters.spinnerLoader;
        },
    },
    methods: { 
        page (link) {
            this.$store.dispatch('call', {url: link, method: "get", paginate: this.params.pagination, sort: this.sorting}); 
        }
    }
}
</script>