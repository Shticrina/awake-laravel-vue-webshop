<template>
    <div>
        <div class="container d-flex justify-content-start">
            <a href="/cart"><h3 class="title pt-5">Shopping cart</h3></a>
        </div>

        <div class="container row d-flex justify-content-center mx-auto mb-5 pb-5 mt-2" id="shoppingCart">
            <h3 v-if="!getCart || getCart.length == 0" class="pb-5 m-5">You have no items yet in your cart!</h3>

            <div class="table-responsive" v-if="getCart && getCart.length > 0">
                <table class="table mb-0" id="cartTable">
                    <thead>
                        <tr>
                            <th class="text-center">Photo</th>
                            <th>Product</th>
                            <th>Size</th>
                            <th>Color</th>
                            <th class="text-center">Quantity</th>
                            <th class="">Price</th>
                            <th class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in getCart" @key="index" :id="'item_'+item.id">
                            <td class="text-center">
                                <div v-if="item.details">
                                    <router-link :to="{ path: '/products/'+item.product_id+'/'+item.details.id }">
                                        <img :src="item.image" :alt="item.name" class="w-60">
                                    </router-link>
                                </div>
                                
                                <div v-else>
                                    <img :src="item.image" :alt="item.name" class="w-60">
                                </div>
                            </td>
                            <td>{{ item.name }}</td>
                            <td>{{ item.size ? item.size : '-' }}</td>
                            <td>{{ item.color ? item.color : '-' }}</td>
                            <td class="text-center">
                                <input type="number" name="units" min="1" :max="item.units" class="qty w-40 text-right" v-model="item.quantity" @change="updateQty(item, item.quantity)">
                            </td>
                            <td class="">{{ item.price }} €</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-danger" @click="removeItem(item)"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <hr class="mt-0">

                <div class="col-12 mb-3 text-right">
                    <h4 class="pt-3">Total price: {{ total_price }} €</h4>
                    <h3 class="text-dark"><strong>Total VATC: {{ total_price }} €</strong></h3>
                </div>

                <div class="col-12 d-flex justify-content-between mb-4">
                    <router-link :to="{path: '/'}" class="btn btn-lg btn-secondary">Continue shopping</router-link>
                    <router-link :to="{path: '/checkout'}" class="btn btn-lg btn-success">Checkout</router-link>
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
                isLoggedIn : localStorage.getItem('jwt') != null
            }
        },
        computed: {
            ...mapGetters([
                'getCart',
                'total_price'
            ])
        },
        mounted() {
            $('#basketDropdownBlock').hide();
        },
        methods : {
            updateQty(item, qty) {
                if (qty > item.units) {
                    this.$toastr.defaultStyle = { "background-color": "orange" };
                    this.$toastr.w("Available quantity is " + item.units, "Warning!");
                } else if (qty == 0) {
                    this.removeItem(item);
                } else {
                    axios.post('/api/basket/update', {
                        item_id : item.id,
                        order_id : item.order_id,
                        product_id : item.product_id,
                        size : item.size,
                        color : item.color,
                        price : item.price,
                        qty : qty
                    })
                    .then(response => {
                        if (response.data.success) {
                            this.$toastr.s(response.data.success, "Success!");

                            let data = {
                                items: response.data.order_items,
                                order: response.data.order,
                                total_price: response.data.total_price
                            };

                            this.$store.dispatch('updateShoppingCart', data);
                            refreshShoppingCart(response.data.order_items);
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        this.$toastr.e(error, "ERRROR!");
                    });
                }
            },
            removeItem(item) {
                axios.post('api/basket/removeItem', {
                    item_id : item.id
                })
                .then(response => {
                    if (response.data.success) {
                        this.$toastr.s(response.data.success, "Success!");

                        let data = {
                            items: response.data.order_items,
                            order: response.data.order,
                            total_price: response.data.total_price
                        };

                        this.$store.dispatch('updateShoppingCart', data);
                        refreshShoppingCart(response.data.order_items);

                        // remove item from html
                        $('#cartTable #item_'+item.id).detach();
                    }
                })
                .catch(error => {
                    console.error(error);
                    this.$toastr.e(error, "ERRROR!");
                });
            }      
        }
    }
</script>

<style scoped>
    #basket.dropdown:hover>#basketDropdownBlock {
        display: block !important;
    }
    .small-text {
        font-size: 18px;
    }
    .order-box {
        border: 1px solid #cccccc;
        padding: 10px 15px;
    }
    .w-40 {
        width: 40px;
    }
    .w-60 {
        width: 60px;
    }
    tbody td {
        vertical-align: middle;
    }
    table {
        background-color: #fff;
    }
</style>