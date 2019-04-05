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
            E-mail:
            {{ email }}
            <br>
            Ucet bol vytvoreny dna {{ created_at }}
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
                id: null,
                surname: null,
                email: null,
                created_at: null,
                auth: auth,
                currentUrl: null,
                message: null,
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
                        this.message = "Používateľ bol upravený.";
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