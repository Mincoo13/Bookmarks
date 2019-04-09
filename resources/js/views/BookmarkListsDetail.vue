<template>
    <div>

        <h1>{{ bookmarklist.name }}</h1>
        <p>{{ bookmarklist.created_at }}</p>

        <form @submit.prevent="editBookmarkList(id)">
            <div v-if="message">
                <p>{{ message }}</p>
            </div>
            <input type="text" v-model="name">
            <input v-if="isVisible != 1" type="checkbox" v-model="isVisible">
            <input v-else type="checkbox" checked v-model="isVisible">

            <button type="submit">Upraviť</button>
        </form>

        <form @submit.prevent="addBookmark(id)">

        </form>
        <draggable :list="bookmarksNew" :options="{animation:200}" @change="update">
        <div v-for="bookmark in bookmarksNew" >
            <a :href="bookmark.url" target="_blank">{{ bookmark.name }}</a>
            <br>
            {{ bookmark.order }}
            <router-link tag="button" :to="{ path:'/bookmark/'+bookmark.id }">Podrobnosti</router-link>
            <button @click="deleteBookmark(id, bookmark.id)">Odstrániť</button>
            <hr>
        </div>
        </draggable>
    </div>
</template>

<script>
    import draggable from 'vuedraggable';
    import auth from "../auth/index.js";

    export default {
        components: {
            draggable
        },
        data(){
            return{
                auth: auth,
                bookmarksNew: [],
                bookmarks: [],
                bookmarklist: [],
                name: null,
                isVisible: null,
                id: null,
                message: "",
            }
        },
        mounted(){
            this.getId();
            this.getBookmarkList(this.id);
            this.getBookmarks(this.id);
        },
        computed: {
            sorted: function() {
                function compare(a, b) {
                    if (a.order < b.order)
                        return -1;
                    if (a.order > b.order)
                        return 1;
                    return 0;
                }
                return this.bookmarksNew.sort(compare);
            }
        },
        methods: {
            sort(a,b){
                if (a.order < b.order)
                    return -1;
                if (a.order > b.order)
                    return 1;
                return 0;
            },
            getBookmarkList(id){
                axios
                    .get('/bookmark-lists/'+id,
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.bookmarklist = response.data;
                        this.name = response.data.name;
                        this.isVisible = response.data.isVisible;
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
            sortArrays(arrays) {
                this.bookmarksNew = _.orderBy(arrays, 'order', 'asc');
            },
            getBookmarks(id){
                axios
                    .get('bookmark-lists/'+id+'/content',
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.bookmarks = response.data;
                        this.bookmarksNew = this.bookmarks;
                        // console.log(this.bookmarksNew);
                        // this.sortArrays(this.bookmarksNew);
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            deleteBookmark(id, bookmark_id){
                axios
                    .patch('bookmark-lists/'+id,
                        {
                            bookmark_id: bookmark_id,
                        },
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.message = "Položka bola zo zoznamu odstránená.";
                        this.getBookmarks(id);
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            addBookmark(){

            },
            editBookmarkList(id){
                console.log(this.isVisible+" "+this.name);
                axios
                    .patch('bookmark-lists/'+id+'/edit',
                        {
                            name: this.name,
                            isVisible: this.isVisible,
                        },
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.message = "Zoznam bol upravený.";
                        this.getBookmarkList(id);
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            update(){
                this.bookmarks.map((bookmark, index) => {
                    bookmark.order = index + 1;
                    console.log(bookmark.name+" "+bookmark.order);
                    axios
                        .patch('/bookmark-lists/'+this.id+'/order',
                            {
                                order: bookmark.order,
                                bookmark_id: bookmark.id,
                            },
                            {
                                headers: {Authorization: "Bearer " + this.auth.getToken()}
                            });
                })

            }
        }
    }
</script>

<style scoped>

</style>