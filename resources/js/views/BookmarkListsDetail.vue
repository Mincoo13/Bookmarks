<template>
    <div>
        <h1>{{ bookmarklist.name }}</h1>
        <p>{{ bookmarklist.created_at }}</p>
        <router-link tag="button" :to="{ path:'/bookmark-lists/'+bookmarklist.id+'/edit' }">Upraviť</router-link>
    </div>
</template>

<script>
    import auth from "../auth/index.js";

    export default {
        data(){
            return{
                auth: auth,
                bookmarks: [],
                bookmarklist: [],
                id: null,
            }
        },
        mounted(){
            this.getId();
            this.getBookmarkList(this.id);
        },
        methods: {
            getBookmarkList(id){
                axios
                    .get('/bookmark-lists/'+id,
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.bookmarklist = response.data;
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            getId() {
                var currentUrl = window.location.href;
                var strings = currentUrl.split("/");
                this.id = strings[5];
            },
        }
    }
</script>

<style scoped>

</style>