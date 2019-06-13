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
                    value: 'title',
                    input: "text",
                    validate: [
                        v => !!v || 'Поле "Имя" обязательно',
                    ],
                },
                { 
                    text: 'Текст', 
                    value: 'text',
                    input: "text",
                    validate: [
                        v => !!v || 'Поле "Текст" обязательно',
                    ],
                },
                { 
                    text: 'Параметры',
                    sortable: false,
                    edit: true,
                }
            ],
            filters: [

            ],
            search: true,
            pagination: true,
            excel: true
        }
        
    })
}
</script>