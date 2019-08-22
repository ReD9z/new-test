<template>
    <div class="maps-wrap">
        <v-progress-linear height="5" v-show="mapLoader" indeterminate class="maps-loader"></v-progress-linear>
        <div id="orderMaps" v-show="mapsShow"></div>
        <!-- <v-progress-circular indeterminate class="maps-loader" v-show="mapLoader"></v-progress-circular> -->
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
                    center: [51.551407, 46.036561],
                    zoom: 11
                }, {
                    searchControlProvider: 'yandex#search'
                });
            
                vm.items.forEach((element) => {
                    if(element.data != null) {
                        vm.mapsShow = true;
                        ymaps.geocode(element.city + ", " + element.street + ", " + element.house_number).then(function(res) {
                            const coord = res.geoObjects.get(0).geometry.getCoordinates();
                            const address = res.metaData.geocoder.request;

                            const myPlacemark = new ymaps.Placemark(coord, {
                                preset: 'islands#blueDotIcon',
                                balloonContent: address,
                                draggable: true,
                            });
                            vm.mapLoader = false;
                            myMap.geoObjects.add(myPlacemark);    
                        });
                    }
                    if(vm.mapsShow == false) {
                        vm.mapLoader = false;
                    }
                });
            });
        }
    },
    created () {
        let scriptYandexMap = document.createElement('script');
        scriptYandexMap.setAttribute('src', 'https://api-maps.yandex.ru/2.1/?lang=ru_RU');
        document.head.appendChild(scriptYandexMap);

        scriptYandexMap.addEventListener("load", this.initializeYandexMap);
    }
    
}
</script>