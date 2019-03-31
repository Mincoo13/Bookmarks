<template>
    <div>
        <p>This is the login page</p>
        <LoginForm></LoginForm>
        <button type="submit" v-on:click="forgottenpassword()">Zabudnuté heslo</button>
        <button type="submit" v-on:click="register()">Registrácia</button>
    </div>
</template>

<script>
    import LoginForm from '../components/LoginForm.vue';
    export default {
        components: {
            LoginForm
        },
        methods: {
            forgottenpassword(){
                this.$router.push("/forgottenpassword");
            },
            register(){
                this.$router.push("/register-user")
            }
        }

    };
</script>