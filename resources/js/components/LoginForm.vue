<template>
    <form @submit.prevent="login()">
        <div v-if="message">
            <p>{{ message }}</p>
        </div>
        <label for="email">E-mail:</label>
        <br>
        <input type="text" v-model="email">
        <p v-if="errors.email">{{ errors.email[0] }}</p>
        <br>
        <label for="password">Heslo:</label>
        <br>
        <input type="password" v-model="password">
        <p v-if="errors.password">{{ errors.password[0] }}</p>
        <br>
        <button type="submit">prihlasit</button>
    </form>
</template>

<script>
    import auth from "../auth/index.js";
    export default {
        data() {
            return {
                email: null,
                password: null,
                auth: auth,
                message: null,
                errors: []
            };
        },
        methods: {
            login() {
                axios
                    .post("login", {
                        email: this.email,
                        password: this.password
                    })
                    .then(response => {
                        auth.setToken(response.data.data.token);
                        this.$router.push("/");
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