<template>
    <form @submit.prevent="register()">
        <div v-if="message">
            <p>{{ message }}</p>
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
        <input type="text" v-model="email">
        <p v-if="errors.email">{{ errors.email[0] }}</p>
        <br>
        <label for="password">Heslo:</label>
        <br>
        <input type="password" v-model="password">
        <p v-if="errors.password">{{ errors.password[0] }}</p>
        <br>
        <label for="password_confirmation">Potvrdte heslo:</label>
        <br>
        <input type="password" v-model="password_confirmation">
        <p v-if="errors.password_confirmation">{{ errors.password_confirmation[0] }}</p>
        <br>
        <button type="submit">Zaregistrovat</button>
    </form>
</template>

<script>
    export default {
        data() {
            return {
                name: null,
                surname: null,
                email: null,
                password: null,
                password_confirmation: null,
                message: null,
                errors: []
            };
        },
        methods: {
            register() {
                axios
                    .post("register-user", {
                        name: this.name,
                        surname: this.surname,
                        email: this.email,
                        password: this.password,
                        password_confirmation: this.password_confirmation
                    })
                    .then(response => {
                        this.$router.push("/login");
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