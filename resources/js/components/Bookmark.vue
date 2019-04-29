<template>
    <div>
        <div class="row">
            <div class="col-md-3">
            </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h3 class="card-title">{{ bookmark.name }}</h3>
                            <p class="card-category">{{ fullnameBookmark }}</p>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="row col-md-12">
                                    <div class="form-group">
                                        <b>URL:</b><br><a :href="bookmark.url" target="_blank">{{ bookmark.url }}</a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <b>Popis:</b><br>{{ bookmark.description }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">

                                            <div v-if="bookmark.isRead == true "><b>Prečítaná:  </b>áno</div>
                                            <div v-else><b>Prečítaná:   </b>nie</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">

                                            <div v-if="bookmark.isVisible == true "><b>Verejná:  </b>áno</div>
                                            <div v-else><b>Verejná:    </b>nie</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">

                                            <div v-if="bookmark.category_name == null"><b>Kategória:  </b><span class="text-gray">Žiadna</span></div>
                                            <div v-else><b>Kategória:  </b>{{ bookmark.category_name }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">

                                            <b>Vytvorená:   </b>{{ bookmark.created_at }}
                                        </div>
                                    </div>
                                </div>
                                <button onclick="window.history.back()" type="submit" class="btn btn-info pull-left"><i class="material-icons">arrow_back</i></button>
                                <button v-on:click="share = true" class="btn btn-primary pull-right">Zdieľať</button>
                                <router-link :to="'/bookmark/'+id+'/edit'" v-if="isAdmin == 1 || bookmark.user_id == userId" tag="button" class="btn btn-primary pull-right"><i class="material-icons">edit</i></router-link>

                                <!--<button  class="btn btn-primary pull-right">Update Profile</button>-->
                                <div class="clearfix"></div>
                            </form>
                            <form v-if="share == true" @submit.prevent="shareBookmark()">
                                <hr>
                                <div v-if="share_message_error">
                                    <p class="text-danger">{{ share_message_error }}</p>
                                </div>
                                <div v-else-if="share_message_success">
                                    <p class="text-success">{{ share_message_success}}</p>
                                </div>
                                <div class="form-group">
                                    <label>Zadajte Email:</label>
                                    <input class="form-control" v-model="email" />
                                </div>
                                <button class="btn btn-primary pull-right"><i class="material-icons">send</i></button>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header card-header-text">
                            <h3 class="card-title">Komentáre ku záložke</h3>
                        </div>
                        <div class="card-body">
                            <form @submit.prevent="addComment()">
                                <div class="form-group">
                                    <div class="form-group bmd-form-group">
                                        <h4>Pridať nový komentár</h4>
                                        <textarea  class="form-control" v-model="text"></textarea>
                                        <button type="submit" class="btn btn-primary btn-sm pull-right">Pridať</button>
                                    </div>
                                </div>
                            </form>
                            <br>
                            <br>
                            <div v-for="comment in comments">
                                <div class="row">
                                    <div class="col-md-12">
                                        <span><b>{{ comment.fullname }}</b> |</span>
                                        <span v-if="comment.updated_at == null">{{ comment.created_at }}</span>
                                        <span v-else >{{ comment.updated_at }}</span>

                                        <a class="text-primary comment" style="text-decoration: underline" v-if="((comment.fullname == userFullname || isAdmin == 1) && (currentComment!=comment.text || !expand))" v-on:click="(expand = true) && (currentComment = comment.text) && (textComment = comment.text)">Upraviť</a>
                                        <a class="text-primary comment" style="text-decoration: underline" v-if="((comment.fullname == userFullname || isAdmin == 1 || userFullname == fullnameBookmark) && currentComment == comment.text) && expand" v-on:click="expand = !expand">Skryť</a>

                                        <a class="text-primary comment" style="text-decoration: underline" v-if="(comment.fullname == userFullname || isAdmin == 1 || userFullname == fullnameBookmark)" v-on:click="deleteComment(comment.id)">Zmazať</a>

                                    </div>
                                </div>

                                <br>

                                <div v-if="expand == true && currentComment == comment.text">
                                    <form @submit.prevent="editComment(comment.id)">
                                        <div class="form-group">
                                            <div class="form-group bmd-form-group">
                                                <textarea  class="form-control" v-model="textComment"></textarea>
                                                <button type="submit" class="btn btn-primary btn-sm pull-right">Upraviť</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div v-else>
                                    {{ comment.text }}
                                    <br>
                                </div>

                                <hr>

                            </div>
                        </div>
                    </div>
                </div>
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
                share: false,
                email: null,
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
                share_message_error: "",
                share_message_success: "",
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
            shareBookmark(){
                axios
                    .post('share-bookmark/'+this.id,
                        {
                            email: this.email,
                        },
                        {
                            headers: { Authorization: "Bearer " + this.auth.getToken() }
                        })
                    .then(response => (
                        this.share_message_error = "",
                        this.share_message_success = "Záložka bola poslaná na email "+this.email
                    ))
                    .catch(error => {
                        console.log(error.response);
                        this.share_message_success = "";
                        this.share_message_error = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
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