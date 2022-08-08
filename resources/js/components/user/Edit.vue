<template>
    <div>
        <h4 class="text-center">Edit User</h4>
            <div class="row">
                <div class="col-md-6">
                    <form @submit.prevent="updateUser">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" v-model="user.name"
                                :class="{ 'is-invalid': isSubmitted && v$.user.name.$error }" />
                            <div class="invalid-feedback" v-if="isSubmitted && v$.user.name.required">
                                Name field is required.
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" v-model="user.email" 
                                :class="{ 'is-invalid' : isSubmitted && v$.user.email.$error }" />
                            <div class="invalid-feedback" v-if="isSubmitted && v$.user.email.required">
                                Email field is required.
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" v-model="user.password"  />
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" class="form-control" v-model="user.password_confirmation"  />
                        </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update User</button>
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
            user: [],
            isSubmitted: false,
        }
    },
    validations: {
        user: {
            name: {
                required
            },
            email:{
                required
            },
        }
    },
    mounted() {
        this.getUser();
    },
    methods: {
        getUser() {
            NProgress.start();
            this.$axios
                .get('/api/user/edit/'+this.$route.params.id)
                .then((response) => {
                    this.user = response.data.data
                }).catch((error) => {
                    toastr.error(error.response.message, "Error");
                    this.user = []
                })
                .then(() => {
                    NProgress.done();
                })
        },
        updateUser(e) {
            e.preventDefault();
            this.isSubmitted = true;
            this.v$.$touch();
            if (!this.v$.$invalid) {
            NProgress.start();
            this.$axios
                .post('/api/user/update/'+this.$route.params.id, {
                    name: this.user.name,
                    email: this.user.email,
                    password: this.user.password,
                    password_confirmation:this.user.password_confirmation
                })
                .then((response) => {
                     if (response["data"]["status"] == "Error") {
                        toastr.error(response.data.message, "Error");
                    }else{
                        toastr.success(response["data"]["message"], "Success");
                        setTimeout(() => {
                            this.$router.push({ name: "user" });
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
