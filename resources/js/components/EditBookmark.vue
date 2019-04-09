<template>
    <div>
        <h1>Úprava záložky</h1>
        <div v-if="message">
            <p>{{ message }}</p>
        </div>
        <form @submit.prevent="editBookmark(id)">
            <label for="name">Názov:</label>
            <br>
            <input type="text" v-model="name">
            <p v-if="errors.name">{{ errors.name[0] }}</p>
            <br>
            <label for="url">URL:</label>
            <br>
            <input type="text" v-model="url">
            <p v-if="errors.url">{{ errors.url[0] }}</p>
            <br>
            <label for="description">Popis:</label>
            <br>
            <textarea v-model="description"></textarea>
            <p v-if="errors.description">{{ errors.description[0] }}</p>
            <br>
            <label for="isVisible">Verejná:</label>
            <input type="checkbox" v-model="isVisible" true-value="1" false-value="0">
            <p v-if="errors.isVisible">{{ errors.isVisible[0] }}</p>
            <br>
            <label>Kategória</label>
            <br>
            <select v-model="category_id">
                <option value=null>--Žiadna--</option>
                <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
            </select>
            <button type="submit">Upraviť</button>
        </form>
    </div>
</template>

<script>
    import auth from "../auth/index.js";
    export default {
        data() {
            return {
                auth: auth,
                name: null,
                url: null,
                description: null,
                isVisible: null,
                category_id: null,
                category_name: null,
                id: null,
                isAdmin: null,
                fullname: null,
                userId: null,
                bookmark: [],
                categories: [],
                errors: [],
                message: "",
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
                            this.category_id = response.data.category_id
                    ))
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
                axios
                    .patch('bookmarks/'+id,
                        {
                            name: this.name,
                            url: this.url,
                            description: this.description,
                            isVisible: this.isVisible,
                            category_id: this.category_id,
                        },
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.message = "Záložka bola upravená.";
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