<template>
    <div class="col-md-12 bg-white mt-2 px-0">
        <form-generator :form_id="form_id" :form_model="model" :form_schema="form_schema" @update="modifyUser($event)" v-if="validateForm"></form-generator>
    </div>
</template>

<script>
    import * as functions from '../../functions.js';
    import userSchema from '../../schemas/updateUserSchema.js';
    import FormGenerator from '../../components/forms/FormGenerator';

	export default {
        name: "Main",
        props: ['user', 'user_id', 'order_id'],
        data() {
            return {
                model: {
                    name: this.user.name,
                    email: this.user.email,
                    phone: this.user.phone ? this.user.phone : "",
                    address: this.user.address,
                    country: this.user.country,
                    city: this.user.city,
                    postal_code: this.user.postal_code,
                    password: '',
                    password_confirmation: ''
                },
                form_schema: userSchema,
                form_id: "personalDetails",
                attemptSubmit: false,
            }
        },
        computed: {
            validateForm() {
                if (this.attemptSubmit && this.attemptSubmit == true) {
                    functions.validateForm(this.model, userSchema, this.form_id);
                }

                return true;
            }
        },
        components: {
            FormGenerator
        },
        mounted() {
            // console.log('the new Main');
        },
        methods: {
            modifyUser: function() {
                // event.preventDefault();
                this.attemptSubmit = true;

                this.model.name = $('input[name="name"]').val();
                this.model.email = $('input[name="email"]').val();
                this.model.phone = $('input[name="phone"]').val();
                this.model.address = $('input[name="address"]').val();
                this.model.country = $('input[name="country"]').val();
                this.model.city = $('input[name="city"]').val();
                this.model.postal_code = $('input[name="postal_code"]').val();
                this.model.password = $('input[name="password"]').val();
                this.model.password_confirmation = $('input[name="password_confirmation"]').val();

                let resultValidation = functions.validateForm(this.model, userSchema, this.form_id);
                let hasValidationErrors = Object.keys(resultValidation).length > 0;
                $('#'+this.form_id+' .form-control.reactive.is-invalid').first().focus();

                if (!hasValidationErrors) {
                    let data = this.model;
                    data.user_id = this.user_id;
                    data.order_id = this.order_id;

                    this.$store.dispatch('updateUser', {vm: this, formData: data});
                } else {
                    console.log('Update user data validation error!');
                }
            }
        }
    }
</script>

<style scoped>
    .big-text {
        font-size: 28px;
    }
	.product-box {
        border: 1px solid #cccccc;
        padding: 10px 15px;
        height: 20vh
    }
</style>