<template>
    <div>
        <h1>Vyhľadať záložky</h1>
        <form @submit.prevent="searchBookmarks()">
            <div v-if="message">
                <p>{{ message }}</p>
            </div>
            <label for="text">Text</label>
            <br>
            <input type="text" v-model="text">
            <p v-if="errors.text">{{ errors.text[0] }}</p>
            <br>
            <label for="category">Vyberte kategóriu:</label>
            <br>
            <select v-model="category_name" v-on:change="allCategories()">
                <option value="">--Žiadna--</option>
                <option v-for="category in categories" v-bind:value=category.name >
                    {{ category.name }}
                </option>
            </select>
            <br>
            <div v-if="category_name == ''">
                <label for="global">Hľadať globálne: </label>
                <input  type="checkbox" v-model="global">
                <p v-if="errors.global">{{ errors.global[0] }}</p>
            </div>
            <div v-else>
                <label for="global">Hľadať globálne: </label>
                <input  type="checkbox" v-model="global" disabled>
                <p v-if="errors.global">{{ errors.global[0] }}</p>
            </div>

            <div v-if="global == 1">
                <label for="read">Hľadať na základe prečítanosti: </label>
                <br>
                <label>Nezáleží</label>
                <input  type="radio" v-model="read" disabled checked value="all">
                <label>Prečítané</label>
                <input  type="radio" v-model="read" disabled value=1>
                <label>Neprečítané</label>
                <input  type="radio" v-model="read" disabled value=2>
                <p v-if="errors.read">{{ errors.read[0] }}</p>
            </div>
            <div v-else>
                <label for="read">Hľadať na základe prečítanosti: </label>
                <br>
                <label>Nezáleží</label>
                <input  type="radio" v-model="read" checked value="all">
                <label>Prečítané</label>
                <input  type="radio" v-model="read" value=1>
                <label>Neprečítané</label>
                <input  type="radio" v-model="read" value=2>
                <p v-if="errors.read">{{ errors.read[0] }}</p>
            </div>
            <br>
            <button type="submit">Zobraziť</button>





        </form>


        <h2>Výsledky</h2>
        <div v-if="bookmarks">
            <div v-for="bookmark in bookmarks">
                <p><a :href="bookmark.url" target="_blank">{{ bookmark.name }}</a></p>
                <p>{{ bookmark.description }}</p>
                <router-link :to="'bookmark/' + bookmark.id " tag="button">Podrobnosti</router-link>
                <button v-if="isAdmin == true" v-on:click="deleteBookmark(bookmark.id)">Zmazať</button>
                <hr>

            </div>
        </div>
        <div v-else>
            <p>{{ message }}</p>
        </div>
    </div>
</template>

<script>
    import auth from "../auth/index.js";
    export default {
        data() {
            return {
                data: null,
                text: null,
                global: false,
                read: "all",
                bookmarks: this.bookmarks,
                categories: this.categories,
                url: null,
                name: null,
                description: null,
                category_id: null,
                category_name: "",
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
                axios
                    .post("search-bookmarks",{
                        text: this.text,
                        category: this.category_name,
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