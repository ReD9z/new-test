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
            headers: [
                {
                    text: 'Город',
                    align: 'left',
                    sortable: true,
                    value: 'city_id',
                    selectText: 'name',
                    TableGetIdName: 'city',
                    selectApi: 'api/cities_to_works',
                    input: "select",
                },
                { 
                    text: 'Район', 
                    input: "text",
                    value: 'area' 
                },
                { 
                    text: 'Улица', 
                    input: "text",
                    value: 'street' 
                },
                { 
                    text: 'Номер дома', 
                    input: "text",
                    value: 'house_number' 
                },
                { 
                    text: 'Количество подъездов', 
                    input: "text",
                    value: 'number_entrances' 
                },
                { 
                    text: 'Управляющая компания', 
                    input: "text",
                    value: 'management_company' 
                },
                { 
                    text: 'Параметры',
                    sortable: false,
                    edit: true,
                }
            ],
            search: true,
            pagination: true,
            excel: true
        }
    })
}
</script>