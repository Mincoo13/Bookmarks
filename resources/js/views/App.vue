<template>

    <div>

        <h1>Shared Bookmarks</h1>

        <p>
            <router-link :to="{ name: 'home' }">Home</router-link> |
            <router-link :to="{ name: 'hello' }">Hello World</router-link> |
            <router-link :to="{ name: 'login' }">Login</router-link> |
            <router-link :to="{ name: 'profile' }">Profile</router-link> |
            <router-link :to="{ name: 'users' }">Users</router-link>
        </p>

        <div class="container">
            <router-view></router-view>
        </div>
        <button type="button" @click="logout()" v-if="auth.check()">Odhlásiť sa</button>
    </div>
</template>
<script>
    import auth from "../auth/index.js";
    export default {
        data() {
            return {
                auth: auth
            };
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
            }
        }
    };
</script>