<template>
    <div>
        Meno:
        {{ name }}
        <br>
        Priezvisko:
        {{ surname }}
        <br>
        E-mail:
        {{ email }}
        <br>
        Ucet bol vytvoreny dna {{ created_at }}
        <br>
        <button type="button" @click="edit()" >Upravit profil</button>
        <button type="button" @click="password()" >Zmenit heslo</button>
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