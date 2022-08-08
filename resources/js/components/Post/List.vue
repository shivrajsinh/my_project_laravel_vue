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
            <router-link :to='{name:"addpost"}' class="btn btn-primary">Create</router-link>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Post</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th v-if="is_show_user"> User </th>
                                    <th>Image</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody v-if="filter.length > 0">
                                <tr v-for="(post,key) in filter" :key="key">
                                    <td>{{ post.id }}</td>
                                    <td>{{ post.name }}</td>
                                    <td>{{ post.description }}</td>
                                    <td v-if="is_show_user">{{ post.user.name }}</td>
                                    <td><img width="100" height="100" :src="'/images/'+post.image" alt="image" /></td>
                                    <td>
                                        <router-link :to='{name:"editpost",params:{id:post.id}}' class="btn btn-success">Edit</router-link>
                                        <button type="button" @click="deletePost(post.id)" class="btn btn-danger">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="4" align="center">No Post Found.</td>
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
    name:"post",
    data(){
        return {
            posts:[],
            is_show_user:false,
            search:""
        }
    },
    created() {
        if (window.Laravel.user && window.Laravel.user.type == "SuperAdmin") {
            this.is_show_user = true
        }
    },
    computed: {
        filter() {
            if (!this.search) {
                return this.posts;
            } else {
                return this.posts.filter(({ name }) =>
                (name).toLowerCase().includes(this.search.toLowerCase())
                );
            }
        },
    },
    mounted(){
        this.getPosts()
    },
    methods:{
        getPosts(){
            NProgress.start();
            this.$axios
            .get('/api/post')
            .then((response) => {
                this.posts = response.data.data
            }).catch((error) => {
                toastr.error(error.response.message, "Error");
                this.posts = [];
            })
            .then(() => {
                NProgress.done();
            })
        },
        deletePost(id){
            if(confirm("Are you sure to delete this Post ?")){
                NProgress.start();
                this.$axios
                .delete(`/api/post/delete/${id}`)
                .then((response) => {
                    toastr.success(response.data.message, "Success");
                    this.getPosts()
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