<template>
    <div>
        <div class="row">
            <div class="col-md-3">

            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h3 class="card-title">Vyhľadať záložky</h3>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="searchBookmarks()">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="bmd-label-static"  for="name">Text</label>
                                        <input class="form-control" type="text" v-model="text">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="category_table">Vyberte kategóriu:</label>
                                        <br>
                                        <multiselect style="z-index: 5 !important" v-model="selected" @remove="nullCategory" deselect-label="Odstrániť" track-by="name" label="name" placeholder="Vyberte" :options="categories" :searchable="false" :allow-empty="true">
                                            <template slot="singleLabel" slot-scope="{ option }"><strong>{{ option.name }}</strong></template>
                                        </multiselect>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                        <div v-if="selected == null" class="form-group" >
                                            <!--<label class="form-check-label">-->
                                                <!--Hľadať globálne-->
                                                <!--<input class="form-check-input" type="checkbox" checked v-model="global">-->
                                                <!--<span class="form-check-sign">-->
                                                <!--<span class="check"></span>-->
                                            <!--</span>-->
                                            <!--</label>-->

                                                <toggle-button :sync="true" color="#9c27b0" v-model="global" true-value=1 false-value=0 />
                                            <label class="bmd-label-floating">Hľadať globálne</label>


                                        </div>
                                    <div v-else class="form-group" >
                                            <toggle-button :sync="true" color="#9c27b0" v-model="global" true-value=1 false-value=0 />
                                        <label class="bmd-label-static">Hľadať globálne</label>

                                        <!--<label class="form-check-label">-->
                                            <!--Hľadať globálne:-->
                                            <!--<input class="form-check-input" type="checkbox" disabled v-model="global">-->
                                            <!--<span class="form-check-sign">-->
                                                <!--<span class="check"></span>-->
                                            <!--</span>-->
                                        <!--</label>-->
                                    </div>

                                </div>
                                <div class="col-md-12">
                                    <!--<div v-if="global == 1">-->
                                        <!--<label for="read">Hľadať na základe prečítanosti: </label>-->
                                        <!--<div class="row">-->
                                            <!--<div class="col-md-4">-->
                                                <!--<span class="custom-control custom-radio">-->
                                                    <!--<input type="radio" class="custom-control-input"  name="defaultExample2" disabled v-model="read" value="all" checked>-->
                                                    <!--<label class="custom-control-label" for="defaultGroupExample1">Nezáleží</label>-->
                                                <!--</span>-->
                                            <!--</div>-->
                                            <!--<div class="col-md-4">-->
                                                <!--<span class="custom-control custom-radio">-->
                                                    <!--<input type="radio" class="custom-control-input" name="defaultExample2" disabled v-model="read" value=1 >-->
                                                    <!--<label class="custom-control-label" for="defaultGroupExample2">Prečítané</label>-->
                                                <!--</span>-->
                                            <!--</div>-->
                                            <!--<div class="col-md-4">-->
                                                <!--<span class="custom-control custom-radio">-->
                                                    <!--<input type="radio" class="custom-control-input" name="defaultExample2" disabled v-model="read" value=2>-->
                                                    <!--<label class="custom-control-label" for="defaultGroupExample3">Neprečítané</label>-->
                                                <!--</span>-->
                                            <!--</div>-->
                                        <!--</div>-->
                                    <!--</div>-->
                                    <div v-if="global == 0 || selected != null" class="form-group">
                                        <label for="read">Hľadať na základe prečítanosti: </label>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <span class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" id="defaultGroupExample1" name="defaultExample2" v-model="read" value="all" checked>
                                                    <label class="custom-control-label" for="defaultGroupExample1">Nezáleží</label>
                                                </span>
                                            </div>
                                            <div class="col-md-4">
                                                <span class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" id="defaultGroupExample2" name="defaultExample2" v-model="read" value=1 >
                                                    <label class="custom-control-label" for="defaultGroupExample2">Prečítané</label>
                                                </span>
                                            </div>
                                            <div class="col-md-4">
                                                <span class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" id="defaultGroupExample3" name="defaultExample2" v-model="read" value=2>
                                                    <label class="custom-control-label" for="defaultGroupExample3">Neprečítané</label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button  type="submit"  class="btn btn-primary pull-right"><i class="material-icons">search</i></button>

                            <div class="clearfix"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-3">

            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h3 class="card-title">Výsledky</h3>
                    </div>
                    <div class="card-body">
                        <div v-if="bookmarks">
                                <div v-for="bookmark in bookmarks">
                                    <div class="row">

                                    <div class="col-md-9">
                                        <p><b><a :href="bookmark.url" target="_blank">{{ bookmark.name }}</a></b></p>
                                        <p>{{ bookmark.description }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <router-link class="btn btn-info btn-sm" :to="'bookmark/' + bookmark.id " tag="button"><i class="material-icons">trending_flat</i></router-link>
                                        <button class="btn btn-danger btn-sm" v-if="isAdmin == true" v-on:click="deleteBookmark(bookmark.id)"><i class="material-icons">delete_outline</i></button>
                                    </div>
                                    </div>
                                    <hr>
                                </div>
                        </div>
                        <div v-else>
                            <h4>{{ message }}</h4>
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
        data() {
            return {
                data: null,
                text: null,
                global: true,
                read: "all",
                bookmarks: this.bookmarks,
                categories: [],
                url: null,
                name: null,
                description: null,
                category_id: null,
                category_name: "",
                selected: null,
                category_table: null,
                category_select: null,
                isVisible: true,
                isRead: null,
                isAdmin: null,
                key: "",
                auth: auth,
                message: null,
                info: null,
                errors: []
            };
        },
        mounted(){
            this.getUserData();
            this.allCategories();
        },
        methods: {
            nullCategory(){
                this.selected = '';
                // this.category_name.name = null;
            },
            // selectCategory(value){
            //     console.log(value);
            //     console.log(value.name);
            //     if(value == null){
            //         this.category_name.name = null;
            //         return;
            //     }
            //     this.category_name.name = value.id;
            //
            // },
            allCategories(){
                axios
                    .get("/categories", {
                        headers: {Authorization: "Bearer " + this.auth.getToken()}
                    })
                    .then(response => {
                        this.categories = response.data;
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            searchBookmarks(){
                // console.log(this.selected);
                // console.log(this.text);
                // console.log(this.global);
                // console.log(this.read);
                var category;
                if(this.selected == null){
                    category = null;
                }
                else{
                    category = this.selected.name;
                }
                axios
                    .post("search-bookmarks",{
                        text: this.text,
                        category: category,
                        global: this.global,
                        read: this.read,
                    },
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.bookmarks = response.data;
                        this.message = null;
                        console.log(this.text+" "+this.category_name+ " "+this.global+" "+this.read+" "+this.message);

                    })
                    .catch(error => {
                        console.log(error.response);
                        this.bookmarks = null;
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                        console.log("error: "+this.message+" "+this.bookmarks);
                    });
            },
            deleteBookmark(id){
                axios
                    .delete('bookmarks/'+id,
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.message = "Záložka bola vymazaná";
                        this.searchBookmarks();
                    })
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
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
        }
    }
</script>

<style scoped>

</style>