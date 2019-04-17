<template>
    <div>
        <div class="row">
            <div class="col-md-2">

            </div>
            <div class="col-md-8">
                <h2>
                    Vytvoriť nový zoznam
                </h2>
                <button id="btn-list" class="btn btn-primary pull-left" @click="showForm()"><i class="material-icons">add</i><div class="ripple-container"></div></button>

                <div class="card" id="list-form" >
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">Vytvoriť nový zoznam</h4>
                        <!--<p class="card-category"></p>-->
                    </div>
                    <div class="card-body">
                        <div v-if="message">
                            <p class="text-danger">{{ message }}</p>
                        </div>
                        <div v-else-if="message_success">
                            <p class="text-success">{{ message_success }}</p>
                        </div>
                        <form @submit.prevent="createList()">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="bmd-label-floating">Zvoľte názov:</label>
                                        <input class="form-control"  v-model="name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="bmd-label-floating">Nastaviť ako viditeľný:</label>
                                        <toggle-button checked color="#9c27b0" v-model="isVisible" true-value=1 false-value=0 />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="typo__label">Vyberte záložky</label>
                                        <multiselect  v-model="selected" :options="bookmarks" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Vyberte" label="name" track-by="name" :preselect-first="true">
                                            <template slot="selection" slot-scope="{ values, search, isOpen }"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} záložiek zvolených</span></template>
                                        </multiselect>
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-primary pull-right" type="submit">Odoslat</button>
                        </form>
                        <div class="clearfix"></div>

                    </div>
                </div>

                <br v-if="!space">
                <br v-if="!space">
                <br v-if="!space">

                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">Moje zoznamy</h4>
                        <!--<p class="card-category"></p>-->
                    </div>
                    <div class="card-body">
                        <div v-for="bookmarklist in bookmarklists">
                            <div class="row">
                                <div class="col-md-6">
                                    <h3><router-link  :to="{ path: '/bookmark-lists/' + bookmarklist.id }"> {{ bookmarklist.name }} </router-link></h3>
                                    <p v-if="bookmarklist.count == 0 || bookmarklist.count > 4">{{ bookmarklist.count }} záložiek v zozname</p>
                                    <p v-else-if="bookmarklist.count > 1 && bookmarklist.count<5 || bookmarklist.count > 4">{{ bookmarklist.count }} záložky v zozname</p>
                                    <p v-if="bookmarklist.count == 1">{{ bookmarklist.count }} záložka v zozname</p>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-danger btn-sm pull-right" @click="deleteList(bookmarklist.id)"><i class="material-icons">delete_outline</i></button>
                                    <router-link class="btn btn-info btn-sm pull-right" tag="button" :to="{ path:'/bookmark-lists/'+bookmarklist.id }"><i class="material-icons">trending_flat</i></router-link>
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
    import auth from "../auth/index.js";
    import Multiselect from 'vue-multiselect';

    export default {
        components:{
            Multiselect,
        },
        data(){
            return {
                auth: auth,
                isVisible: true,
                name: null,
                space: null,
                bookmarklists: [],
                bookmarks: [],
                selected: [],
                message: "",
                message_success: "",
                item: null,
                id: null,
            }
        },
        mounted(){
            this.getBookmarkLists();
            this.getBookmarks();
        },
        methods: {
            showForm(){
                this.space = 1;
                $('#list-form').animate({height: "toggle", opacity: "toggle"}, "fast");
                document.getElementById("btn-list").style="display: none" ;

            },
            deleteList(id){
                axios
                    .delete('bookmark-lists/'+id+'/delete',
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.message = "Zoznam bol odstránený.";
                        this.getBookmarkLists();
                        this.getBookmarks();
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
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
            },
            getBookmarks(){
                axios
                    .post('/get-bookmarks',
                        {
                          category_id: null,
                        },
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.bookmarks = response.data;
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            createList(){
                axios
                    .post('/bookmark-lists/create',
                        {
                            name: this.name,
                            isVisible: this.isVisible,
                        },
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.getBookmarkLists();
                        this.getBookmarks();
                        this.id = response.data[0].id;
                        this.message_success = "Zoznam bol vytvorený";
                        this.message = "";
                        var item = null;
                        for(item in this.selected){
                            axios
                                .post('bookmark-lists/'+response.data[0].id,
                                    {
                                        bookmark_id: this.selected[item].id,
                                    },
                                    {
                                        headers: {Authorization: "Bearer " + this.auth.getToken()}
                                    })
                                .then(response => {

                                })
                                .catch(error => {
                                    console.log(error.response);
                                    this.message_success = "";
                                    this.message = error.response.data.message;
                                    this.errors = error.response.data.errors ? error.response.data.errors : [];
                                });
                        }
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