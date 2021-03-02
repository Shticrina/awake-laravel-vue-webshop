<template>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-10">
                <div class="card card-default">
                    <div class="card-header">Login</div>

                    <div class="card-body">
                        <!-- <h4 v-if="login_error" id="loginError" class="text-danger">{{ login_error }}</h4> -->
                        <h4 id="loginError" class="text-danger text-center mt-3">login_error</h4>

                        <form id="loginForm" v-if="!isLoggedIn" class="px-5 py-3">
                            <input type="hidden" name="_token" :value="csrf">

                            <div class="form-group d-flex flex-column col-8 mx-auto">
                                <label for="email" class="">E-Mail Address</label>
                                <input id="email" type="email" class="form-control" v-model="email" required autofocus>
                            </div>

                            <div class="form-group d-flex flex-column col-8 mx-auto">
                                <label for="password" class="">Password</label>
                                <input id="password" type="password" class="form-control" v-model="password" required>
                            </div>

                            <div class="form-group d-flex col-8 justify-content-end mx-auto mt-3 mb-0">
                                <button type="submit" class="btn btn-primary" @click="handleSubmit">Login</button>
                            </div>
                        </form>

                        <h5 v-else class="text-info">You are already logged in...</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { refreshShoppingCart } from '../functions.js';
    import { mapGetters, mapActions } from 'vuex';

    export default {
        data() {
            return {
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                isLoggedIn: localStorage.getItem('jwt') != null,
                email: "",
                password: "",
                previous_route: 'myroute',
                login_error: ''
            }
        },
        beforeRouteEnter(to, from, next) {
            next((vm) => {
                vm.previous_route = from;
            });
        },
        mounted() {
            $('#loginError').hide();
            // console.log('previous_route in login: ', this.previous_route); // ok
            if (this.isLoggedIn) {
                this.$router.push({name : 'userboard'});
                // this.$router.push({name : 'userboard-pages', params : {page: 'main'}});
                // window.location.reload();
            }
        },
        methods : {
           handleSubmit(e) {
                e.preventDefault();
                
                if (this.password.length > 0) {
                    axios.post('api/login', {
                        email: this.email,
                        password: this.password
                    })
                    .then(response => {
                        let data = {
                            items: response.data.order_items,
                            order_id: response.data.order_id,
                            order: response.data.order
                        };

                        let is_admin = response.data.user.is_admin;
                        localStorage.setItem('user',JSON.stringify(response.data.user));
                        localStorage.setItem('jwt',response.data.token);
                        
                        if (localStorage.getItem('jwt') != null) { // if logged in
                            this.$emit('loggedIn');

                            this.$store.dispatch('setDefaults', true);
                            this.$store.dispatch('updateShoppingCart', data);
                            refreshShoppingCart(response.data.order_items);

                            if (this.$route.params.nextUrl != null) {
                                this.$router.push(this.$route.params.nextUrl).catch(err => {});
                            } else {
                                if (is_admin == 1) {
                                    this.$router.push('admin').catch(err => {});
                                } else {
                                    if (this.previous_route.path == '/checkout') { // and user_id == previous user_id
                                    // if (this.$route.query.prev == 'checkout') {
                                        this.$router.push('checkout').catch(err => {});
                                    } else {
                                        this.$router.push('userboard').catch(err => {});
                                    }
                                }
                            }
                        }
                    })
                    .catch(function (error) {
                        let err_login = error.response.data;
                        console.error('login error: ', err_login);

                        $('#loginError').show();
                        $('#loginError').text(err_login.error);

                        this.login_error = err_login.error;
                    });
                }
            }
        }
    }
</script>

