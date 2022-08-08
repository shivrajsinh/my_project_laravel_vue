<template>
  <div>
    <h4 class="text-center">Add Post</h4>
    <div class="row">
      <div class="col-md-6">
        <form @submit.prevent="addPost">
          <div class="form-group">
            <label>Name</label>
            <input type="text" class="form-control" v-model="name"
              :class="{ 'is-invalid': isSubmitted && v$.name.$error }"
            />
            <div class="invalid-feedback" v-if="isSubmitted && v$.name.required">
              Name field is required.
            </div>
          </div>
          <div class="form-group">
            <label>Description</label>
            <input type="text" class="form-control" v-model="description" 
              :class="{ 'is-invalid' : isSubmitted && v$.description.$error }"
            />
            <div class="invalid-feedback" v-if="isSubmitted && v$.description.required">
              Description field is required.
            </div>
          </div>
          <div class="form-group">
            <label>Image</label>
            <input type="file" class="form-control" v-on:change="onImageChange"
              :class="{ 'is-invalid' : isSubmitted && v$.image.$error }"
            />
            <div class="invalid-feedback" v-if="isSubmitted && v$.image.required">
              Image field is required.
            </div>
          </div>
          <div class="form-group">
            <button type="submit" class="btn btn-primary">Add Post</button>
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
      name: "",
      description: "",
      image: "",
      isSubmitted: false,
    };
  },
  validations: {
    name: { required },
    description: { required },
    image: { required },
  },
  methods: {
    onImageChange(e) {
      this.image = e.target.files[0];
    },
    addPost(e) {
      e.preventDefault();
      this.isSubmitted = true;
      this.v$.$touch();
      if (!this.v$.$invalid) {
        NProgress.start();
        const config = {
          headers: { 'content-type': 'multipart/form-data' }
        }

        let formData = new FormData();
        formData.append('image', this.image);
        formData.append('name', this.name);
        formData.append('description', this.description);

        this.$axios.get("/sanctum/csrf-cookie").then((response) => {
          this.$axios
          .post("/api/post/add", formData, config)
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