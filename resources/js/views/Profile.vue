<template>
    <div>
        <div class="row">
            <div class="col-md-3">
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h3 class="card-title">Môj profil</h3>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <b>Meno:</b><br>{{ name }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <b>Priezvisko:</b><br>{{ surname }}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <b>E-mail:</b><br>{{ email }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <b>Profil založený dňa:</b><br>{{ created_at }}
                                    </div>
                                </div>
                            </div>
                            <!--<button onclick="window.history.back()" type="submit" class="btn btn-info pull-left"><i class="material-icons">arrow_back</i></button>-->
                            <button  type="button" @click="edit()" class="btn btn-primary pull-right">Upraviť profil</button>
                            <button type="button" class="btn btn-primary pull-right" @click="password()" >Zmeniť heslo</button>

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
                this.$router.push("/edit-profile");
            },
            password(){
                this.$router.push("/edit-password");
            }
        }
    };
</script>