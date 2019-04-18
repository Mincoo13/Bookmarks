<template>
    <div>
        <div class="row">
            <div class="col-md-2">

            </div>
            <div class="col-md-8">
                <h2>{{ bookmarklist.name }}</h2>
                <button v-if="isAdmin == 1 || userId == bookmarklist.user_id" id="btn-bookmark-list" class="btn btn-primary pull-left" @click="showForm()"><i class="material-icons">edit</i><div class="ripple-container"></div></button>

                <div v-if="isAdmin == 1 || userId == bookmarklist.user_id" class="card" id="bookmarkList-form">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">Upraviť</h4>
                        <!--<p class="card-category"></p>-->
                    </div>
                    <div  class="card-body">
                        <h3>Upraviť zoznam</h3>
                        <div v-if="message_edit_error">
                            <p class="text-danger">{{ message_edit_error }}</p>
                        </div>
                        <div v-else-if="message_edit_success">
                            <p class="text-success">{{ message_edit_success}}</p>
                        </div>
                        <form @submit.prevent="editBookmarkList(id)">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="bmd-label-static">Upraviť názov</label>
                                        <input class="form-control" type="text" v-model="name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                            <label class="bmd-label-floating">Nastaviť ako viditeľný:</label>
                                            <toggle-button :sync="true" color="#9c27b0" v-model="isVisible" true-value=1 false-value=0 />
                                    </div>
                                    <button class="btn btn-primary pull-right" type="submit">Upraviť</button>
                                </div>
                            </div>
                            <!--<input v-if="isVisible != 1" type="checkbox" v-model="isVisible">-->
                            <!--<input v-else type="checkbox" checked v-model="isVisible">-->
                        </form>
                    </div>
                </div>
                <br v-if="!space">
                <br v-if="!space">
                <br v-if="!space">

                <div v-if="userId == bookmarklist.user_id" class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">{{ bookmarklist.name }}</h4>
                        <!--<p class="card-category"></p>-->
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h3>Pridať nové záložky do zoznamu</h3>
                                <div v-if="message_add_error">
                                    <p class="text-danger">{{ message_add_error }}</p>
                                </div>
                                <div v-else-if="message_add_success">
                                    <p class="text-success">{{ message_add_success}}</p>
                                </div>
                                <form @submit.prevent="addBookmark(id)">
                                    <div>
                                        <multiselect style="z-index: 50 !important" select-label="Stlačte enter pre výber" deselect-label="Odstrániť" selectedLabel="Vybraté" v-model="selected" :options="allBookmarks" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Vyberte" label="name" track-by="name" :preselect-first="true">
                                            <template slot="selection" slot-scope="{ values, search, isOpen }"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} záložiek zvolených</span></template>
                                        </multiselect>
                                    </div>
                                    <button onclick="window.history.back()" type="submit" class="btn btn-info pull-left"><i class="material-icons">arrow_back</i></button>
                                    <button class="btn btn-primary pull-right" type="submit">Pridať</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">Zdieľať</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form @submit.prevent="shareBookmarkList()">
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
                    </div>
                </div>

                <div class="card" v-if="isAdmin == 1 || userId == bookmarklist.user_id">
                    <div class="card-header card-header-text">
                        <h4 class="card-title">Prečítanosť</h4>
                        <div class="progress md-progress" style="height: 20px">
                            <div class="progress-bar" role="progressbar" :style="'width:'+ progress +'%; height: 20px'" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">{{ progress }}%</div>
                        </div>
                        <!--<p class="card-category"></p>-->
                    </div>

                    <div class="card-body">
                        <p>tip: Kliknutím a presunutím záložky môžete meniť jednotlivé poradia v zozname</p>

                        <draggable :list="bookmarksNew" :options="{animation:200}" @change="update">
                            <div v-for="bookmark in bookmarksNew" >
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3><a :href="bookmark.url" target="_blank">{{ bookmark.name }}</a></h3>

                                        <div v-if="bookmark.isRead" class="form-check" >
                                            <label class="form-check-label">
                                                Prečítaná
                                                <input class="form-check-input" type="checkbox" checked v-on:change="markRead(bookmark.id)">
                                                <span class="form-check-sign">
                                                <span class="check"></span>
                                            </span>
                                            </label>
                                        </div>

                                        <div v-else class="form-check" >
                                            <label class="form-check-label">
                                                Neprečítaná
                                                <input class="form-check-input" type="checkbox" v-on:click="markRead(bookmark.id)">
                                                <span class="form-check-sign">
                                                <span class="check"></span>
                                            </span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <button class="btn btn-danger btn-sm pull-right" @click="deleteBookmark(id, bookmark.id)"><i class="material-icons">delete_outline</i></button>
                                        <router-link class="btn btn-info btn-sm pull-right" tag="button" :to="{ path:'/bookmark/'+bookmark.id }"><i class="material-icons">trending_flat</i></router-link>
                                    </div>
                                </div>
                                <hr>
                            </div>
                        </draggable>
                    </div>
                </div>
                <div v-else class="card">
                    <div class="card-body">
                        <div v-for="bookmark in bookmarksNew" >
                            <div class="row" v-if="isAdmin == 1 || bookmark.isVisible == 1">
                                <div class="col-md-6">
                                    <h3><a :href="bookmark.url" target="_blank">{{ bookmark.name }}</a></h3>
                                </div>
                                <div class="col-md-6">
                                    <router-link class="btn btn-info btn-sm pull-right" tag="button" :to="{ path:'/bookmark/'+bookmark.id }"><i class="material-icons">trending_flat</i></router-link>
                                </div>
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
    import draggable from 'vuedraggable';
    import auth from "../auth/index.js";
    import Multiselect from 'vue-multiselect';

    export default {
        components: {
            draggable,
            Multiselect,
        },
        data(){
            return{
                auth: auth,
                isAdmin: false,
                userId: 999,
                bookmarksNew: [],
                bookmarks: [],
                allBookmarks: [],
                bookmarklist: [],
                space: null,
                name: null,
                share: true,
                email: null,
                expand: false,
                isVisible: this.isVisible,
                id: null,
                progress: 0,
                message_edit_error: "",
                message_edit_success: "",
                message_add_error: "",
                message_add_success: "",
                share_message_success: "",
                share_message_error: "",
                selected: [],
            }
        },
        mounted(){
            this.getId();
            this.getUserData();
            this.getBookmarkList(this.id);
            this.getAllBookmarks();
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
            showForm(){
                this.space = 1;
                $('#bookmarkList-form').animate({height: "toggle", opacity: "toggle"}, "fast");
                document.getElementById("btn-bookmark-list").style="display: none" ;
            },
            shareBookmarkList(){
                var currentUrl = window.location.href;
              axios
                  .post('share-bookmark-list/'+this.id,
                      {
                          email: this.email,
                          url: currentUrl,
                      },
                      {
                          headers: {Authorization: "Bearer " + this.auth.getToken()}
                      })
                  .then(response => (
                      this.share_message_error = "",
                          this.share_message_success = "Zoznam záložiek bol poslaný na email "+this.email
                  ))
                  .catch(error => {
                      console.log(error.response);
                      this.share_message_success = "";
                      this.share_message_error = error.response.data.message;
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
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            markRead(id){
                axios
                    .patch("bookmarks/"+id+"/mark-read",{
                            data: this.data},
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.getBookmarks(this.id);
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
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
                        if(response.data.isVisible == 1)this.isVisible = true;
                        else this.isVisible = false;
                        console.log(this.isAdmin+" "+this.userId+" "+this.bookmarklist.user_id);
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
            getAllBookmarks(){
                axios
                    .post('/get-bookmarks',
                        {
                            category_id: null,
                        },
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.allBookmarks = response.data;
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
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
                        var i;
                        var read = 0;
                        var bookmarks = this.bookmarks;
                        var count = bookmarks.length;
                        for(i in bookmarks){
                            if(bookmarks[i].isRead == 1) {
                                read++;
                            }
                        }
                        this.progress = Math.round((read/count)*100);
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
                var item = null;
                for(item in this.selected){
                    axios
                        .post('bookmark-lists/'+this.id,
                            {
                                bookmark_id: this.selected[item].id,
                            },
                            {
                                headers: {Authorization: "Bearer " + this.auth.getToken()}
                            })
                        .then(response => {
                            this.message_add_error = "";
                            this.message_add_success = "Záložky boli pridané do zoznamu.";
                            this.getBookmarks(this.id);
                        })
                        .catch(error => {
                            console.log(error.response);
                            this.message_add_success = "";
                            this.message_add_error = error.response.data.message;
                            this.errors = error.response.data.errors ? error.response.data.errors : [];
                        });
                }
            },
            editBookmarkList(id){
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
                        this.message_edit_success = "Zoznam bol upravený.";
                        this.message_edit_error = "";
                        this.getBookmarkList(id);
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message_edit_error = error.response.data.message;
                        this.message_edit_success = "";
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            update(){
                this.bookmarks.map((bookmark, index) => {
                    bookmark.order = index + 1;
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