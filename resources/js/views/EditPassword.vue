<template>
    <div>
        <form @submit.prevent="edit()">
            <div v-if="message">
                <p>{{ message }}</p>
            </div>
            <label for="oldPassword">Aktualne heslo:</label>
            <br>
            <input type="password" v-model="oldPassword" >
            <p v-if="errors.oldPassword">{{ errors.oldPassword[0] }}</p>
            <br>
            <label for="newPassword">Nove heslo:</label>
            <br>
            <input type="password" v-model="newPassword" >
            <p v-if="errors.newPassword">{{ errors.newPassword[0] }}</p>
            <br>
            <label for="confirmPassword">Zopakujte nove heslo:</label>
            <br>
            <input type="password" v-model="confirmPassword">
            <p v-if="errors.confirmPassword">{{ errors.confirmPassword[0] }}</p>
            <br>
            <button type="submit">Zmenit heslo</button>
        </form>
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