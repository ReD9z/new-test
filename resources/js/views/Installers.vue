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
            baseUrl: 'api/installers',
            fields: [
                {   
                    key: "name",
                    // keyValue: "name", 
                    label: "Имя",
                    sort: true, 
                    edit: true,
                    type: "text",
                    input: "input"
                },
                {   
                    key: "email", 
                    label: "Email",
                    sort: true, 
                    edit: true,
                    type: "text",
                    input: "input"
                },
                {   
                    key: "password", 
                    label: "Пароль",
                    sort: false, 
                    edit: false,
                    type: "password",
                    input: "input"
                },
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
                // {   
                //     key: "moderator_id", 
                //     label: "Пароль",
                //     sort: false, 
                //     edit: false,
                //     type: "text",
                //     input: "input"
                // }
            ],
            search: true,
            pagination: true,
            excel: false
        }
    })
}
</script>