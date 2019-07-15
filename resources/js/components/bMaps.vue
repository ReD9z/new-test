<template>
    <div style="position:relative">
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
                        // console.log(element.city + " " + element.street + " " + element.house_number);
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
        // Установить скрипты для использования яндекс карты
        let scriptYandexMap = document.createElement('script');
        scriptYandexMap.setAttribute('src', 'https://api-maps.yandex.ru/2.1/?lang=ru_RU');
        document.head.appendChild(scriptYandexMap);

        // Инициализировать яндекс карту
        scriptYandexMap.addEventListener("load", this.initializeYandexMap);
    
        // let vm = this;
        // ymaps.ready().done(function (ym) {
        //     const myMap = new ym.Map('YMapsID', {
        //         center: [55.751574, 37.573856],
        //         zoom: 3
        //     }, {
        //         searchControlProvider: 'yandex#search'
        //     });

        //     vm.items.forEach((element) => {
        //         if(element.data != null) {
        //             console.log(element.city + " " + element.street + " " + element.house_number);
        //             ymaps.geocode(element.city + " " + element.street + " " + element.house_number).then(function(res) {
        //                 const coord = res.geoObjects.get(0).geometry.getCoordinates();
        //                 const test = res.metaData.geocoder.request;

        //                 const myPlacemark = new ymaps.Placemark(coord, {
        //                     preset: 'islands#blueDotIcon',
        //                     balloonContent: test,
        //                     draggable: true
        //                 });

        //                 /* Событие dragend - получение нового адреса */
        //                 // myPlacemark.events.add('dragend', function(e){
        //                 //     var cord = e.get('target').geometry.getCoordinates();
        //                 //     $('#ypoint').val(cord);
        //                 //     ymaps.geocode(cord).then(function(res) {
        //                 //         var data = res.geoObjects.get(0).properties.getAll();
        //                 //         $('#address').val(data.text);
        //                 //     });
        //                 // });
                        
        //                 myMap.geoObjects.add(myPlacemark);    
        //                 myMap.setCenter(coord, 15);
        //             });
        //         }
        //     });


        //     // jQuery.getJSON('data.json', function (json) {
              
        //     // var geoObjects = ym.geoQuery(json) .addToMap(myMap) .applyBoundsToMap(myMap, {checkZoomRange: true }); });
        // });
    }
    
}
</script>