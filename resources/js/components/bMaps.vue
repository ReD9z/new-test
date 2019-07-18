<template>
    <div style="position:relative" v-show="this.items.lenght > 0">
        <div id="YMapsID"></div>
        <v-progress-circular indeterminate class="maps-loader" v-show="mapLoader"></v-progress-circular>
    </div>
</template>
<style type="text/css">
    #YMapsID {
        width: 100%;
        height: 400px;
        padding: 0;
        margin: 0;
    }
    .maps-loader {
        position: absolute;
        margin: auto;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
    }
</style>

<script>
export default {
    props: {
        items: Array,
    },
    data: () => ({
       mapLoader:true, 
    }),
    methods: {
        initializeYandexMap() { 
            let vm = this;
            ymaps.ready().done(function (ym) {
                const myMap = new ym.Map('YMapsID', {
                    center: [51.551407, 46.036561],
                    zoom: 11
                }, {
                    searchControlProvider: 'yandex#search'
                });
                vm.items.forEach((element) => {
                    if(element.data != null) {
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
                });
            });
        }
    },
    created () { 
        if(this.items.lenght > 0) {
            let scriptYandexMap = document.createElement('script');
            scriptYandexMap.setAttribute('src', 'https://api-maps.yandex.ru/2.1/?lang=ru_RU');
            document.head.appendChild(scriptYandexMap);
    
            scriptYandexMap.addEventListener("load", this.initializeYandexMap);
        } else {
            this.mapLoader = false;
        }

    }
    
}
</script>