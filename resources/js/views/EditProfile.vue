<template>
    <div>
        <div class="row">
            <div class="col-md-3">
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h3 class="card-title">Upraviť profil</h3>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="edit()">
                            <div v-if="message">
                                <p class="text-danger">{{ message }}</p>
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
                surname: null,
                email: null,
                created_at: null,
                auth: auth,
                message: null,
                info: null,
                errors: []
            };
        },
        mounted(){
            axios
                .get('/profile',{
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
                    .patch('/profile',{
                        name: this.name,
                        surname: this.surname
                    },
                        {
                        headers: {Authorization: "Bearer " + this.auth.getToken()}
                    })
                    .then(response => {
                        this.$router.push("/profile");
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