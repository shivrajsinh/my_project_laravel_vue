<template>
    <div class="row">
        <div class="col-2 mb-2 text-end">
            <input
                class="form-control"
                type="text"
                name="search"
                placeholder="search..."
                v-model="search"
            />
        </div>
        <div class="col-10 mb-2 text-end">
            <router-link to="/user/add" class="btn btn-primary">Create</router-link>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Users</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Posts</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody v-if="filter.length > 0">
                                <tr v-for="(user,key) in filter" :key="key">
                                    <td>{{ user.id }}</td>
                                    <td>{{ user.name }}</td>
                                    <td>{{ user.email }}</td>
                                    <td>{{ user.post_count }}</td>
                                    <td>
                                        <router-link :to='{name:"edituser",params:{id:user.id}}' class="btn btn-success">Edit</router-link>
                                        <button type="button" @click="deleteUser(user.id)" class="btn btn-danger">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="4" align="center">No User Found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import toastr from "toastr";
import "toastr/toastr.scss";
import NProgress from "nprogress";
import "nprogress/nprogress.css";

export default {
    data(){
        return {
            users: [],
            search:""
        }
    },
    mounted(){
        this.getUsers();
    },
    computed: {
        filter() {
            if (!this.search) {
                return this.users;
            } else {
                return this.users.filter(({ name,email }) =>
                (name,email).toLowerCase().includes(this.search.toLowerCase())
                );
            }
        },
    },
    methods:{
        getUsers() {
            NProgress.start();
            this.$axios
            .get('/api/user')
            .then((response) => {
                this.users = response.data.data
            }).catch((error) => {
                toastr.error(error.response.message, "Error");
                this.users = [];
            })
            .then(() => {
                NProgress.done();
            })
        },
        deleteUser(id){
            if(confirm("Are you sure to delete this User ?")){
                NProgress.start();
                this.$axios
                .delete(`/api/user/delete/${id}`)
                .then((response) => {
                    toastr.success(response.data.message, "Success");
                    this.getUsers()
                }).catch((error) => {
                    toastr.error(error.response.message, "Error");
                })
                .then(() => {
                    NProgress.done();
                })
            }
        }
    },
    beforeRouteEnter(to, from, next) {
        if (!window.Laravel.isLoggedin) {
            window.location.href = "/";
        }
        next();
    }
}
</script>