<template>

    <div>
        <div class="row">
            <div class="col-md-2">

            </div>
            <div class="col-md-8">
                <h2>Pridať záložku</h2>
                <button id="btn-bookmark" class="btn btn-primary pull-left" @click="showForm()"><i class="material-icons">add</i><div class="ripple-container"></div></button>

                <div class="card" id="bookmarkForm">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">Pridaj novú záložku</h4>
                        <!--<p class="card-category"></p>-->
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="createBookmark()">
                            <div v-if="message">
                                <p class="text-danger">{{ message }}</p>
                            </div>
                            <div v-else-if="message_success">
                                <p class="text-success">{{ message_success }}</p>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group bmd-form-group">
                                        <label class="bmd-label-floating"  for="name">Názov:</label>
                                        <input type="text" v-model="name" class="form-control">
                                        <p v-if="errors.name">{{ errors.name[0] }}</p>

                                    </div>
                                    <div class="form-group bmd-form-group">
                                        <label class="bmd-label-floating" for="surname">Url:</label>
                                        <input type="text" v-model="url" class="form-control">
                                        <p v-if="errors.url">{{ errors.url[0] }}</p>

                                    </div>
                                    <div class="form-group bmd-form-group">

                                        <div>
                                            <label class="typo__label">Vybert kategóriu:</label>
                                            <multiselect v-model="selected" deselect-label="Odstrániť"  track-by="name" label="name" placeholder="Select one" :options="categories" :searchable="false" :allow-empty="true">
                                                <template slot="singleLabel" slot-scope="{ option }"><strong>{{ option.name }}</strong></template>
                                            </multiselect>
                                            <pre class="language-json"><code>{{ categories.name  }}</code></pre>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <div class="form-group bmd-form-group">
                                            <label class="bmd-label-floating"  for="description">Popis</label>
                                            <textarea v-model="description" class="form-control" rows="5"></textarea>
                                            <p v-if="errors.description">{{ errors.description[0] }}</p>

                                        </div>
                                    </div>
                                    <div class="">
                                        <label class="bmd-label-floating"  for="isVisible">Nastaviť záložku ako viditeľnú pre ostatných:</label>
                                        <br>
                                        <toggle-button :value="isVisible" color="#9c27b0" v-model="isVisible"/>
                                        <p v-if="errors.isVisible">{{ errors.isVisible[0] }}</p>
                                    </div>

                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary pull-right">Vytvoriť<div class="ripple-container"></div></button>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <br>
        <br>
        <br>
        <br>

        <div class="row">
        <div class="col-md-2">

        </div>

        <div class="col-md-8">
            <form>
                <label for="category_table">Vyberte kategóriu:</label>
                <br>
                <multiselect style="z-index: 5 !important" v-model="category_select" @remove="nullCategory" @select="selectCategory" deselect-label="Odstrániť" track-by="name" label="name" placeholder="Vyberte" :options="categories" :searchable="false" :allow-empty="true">
                    <template slot="singleLabel" slot-scope="{ option }"><strong>{{ option.name }}</strong></template>
                </multiselect>
            </form>
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title ">Moje záložky</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" >
                            <thead class=" text-primary">
                            <th>Názov</th>
                            <th>Popis</th>
                            <th>Prečítaná</th>
                            <th>Kategória</th>
                            <th>Akcie</th>
                            </thead>
                            <tbody>
                            <tr v-for="bookmark in bookmarks" ref="datatable" >
                                <td><a target="_blank" :href="bookmark.url"><b>{{ bookmark.name }}</b></a> </td>
                                <td>
                                    <div>{{ bookmark.description }} </div>
                                </td>
                                <td>
                                    <div v-if="bookmark.isRead" class="form-check" style="text-align: center">
                                        <label class="form-check-label">
                                            <input class="form-check-input" type="checkbox" checked v-on:click="markRead(bookmark.id)">
                                            <span class="form-check-sign">
                                                <span class="check"></span>
                                            </span>
                                        </label>
                                    </div>

                                    <div v-else class="form-check" style="text-align: center">
                                        <label class="form-check-label">
                                            <input class="form-check-input" type="checkbox" v-on:click="markRead(bookmark.id)">
                                            <span class="form-check-sign">
                                                <span class="check"></span>
                                            </span>
                                        </label>
                                    </div>
                                </td>
                                <td > {{ bookmark.category_name }}</td>
                                <td>
                                    <router-link :to="'bookmark/' + bookmark.id " tag="button" class="btn btn-sm btn-info"><i class="material-icons">trending_flat</i></router-link>
                                    <button v-on:click="deleteBookmark(bookmark.id)" class="btn btn-sm btn-danger"><i class="material-icons">delete_outline</i></button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
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
                bookmarks: [],
                categories: [],
                value: null,
                url: null,
                name: null,
                description: null,
                category_id: null,
                category_name: null,
                category_table: null,
                category_select: null,
                isVisible: true,
                isRead: null,
                key: "",
                table_id: null,
                selected: null,
                auth: auth,
                message: null,
                message_success: null,
                info: null,
                errors: []
            };
        },

        mounted(){
            this.allCategories();
            this.allBookmarks();
        },
        methods: {

            showForm(){

                    $('#bookmarkForm').animate({height: "toggle", opacity: "toggle"}, "fast");
                    document.getElementById("btn-bookmark").style="display: none" ;

            },
            allBookmarks() {
                axios
                    .post("/get-bookmarks", {
                        category_id: this.category_select
                        },
                    {
                    headers: {Authorization: "Bearer " + this.auth.getToken()}
                })
                    .then(response => {
                        var i;
                        var desc;
                        this.bookmarks = response.data;
                        for(i in this.bookmarks){
                            desc = this.bookmarks[i].description;
                            if(desc.length > 100){
                                this.bookmarks[i].description = desc.substring(0, 70) + '...';
                            }
                        }

                        this.table_id = "load";

                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
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
            createBookmark(){
                var cat_id;
                if(this.selected == null){
                    cat_id = null;
                }
                else{
                    cat_id = this.selected.id;
                }
                axios
                    .post('/bookmarks',{
                        name: this.name,
                        url: this.url,
                        description: this.description,
                        isVisible: this.isVisible,
                        isRead: this.isRead,
                        category_id: cat_id,
                        },
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.message_success = "Záložka bola úspešne vytvorená.";
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            getCategory(id){
                axios
                    .get('/categories/'+id,
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.category_table = response.data ;
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            nullCategory(value){
                this.category_select = null;
                this.allBookmarks();
            },
            selectCategory(value){
                if(value == null){
                    this.category_select = null;
                    this.allBookmarks();
                    return;
                }
                this.category_select = value.id;
                this.allBookmarks();

            },
            markRead(id){
                axios
                    .patch("bookmarks/"+id+"/mark-read",{
                            data: this.data},
                        {
                        headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.category_table = response.data ;
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            deleteBookmark(id){
                axios
                    .delete('bookmarks/'+id,
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.allBookmarks();
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
