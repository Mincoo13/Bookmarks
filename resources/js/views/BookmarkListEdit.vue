<template>
    <div>
        <form @submit.prevent="edit()">
            <button type="submit">Upraviť</button>
        </form>
    </div>
</template>

<script>
    export default {
        name: "BookmarkListEdit"
    }
</script>

<style scoped>

</style>