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
                        <div class="row">
                            <div class="col-md-6">
                                <span class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="choice-bookmark" name="defaultExample1" v-model="bookmark_or_list" value="bookmark" checked>
                                    <label class="custom-control-label" for="choice-bookmark">Hľadať záložky</label>
                                </span>
                            </div>
                            <div class="col-md-6">
                                <span class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="choice-list" name="defaultExample1" v-model="bookmark_or_list" value="list" >
                                    <label class="custom-control-label" for="choice-list">Hľadať zoznamy</label>
                                </span>
                            </div>
                        </div>

                        <hr>

                        <form v-if="bookmark_or_list == 'bookmark'" @submit.prevent="searchBookmarks()">
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
                                        <multiselect style="z-index: 5 !important" select-label="Stlačte enter pre výber" selectedLabel="Vybraté" v-model="selected" @remove="nullCategory" deselect-label="Odstrániť" track-by="name" label="name" placeholder="Vyberte" :options="categories" :searchable="false" :allow-empty="true">
                                            <template slot="singleLabel" slot-scope="{ option }"><strong>{{ option.name }}</strong></template>
                                        </multiselect>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                        <div v-if="selected == null" class="form-group" >
                                            <toggle-button :sync="true" color="#9c27b0" v-model="global" true-value=1 false-value=0 />
                                            <label class="bmd-label-floating">Hľadať globálne</label>
                                        </div>
                                    <div v-else class="form-group" >
                                            <toggle-button :sync="true" color="#9c27b0" disabled v-model="global" true-value=1 false-value=0 />
                                        <label class="bmd-label-static">Hľadať globálne</label>
                                    </div>

                                </div>
                                <div class="col-md-12">
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

                        <form v-else-if="bookmark_or_list == 'list'" @submit.prevent="searchBookmarkLists()">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="bmd-label-static"  for="name">Text</label>
                                        <input class="form-control" type="text" v-model="list_text">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div v-if="selected == null" class="form-group" >
                                        <toggle-button :sync="true" color="#9c27b0" v-model="list_global" true-value=1 false-value=0 />
                                        <label class="bmd-label-floating">Hľadať globálne</label>
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
                <div v-if="bookmark_or_list == 'bookmark'" class="card">
                    <div class="card-header card-header-primary">
                        <h3 class="card-title">Výsledky</h3>
                    </div>
                    <div class="card-body">
                        <div v-if="results">
                                <div v-for="item in results">
                                    <div class="row">

                                    <div class="col-md-9">
                                        <p><b><a :href="item.url" target="_blank">{{ item.name }}</a></b></p>
                                        <p>{{ item.description }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <router-link class="btn btn-info btn-sm" :to="'bookmark/' + item.id " tag="button"><i class="material-icons">trending_flat</i></router-link>
                                        <button class="btn btn-danger btn-sm" v-if="isAdmin == true" v-on:click="deleteBookmark(item.id)"><i class="material-icons">delete_outline</i></button>
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
                <div v-else-if="bookmark_or_list == 'list'" class="card">
                    <div class="card-header card-header-primary">
                        <h3 class="card-title">Výsledky</h3>
                    </div>
                    <div class="card-body">
                        <div v-if="results">
                            <div v-for="item in results">
                                <div class="row">

                                    <div class="col-md-9">
                                        <p><b><a :href="item.url" target="_blank">{{ item.name }}</a></b></p>
                                        <p>{{ item.description }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <router-link class="btn btn-info btn-sm" :to="'bookmark-lists/' + item.id " tag="button"><i class="material-icons">trending_flat</i></router-link>
                                        <button v-if="bookmark_or_list == 'list' && isAdmin == true" class="btn btn-danger btn-sm" v-on:click="deleteBookmarkList(item.id)"><i class="material-icons">delete_outline</i></button>
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
                bookmark_or_list: 'bookmark',
                list_text: null,
                list_global: true,
                text: null,
                global: true,
                read: "all",
                results: this.results,
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
            },
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
            searchBookmarkLists(){
                axios
                    .post("search-bookmark-lists",{
                        text: this.list_text,
                        global: this.list_global,
                    },
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.results = response.data;
                        this.message = null;
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.results = null;
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                        console.log("error: "+this.message+" "+this.results);
                    });
            },
            searchBookmarks(){
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
                        this.results = response.data;
                        this.message = null;
                        console.log(this.text+" "+this.category_name+ " "+this.global+" "+this.read+" "+this.message);

                    })
                    .catch(error => {
                        console.log(error.response);
                        this.results = null;
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                        console.log("error: "+this.message+" "+this.results);
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
            deleteBookmarkList(id){
              axios
                  .delete('bookmark-lists/'+id+'/delete',
                      {
                          headers: {Authorization: "Bearer " + this.auth.getToken()}
                      })
                  .then(response => {
                      this.message = "Zoznam bol vymazaná";
                      this.searchBookmarkLists();
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