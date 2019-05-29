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
            baseUrl: 'api/managers',
            fields: [
                {   
                    key: "user_id", 
                    label: "Имя",
                    sort: true, 
                    edit: true,
                    type: "text",
                    input: "input"
                },
                {   
                    key: "city_id", 
                    label: "Email",
                    sort: true, 
                    edit: true,
                    type: "text",
                    input: "input"
                },
            ],
            search: true,
            pagination: true,
            excel: false
        }
    })
}
</script>