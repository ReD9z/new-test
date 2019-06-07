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
            baseUrl: 'api/cities_to_works',
            headers: [
                {
                    text: 'Название',
                    align: 'left',
                    sortable: true,
                    value: 'name',
                    input: "text"
                },
                { 
                    text: 'Параметры', 
                    sortable: false,
                    edit: true,
                }
            ],
            search: true,
            pagination: true,
            excel: false
        }
    })
}
</script>