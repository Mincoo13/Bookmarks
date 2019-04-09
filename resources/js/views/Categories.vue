<template>
    <div>
        <h1>Pridať kategóriu</h1>
        <form @submit.prevent="addCategory()">
            <input v-model="name">
            <button type="submit">Vytvoriť</button>
        </form>
        <h1>Moje kategórie</h1>
        <div v-for="category in categories">
            <p>{{ category.name }}</p>
            <div v-if="message && currentCategory == category.name">
                <br>
                <p>{{ message }}</p>
            </div>
            <button v-if="!expand || currentCategory != category.name" v-on:click="(expand = true) && (currentCategory = category.name) && (categoryName = category.name)">Upraviť</button>
            <button v-if="expand == true && currentCategory == category.name" v-on:click="(expand = !expand) && (currentCategory = null)">Skryť</button>
            <button v-on:click="(currentCategory = category.name) && deleteCategory(category.id)">Zmazať</button>


            <div v-if="expand && currentCategory == category.name">
                <form @submit.prevent="editCategory(category.id)">
                    <input v-model="categoryName">
                    <button type="submit">Upraviť</button>
                </form>
            </div>
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
                id: null,
                fullnameBookmark: null,
                userId: null,
                categories: [],
                expand: false,
                currentCategory: null,
                message: "",
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
                        this.message = "Kategória bola vytvorená.";
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
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
                        this.message = "Kategória bola upravená.";
                        this.allCategories();
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
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