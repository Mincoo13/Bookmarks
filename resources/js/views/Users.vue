<template>
    <div>
        <div class="card">
            <div class="card-header card-header-primary">
                <h4 class="card-title ">Moje záložky</h4>
            </div>
            <div class="card-body">
                <div v-if="message">
                    <p class="text-danger">{{ message }}</p>
                </div>
                <div v-else-if="message_success">
                    <p class="text-success">{{ message_success }}</p>
                </div>
                <div class="table-responsive">
                    <table class="table" >
                        <thead class=" text-primary">
                        <th>ID</th>
                        <th>Meno</th>
                        <th>Priezvisko</th>
                        <th>E-mail</th>
                        <th>Aktívny</th>
                        <th>Akcie</th>
                        </thead>
                        <tbody>
                        <tr v-for="user in users" ref="datatable" >
                            <td>{{ user.id }}</td>
                            <td>{{ user.name }}</td>
                            <td>{{ user.surname }}</td>
                            <td>{{ user.email }}</td>
                            <td>
                                <div v-if="user.isActive" class="form-check" >
                                        <i class="material-icons text-success">check</i>

                                </div>

                                <div v-else class="form-check" >
                                        <i class="material-icons text-danger">close</i>
                                </div>
                            </td>

                            <td>
                                <div class="row">
                                    <!--<div class="col-5" style="width: auto !important">-->
                                        <!--<router-link :to="'bookmark/' + bookmark.id " tag="button" class="btn btn-sm btn-info"><i class="material-icons">trending_flat</i></router-link>-->
                                    <!--</div>-->
                                    <!--<div class="col-5">-->
                                        <!--<button v-on:click="deleteBookmark(bookmark.id)" class="btn btn-sm btn-danger"><i class="material-icons">delete_outline</i></button>-->
                                    <!--</div>-->
                                    <router-link :to="'users/' + user.id " tag="button" class="btn btn-sm btn-info"><i class="material-icons">trending_flat</i></router-link>

                                    <button class="btn btn-sm btn-warning" v-if="user.isActive" v-on:click="deactivate(user.id)">Deaktivovať</button>
                                    <button class="btn btn-sm btn-warning" v-else="user.isActive" v-on:click="activate(user.id)">Aktivovať</button>
                                        <button v-on:click="destroy(user.id)" class="btn btn-sm btn-danger"><i class="material-icons">delete_outline</i></button>

                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
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
                data: null,
                users: this.users,
                name: null,
                surname: null,
                email: null,
                isActive: null,
                auth: auth,
                message: "",
                message_success: "",
                info: null,
                errors: []
            };
        },
        mounted(){
            this.all();
        },
        methods: {
            all() {
                axios
                    .get("/users", {
                        headers: {Authorization: "Bearer " + this.auth.getToken()}
                    })
                    .then(response => {
                        this.users = response.data;
                    });
            },
            activate(id){
                axios
                    .patch("/users/"+id+"/activate",{
                        data: this.data},
                        {
                        headers: {Authorization: "Bearer " + this.auth.getToken()}
                    })
                    .then(response => {
                        this.message_success = "Používateľ bol aktivovaný.";
                        this.all()
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            deactivate(id){
                axios
                    .patch("/users/"+id+"/deactivate",{
                        data: this.data},
                        {
                        headers: {Authorization: "Bearer " + this.auth.getToken()}
                    })
                    .then(response => {
                        this.message_success = "Používateľ bol deaktivovaný.";
                            this.all()
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            destroy(id){
                axios
                    .delete("/users/"+id,{
                        headers: {Authorization: "Bearer " + this.auth.getToken()}
                    })
                    .then(response => {
                        this.message_success = "Používateľ bol odstránený.";
                        this.all()
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