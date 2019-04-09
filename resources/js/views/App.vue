<template>

    <div>

        <h1>Shared Bookmarks</h1>

        <p>
            <router-link :to="{ name: 'home' }">Home</router-link> |
            <router-link :to="{ name: 'login' }">Login</router-link> |
            <router-link :to="{ name: 'profile' }">Profil</router-link> |
            <router-link :to="{ name: 'categories' }">Kategórie</router-link> |
            <router-link :to="{ name: 'bookmark-lists' }">Zoznamy</router-link> |
            <router-link v-if="isAdmin == 1" :to="{ name: 'users' }">Users</router-link> |
    <router-link :to="{ name: 'search-bookmarks' }">Vyhľadať záložky</router-link> |
            <button type="button" @click="logout()" v-if="auth.check()">Odhlásiť sa</button>
        </p>

        <div class="container">
            <router-view></router-view>
        </div>
    </div>
</template>
<script>
    import auth from "../auth/index.js";
    export default {
        data() {
            return {
                isAdmin: null,
                userId: null,
                auth: auth
            };
        },
        mounted() {
          this.getUserData();
        },
        methods: {
            logout() {
                axios
                    .get("logout", {
                        headers: { Authorization: "Bearer " + this.auth.getToken() }
                    })
                    .then(response => {
                        auth.removeToken();
                        this.$router.push("/login");
                    })
                    .catch(error => {
                        console.log(error.response.data);
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
    };
</script>