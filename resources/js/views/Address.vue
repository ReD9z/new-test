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
                    input: "text",
                    value: 'city'
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