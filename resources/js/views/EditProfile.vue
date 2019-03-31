<template>
    <div>
        <form @submit.prevent="edit()">
            <div v-if="message">
                <p>{{ message }}</p>
            </div>
            <label for="name">Meno:</label>
            <br>
            <input type="text" v-model="name" >
            <p v-if="errors.name">{{ errors.name[0] }}</p>
            <br>
            <label for="surname">Priezvisko:</label>
            <br>
            <input type="text" v-model="surname" >
            <p v-if="errors.surname">{{ errors.surname[0] }}</p>
            <br>
            <button type="submit">Upravit </button>
        </form>
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