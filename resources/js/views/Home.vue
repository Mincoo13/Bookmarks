<template>

    <div>
        <h1>Moje záložky</h1>

        <h2>Pridať záložku</h2>
        <form @submit.prevent="createBookmark()">
            <div v-if="message">
                <p>{{ message }}</p>
            </div>
            <label for="name">Názov:</label>
            <br>
            <input type="text" v-model="name">
            <p v-if="errors.name">{{ errors.name[0] }}</p>
            <br>
            <label for="surname">Url:</label>
            <br>
            <input type="text" v-model="url">
            <p v-if="errors.url">{{ errors.url[0] }}</p>
            <br>
            <label for="description">Popis:</label>
            <br>
            <textarea v-model="description" ></textarea>
            <p v-if="errors.description">{{ errors.description[0] }}</p>
            <br>
            <label for="category">Vyberte kategóriu:</label>
            <br>
            <select v-model="category_id" >
                <option value=null>--Žiadna--</option>
                    <option v-for="category in categories" v-bind:value=category.id >
                        {{ category.name }}
                    </option>
            </select>
            <br>
            <label for="isVisible">Verejná:</label>
            <input type="checkbox" v-model="isVisible" checked>
            <br>
            <p v-if="errors.isVisible">{{ errors.isVisible[0] }}</p>
            <button type="submit">Vytvoriť</button>
        </form>


        <h2>Záložky</h2>
        <form>
            <label for="category_table">Vyberte kategóriu:</label>
            <br>
            <select v-model="category_select" @change="selectCategory($event)">
                <option :value=null>--Všetky--</option>
                <option v-for="category in categories" v-bind:value=category.id >
                    {{ category.name }}
                </option>
            </select>
        </form>
        <div >
                <table id="mytable" class="display">
                    <thead>
                    <tr>
                        <th>Záložka</th>
                        <th>Popis</th>
                        <th>Prečítaná</th>
                        <th>Kategória</th>
                        <th>Akcie</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="bookmark in bookmarks" ref="datatable" >
                        <td><a target="_blank" :href="bookmark.url">{{ bookmark.name }}</a> </td>
                        <td> {{ bookmark.description }}</td>
                        <td>
                            <input v-if="bookmark.isRead" type="checkbox" checked v-on:click="markRead(bookmark.id)">
                            <input v-else type="checkbox" v-on:click="markRead(bookmark.id)">
                        </td>
                        <td > {{ bookmark.category_name }}</td>
                        <td>
                            <router-link :to="'bookmark/' + bookmark.id " tag="button">Podrobnosti</router-link>
                            <button v-on:click="deleteBookmark(bookmark.id)">Zmazať</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
        </div>
    </div>
</template>

<script>

    import auth from "../auth/index.js";
    export default {
        data() {
            return {
                data: null,
                bookmarks: this.bookmarks,
                categories: this.categories,
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
                auth: auth,
                message: null,
                info: null,
                errors: []
            };
        },

        mounted(){
            this.allCategories();
            this.allBookmarks();
            setTimeout(function(){
                $(document).ready(function() {
                    $('#mytable').DataTable( {
                        data: this.bookmarks,
                        columns: [
                            { title: "Názov" },
                            { title: "Popis" },
                            { title: "Verejná" },
                            { title: "Kategória" },
                            { title: "Akcie" },
                        ]
                    } );
                } );
            }, 1000);
        },
        methods: {
            allBookmarks() {
                axios
                    .post("/get-bookmarks", {
                        category_id: this.category_select
                        },
                    {
                    headers: {Authorization: "Bearer " + this.auth.getToken()}
                })
                    .then(response => {
                        this.bookmarks = response.data;
                        this.table_id = "load";

                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            table(){
                $(document).ready(function() {
                    $('#mytable').DataTable( {
                        data: this.bookmarks,
                        columns: [
                            { title: "Name" },
                            { title: "Position" },
                            { title: "Office" },
                            { title: "Extn." },
                            { title: "Start date" },
                        ]
                    } );
                } );
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
                axios
                    .post('/bookmarks',{
                        name: this.name,
                        url: this.url,
                        description: this.description,
                        isVisible: this.isVisible,
                        isRead: this.isRead,
                        category_id: this.category_id,
                        },
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.message = "Záložka bola úspešne vytvorená.";
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
            selectCategory(event){
                if(event.target.value == null){
                    this.allBookmarks();
                    this.category_select = null;
                    return;
                }
                this.allBookmarks();
                this.category_select = event.target.value;
                console.log(this.category_select);

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
