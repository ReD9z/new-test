<template>
    <div class="maps-wrap">
        <v-progress-linear height="5" v-show="mapLoader" indeterminate class="maps-loader"></v-progress-linear>
        <div id="orderMaps" v-show="mapsShow"></div>
    </div>
</template>
<style type="text/css">
    #orderMaps {
        width: 100%;
        height: 400px;
        padding: 5px 0;
    }
    .maps-loader {
        position: absolute;
        margin: auto;
        top: 0;
    }
    .maps-wrap {
        position:relative
    }
</style>

<script>
export default {
    props: {
        items: Array,
    },
    data: () => ({
       mapLoader:true,
       mapsShow:false 
    }),
    methods: {
        initializeYandexMap() { 
            let vm = this;
            ymaps.ready().done(function (ym) {
                const myMap = new ym.Map('orderMaps', {
                    center: [64.25926230053398, 108.11586741406248],
                    zoom: 3
                }, {
                    searchControlProvider: 'yandex#search'
                });
            
                vm.items.forEach((element) => {
                    if(element.data != null) {
                        vm.mapsShow = true;
                        const myPlacemark = new ymaps.Placemark(element.coordinates.split(', '), {
                            preset: 'islands#blueDotIcon',
                            balloonContent: element.city + ", " + element.street + ", " + element.house_number,
                            draggable: true,
                        });
                        vm.mapLoader = false;
                        myMap.geoObjects.add(myPlacemark);    
                        
                    }
                    if(vm.mapsShow == false) {
                        vm.mapLoader = false;
                    }
                });
            });
        }
    },
    created () {
        if(this.items.length == 0) {
            this.mapLoader = false;
        } 
        let scriptYandexMap = document.createElement('script');
        scriptYandexMap.setAttribute('src', 'https://api-maps.yandex.ru/2.1/?lang=ru_RU');
        document.head.appendChild(scriptYandexMap);

        scriptYandexMap.addEventListener("load", this.initializeYandexMap);
    
    }
    
}
</script>