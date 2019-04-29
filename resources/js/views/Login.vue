<template>
    <div>
        <div class="login-page login-form" style="height:100% !important; min-height: 100vh !important;">
            <div class="form">
                <!--<button type="button" v-on:click="reload()">Reload</button>-->

                <form id="register-form" class="register-form" @submit.prevent="register()">
                    <div class="text-danger" v-if="message_reg">
                        <p>{{ message_reg }}</p>
                    </div>
                    <label for="name">Meno:</label>
                    <br>
                    <input type="text" v-model="name">
                    <p v-if="errors.name">{{ errors.name[0] }}</p>
                    <br>
                    <label for="surname">Priezvisko:</label>
                    <br>
                    <input type="text" v-model="surname">
                    <p v-if="errors.surname">{{ errors.surname[0] }}</p>
                    <br>
                    <label for="email">E-mail:</label>
                    <br>
                    <input type="text" v-model="email_reg">
                    <p v-if="errors.email_reg">{{ errors.email_reg[0] }}</p>
                    <br>
                    <label for="password">Heslo:</label>
                    <p>Heslo musí obsahovať minimálne 8 znakov, jedno veľké písmeno, jednu číslicu a jeden špeciálny znak. </p>
                    <br>
                    <input type="password" v-model="password_reg">
                    <p v-if="errors.password_reg">{{ errors.password_reg[0] }}</p>
                    <br>
                    <label for="password_confirmation">Potvrdte heslo:</label>
                    <br>
                    <input type="password" v-model="password_confirmation">
                    <p v-if="errors.password_confirmation">{{ errors.password_confirmation[0] }}</p>
                    <br>
                    <button type="submit">Zaregistrovať</button>
                    <p class="message log">Máte už účet? <a>Prihláste sa</a></p>
                </form>

                <!--Prihlasenie formular-->

                <form id="login-form" class="login-form-1" @submit.prevent="login()">
                    <div class="text-danger" v-if="message_login">
                        <p>{{ message_login }}</p>
                    </div>
                    <div v-else-if="message_login_success" class="text-success">
                        <p>{{ message_login_success }}</p>
                    </div>
                    <input type="text" placeholder="E-mail" v-model="email"/>
                    <p v-if="errors.email">{{ errors.email[0] }}</p>

                    <input type="password" placeholder="Heslo" v-model="password"/>
                    <p v-if="errors.password">{{ errors.password[0] }}</p>

                    <button type="submit">prihlásiť</button>
                    <p class="message reg">Nemáte účet? <a>Vytvorte si ho</a></p>

                    <p class="message forg"><a>Zabudol som heslo</a></p>
                </form>

                <form id="reset-password-form" class="reset-password-form" @submit.prevent="reset()">
                    <div v-if="message_forg" class="text-danger">
                        <p>{{ message_forg }}</p>
                    </div>
                    <label for="email">E-mail:</label>
                    <br>
                    <input type="text" v-model="email_forg">
                    <p v-if="errors.email_forg">{{ errors.email_forg[0] }}</p>
                    <br>
                    <button type="submit">Odoslať</button>
                    <p class="message log-forg">Vrátiť sa späť na <a>prihlásenie</a></p>
                </form>

            </div>
        </div>
    </div>
</template>

<script>
    import auth from "../auth/index.js";

    export default {
        data() {
            return {
                email: null,
                password: null,
                auth: auth,
                message_login: null,
                message_login_success: null,
                message_forg: null,
                message_reg: null,
                name: null,
                surname: null,
                email_reg: null,
                email_forg: null,
                password_reg: null,
                password_confirmation: null,
                errors: []
            };
        },
        mounted(){

            $('.reg a').click(function(){
                // document.getElementsByClassName("reset-password-form").style="display:none";

                $('form').animate({height: "toggle", opacity: "toggle"}, "slow");
                document.getElementById("reset-password-form").style="display:none !important";
                this.message_login = null;

            });
            $('.log a').click(function(){
                $('form').animate({height: "toggle", opacity: "toggle"}, "slow");
                document.getElementById("reset-password-form").style="display:none !important";
                // document.getElementsByClassName("register-form").style="display:none";
                this.message_reg = null;


            });
            $('.forg a').click(function(){
                $('form').animate({height: "toggle", opacity: "toggle"}, "slow");
                document.getElementById("register-form").style="display:none";
                // document.getElementsByClassName("reset-password-form").style="display:none";
                this.message_login = null;

            });
            $('.log-forg a').click(function(){
                $('form').animate({height: "toggle", opacity: "toggle"}, "slow");
                document.getElementById("register-form").style="display:none";
                // document.getElementsByClassName("reset-password-form").style="display:none";
                this.message_forg = null;

            });

            // $('.message a').click(function(){
            //     $('reset-password-form').animate({height: "toggle", opacity: "toggle"}, "slow");
            // });
        },
        methods: {
            reset() {
                axios
                    .post("/forgotten-password",{
                        email: this.email_forg,
                    })
                    .then(response => {
                        this.message_forg = null;
                        this.message_login_success = "Na e-mailovú adresu " + this.email_forg + " bolo odoslané novo vygenerované heslo.";
                        $('form').animate({height: "toggle", opacity: "toggle"}, "slow");
                        document.getElementById("register-form").style="display:none !important";
                        // document.getElementsByClassName("reset-password-form").style="display:none";
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message_forg = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            register(){
                axios
                    .post("register-user", {
                        name: this.name,
                        surname: this.surname,
                        email: this.email_reg,
                        password: this.password_reg,
                        password_confirmation: this.password_confirmation
                    })
                    .then(response => {
                        this.message_login_success = "Boli ste zaregistrovaný, teraz sa môžete prihlásiť.";
                        $('form').animate({height: "toggle", opacity: "toggle"}, "slow");
                        document.getElementById("reset-password-form").style="display:none !important";
                        // document.getElementsByClassName("register-form").style="display:none";
                        this.message_reg = null;
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message_reg = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            reload(){
                location.reload();

            },
            login() {
                axios
                    .post("login", {
                        email: this.email,
                        password: this.password
                    })
                    .then(response => {
                        auth.setToken(response.data.data.token);
                        this.$router.push("/");
                        location.reload();
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message_login = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            }
        }
    };
</script>