<template>
    <div class="maps-wrap">
        <v-progress-linear height="5" v-show="mapLoader" indeterminate class="maps-loader"></v-progress-linear>
        <div v-show="!mapLoader" id="orderMaps"></div>
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
        city: String
    },
    data: () => ({
        mapLoader:true,
        newItems: {
            "type": "FeatureCollection",
            "features" : []
        },
        mapsShow:false 
    }),
    methods: {
        initializeYandexMap() { 
            let vm = this;
            ymaps.ready().done(function (ym) {
                var myMap = new ymaps.Map('orderMaps', {
                    center: vm.city.split(', '),
                    zoom:11
                }, {
                        searchControlProvider: 'yandex#search'
                }),
                objectManager = new ymaps.ObjectManager();
                myMap.geoObjects.add(objectManager);
              
                objectManager.add(vm.newItems);
                vm.mapLoader = false;
            });
            
        }
    },
    created () {
        this.items.forEach((item, key) => {
            this.newItems.features.push(
                {
                    "type": "Feature",
                    "id": key,
                    "geometry": {
                        "type": "Point",
                        "coordinates": item.data.coordinates.split(', ')
                    },
                    "properties": {
                        "balloonContent": item.city + ", " + item.street + ", " + item.house_number,
                        "clusterCaption": "Метка с iconContent",
                        "hintContent": item.city + ", " + item.street + ", " + item.house_number,
                    },
                    "options": {
                        "preset": item.entrancesStatus == 1 ? "islands#redIcon" : "islands#greenIcon"
                    }
                },
            );
        });
        if(this.items.length == 0) {
            this.mapLoader = false;
        } 
        let scriptYandexMap = document.createElement('script');
        scriptYandexMap.setAttribute('src', 'https://api-maps.yandex.ru/2.1/?lang=ru_RU');
        document.head.appendChild(scriptYandexMap);

        scriptYandexMap.addEventListener("load", this.initializeYandexMap);
        // console.log(this.newItems);
    
    }
    
}
</script>