<template>
    <div class="card mb-4 mt-4">
        <div class="col-md-12 mb-4 mt-4">
            <b-table :params="params"></b-table>
        </div>
    </div>
</template>

<script>
export default {
    data: () => ({
        params: {
            baseUrl: 'api/address',
            fields: [
                {   
                    key: "city", 
                    label: "Город",
                    sort: true, // false состояние
                    edit: true, // false состояние
                    type: "text", // Сделать отображение картинок
                    input: "input"
                },
                {   
                    key: "area", 
                    label: "Район",
                    sort: true, // false состояние
                    edit: true, // false состояние
                    type: "text",
                    input: "input"
                },
                {   
                    key: "street", 
                    label: "Улица",
                    sort: true, // false состояние
                    edit: true, // false состояние
                    type: "text",
                    input: "input"
                },
                {   
                    key: "house_number", 
                    label: "Номер дома",
                    sort: true, // false состояние
                    edit: true, // false состояние
                    type: "text",
                    input: "input"
                },
                {   
                    key: "number_entrances", 
                    label: "Количество подъездов",
                    sort: true, // false состояние
                    edit: true, // false состояние
                    type: "text",
                    input: "input"
                }
            ],
            excel: true,
            search: true,
            pagination: true,
        }
    })
}
</script>