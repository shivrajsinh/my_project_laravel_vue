<template>
    <div>
        <h4 class="text-center">Update Post</h4>
        <div class="row">
            <div class="col-md-6">
                <form @submit.prevent="updatePost">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" v-model="post.name" 
                            :class="{ 'is-invalid': isSubmitted && v$.post.name.$error }"
                        />
                        <div class="invalid-feedback" v-if="isSubmitted && v$.post.name.required">
                            Name field is required.
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <input type="text" class="form-control" v-model="post.description" 
                            :class="{ 'is-invalid' : isSubmitted && v$.post.description.$error }"
                        />
                        <div class="invalid-feedback" v-if="isSubmitted && v$.post.description.required">
                            Description field is required.
                        </div>
                    </div>
                    <div class="form-group">
                        <img width="100" height="100" :src="'/images/'+post.image" alt="image" />
                        <input type="file" class="form-control" @change="onImageChange"
                        />
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update Post</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import toastr from "toastr";
import "toastr/toastr.scss";
import NProgress from "nprogress";
import "nprogress/nprogress.css";
import useVuelidate from '@vuelidate/core'
import { required } from '@vuelidate/validators'

export default {
    setup () {
        return { v$: useVuelidate() }
    },
    data() {
        return {
            post: [],
            image: "",
            isSubmitted: false,
        }
    },
    validations: {
        post: {
            name: {
                required
            },
            description:{
                required
            }
            
        }
    },
    mounted() {
        this.getPost();
    },
    methods: {
        onImageChange(e) {
            this.image = e.target.files[0];
        },
        getPost() {
            NProgress.start();
            this.$axios
            .get('/api/post/edit/'+this.$route.params.id)
            .then((response)=>{
                this.post = response.data.data
            }).catch((error)=>{
                toastr.error(error.response.message, "Error");
                this.post = []
            })
            .then(() => {
                NProgress.done();
            })
        },
        updatePost(e) {
            e.preventDefault();
            this.isSubmitted = true;
            this.v$.$touch();
            if(!this.v$.$invalid) {
                NProgress.start();
                const config = {
                    headers: { 'content-type': 'multipart/form-data' }
                }

                let formData = new FormData();
                if (this.image) {
                    formData.append("image", this.image);
                }
                formData.append('name', this.post.name);
                formData.append('description', this.post.description);

                this.$axios.get("/sanctum/csrf-cookie").then((response) => {
                    this.$axios
                    .post("/api/post/update/"+this.$route.params.id, formData, config)
                    .then((response) => {
                         if (response["data"]["status"] == "Error") {
                            toastr.error(response.data.message, "Error");
                        }else{
                            toastr.success(response["data"]["message"], "Success");
                            setTimeout(() => {
                                this.$router.push({ name: "post" });
                            }, 500);
                        } 
                    })
                    .catch((error) => {
                         if (error.response.status === 422) {
                            let error_array = Array();
                            for (var key in error.response.data.errors) {
                                var obj = error.response.data.errors[key];
                                error_array.push(obj[0]);
                            }
                            toastr.error(error_array.join('</br> '), "Error");
                        }else{
                            toastr.error(error);
                        }
                    })
                    .then(() => {
                        NProgress.done();
                        this.isSubmitted = false;
                    });
                });
            }
        }
    },
    beforeRouteEnter(to, from, next) {
        if (!window.Laravel.isLoggedin) {
            window.location.href = "/";
        }
        next();
    },
}

</script>