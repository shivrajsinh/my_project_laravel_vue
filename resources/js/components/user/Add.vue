<template>
    <div>
        <h4 class="text-center">Add User</h4>
            <div class="row">
                <div class="col-md-6">
                    <form @submit.prevent="addUser">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" v-model="name"
                                :class="{ 'is-invalid': isSubmitted && v$.name.$error }" />
                            <div class="invalid-feedback" v-if="isSubmitted && v$.name.required">
                                Name field is required.
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" v-model="email" 
                                :class="{ 'is-invalid' : isSubmitted && v$.email.$error }" />
                            <div class="invalid-feedback" v-if="isSubmitted && v$.email.required">
                                Email field is required.
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" v-model="password" 
                                :class="{ 'is-invalid' : isSubmitted && v$.password.$error }" />
                            <div class="invalid-feedback" v-if="isSubmitted && v$.password.$error">
                                <span v-if="v$.password.required">
                                    Password field is required.
                                </span>
                                <span v-if="v$.password.minLength">
                                    Password must contain atleast 8 characters.
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" class="form-control" v-model="confirm_password" 
                                :class="{ 'is-invalid' : isSubmitted && v$.confirm_password.$error }" />
                            <div class="invalid-feedback" v-if="isSubmitted && v$.confirm_password.$error">
                                <span v-if="v$.confirm_password.required">
                                    Confirm Password field is required.
                                </span>
                            </div>
                        </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Add User</button>
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
import { required, email, minLength, sameAs } from '@vuelidate/validators'

export default {
    setup () {
        return { v$: useVuelidate() }
    },
    data() {
        return {
            name: "",
            email: "",
            password: "",
            confirm_password:"",
            isSubmitted: false,
        };
    },
    validations: {
        name: { required },
        email: { required, email },
        password: {
            required,
            minLength: minLength(8),
        },
        confirm_password:{
            required,
        }
    },
    methods: {
        addUser(e) {
            e.preventDefault();
            this.isSubmitted = true;
            this.v$.$touch();
            if (!this.v$.$invalid) {
            NProgress.start();
            this.$axios
                .post('/api/user/add', {
                    name: this.name,
                    email: this.email,
                    password: this.password,
                    password_confirmation:this.confirm_password
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
        },
    },
    beforeRouteEnter(to, from, next) {
        if (!window.Laravel.isLoggedin) {
            window.location.href = "/";
        }
        next();
    },
};
</script>