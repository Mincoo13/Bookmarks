<template>
    <div>
        <div class="row">
            <div class="col-md-3">
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h3 class="card-title">Úprava záložky</h3>
                        <p class="card-category">{{ fullname }}</p>

                    </div>
                    <div class="card-body">
                        <form @submit.prevent="editBookmark(id)">
                            <div v-if="message" class="text-danger">
                                <p>{{ message }}</p>
                            </div>
                            <div v-else-if="message_success" class="text-success">
                                <p>{{ message_success }}</p>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="bmd-label-static"  for="description">Názov</label>
                                        <input class="form-control" type="text" v-model="name">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="bmd-label-static"  for="description">Url</label>
                                        <input class="form-control" type="text" v-model="url">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="bmd-label-static"  for="description">Popis</label>
                                        <textarea class="form-control" type="text" rows="4" v-model="description"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div v-if="isVisible == false ">
                                            <div class="form-check" style="text-align: center">
                                                <label class="form-check-label">
                                                    <p class="text-dark">Neverejná  </p>

                                                    <input class="form-check-input" type="checkbox" v-model="isVisible" true-value="1" false-value="0">
                                                    <span class="form-check-sign">
                                                <span class="check"></span>
                                            </span>
                                                </label>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <div class="form-check" style="text-align: center">
                                                <label class="form-check-label">
                                                    <p class="text-dark">Verejná  </p>

                                                    <input class="form-check-input" type="checkbox" v-model="isVisible" checked true-value="1" false-value="0">
                                                    <span class="form-check-sign">
                                                <span class="check"></span>
                                            </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div v-if="isRead == false ">
                                            <div class="form-check" style="text-align: center">
                                                <label class="form-check-label">
                                                    <p class="text-dark">Neprečítaná  </p>

                                                    <input class="form-check-input" type="checkbox" v-model="isRead" true-value="1" false-value="0">
                                                    <span class="form-check-sign">
                                                <span class="check"></span>
                                            </span>
                                                </label>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <div class="form-check" style="text-align: center">
                                                <label class="form-check-label">
                                                    <p class="text-dark">Prečítaná  </p>

                                                    <input class="form-check-input" type="checkbox" v-model="isRead" checked true-value="1" false-value="0">
                                                    <span class="form-check-sign">
                                                <span class="check"></span>
                                            </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div>
                                        <label class="typo__label">Vybert kategóriu:</label>
                                        <multiselect v-model="selected" deselect-label="Odstrániť" track-by="name" label="name" placeholder="Select one" :options="categories" :searchable="false" :allow-empty="true">
                                            <template slot="singleLabel" slot-scope="{ option }"><strong>{{ option.name }}</strong></template>
                                        </multiselect>
                                        <pre class="language-json"><code>{{ categories.name  }}</code></pre>
                                    </div>
                                </div>
                            </div>
                            <button onclick="window.history.back()" type="submit" class="btn btn-info pull-left"><i class="material-icons">arrow_back</i></button>
                            <button class="btn btn-primary pull-right" type="submit">Upraviť</button>
                            <!--<button  class="btn btn-primary pull-right">Update Profile</button>-->
                            <div class="clearfix"></div>
                        </form>
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
                auth: auth,
                name: null,
                url: null,
                description: null,
                isVisible: false,
                isRead: null,
                category_id: null,
                selected: null,
                category_name: null,
                id: null,
                value: true,
                isAdmin: null,
                fullname: null,
                userId: null,
                bookmark: [],
                categories: [],
                errors: [],
                message: "",
                message_success: "",
            };
        },
        mounted(){
            this.getUserData();
            this.getId();
            this.allCategories();
            this.showBookmark();
            this.getUserName(this.id);
        },
        methods: {
            showBookmark() {
                axios
                    .get("bookmark/"+this.id, {
                        headers: { Authorization: "Bearer " + this.auth.getToken() }
                    })
                    .then(response => (
                        this.name = response.data.name,
                            this.url = response.data.url,
                            this.description = response.data.description,
                            this.isVisible = response.data.isVisible,
                            this.category_name = response.data.category_name,
                            this.category_id = response.data.category_id,
                            this.isRead = response.data.isRead


                    ))
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });

                if(this.isVisible == 1){
                    this.value = true;
                }
                else{
                    this.value = false;

                }
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
            getId() {
                var currentUrl = window.location.href;
                var strings = currentUrl.split("/");
                this.id = strings[5];
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
            getUserName(id){
                axios
                    .get("/bookmarks/"+id+"/user",
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.fullname = response.data;
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            editBookmark(id){
                var cat_id;
                if(this.selected == null){
                    cat_id = null;
                }
                else{
                    cat_id = this.selected.id;
                }
                axios
                    .patch('bookmarks/'+id,
                        {
                            name: this.name,
                            url: this.url,
                            description: this.description,
                            isVisible: this.isVisible,
                            category_id: cat_id,
                            isRead: this.isRead,
                        },
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {

                        this.message_success = "Záložka bola upravená.";
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            }
        }
    };
</script>

<style scoped>

</style>