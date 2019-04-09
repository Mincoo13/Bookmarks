<template>
    <div>
        <h1>
           Vytvoriť nový zoznam
        </h1>
        <h1>Moje zoznamy</h1>
        <div v-for="bookmarklist in bookmarklists">
            <router-link :to="{ path: '/bookmark-lists/' + bookmarklist.id }"> {{ bookmarklist.name }} </router-link>
            <p>{{ bookmarklist.count }}</p>
            <button>Zmazať</button>
            <hr>
        </div>
    </div>
</template>

<script>
    import auth from "../auth/index.js";

    export default {
        data(){
            return {
                auth: auth,
                bookmarklists: [],
                bookmarks: [],
            }
        },
        mounted(){
            this.getBookmarkLists();
        },
        methods: {
            getBookmarkLists(){
                axios
                    .get('/bookmark-lists',
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.bookmarklists = response.data;
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            }
        }
    }
</script>

<style scoped>

</style>