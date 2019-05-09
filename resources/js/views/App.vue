<template>

    <div>
    <div class="wrapper" v-if="auth.check()">
        <div class="sidebar" data-color="purple" data-background-color="white" data-image="../assets/img/sidebar-1.jpg">
            <div class="logo">
                <a class="simple-text logo-normal">
                    Shared Bookmarks
                </a>
            </div>
            <div class="sidebar-wrapper">
                <ul class="nav">
                    <li id="dashboard" class="nav-item active" @click="active()">
                        <router-link class="nav-link" tag="a" :to="{ name: 'home' }">
                            <i class="material-icons">bookmarks</i>
                            <p>Domov</p>
                        </router-link>
                    </li>
                    <!--<li id="login" class="nav-item " @click="active()">-->
                        <!--<router-link class="nav-link" tag="a" :to="{ name: 'login' }">-->
                            <!--<i class="material-icons">person</i>-->
                            <!--<p>Login</p>-->
                        <!--</router-link>-->
                    <!--</li>-->
                    <li id="profile" class="nav-item " @click="active()">
                        <router-link class="nav-link" tag="a" :to="{ name: 'profile' }">
                            <i class="material-icons">person</i>
                            <p>Profil</p>
                        </router-link>
                    </li>
                    <li id="categories" class="nav-item " @click="active()">
                        <router-link class="nav-link" tag="a" :to="{ name: 'categories' }">
                            <i class="material-icons">category</i>
                            <p>Kategórie</p>
                        </router-link>
                    </li>
                    <li id="lists" class="nav-item " @click="active()">
                        <router-link class="nav-link" tag="a" :to="{ name: 'bookmark-lists' }">
                            <i class="material-icons">library_books</i>
                            <p>Zoznamy</p>
                        </router-link>
                    </li>
                    <li id="search" class="nav-item " @click="active()">
                        <router-link class="nav-link" tag="a" :to="{ name: 'search-bookmarks' }">
                            <i class="material-icons">search</i>
                            <p>Vyhľadať záložky</p>
                        </router-link>
                    </li>
                    <li id="users" class="nav-item " @click="active()">
                        <router-link class="nav-link" tag="a" v-if="isAdmin == 1" :to="{ name: 'users' }">
                            <i class="material-icons">people</i>
                            <p>Používatelia</p>
                        </router-link>

                    </li>
                    <li id="logout" class="nav-item active-pro logout">
                        <a class="nav-link" @click="logout()" v-if="auth.check()">
                        <i class="material-icons">arrow_drop_down</i>
                        <p>Odhlásiť</p>
                    </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="main-panel">
            <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
                <div class="container-fluid">
                    <div class="navbar-wrapper">
                        <a class="navbar-brand">{{ fullname }}</a>
                    </div>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="navbar-toggler-icon icon-bar"></span>
                        <span class="navbar-toggler-icon icon-bar"></span>
                        <span class="navbar-toggler-icon icon-bar"></span>
                    </button>

                </div>
            </nav>
            <div class="content">
                <div class="container-fluid">
                    <router-view></router-view>
                </div>
            </div>
            <div class="container">
            </div>

        </div>
    </div>
        <div v-else>
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
                auth: auth,
                activeURL: null,
                fullname: null,
            };
        },
        mounted() {
          this.getUserData();
          this.active();
        },
        methods: {
            active(){
                    var currentUrl = window.location.href;
                    var strings = currentUrl.split("/");
                    this.activeURL = strings[4];
                    switch(this.activeURL){
                        case "":
                            document.getElementById("dashboard").classList.add("active");

                            document.getElementById("profile").classList.remove("active");
                            document.getElementById("categories").classList.remove("active");
                            document.getElementById("search").classList.remove("active");
                            document.getElementById("lists").classList.remove("active");
                            document.getElementById("users").classList.remove("active");

                            break;
                        case "profile":
                            document.getElementById("profile").classList.add("active");

                            document.getElementById("dashboard").classList.remove("active");
                            document.getElementById("categories").classList.remove("active");
                            document.getElementById("search").classList.remove("active");
                            document.getElementById("lists").classList.remove("active");
                            document.getElementById("users").classList.remove("active");
                            break;
                        case "categories":
                            document.getElementById("categories").classList.add("active");

                            document.getElementById("profile").classList.remove("active");
                            document.getElementById("dashboard").classList.remove("active");
                            document.getElementById("search").classList.remove("active");
                            document.getElementById("lists").classList.remove("active");
                            document.getElementById("users").classList.remove("active");
                            break;
                        case "search-bookmarks":
                            document.getElementById("search").classList.add("active");

                            document.getElementById("profile").classList.remove("active");
                            document.getElementById("categories").classList.remove("active");
                            document.getElementById("dashboard").classList.remove("active");
                            document.getElementById("lists").classList.remove("active");
                            document.getElementById("users").classList.remove("active");
                            break;
                        case "bookmark-lists":
                            document.getElementById("lists").classList.add("active");

                            document.getElementById("profile").classList.remove("active");
                            document.getElementById("categories").classList.remove("active");
                            document.getElementById("search").classList.remove("active");
                            document.getElementById("dashboard").classList.remove("active");
                            document.getElementById("users").classList.remove("active");
                            break;
                        case "users":
                            document.getElementById("users").classList.add("active");

                            document.getElementById("profile").classList.remove("active");
                            document.getElementById("categories").classList.remove("active");
                            document.getElementById("search").classList.remove("active");
                            document.getElementById("lists").classList.remove("active");
                            document.getElementById("dashboard").classList.remove("active");
                            break;

                    }
            },
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
                        this.fullname = response.data.name+" "+response.data.surname;
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