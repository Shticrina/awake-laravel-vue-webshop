<template>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-10 mb-5">
                <div class="card card-default">
                    <div class="card-header">Register</div>

                    <div class="card-body">
                        <form-generator :form_id="form_id" :form_model="data" :form_schema="form_schema" @register="registerUser($event)" v-if="validateRegisterForm"></form-generator>
                        
                        <!-- <div class="form-group flex-fill">
                            <label for="country" class="">Country*</label>
                            <input name="country" type="text" class="form-control" v-model="data.country"> -->

                            <!-- <p>another way to do it</p>
                            <select name="country">
                                <option value="Belgium">Belgium</option>
                                <option value="France">France</option>
                            </select> -->
                        <!-- </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import * as functions from '../functions.js';
    import registerUserSchema from '../schemas/registerUserSchema.js';
    import FormGenerator from '../components/forms/FormGenerator';
    import { refreshShoppingCart } from '../functions.js';
    import { mapGetters, mapActions } from 'vuex';

    export default {
        props : ['nextUrl'],
        data() {
            return {
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                data: {
                    name : "",
                    email : "",
                    phone : "",
                    address : "",
                    country : "",
                    city : "",
                    postal_code : "",
                    password : "",
                    password_confirmation : ""
                },
                form_schema: registerUserSchema,
                form_id: "registerForm",
                previous_route : null,
                attemptSubmit: false
            }
        },
        beforeRouteEnter(to, from, next) {
            next((vm) => {
                vm.previous_route = from;
            });
        },
        computed: {
            ...mapGetters([
                'getCart',
                'order_id'
            ]),
            validateRegisterForm() {
                if (this.attemptSubmit && this.attemptSubmit == true) {
                    functions.validateForm(this.data, registerUserSchema, this.form_id);
                }

                return true;
            }
        },
        components: {
            FormGenerator
        },
        mounted() {
            // console.log('order_id in register: ', this.order_id);
        },
        methods : {
            registerUser() {
                // e.preventDefault();
                this.attemptSubmit = true;

                this.data.name = $('input[name="name"]').val();
                this.data.phone = $('input[name="phone"]').val();
                this.data.email = $('input[name="email"]').val();
                this.data.address = $('input[name="address"]').val();
                this.data.country = $('input[name="country"]').val();
                this.data.city = $('input[name="city"]').val();
                this.data.postal_code = $('input[name="postal_code"]').val();
                this.data.password = $('input[name="password"]').val();
                this.data.password_confirmation = $('input[name="password_confirmation"]').val();

                let resultValidation = functions.validateForm(this.data, registerUserSchema, this.form_id);
                let hasValidationErrors = Object.keys(resultValidation).length > 0;
                $('#'+this.form_id+' .form-control.reactive.is-invalid').first().focus();

                console.log('resultValidation: ', resultValidation);

                if (!hasValidationErrors) {
                    let formData = this.data;
                    formData.order_id = this.order_id;
                    console.log('all good: ', formData);

                    axios.post('api/register', formData)
                    .then(response => {
                        let data = {
                            items: response.data.order_items,
                            order_id: response.data.order_id,
                            order: response.data.order
                        };

                        localStorage.setItem('user', JSON.stringify(response.data.user));
                        localStorage.setItem('jwt', response.data.token);

                        if (localStorage.getItem('jwt') != null) {
                            this.$emit('loggedIn');

                            this.$store.dispatch('setDefaults', true);
                            this.$store.dispatch('updateShoppingCart', data);
                            refreshShoppingCart(response.data.order_items);

                            if (this.$route.params.nextUrl != null) {
                                this.$router.push(this.$route.params.nextUrl);
                            } else {
                                if (this.previous_route.path === '/checkout') { // and current order after login == previous order
                                    // console.log('venim din checkout');
                                    this.$router.push('/checkout').catch(err => {});
                                } else {
                                    // console.log('this.previous_route.path != checkout: ', this.previous_route.path);
                                    this.$router.push('/userboard').catch(err => {});
                                }

                                this.$router.push('/');
                            }
                        }
                    })
                    .catch(error => {
                        var validation_errors = error.response.data.errors;
                        console.log('validation_errors: ', validation_errors);

                        if (validation_errors) {
                            $('.error-block').remove();

                            for (let error in validation_errors) {
                                let $error_div = $('<div class="error-block text-danger ml-2"></div>');
                                let $input = $('input[name="'+error+'"]');

                                $error_div.text(validation_errors[error]);
                                $input.parent().append($error_div);
                                $input.css("border-color", "#e3342f");
                            }
                        }
                    });
                } else {
                    console.log('Register form validation error!')
                }
            }
        }
    }
</script>