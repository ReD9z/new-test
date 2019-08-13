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
            baseUrl: 'api/types_to_work',
            headers: [
                {
                    text: 'Название',
                    align: 'left',
                    sortable: true,
                    input: "text",
                    value: 'title',
                    validate: 'required'
                }
            ],
            searchValue: ['title'],
            search: true,
            pagination: true,
            excel: false
        }
    })
}
</script>