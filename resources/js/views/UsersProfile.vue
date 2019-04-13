<template>
    <div>
        <div class="row">
            <div class="col-md-3">
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h3 class="card-title">Profil používateľa {{ name }} {{ surname }}</h3>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="edit()">
                            <div v-if="message">
                                <p class="text-danger">{{ message }}</p>
                            </div>
                            <div v-else-if="message_success">
                                <p class="text-success">{{ message_success }}</p>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="bmd-label-static"  for="name">Meno</label>
                                        <input class="form-control" type="text" v-model="name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="bmd-label-static"  for="surname">Priezvisko:</label>
                                        <input class="form-control" type="text" v-model="surname">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="bmd-label-static"  for="email">Email:</label>
                                        <br>
                                        {{ email }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="bmd-label-static"  for="created_at">Vytvoerný dňa:</label>
                                        <br>
                                        {{ created_at }}
                                    </div>
                                </div>
                            </div>

                            <button onclick="window.history.back()" type="submit" class="btn btn-info pull-left"><i class="material-icons">arrow_back</i></button>
                            <button  type="submit"  class="btn btn-primary pull-right">Upraviť</button>

                            <div class="clearfix"></div>
                        </form>
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
                name: null,
                id: null,
                surname: null,
                email: null,
                created_at: null,
                auth: auth,
                currentUrl: null,
                message: "",
                message_success: "",
                info: null,
                errors: []
            };
        },
        created() {
                var currentUrl = window.location.href;
                var strings = currentUrl.split("/");
                this.id = strings[5];
                console.log(strings[5]);
        },
        mounted(){
            axios
                .get('/users/'+this.id,{
                    headers: {Authorization: "Bearer " + this.auth.getToken()}
                })
                .then(response => (
                    this.name = response.data.name,
                        this.surname = response.data.surname,
                        this.email = response.data.email,
                        this.created_at = response.data.created_at
                ))
        },

        methods: {
            edit(){
                axios
                    .patch('/profile/'+this.id,{
                            name: this.name,
                            surname: this.surname
                        },
                        {
                            headers: {Authorization: "Bearer " + this.auth.getToken()}
                        })
                    .then(response => {
                        this.message_success = "Používateľ bol upravený.";
                        this.message = "";
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message_success = "";
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            }
        }
    };
</script>