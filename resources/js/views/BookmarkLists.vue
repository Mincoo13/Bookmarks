<template>
    <div>
        <h1>
           Vytvoriť nový zoznam
        </h1>
        <form @submit.prevent="createList()">
            <div>
                <label class="typo__label">Simple select / dropdown</label>
                <multiselect v-model="selected" :options="bookmarks" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Vyberte" label="name" track-by="name" :preselect-first="true">
                    <template slot="selection" slot-scope="{ values, search, isOpen }"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} záložiek zvolených</span></template>
                </multiselect>
            </div>
            <label for="name">Nazov</label>
            <br>
            <input type="text" v-model="name">
            <br>
            <label for="isVisible">Viditelnost</label>
            <br>
            <input type="checkbox" v-model="isVisible"  true-value=1 false-value=0>
            <br>
            <button type="submit">Odoslat</button>
        </form>
        <h1>Moje zoznamy</h1>
        <div v-for="bookmarklist in bookmarklists">
            <router-link :to="{ path: '/bookmark-lists/' + bookmarklist.id }"> {{ bookmarklist.name }} </router-link>
            <p>{{ bookmarklist.count }}</p>
            <button>Zmazať</button>
            <hr>
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
                isVisible: null,
                name: null,
                bookmarklists: [],
                bookmarks: [],
                selected: [],
                item: null,
                id: null,
            }
        },
        mounted(){
            this.getBookmarkLists();
            this.getBookmarks();
        },
        methods: {
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
                        this.id = response.data[0].id;

                        var item = null;
                        for(item in this.selected){
                            console.log(this.id+" "+this.selected[item].id);
                            axios
                                .post('bookmark-lists/'+response.data[0].id,
                                    {
                                        bookmark_id: this.selected[item].id,
                                    },
                                    {
                                        headers: {Authorization: "Bearer " + this.auth.getToken()}
                                    })
                                .then(response => {
                                    console.log(this.selected[item].id);
                                })
                                .catch(error => {
                                    console.log(error.response);
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