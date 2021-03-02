<template>
    <div class="container d-flex justify-content-center py-5">
        <div class="col-6">
            <img class="w-80" :src="product.image" :alt="product.name">
        </div>

        <div class="col-6">
            <h5 class="title"><span v-html="product.name"></span></h5>
            <p class="text-16">{{ product.description }}</p>
            <p><strong>Price: </strong><span class="text-danger text-18"><strong>{{ product.price }} €</strong></span></p>

            <form id="addItemForm" action="" method="post">
                <p>
                    <input type="hidden" name="_token" :value="csrf">
                    <input type="hidden" name="name" v-model="product.name">
                    <input type="hidden" name="price" v-model="product.price">
                    <input type="hidden" name="image" v-model="product.image">
                    <input type="hidden" name="product_id" v-model="product.id">
                    <input type="hidden" name="product_units" v-model="product.units">

                    <strong>Quantity: </strong> 
                    <select v-model="qtySelected" name="quantity" @change="checkQty(product, qtySelected)"> 
                        <option v-for="option in qtyOptions" v-bind:value="option.value">
                            {{ option.text }}
                        </option>
                    </select>
                </p> 

                <p>
                    <strong>Size: </strong> 
                    <select v-model="sizeSelected" name="size" v-if="sizeOptions.length > 1" @change="changeSize(product, sizeSelected)">
                        <option v-for="option in sizeOptions" v-bind:value="option.value">
                            {{ option.text }}
                        </option>
                    </select>

                    <span class="d-none" id="singleSize"></span>
                    <span class="" v-if="sizeOptions.length == 0">one size</span>
                </p>

                <p>
                    <strong>Color: </strong> 
                    <select v-model="colorSelected" name="color" v-if="colorsOptions.length > 1" @change="changeColor(product, colorSelected)">
                        <option v-for="option in colorsOptions" v-bind:value="option.value">
                            {{ option.text }}
                        </option>
                    </select>

                    <span class="d-none" id="singleColor"></span>
                </p>

                <div class="d-flex justify-content-start mt-5">
                    <a class="mr-5 btn btn-sm btn-success" @click.prevent="addToBasket()">Add to cart</a>
                    <a class="btn btn-sm btn-info text-light" @click.prevent="addToBasket(product.id)">Buy now</a>
                </div>
            </form>
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
                product : [],
                qtySelected: 1,
                sizeSelected: '',
                colorSelected: '',
                qtyOptions: [],
                sizeOptions: [],
                colorsOptions: [],
                sizes: [],
                colors: [],
                isLoggedIn : localStorage.getItem('jwt') != null
            }
        },
        computed: {
            ...mapGetters([
                'user_id',
                'user'
            ])
        },
        mounted() {
            axios.get(`/api/products/${this.$route.params.id}/${this.$route.params.detailsId}`)
            .then(response => {
                this.product = response.data;
                this.updateProduct(this.product);
            })
            .catch(error => {
                console.error(error);
            });
        },
        methods: {
            updateProduct(product) {
                this.sizes = product.all_sizes;
                this.colors = product.all_colors;
                
                if (this.sizes) {
                    for (let size in this.sizes) {
                        let item = { text: this.sizes[size], value: this.sizes[size] };
                        this.sizeOptions[size] = item;
                    }

                    if (this.sizeOptions.length == 1) {
                        $('#singleSize').text(this.sizeOptions[0].value);
                        $('#singleSize').removeClass('d-none');
                    } 
                }

                if (this.colors) {
                    for (let color in this.colors) {
                        let item = { text: this.colors[color], value: this.colors[color] };
                        this.colorsOptions[color] = item;
                    }

                    if (this.colorsOptions.length == 1) {
                        $('#singleColor').text(this.colorsOptions[0].value);
                        $('#singleColor').removeClass('d-none');
                    }
                }

                for (var i = product.units ; i >= 1; i--) {
                    let item = { text: i, value: i };
                    this.qtyOptions[i-1] = item;
                }

                this.qtySelected = 1;
                this.sizeSelected = this.product.size ? this.product.size : '';
                this.colorSelected = this.product.color ? this.product.color : '';
            },
            checkQty(product, quantity) {
                if (quantity > product.units) {
                    this.qtySelected = product.units;
                    this.$toastr.defaultStyle = { "background-color": "orange" };
                    this.$toastr.w("Available quantity is "+product.units, "Warning!");
                }
            },
            // when change size & color => get the right product details & replace price & image
            changeSize(product, size) {
               axios.post(`/api/product/updateDetails`, {
                    product_id : product.id,
                    size : size,
                    product_color : product.color
                })
                .then(response => {
                    this.product = response.data.product;
                    this.updateProduct(this.product);

                    if (response.data.message) {
                        this.$toastr.w(response.data.message, "Warning!");
                    }
                })
                .catch(error => {
                    console.error(error);
                });
            },
            changeColor(product, color) {
                axios.post(`/api/product/updateDetails`, {
                    product_id : product.id,
                    color : color,
                    product_size : product.size
                })
                .then(response => {
                    this.product = response.data.product;
                    this.updateProduct(this.product);

                    if (response.data.message) {
                        this.$toastr.w(response.data.message, "Warning!");
                    }
                })
                .catch(error => {
                    console.error(error);
                });
            },
            addToBasket(productId) {
                let formData = new FormData(document.getElementById("addItemForm"));
                formData.append('isLoggedIn', this.isLoggedIn);
                formData.append('user_id', this.user_id);

                // add product item in the database, then update cart
                axios.post('/api/basket/addItem', formData)
                .then(response => {
                    // console.log('productId: ', productId);
                    if (response.data.success) {
                        this.$toastr.s(response.data.success, "Success!");

                        let data = {
                            items: response.data.order_items,
                            order_id: response.data.order_id,
                            order: response.data.basket
                        };

                        this.$store.dispatch('updateShoppingCart', data);
                        refreshShoppingCart(response.data.order_items);
                        
                        this.qtyOptions.splice(this.qtyOptions.length-this.qtySelected, this.qtySelected);
                        this.qtySelected = 1;

                        if (productId) {
                            // redirect to checkout
                            // this.$router.push('checkout').catch(err => {});
                            this.$router.push({
                                name: 'checkout', 
                                params: { pid: productId }
                            }).catch(err => {});
                            // Now it will have correct value in this.$route.params.
                        }
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
    .small-text {
        font-size: 18px;
    }
    .title {
        font-size: 36px;
    }
    .text-14 {
        font-size: 14px;
    }
    .text-16 {
        font-size: 16px;
    }
    .text-18 {
        font-size: 18px;
    }
    .w-80 {
        width: 90% !important;
    }
</style>