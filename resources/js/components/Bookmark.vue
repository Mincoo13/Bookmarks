<template>
    <div>
        <b>{{ fullnameBookmark }}</b>
        <br>
        Nazov:
        {{ bookmark.name }}
        <br>
        URL:
        {{ bookmark.url }}
        <br>
        Popis:
        {{ bookmark.description }}
        <br>
        Precitana:
        <div v-if="bookmark.isRead == true ">
            Ano
        </div>
        <div v-else>
            Nie
        </div>
        <br>
        Verejna:
        <div v-if="bookmark.isVisible == true ">
            Ano
        </div>
        <div v-else>
            Nie
        </div>
        <br>
        Kategoria:
        <div v-if="bookmark.category_name == null">
            Ziadna
        </div>
        <div v-else>
            {{ bookmark.category_name }}
        </div>
        Vytvorena:
        {{ bookmark.created_at }}
        <br>
        <router-link :to="'/bookmark/'+id+'/edit'" v-if="isAdmin == 1 || bookmark.user_id == userId" tag="button">Upraviť záložku</router-link>

        <h2>Pridať nový komentár</h2>
        <form @submit.prevent="addComment()">
            <textarea v-model="text"></textarea>
            <br>
            <button type="submit">Pridať</button>
        </form>
        <br>
        <h2>Komentare</h2>
        <div v-for="comment in comments"  >
            <b>{{ comment.fullname }}</b> |
            {{ comment.user_id }}
            {{ comment.created_at }}
            <a style="text-decoration: underline" v-if="((comment.fullname == userFullname || isAdmin == 1) && (currentComment!=comment.text || !expand))" v-on:click="(expand = true) && (currentComment = comment.text) && (textComment = comment.text)">Upraviť</a>
            <a style="text-decoration: underline" v-if="((comment.fullname == userFullname || isAdmin == 1 || userFullname == fullnameBookmark) && currentComment == comment.text) && expand" v-on:click="expand = !expand">Skryť</a>

            <a style="text-decoration: underline" v-if="(comment.fullname == userFullname || isAdmin == 1 || userFullname == fullnameBookmark)" v-on:click="deleteComment(comment.id)">Zmazať</a>

            <br>
            <div v-if="expand == true && currentComment == comment.text">
                <form @submit.prevent="editComment(comment.id)">
                    <textarea v-model="textComment"></textarea>
                    <button type="submit">Upraviť</button>
                </form>
            </div>

            <br>
            {{ comment.text }}
            <hr>

        </div>
    </div>
</template>

<script>
    import auth from "../auth/index.js";
    export default {
        data() {
            return {
                auth: auth,
                name: null,
                expand: false,
                id: null,
                isAdmin: null,
                fullnameBookmark: null,
                userFullname: null,
                userId: null,
                comment_userId: null,
                text: null,
                bookmark: [],
                comments: [],
                currentComment: null,
                editText: "",
            };
        },
        mounted(){
            this.getUserData();
            this.getId();
            this.getUserBookmark(this.id);
            this.showBookmark();
            this.getComments();
        },
        methods: {
            showBookmark() {
                axios
                    .get("bookmark/"+this.id, {
                        headers: { Authorization: "Bearer " + this.auth.getToken() }
                    })
                    .then(response => (
                        this.bookmark = response.data
                    ))
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
            getComments(){
                axios
                    .get("comments/"+this.id, {
                        headers: { Authorization: "Bearer " + this.auth.getToken() }
                    })
                    .then(response => (
                        this.comments = response.data
                    ))
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            getUserData(){
                axios
                    .get("/profile",
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.isAdmin = response.data.isAdmin;
                        this.userId = response.data.id;
                        var userName = response.data.name;
                        var userSurname = response.data.surname;
                        this.userFullname = userName + " " + userSurname;
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            getUserBookmark(id){
                axios
                    .get("/bookmarks/"+id+"/user",
                    {
                        headers: {Authorization: "Bearer " + this.auth.getToken()}
                    })
                    .then(response => {
                        this.fullnameBookmark = response.data;
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            addComment(){
                axios
                    .post("/comments",
                        {
                            text: this.text,
                            bookmark_id: this.id,
                        },
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.message = "Komentár bol pridaný.";
                        this.text = null;
                        this.getComments();
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            editComment(id){
                axios
                    .patch('/comments/'+id,
                        {
                            text: this.textComment,
                        },
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.message = "Komentár bol upravený.";
                        this.expand = false;
                        this.getComments();
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            deleteComment(id){
                axios
                    .delete('comments/'+id,
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.message = "Komentár bol zmazaný.";
                        this.getComments();
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            }
        },
    };
</script>

<style scoped>

</style>