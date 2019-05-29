<template>
    <b-table :params="params"></b-table>
</template>
<script>
export default {
    data: () => ({
        params: {
            baseUrl: 'api/article',
           
            
        }
    })
}
</script>