<template>
    <div>
        <div v-if="message">
            <p>{{ message }}</p>
        </div>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Meno</th>
                <th>Priezvisko</th>
                <th>Email</th>
                <th>Aktivny</th>
                <th>Akcie</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="user in users" ref="datatable">
                <td> {{ user.id }}</td>
                <td> {{ user.name }}</td>
                <td> {{ user.surname }}</td>
                <td> {{ user.email }}</td>
                <td> {{ user.isActive }}</td>
                <td>
                    <button v-on:click="showProfile(user.id)">Zobrazit profil</button>
                    <button v-if="user.isActive" v-on:click="deactivate(user.id)">Deaktivovať</button>
                    <button v-else="user.isActive" v-on:click="activate(user.id)">Aktivovať</button>
                    <button v-on:click="destroy(user.id)">Zmazať</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import auth from "../auth/index.js";
    export default {
        data() {
            return {
                data: null,
                users: this.users,
                name: null,
                surname: null,
                email: null,
                isActive: null,
                auth: auth,
                message: null,
                info: null,
                errors: []
            };
        },
        mounted(){
            this.all();
        },
        methods: {
            all() {
                axios
                    .get("/users", {
                        headers: {Authorization: "Bearer " + this.auth.getToken()}
                    })
                    .then(response => {
                        this.users = response.data;
                    });
            },
            activate(id){
                axios
                    .patch("/users/"+id+"/activate",{
                        data: this.data},
                        {
                        headers: {Authorization: "Bearer " + this.auth.getToken()}
                    })
                    .then(response => {
                        this.message = "Používateľ bol aktivovaný.";
                        this.all()
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            deactivate(id){
                axios
                    .patch("/users/"+id+"/deactivate",{
                        data: this.data},
                        {
                        headers: {Authorization: "Bearer " + this.auth.getToken()}
                    })
                    .then(response => {
                        this.message = "Používateľ bol deaktivovaný.";
                            this.all()
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            destroy(id){
                axios
                    .delete("/users/"+id,{
                        headers: {Authorization: "Bearer " + this.auth.getToken()}
                    })
                    .then(response => {
                        this.message = "Používateľ bol odstránený.";
                        this.all()
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            },
            showProfile(id){
                axios
                    .get("/users/"+id,{
                        headers: {Authorization: "Bearer " + this.auth.getToken()}
                    })
                    .then(response => {
                        this.$router.push("/users/"+id);
                    })
                    .catch(error => {
                        console.log(error.response);
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            }
        }
    }
</script>

<style scoped>

</style>