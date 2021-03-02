<template>
    <div class="container-fluid" id="grad1">
        <div class="row justify-content-center mt-4">
            <div class="col-12 col-sm-9 text-center">
                <div class="card pt-5 mb-5" id="wizard">
                    <div v-if="(!getCart || getCart.length == 0 || !currentOrder) && feedback.type == null && feedback.text == null">
                        <h3 class="pb-5 m-5">You have no items yet in your cart!</h3>
                    </div>

                    <div v-else>
                        <ul id="progressbar" class="pr-5">
                            <li id="personal-tab" class="active">Personal</li>
                            <li id="summary-tab">Summary</li>
                            <li id="payment-tab"><i class="fas fa-dolly"></i>Payment</li>
                            <li id="finish-tab">Finish</li>
                        </ul>

                        <fieldset id="personal" class="px-5 pb-4">
                            <h3 class="fs-title">Personal information</h3>
                            
                            <div v-if="!isLoggedIn" class="row p-4">
                                <h2 class="col-12 text-danger">You need to login or register to continue</h2>

                                <div class="d-flex col-12 justify-content-center my-3">
                                    <div class="col-4">
                                        <div class="icon">
                                            <i class="fa fa-user fa-3x text-dark mb-2"></i>
                                        </div>
                                        <router-link :to="{path: '/login?prev=checkout'}" class="btn btn-info">Login</router-link>
                                    </div>

                                    <div class="col-4">
                                        <div class="icon">
                                            <i class="fa fa-user-plus fa-3x text-dark mb-2"></i>
                                        </div>
                                        <router-link :to="{name: 'register'}" class="btn btn-warning">Register</router-link>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="col-12 d-flex flex-column mt-2 ml-1 px-5">
                                <form-generator :form_id="user_form_id" :form_model="myuser" :form_schema="user_schema" @update="modifyUser($event)" v-if="getCart && getCart.length > 0 && validateUserForm"></form-generator>
                            </div>
                        </fieldset>

                        <fieldset v-if="isLoggedIn" id="summary" class="px-5 pb-4">
                            <div class="row">
                                <h3 class="fs-title col-12">Order summary</h3>    
                                
                                <div class="col-12 d-flex flex-column ml-4 my-2 pl-5">
                                    <h5 class="text-success text-left mb-0">Order items</h5>   

                                    <table class="table table-responsive table-borderless col-12 mb-0" id="order-table">
                                        <thead class="border-bottom">
                                            <tr class="">
                                                <th>Photo</th>
                                                <th>Name</th>
                                                <th>Size</th>
                                                <th>Color</th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                            </tr>
                                        </thead>
                                        
                                        <tbody>
                                            <tr v-for="(item, index) in getCart" @key="index">
                                                <td class="text-center align-middle p-0">
                                                    <img :src="item.image" :alt="item.name" class="w-30">                    
                                                </td>
                                                <td class="text-left align-middle">{{ item.name }}</td>
                                                <td class="align-middle">{{ item.size ? item.size : '-' }}</td>
                                                <td class="align-middle">{{ item.color ? item.color : '-' }}</td>
                                                <td class="text-center align-middle">
                                                    {{ item.quantity }}
                                                </td>
                                                <td class="align-middle">{{ item.price }} €</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-12 d-flex m-5 pl-2">
                                    <div class="col-6 text-left">
                                        <h5 class="text-success mb-3">Billing address</h5>

                                        <p class="mb-1"><span class="font-weight-bold">Address:</span> {{ user.address }}</p>
                                        <p class="mb-1"><span class="font-weight-bold">City:</span> {{ user.city }}</p>
                                        <p class="mb-1"><span class="font-weight-bold">Country:</span> {{ user.country }}</p>
                                        <p class="mb-1"><span class="font-weight-bold">Postal code:</span> {{ user.postal_code }}</p>
                                    </div>

                                    <div class="text-left">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="text-success mb-3">Shipping address</h5>

                                            <div class="form-group ml-5">
                                                <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modifyShippingAddressModal">Modify</button>
                                            </div>
                                        </div>

                                        <p class="mb-1"><span class="font-weight-bold">Address:</span> {{ shipping.address }}</p>
                                        <p class="mb-1"><span class="font-weight-bold">City:</span> {{ shipping.city }}</p>
                                        <p class="mb-1"><span class="font-weight-bold">Country:</span> {{ shipping.country }}</p>
                                        <p class="mb-1"><span class="font-weight-bold">Postal code:</span> {{ shipping.postal_code }}</p>
                                    </div>
                                </div>

                                <div class="col-12 d-flex justify-content-between mx-4 px-5">
                                    <div class="rtt">
                                        <div class="flex-column">
                                            <h5 class="text-success text-left">Shipping mode</h5>

                                            <div class="d-inline-flex text-left mr-5">
                                                <label class="ffff" for="price1">
                                                    <input class="" type="radio" value="60" v-model="shipping_price" id="price1" checked>
                                                </label>

                                                <div class="flex-fill ml-2">
                                                    <p class="mb-1">Standard shipping</p>
                                                    <p class="">60 &euro;</p>
                                                </div>
                                            </div>

                                            <div class="d-inline-flex text-left">
                                                <label class="kkkk" for="price2">
                                                    <input class="" type="radio" value="0" v-model="shipping_price" id="price2">
                                                </label>

                                                <div class="flex-fill ml-2">
                                                    <p class="mb-1">Free shipping</p>
                                                    <p class="">0 &euro;</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-8 d-flex flex-column text-right mt-2">
                                        <p class="text-16">Total price: <span class="font-weight-bold">{{ total_price }} &euro;</span></p>
                                        <p class="text-16">Other taxes: <span class="font-weight-bold">0 &euro;</span></p>
                                        <p class="text-16">Shipping: <span class="font-weight-bold">{{ shipping_price }} &euro;</span></p>

                                        <div class="text-22 text-success w-75 ml-auto">
                                            <p class="border-top pt-2">Total price to pay: <span class="font-weight-bold text-dark">{{ total_price_to_pay }} &euro;</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset v-if="isLoggedIn" id="payment" class="px-5 pb-4">
                            <div class="row">
                                <h3 class="fs-title">Payment informations</h3>

                                <div class="col-12 d-flex justify-content-between mt-2 ml-4 pl-5">
                                    <div class="flex-column">
                                        <h5 class="text-success text-left">Payment method</h5>

                                        <div class="d-inline-flex text-left mr-5">
                                            <input type="radio" class="radio mr-2 mt-1" v-model="activeType" id="credit" value="Credit" checked="checked">
                                            <label for="credit">Credit card</label>
                                        </div>

                                        <div class="d-inline-flex text-left mr-5">
                                            <input type="radio" class="radio mr-2 mt-1" v-model="activeType" id="transfer" value="Transfer">
                                            <label for="transfer">Bank transfer</label>
                                        </div>

                                        <div class="d-inline-flex text-left">
                                            <input type="radio" class="radio mr-2 mt-1" v-model="activeType" id="bancontact" value="Bancontact">
                                            <label for="bancontact">Bancontact</label>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="isLoggedIn" class="col-10 d-flex justify-content-start mx-5 my-4">
                                    <component
                                        :is = "activeType"
                                        ref = "paymentComponent"
                                        :order_id = "currentOrderId"
                                        :shipping_price = "shipping_price"
                                        :user = "user"
                                        @feedback="feedback = $event">
                                    </component>
                                </div>

                                <div class="col-12 d-flex justify-content-between ml-4 px-5 mt-5">
                                    <div class="rtt">
                                        <div class="flex-column mt-4">
                                            <h5 class="text-success text-left mb-2">Payment method</h5>
                                            <p class="text-left">{{ payment_method_name }}</p>
                                        </div>
                                    </div>

                                    <div class="col-8 d-flex flex-column text-right mt-2">
                                        <p class="text-16">Total price: <span class="font-weight-bold">{{ total_price }} &euro;</span></p>
                                        <p class="text-16">Other taxes: <span class="font-weight-bold">0 &euro;</span></p>
                                        <p class="text-16">Shipping: <span class="font-weight-bold">{{ shipping_price }} &euro;</span></p>

                                        <div class="text-22 text-success w-75 ml-auto">
                                            <p class="border-top pt-2">Total price to pay: <span class="font-weight-bold text-dark">{{ total_price_to_pay }} &euro;</span></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 d-flex justify-content-center mx-auto" v-if="feedback.type != null">
                                    <div v-if="processing">
                                        <p><i class="fa fa-2x fa-spinner fa-spin"></i></p>
                                        <p>Processing...</p>
                                    </div>

                                    <div v-if="feedback.type === 'error'">
                                        <div class="alert alert-danger" role="alert">
                                            {{ feedback.text }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset v-if="isLoggedIn && watchBcStatus" id="finish" class="px-5">
                            <div class="row justify-content-center mt-3 mb-5">
                                <p>bc_status computed: {{ setBcStatus }}</p>

                                <div v-if="processing">
                                    <p><i class="fa fa-2x fa-spinner fa-spin"></i></p>
                                    <p>Processing...</p>
                                </div>

                                <div v-if="feedback.type === 'error'">
                                    <div class="alert alert-danger" role="alert">
                                        {{ feedback.text }}
                                    </div>
                                </div>

                                <div v-if="feedback.type === 'concurrent'">
                                    <p class="text-danger text-16">{{ feedback.text }}</p>
                                    <button type="button" class="btn btn-success mr-3" @click="cancelPayment">Confirm cancelation</button>
                                    <button type="button" class="btn btn-danger ml-3" @click="feedback.type = null">Refuse cancelation</button>
                                </div>

                                <div v-if="successPayment && (feedback.type == 'success' || feedback.type == null)">
                                    <div class="col-4 mx-auto">
                                        <img src="https://img.icons8.com/color/96/000000/ok--v2.png" class="fit-image w-50">
                                    </div>

                                    <h4 class="col-6 mx-auto text-success mb-4">Success!</h4>

                                    <div class="col-12 justify-content-center pb-5">
                                        <h5 class="info" v-if="feedback.text">{{ feedback.text }}</h5>
                                        <!-- <h5>You have successfully placed your order.</h5> -->
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <div v-if="isLoggedIn" class="d-flex px-5 py-4 bg-secondary" v-bind:class="[justifyContent]">
                            <input type="button" name="previous" class="btn btn-skyblue" value="Previous" @click="previous()" v-if="!firstTab && !lastTab">
                            <input type="button" name="next" class="btn btn-skyblue" value="Next" @click="next()" v-if="!lastTab" :disabled="!isLoggedIn">

                            <input type="button" name="previous" class="btn btn-lg btn-light" value="Back" @click="previous()" v-if="lastTab && !successPayment">
                            <router-link :to="{path: '/'}" class="btn btn-skyblue btn-lg" v-if="lastTab && successPayment">Continue shopping</router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- .modal-dialog for modify shipping address -->
        <div class="modal fade slide-up p-5" tabindex="-1" role="dialog" aria-labelledby="modalSlideUpLabel" aria-hidden="false" id="modifyShippingAddressModal" v-if="currentOrder">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Modify shipping address</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                                
                    <!-- Modal body -->
                    <div class="modal-body px-5">
                        <form-generator :form_id="shipping_form_id" :form_model="shipping" :form_schema="shipping_schema" @update="modifyShippingAddress($event)" v-if="validateShippingForm"></form-generator>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters, mapActions } from 'vuex';
    import { refreshShoppingCart } from '../functions.js';
    import * as functions from '../functions.js';
    import userSchema from '../schemas/updateUserCheckoutSchema.js';
    import shippingSchema from '../schemas/updateShippingSchema.js';
    import FormGenerator from '../components/forms/FormGenerator';

    import Credit from './payment/Credit.vue';
    import Bancontact from './payment/Bancontact.vue';
    import Transfer from './payment/Transfer.vue';
    // import Vue from 'vue';

    var checkBcResult = null;
    
    var test = {
        data() {
            return {
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                product: [],
                isLoggedIn: localStorage.getItem('jwt') != null,
                myuser: {
                    name: "",
                    email: "",
                    phone: "",
                    address: "",
                    country: "",
                    city: "",
                    postal_code: ""
                },
                shipping: {
                    address: "",
                    country: "",
                    city: "",
                    postal_code: ""
                },
                user_schema: userSchema,
                shipping_schema: shippingSchema,
                user_form_id: "updateUserForm",
                shipping_form_id: "shippingAddressForm",
                attemptSubmit: false,
                attemptSubmitShipping: false,
                firstTab : false,
                lastTab : false,
                justifyContent: 'justify-content-between',
                previousroute: 'checkout',
                activeType: "Credit",
                bc_status: checkBcResult,
                feedback: {
                    type: null,
                    text: null
                },
                processing: false,
                successPayment: false,
                shipping_price: 0,
                currentOrderId: null,
                pid: null
            }
        },
        components: {
            Credit,
            Bancontact,
            Transfer,
            FormGenerator
        },
        computed: {
            ...mapGetters([
                'user_id',
                'user',
                'getCart',
                'order_id',
                'currentOrder',
                'total_price'
            ]),
            total_price_to_pay() {
                let price = parseInt(this.total_price) + parseInt(this.shipping_price);
                return price;
            },
            payment_method_name() {
                if (this.activeType == 'Credit') {
                    return 'Credit card';
                } else {
                    return this.activeType;
                }
            },
            validateUserForm() {
                if (this.attemptSubmit && this.attemptSubmit == true) {
                    functions.validateForm(this.myuser, userSchema, this.user_form_id);
                }

                return true;
            },
            validateShippingForm() {
                if (this.attemptSubmitShipping && this.attemptSubmitShipping == true) {
                    functions.validateForm(this.shipping, shippingSchema, this.shipping_form_id);
                }

                return true;
            },
            setBcStatus() {
                this.bc_status = checkBcResult;
                // console.log('this.bc_status in setBcStatus: ', this.bc_status); // null
                return this.bc_status;
            },
            watchBcStatus() {
                this.bc_status = checkBcResult; // null, error, success
                // console.log('this.bc_status watcher: ', this.bc_status); // null

                if (this.bc_status === 'error') {
                    this.handleFeedback({
                        type: 'error',
                        message: 'Bancontact redirect failed.'
                    });

                    // return;
                } else if (this.bc_status === 'success') {
                    // this.triggerPayment();

                    this.handleFeedback({
                        type: 'success',
                        code: 'charge_succeeded',
                        message: 'Your bancontact payment has been accepted.'
                    });
                }

                return true;
            },
        },
        async mounted() {
            await this.setDefaults();
            await refreshShoppingCart(this.getCart);

            this.pid = this.$route.params.pid;
            this.myuser = this.user;
            this.currentOrderId = this.currentOrder ? this.currentOrder.id : this.order_id;

            this.shipping.address = this.currentOrder ? this.currentOrder.shipping_address : this.user.address;
            this.shipping.country = this.currentOrder ? this.currentOrder.shipping_country : this.user.country;
            this.shipping.city = this.currentOrder ? this.currentOrder.shipping_city : this.user.city;
            this.shipping.postal_code = this.currentOrder ? this.currentOrder.shipping_postal_code : this.user.postal_code;

            if (this.pid) {
                axios.get(`/api/products/${this.pid}`)
                .then(response => {
                    this.product = response.data;
                })
                .catch(error => {
                    console.error(error);
                });
            }

            let $current_fs = $('fieldset:visible');
            this.buttonsVisibility($current_fs);
        },
        methods: {
            setDefaults() {
                this.$store.dispatch('setDefaults', true);
            },
            triggerPayment: async function() {
                if (this.processing) return;

                this.feedback.type = null;
                this.processing = true;

                let result = await this.$refs.paymentComponent.initPayment();
                this.processing = false;

                console.log('triggerPayment result:', result); // for bancontact: {type: "success", response: object}
                this.handleFeedback(result);
            },
            handleFeedback: async function(result) {
                let $current_fs = $('fieldset:visible');
                let $next_fs = $current_fs.next(); // finish
                let nextFiledsetId = $next_fs.attr('id');
                
                if (result.type === 'success') {
                    console.log('handleFeedback success, result.response: ', result.response);
                    this.successPayment = true;
                    let redirectStatuses = ['charge_succeeded', 'source_consumed', 'cc_success', 'transfer_validation', 'order_paid', 'intent_success'];
                    let data = result.response ? result.response.data : result;
                    console.log('data.code: ', data.code); // intent_success, charge_succeeded
                    // console.log('data.message: ', data.message);

                    if (redirectStatuses.indexOf(data.code) !== -1) {
                        this.feedback.type = 'success';
                        this.feedback.text = data.message; // Your bancontact payment has been accepted.

                        await this.setDefaults();
                        await refreshShoppingCart([]);
                        // this.redirectToOrder();
                    }

                    if (data.success) {
                        if (data.code === 'redirect_pending') {
                            this.feedback.type = 'redirect_pending';
                            this.feedback.text = data.message;

                            if (this.activeType !== 'Bancontact') return;

                            this.$refs.paymentComponent.openWindow(data.data);
                        }
                    } else if (data.code === 'payment_concurrent') {
                        this.feedback.type = 'concurrent';
                        this.feedback.text = data.message;
                    }

                    // manage fielset visibility & buttons
                    this.buttonsVisibility($next_fs);
                    $("#progressbar li").removeClass("active");
                    $("#progressbar li#"+nextFiledsetId+"-tab").addClass("active");

                    $next_fs.show();
                    $current_fs.hide();

                } else if (result.type === 'error') {
                    console.log('handleFeedback error');
                    console.log('result.message: ', result.message); // undefined

                    this.successPayment = false;
                    this.feedback.type = 'error';

                    if (typeof result.response !== 'undefined') {
                        console.log('result.response.status: ', result.response.status);
                        console.log('result.response.data.error.message: ', result.response.data.error.message);
                        console.log('result.response.data.error.doc_url: ', result.response.data.error.doc_url);
                        console.log('result.response.statusText: ', result.response.statusText);
                    } else {
                        this.feedback.text = "Unknown server error!";
                    }

                    if (typeof result.message !== 'undefined') {
                        this.feedback.text = result.message;
                    } else if (result.response.status === 500) {
                        this.feedback.text = 'payment.errors.server_error';
                    } else if (typeof result.response.data.error.message !== 'undefined') {
                        this.feedback.text = 'Payment Error: ' + result.response.data.error.message;

                        if (typeof result.response.data.error.doc_url !== 'undefined') {
                          this.feedback.text += ' (' + result.response.data.error.doc_url + ')';
                        }
                    } else {
                        this.feedback.text = result.response.statusText;
                    }
                }
            },
            cancelPayment: function() {
                let vm = this;
                this.feedback.type = null;
                console.log('cancel');

                axios.post(`/api/pay/cancel`, {
                    order_id: this.order.id
                })
                .then(function(response) {
                    console.log(response);
                    if (response.data.success) {
                        this.feedback.type = 'success';
                        this.feedback.text = 'canceled';
                    }
                })
                .catch(function(error) {
                    console.log(error.response);
                    vm.feedback.type = 'error';
                    vm.feedback.text = 'payment cancelation failed';
                });
            },
            buttonsVisibility(current) { // finish
                if (current.attr('id') == 'personal') {
                    this.firstTab = true;
                    this.justifyContent = 'justify-content-end';
                } else if (current.attr('id') == 'finish' && this.successPayment) {
                    this.firstTab = false;
                    this.lastTab = true;
                    this.justifyContent = 'justify-content-center';
                } else {
                    this.firstTab = false;
                    this.lastTab = false;
                    this.justifyContent = 'justify-content-between';
                }
            },
            next() { 
                let $current_fs = $('fieldset:visible');
                let $next_fs = $current_fs.next(); // next fieldset
                let nextFiledsetId = $next_fs.attr('id');

                $("input[name=next]").attr("id", "");

                if (nextFiledsetId == 'payment') {
                    $("input[name=next]").val("Place order");
                    $("input[name=next]").attr("id", "pay-me");
                }

                if (nextFiledsetId == 'finish') { // we are in payment fieldset
                    this.triggerPayment(); // Place order == triggerPayment
                } else {
                    this.buttonsVisibility($next_fs);
                    $("#progressbar li").removeClass("active");
                    $("#progressbar li#"+nextFiledsetId+"-tab").addClass("active");

                    $next_fs.show();
                    $current_fs.hide();
                }
            },
            previous() { 
                let $current_fs = $('fieldset:visible');
                let $previous_fs = $current_fs.prev(); // previous fieldset
                let prevFiledsetId = $previous_fs.attr('id');

                this.buttonsVisibility($previous_fs);
                $("input[name=next]").attr("id", "");

                $("#progressbar li").removeClass("active");
                $("#progressbar li#"+prevFiledsetId+"-tab").addClass("active");

                $previous_fs.show();
                $current_fs.hide();

                if ($previous_fs.attr('id') == 'summary') {
                    $("input[name=next]").val("Next");
                }
            },
            modifyUser() {
                // event.preventDefault();
                this.attemptSubmit = true;

                this.myuser.name = $('input[name="name"]').val(); 
                this.myuser.email = $('input[name="email"]').val();
                this.myuser.phone = $('input[name="phone"]').val();
                this.myuser.address = $('input[name="address"]').val();
                this.myuser.country = $('input[name="country"]').val();
                this.myuser.city = $('input[name="city"]').val();
                this.myuser.postal_code = $('input[name="postal_code"]').val();

                let resultValidation = functions.validateForm(this.myuser, userSchema, this.user_form_id);
                let hasValidationErrors = Object.keys(resultValidation).length > 0;
                $('#'+this.user_form_id+' .form-control.reactive.is-invalid').first().focus();

                if (!hasValidationErrors) {
                    let data = this.myuser;
                    data.user_id = this.user_id;
                    data.order_id = this.currentOrder.id;

                    this.$store.dispatch('updateUser', {vm: this, formData: data});
                } else {
                    console.log('Update user data validation error!');
                }
            },
            modifyShippingAddress() {
                // e.preventDefault();
                this.attemptSubmitShipping = true;

                this.shipping.address = $('input[name="shipping_address"]').val();
                this.shipping.country = $('input[name="shipping_country"]').val();
                this.shipping.city = $('input[name="shipping_city"]').val();
                this.shipping.postal_code = $('input[name="shipping_postal_code"]').val();

                let resultValidation = functions.validateForm(this.shipping, shippingSchema, this.shipping_form_id);
                let hasValidationErrors = Object.keys(resultValidation).length > 0;
                $('#'+this.shipping_form_id+' .form-control.reactive.is-invalid').first().focus();

                if (!hasValidationErrors) {
                    let data = {
                        shipping_address: this.shipping.address,
                        shipping_country: this.shipping.country,
                        shipping_city: this.shipping.city,
                        shipping_postal_code: this.shipping.postal_code,
                        user_id: this.user_id,
                        order_id: this.currentOrder.id
                    };
                    // console.log(data); // ok

                    this.$store.dispatch('updateShippingAddress', {vm: this, formData: data});
                } else {
                    console.log('Shipping address validation error!');
                }
            }
        }
    };

    let component_data = test.data();

    window.checkBc = function(data) {
        if (data === 'succeeded') {
            checkBcResult = 'success';
            component_data.successPayment = true;
            component_data.feedback.type = 'success';
            component_data.feedback.text = 'Your bancontact payment has been accepted.';
        } else {
            checkBcResult = 'error';
            component_data.successPayment = false;
            component_data.feedback.type = 'error';
            component_data.feedback.text = 'Bancontact redirect failed.';
        }

        console.log('checkBcResult in window.checkBc: ', checkBcResult); // success
    }

    // console.log('component_data: ', component_data);
    // console.log('test bc_status: ', component_data.bc_status);
    console.log('feedback.type: ', component_data.feedback.type);
    console.log('feedback.text: ', component_data.feedback.text);
    console.log('successPayment: ', component_data.successPayment);

    export default test;
    /*export default {
        data() {
            return {
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                product: [],
                isLoggedIn: localStorage.getItem('jwt') != null,
                myuser: {
                    name: "",
                    email: "",
                    phone: "",
                    address: "",
                    country: "",
                    city: "",
                    postal_code: ""
                },
                shipping: {
                    address: "",
                    country: "",
                    city: "",
                    postal_code: ""
                },
                user_schema: userSchema,
                shipping_schema: shippingSchema,
                user_form_id: "updateUserForm",
                shipping_form_id: "shippingAddressForm",
                attemptSubmit: false,
                attemptSubmitShipping: false,
                firstTab : false,
                lastTab : false,
                justifyContent: 'justify-content-between',
                previousroute: 'checkout',
                activeType: "Credit",
                bc_status: checkBcResult,
                feedback: {
                    type: null,
                    text: null
                },
                processing: false,
                successPayment: false,
                shipping_price: 0,
                currentOrderId: null,
                pid: null
            }
        },
        components: {
            Credit,
            Bancontact,
            Transfer,
            FormGenerator
        },
        computed: {
            ...mapGetters([
                'user_id',
                'user',
                'getCart',
                'order_id',
                'currentOrder',
                'total_price'
            ]),
            total_price_to_pay() {
                let price = parseInt(this.total_price) + parseInt(this.shipping_price);
                return price;
            },
            payment_method_name() {
                if (this.activeType == 'Credit') {
                    return 'Credit card';
                } else {
                    return this.activeType;
                }
            },
            validateUserForm() {
                if (this.attemptSubmit && this.attemptSubmit == true) {
                    functions.validateForm(this.myuser, userSchema, this.user_form_id);
                }

                return true;
            },
            validateShippingForm() {
                if (this.attemptSubmitShipping && this.attemptSubmitShipping == true) {
                    functions.validateForm(this.shipping, shippingSchema, this.shipping_form_id);
                }

                return true;
            },
            setBcStatus() {
                this.bc_status = checkBcResult;
                console.log('this.bc_status in setBcStatus: ', this.bc_status); // null
                return this.bc_status;
            },
            watchBcStatus() {
                this.bc_status = checkBcResult; // null, error, success
                console.log('this.bc_status watcher: ', this.bc_status); // null

                if (this.bc_status === 'error') {
                    this.handleFeedback({
                        type: 'error',
                        message: 'Bancontact redirect failed.'
                    });

                    // return;
                } else if (this.bc_status === 'success') {
                    // this.triggerPayment();

                    this.handleFeedback({
                        type: 'success',
                        code: 'charge_succeeded',
                        message: 'Your bancontact payment has been accepted.'
                    });
                }

                return true;
            },
        },
        async mounted() {
            await this.setDefaults();
            await refreshShoppingCart(this.getCart);

            this.pid = this.$route.params.pid;
            this.myuser = this.user;
            this.currentOrderId = this.currentOrder ? this.currentOrder.id : this.order_id;

            this.shipping.address = this.currentOrder ? this.currentOrder.shipping_address : this.user.address;
            this.shipping.country = this.currentOrder ? this.currentOrder.shipping_country : this.user.country;
            this.shipping.city = this.currentOrder ? this.currentOrder.shipping_city : this.user.city;
            this.shipping.postal_code = this.currentOrder ? this.currentOrder.shipping_postal_code : this.user.postal_code;

            if (this.pid) {
                axios.get(`/api/products/${this.pid}`)
                .then(response => {
                    this.product = response.data;
                })
                .catch(error => {
                    console.error(error);
                });
            }

            let $current_fs = $('fieldset:visible');
            this.buttonsVisibility($current_fs);
        },
        methods: {
            setDefaults() {
                this.$store.dispatch('setDefaults', true);
            },
            triggerPayment: async function() {
                if (this.processing) return;

                this.feedback.type = null;
                this.processing = true;

                let result = await this.$refs.paymentComponent.initPayment();
                this.processing = false;

                console.log('triggerPayment result:', result); // for bancontact: {type: "success", response: object}
                this.handleFeedback(result);
            },
            handleFeedback: async function(result) {
                let $current_fs = $('fieldset:visible');
                let $next_fs = $current_fs.next(); // finish
                let nextFiledsetId = $next_fs.attr('id');
                
                if (result.type === 'success') {
                    console.log('handleFeedback success, result.response: ', result.response);
                    this.successPayment = true;
                    let redirectStatuses = ['charge_succeeded', 'source_consumed', 'cc_success', 'transfer_validation', 'order_paid', 'intent_success'];
                    let data = result.response ? result.response.data : result;
                    console.log('data.code: ', data.code); // intent_success, charge_succeeded
                    // console.log('data.message: ', data.message);

                    if (redirectStatuses.indexOf(data.code) !== -1) {
                        this.feedback.type = 'success';
                        this.feedback.text = data.message; // Your bancontact payment has been accepted.

                        await this.setDefaults();
                        await refreshShoppingCart([]);
                        // this.redirectToOrder();
                    }

                    if (data.success) {
                        if (data.code === 'redirect_pending') {
                            this.feedback.type = 'redirect_pending';
                            this.feedback.text = data.message;

                            if (this.activeType !== 'Bancontact') return;

                            this.$refs.paymentComponent.openWindow(data.data);
                        }
                    } else if (data.code === 'payment_concurrent') {
                        this.feedback.type = 'concurrent';
                        this.feedback.text = data.message;
                    }

                    // manage fielset visibility & buttons
                    this.buttonsVisibility($next_fs);
                    $("#progressbar li").removeClass("active");
                    $("#progressbar li#"+nextFiledsetId+"-tab").addClass("active");

                    $next_fs.show();
                    $current_fs.hide();

                } else if (result.type === 'error') {
                    console.log('handleFeedback error');
                    console.log('result.message: ', result.message); // undefined

                    this.successPayment = false;
                    this.feedback.type = 'error';

                    if (typeof result.response !== 'undefined') {
                        console.log('result.response.status: ', result.response.status);
                        console.log('result.response.data.error.message: ', result.response.data.error.message);
                        console.log('result.response.data.error.doc_url: ', result.response.data.error.doc_url);
                        console.log('result.response.statusText: ', result.response.statusText);
                    } else {
                        this.feedback.text = "Unknown server error!";
                    }

                    if (typeof result.message !== 'undefined') {
                        this.feedback.text = result.message;
                    } else if (result.response.status === 500) {
                        this.feedback.text = 'payment.errors.server_error';
                    } else if (typeof result.response.data.error.message !== 'undefined') {
                        this.feedback.text = 'Payment Error: ' + result.response.data.error.message;

                        if (typeof result.response.data.error.doc_url !== 'undefined') {
                          this.feedback.text += ' (' + result.response.data.error.doc_url + ')';
                        }
                    } else {
                        this.feedback.text = result.response.statusText;
                    }
                }
            },
            cancelPayment: function() {
                let vm = this;
                this.feedback.type = null;
                console.log('cancel');

                axios.post(`/api/pay/cancel`, {
                    order_id: this.order.id
                })
                .then(function(response) {
                    console.log(response);
                    if (response.data.success) {
                        this.feedback.type = 'success';
                        this.feedback.text = 'canceled';
                    }
                })
                .catch(function(error) {
                    console.log(error.response);
                    vm.feedback.type = 'error';
                    vm.feedback.text = 'payment cancelation failed';
                });
            },
            buttonsVisibility(current) { // finish
                if (current.attr('id') == 'personal') {
                    this.firstTab = true;
                    this.justifyContent = 'justify-content-end';
                } else if (current.attr('id') == 'finish' && this.successPayment) {
                    this.firstTab = false;
                    this.lastTab = true;
                    this.justifyContent = 'justify-content-center';
                } else {
                    this.firstTab = false;
                    this.lastTab = false;
                    this.justifyContent = 'justify-content-between';
                }
            },
            next() { 
                let $current_fs = $('fieldset:visible');
                let $next_fs = $current_fs.next(); // next fieldset
                let nextFiledsetId = $next_fs.attr('id');

                $("input[name=next]").attr("id", "");

                if (nextFiledsetId == 'payment') {
                    $("input[name=next]").val("Place order");
                    $("input[name=next]").attr("id", "pay-me");
                }

                if (nextFiledsetId == 'finish') { // we are in payment fieldset
                    this.triggerPayment(); // Place order == triggerPayment
                } else {
                    this.buttonsVisibility($next_fs);
                    $("#progressbar li").removeClass("active");
                    $("#progressbar li#"+nextFiledsetId+"-tab").addClass("active");

                    $next_fs.show();
                    $current_fs.hide();
                }
            },
            previous() { 
                let $current_fs = $('fieldset:visible');
                let $previous_fs = $current_fs.prev(); // previous fieldset
                let prevFiledsetId = $previous_fs.attr('id');

                this.buttonsVisibility($previous_fs);
                $("input[name=next]").attr("id", "");

                $("#progressbar li").removeClass("active");
                $("#progressbar li#"+prevFiledsetId+"-tab").addClass("active");

                $previous_fs.show();
                $current_fs.hide();

                if ($previous_fs.attr('id') == 'summary') {
                    $("input[name=next]").val("Next");
                }
            },
            modifyUser() {
                // event.preventDefault();
                this.attemptSubmit = true;

                this.myuser.name = $('input[name="name"]').val(); 
                this.myuser.email = $('input[name="email"]').val();
                this.myuser.phone = $('input[name="phone"]').val();
                this.myuser.address = $('input[name="address"]').val();
                this.myuser.country = $('input[name="country"]').val();
                this.myuser.city = $('input[name="city"]').val();
                this.myuser.postal_code = $('input[name="postal_code"]').val();

                let resultValidation = functions.validateForm(this.myuser, userSchema, this.user_form_id);
                let hasValidationErrors = Object.keys(resultValidation).length > 0;
                $('#'+this.user_form_id+' .form-control.reactive.is-invalid').first().focus();

                if (!hasValidationErrors) {
                    let data = this.myuser;
                    data.user_id = this.user_id;
                    data.order_id = this.currentOrder.id;

                    this.$store.dispatch('updateUser', {vm: this, formData: data});
                } else {
                    console.log('Update user data validation error!');
                }
            },
            modifyShippingAddress() {
                // e.preventDefault();
                this.attemptSubmitShipping = true;

                this.shipping.address = $('input[name="shipping_address"]').val();
                this.shipping.country = $('input[name="shipping_country"]').val();
                this.shipping.city = $('input[name="shipping_city"]').val();
                this.shipping.postal_code = $('input[name="shipping_postal_code"]').val();

                let resultValidation = functions.validateForm(this.shipping, shippingSchema, this.shipping_form_id);
                let hasValidationErrors = Object.keys(resultValidation).length > 0;
                $('#'+this.shipping_form_id+' .form-control.reactive.is-invalid').first().focus();

                if (!hasValidationErrors) {
                    let data = {
                        shipping_address: this.shipping.address,
                        shipping_country: this.shipping.country,
                        shipping_city: this.shipping.city,
                        shipping_postal_code: this.shipping.postal_code,
                        user_id: this.user_id,
                        order_id: this.currentOrder.id
                    };
                    // console.log(data); // ok

                    this.$store.dispatch('updateShippingAddress', {vm: this, formData: data});
                } else {
                    console.log('Shipping address validation error!');
                }
            }
        }
    }*/
</script>

<style scoped>
    #wizard fieldset:not(:first-of-type) {
        display: none;
    }

    #updateUserForm label {
        margin-bottom: 0;
    }

    #order-table tbody > tr:nth-last-child(2) > td:first-child {
        padding-bottom: 0.8rem !important;
        /*background-color: red;*/
    }    

    #modifyShippingAddressModal {
        /*background-color: purple;*/
    }

    label {
        margin-bottom: 0;
        display: contents important!;
    }

    select.list-dt {
        border: none;
        outline: 0;
        border-bottom: 1px solid #ccc;
        padding: 2px 5px 3px 5px;
        margin: 2px;
    }

    select.list-dt:focus {
        border-bottom: 2px solid skyblue
    }

    .card {
        z-index: 0;
        border: none;
        border-radius: 0.5rem;
        position: relative;
    }

    .fs-title {
        margin-bottom: 20px;
        text-align: left;
        padding: 5px;
        border-left-width: 2.5px;
        border-left-style: solid;
        border-left-color: #d56505;
        padding-left: 10px;
    }

    #progressbar {
        margin-bottom: 30px;
        overflow: hidden;
        color: lightgrey;
    }

    #progressbar .active {
        color: #000000;
    }

    #progressbar li {
        list-style-type: none;
        font-size: 12px;
        width: 25%;
        float: left;
        position: relative;
    }

    #progressbar #personal-tab:before {
        font-family: FontAwesome;
        content: "\f007";
    }

    #progressbar #payment-tab:before {
        font-family: FontAwesome;
        content: "\f09d";
    }

    #progressbar #summary-tab:before {
        font-family: FontAwesome;
        content: "\f0d1";
    }

    #progressbar #finish-tab:before {
        font-family: FontAwesome;
        content: "\f00c";
    }

    #progressbar li:before {
        width: 50px;
        height: 50px;
        line-height: 45px;
        display: block;
        font-size: 18px;
        color: #ffffff;
        background: lightgray;
        border-radius: 50%;
        margin: 0 auto 10px auto;
        padding: 2px;
    }

    #progressbar li:after {
        content: '';
        width: 100%;
        height: 2px;
        background: lightgray;
        position: absolute;
        left: 0;
        top: 25px;
        z-index: -1;
    }

    #progressbar li.active:before,
    #progressbar li.active:after {
        background: skyblue;
    }
</style>