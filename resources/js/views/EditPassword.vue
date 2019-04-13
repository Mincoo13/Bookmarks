<template>
    <div>
        <div class="row">
            <div class="col-md-3">
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h3 class="card-title">Zmeniť heslo</h3>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="edit()">
                            <div v-if="message">
                                <p class="text-danger">{{ message }}</p>
                            </div>
                            <div class="row">
                                <div class="col-md-1">
                                </div>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <label class="bmd-label-static"  for="oldPassword">Aktuálne heslo:</label>
                                        <input class="form-control" type="password" v-model="oldPassword">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-1">
                                </div>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <label class="bmd-label-static"  for="newPassword">Nové heslo:</label>
                                        <input class="form-control" type="password" v-model="newPassword">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-1">
                                </div>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <label class="bmd-label-static"  for="confirmPassword">Zopakujte nové heslo:</label>
                                        <input class="form-control" type="password" v-model="confirmPassword">
                                    </div>
                                </div>
                            </div>

                            <button onclick="window.history.back()" type="submit" class="btn btn-info pull-left"><i class="material-icons">arrow_back</i></button>
                            <button  type="submit"  class="btn btn-primary pull-right">Zmeniť</button>

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
                oldPassword: null,
                newPassword: null,
                confirmPassword: null,
                auth: auth,
                message: null,
                info: null,
                errors: []
            };
        },
        methods: {
            edit(){
                axios
                    .patch('/users/changepassword',{
                            oldPassword: this.oldPassword,
                            newPassword: this.newPassword,
                            confirmPassword: this.confirmPassword
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