<template>
    <form @submit.prevent="reset()">
        <div v-if="message">
            <p>{{ message }}</p>
        </div>
        <label for="email">E-mail:</label>
        <br>
        <input type="text" v-model="email">
        <p v-if="errors.email">{{ errors.email[0] }}</p>
        <br>
        <button type="submit">Odoslať</button>
    </form>
</template>

<script>
    export default {
        data() {
            return {
                email: null,
                password: null,
                message: null,
                errors: []
            };
        },
        methods: {
            reset() {
                axios
                    .post("/forgotten-password")
                    .then(response => {
                        this.message = "Na e-mailovú adresu " + this.email + " bolo odoslané novo vygenerované heslo.";
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

<style scoped>

</style>