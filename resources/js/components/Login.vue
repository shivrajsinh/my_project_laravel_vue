<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card card-default">
                    <div class="card-header">Login</div>
                    <div class="card-body">
                        <form>
                            <div class="form-group row">
                                <label for="email" class="col-sm-4 col-form-label text-md-right">E-Mail Address</label>
                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" v-model="email" required
                                           autofocus autocomplete="off">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" v-model="password"
                                           required autocomplete="off">
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary" @click="handleSubmit" :disabled="is_btn_submit">
                                        Login
                                    </button>
                                </div>
                            </div>
                        </form>
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
    data() {
        return {
            email: "",
            password: "",
            is_btn_submit:false
        }
    },
    methods: {
        handleSubmit(e) {
            e.preventDefault()
            if (this.password.length > 0) {
                NProgress.start();
                this.is_btn_submit = true
                this.$axios.get('/sanctum/csrf-cookie').then(response => {
                    this.$axios.post('api/login', {
                        email: this.email,
                        password: this.password
                    })
                    .then(response => {
                        if (response["data"]["status"] == "Error") {
                            toastr.error(response.data.message, "Error");
                        }else{
                            toastr.success(response["data"]["message"], "Success");
                            setTimeout(() => {
                                this.$router.go('/dashboard')  
                            }, 500);
                        }
                    })
                    .catch(function (error) {
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
                    }).then(() => {
                        NProgress.done();
                        this.is_btn_submit = false
                    });
                })
            }
        }
    },
    beforeRouteEnter(to, from, next) {
        if (window.Laravel.isLoggedin) {
            return next('dashboard');
        }
        next();
    }
}
</script>