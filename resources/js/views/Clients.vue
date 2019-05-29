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
            baseUrl: 'api/clients',
            fields: [
                {   
                    key: "city_id",
                    keyValue: "city",  
                    label: "Город",
                    sort: true,
                    edit: true,
                    type: "text",
                    input: "select",
                    selectApi: 'api/cities_to_works/allCities',
                    selectKey: 'name',
                },
                {   
                    key: "legal_name", 
                    label: "Юридическое название",
                    sort: true, 
                    edit: true,
                    type: "text",
                    input: "input",
                },
                 {   
                    key: "actual_title", 
                    label: "Фактическое название",
                    sort: true, 
                    edit: true,
                    type: "text",
                    input: "input"
                },
                {   
                    key: "legal_address", 
                    label: "Юридический адрес",
                    sort: true, 
                    edit: true,
                    type: "text",
                    input: "input"
                },
                 {   
                    key: "actual_address", 
                    label: "Фактический адрес",
                    sort: true, 
                    edit: true,
                    type: "text",
                    input: "input"
                },
                {   
                    key: "bank_name", 
                    label: "Название банка",
                    sort: true, 
                    edit: true,
                    type: "text",
                    input: "input"
                },
                 {   
                    key: "bik", 
                    label: "БИК",
                    sort: true, 
                    edit: true,
                    type: "text",
                    input: "input"
                },
                {   
                    key: "cor_score", 
                    label: "Кор. счёт",
                    sort: true, 
                    edit: true,
                    type: "text",
                    input: "input"
                },
                {   
                    key: "settlement_account", 
                    label: "Расчётный счёт",
                    sort: true, 
                    edit: true,
                    type: "text",
                    input: "input"
                }
            ],
            search: true,
            pagination: true,
            excel: false
        }
    })
}
</script>