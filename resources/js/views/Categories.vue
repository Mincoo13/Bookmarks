<template>
    <div>
        <div class="row">
            <div class="col-md-3">

            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h3 class="card-title">Vytvoriť novú kategóriu</h3>
                    </div>
                    <div class="card-body">
                        <div v-if="message_create_error">
                            <p class="text-danger">{{ message_create_error }}</p>
                        </div>
                        <div v-else-if="message_create_success">
                            <p class="text-success">{{ message_create_success }}</p>
                        </div>
                            <form @submit.prevent="addCategory()">
                                <div class="row">
                                <div class="col-md-3">
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="bmd-label-floating"  for="name">Názov novej kategórie</label>
                                        <input type="text" class="form-control" v-model="name">

                                    </div>
                                </div>
                                </div>
                                <button  type="submit"  class="btn btn-primary pull-right">Vytvoriť</button>
                            </form>
                            <div class="clearfix"></div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header card-header-text">
                        <h3 class="card-title">Moje kategórie</h3>
                    </div>
                    <div class="card-body">
                        <div v-for="category in categories">
                            <div class="row">
                                <div class="col-md-6">
                                    <div v-if="message_error">
                                        <p class="text-danger">{{ message_error }}</p>
                                    </div>
                                    <div v-else-if="message_success">
                                        <p class="text-success">{{ message_success }}</p>
                                    </div>
                                    <h4>{{ category.name }}</h4>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button  class="btn btn-sm btn-danger pull-right" v-on:click="(currentCategory = category.name) && deleteCategory(category.id)"><i class="material-icons">delete_outline</i></button>
                                            <button class="btn btn-sm btn-primary pull-right" v-if="!expand || currentCategory != category.name" v-on:click="(expand = true) && (currentCategory = category.name) && (categoryName = category.name)"><i class="material-icons">edit</i></button>
                                            <button  class="btn btn-sm btn-primary pull-right" v-if="expand == true && currentCategory == category.name" v-on:click="(expand = !expand) && (currentCategory = null)">Skryť</button>
                                        </div>
                                        <div class="col-md-12">
                                            <div v-if="expand && currentCategory == category.name">
                                                <form @submit.prevent="editCategory(category.id)">
                                                    <div class="form-group">
                                                        <input class="form-control" v-model="categoryName">
                                                    </div>
                                                    <button class="btn btn-sm btn-primary pull-right" type="submit">Upraviť</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <hr>
                        </div>
                        <div class="clearfix"></div>
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
                id: null,
                fullnameBookmark: null,
                userId: null,
                categories: [],
                expand: false,
                currentCategory: null,
                message_create_error: "",
                message_create_success: "",
                message_error: "",
                message_success: "",
            };
        },
        mounted(){
            this.allCategories();
        },
        methods: {
            addCategory(){
                axios
                    .post('/categories',
                        {
                            name: this.name,
                        },
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.message_create_error= "";
                        this.message_create_success = "Kategória bola vytvorená.";
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message_create_success = "";
                        this.message_create_error = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            allCategories(){
                axios
                    .get('/categories',
                        {
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
            editCategory(id){
                axios
                    .patch('/categories/'+id,
                        {
                           name: this.categoryName,
                        },
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.message_error = "";
                        this.message_success = "Kategória bola upravená.";
                        this.allCategories();
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message_success = "";
                        this.message_error = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            deleteCategory(id){
                axios
                    .delete('categories/'+id,
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.message = "Kategória bola zmazaná.";
                        this.allCategories();
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