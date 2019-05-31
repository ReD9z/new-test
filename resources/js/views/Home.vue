<template>
    <b-table :params="params"></b-table>
</template>
<script>
export default {
    data: () => ({
        params: {
            baseUrl: 'api/article',
            headers: [
                {
                    text: 'Название',
                    align: 'left',
                    sortable: true,
                    value: 'title'
                },
                { 
                    text: 'Текст', 
                    value: 'text' 
                },
                { 
                    text: 'Изображения',
                    value: 'files', 
                    sortable: false,
                    input: "images"
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