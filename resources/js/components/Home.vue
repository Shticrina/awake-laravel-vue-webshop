<template>
    <div>
        <!-- Carousel slider -->
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
            </ol>

            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="d-block w-100" src="/images/sunset.jpg" alt="First slide">

                    <div class="carousel-caption d-none d-md-block jumbotron py-5 px-0 mb-5">
                        <h2 class="text-uppercase">Discover the real you</h2>
                        <h3 class="display-4">Change your look with your own style</h3>

                        <button class="btn btn-success btn-lg">Shop now</button>
                    </div>
                </div>

                <div class="carousel-item">
                    <img class="d-block w-100" src="/images/redsky.jpg" alt="Second slide">

                    <div class="carousel-caption d-none d-md-block jumbotron py-5 px-0 mb-5">
                        <h2 class="text-uppercase">Discover the real you</h2>
                        <h3 class="display-4">Change your look with your own style</h3>

                        <button class="btn btn-success btn-lg">Shop now</button>
                    </div>
                </div>

                <div class="carousel-item">
                    <img class="d-block w-100" src="/images/jungle.jpg" alt="Third slide">

                    <div class="carousel-caption d-none d-md-block jumbotron py-5 px-0 mb-5">
                        <h2 class="text-uppercase">Discover the real you</h2>
                        <h3 class="display-4">Change your look with your own style</h3>

                        <button class="btn btn-success btn-lg">Shop now</button>
                    </div>
                </div>
            </div>

            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>

            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
        <!-- END Carousel slider -->

        <!-- Filter & search -->
        <div class="container-fluid d-flex align-content-center justify-content-center flex-wrap ml-auto py-3">
            <div class="col-3 text-center">
            </div>

            <div class="col-6">
                <ul class="nav justify-content-center">
                    <li class="nav-item" v-for="(category,index) in categories" @key="index">
                        <a class="nav-link text-uppercase text-dark active" href="#" @click.prevent="showProductsByCat(category.id)">{{ category.name }}</a>
                    </li>
                </ul>
            </div>   

            <div class="col-3 d-flex justify-content-end align-items-center">
               <div class="col-8">
                    <form @submit.prevent="searchProducts()" class="">
                        <input type="hidden" name="_token" :value="csrf">
                        
                        <div class="input-group">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search product here..." v-model="search" name="search">

                                <div class="input-group-append">
                                    <button @click.prevent="searchProducts()" class="input-group-text">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- END Filter & search -->

        <!-- Show products -->
        <div class="container">
            <div class="row">
                <!-- Show search results -->
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 v-if="showsearch">{{ products.length }} results found...</h5>
                    </div>

                    <div class="text-center">
                        <a class="text-info" href="#" v-if="showsearch" @click.prevent="showAllProducts()">Back to all products</a>
                    </div>
                </div>

                <!-- Show all products -->
                <div class="col-md-12" v-bind:class="{ 'pt-4': !showsearch }">
                    <div class="row justify-content-center">
                        <div class="col-md-4 pb-3 mb-3 product-box" v-for="(product,index) in products" @key="index">
                            <router-link :to="{ path: '/products/'+product.id+'/'+product.detailsId }">
                                <img :src="product.image" :alt="product.name">
                                <h5><span v-html="product.name"></span></h5>
                            </router-link>

                            <div class="d-flex justify-content-between">
                                <h3 class="">
                                    <span class="text-success text-18">Price: </span>
                                    <strong class="">{{ product.price }} €</strong>
                                </h3>

                                <router-link :to="{ path: '/products/'+product.id+'/'+product.detailsId }" class="btn btn-xs btn-success">Details</router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END Show products -->
    </div>
</template>

<script>
    import { mapGetters, mapActions } from 'vuex';
    import { refreshShoppingCart } from '../functions.js';

    export default {
        data() {
            return {
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                search: '',
                showsearch: false,
                isLoggedIn : localStorage.getItem('jwt') != null
            }
        },
        computed: {
            ...mapGetters([
                'products',
                'categories',
                'user',
                'user_id',
                'user_type',
                'getCart'
            ])
        },
        beforeMount() {
            axios.defaults.headers.common['Content-Type'] = 'application/json';
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('jwt');
        },
        async mounted() {
            this.$store.dispatch('getAllProducts');
            await this.setDefaults();
            refreshShoppingCart(this.getCart);
        },
        methods: {
            setDefaults() {
                this.$store.dispatch('setDefaults', this.isLoggedIn);
            },
            showAllProducts() {
                let vm = this;
                this.$store.dispatch('getAllProducts');
                this.showsearch = false;
                vm.$set(this.products, 'quantity', 1);
            },
            searchProducts() {
                this.$store.dispatch('searchProducts', this.search);
                this.search = '';
                this.showsearch = true;
            },
            showProductsByCat(cat_id) {
                this.showsearch = true;
                this.$store.dispatch('productsByCat', cat_id);
            }
        }
    }

    $('.carousel').carousel({
        interval: 1000
    });
</script>

<style scoped>
    .small-text {
        font-size: 14px;
    }
    .product-box {
        border: 1px solid #cccccc;
        background-color: #fff;
    }
    .product-box h5 {
        height: 45px;
        text-transform: uppercase;
    }
    .hero-section {
        height: 30vh;
        background: #ababab;
        align-items: center;
        margin-bottom: 20px;
        margin-top: -20px;
    }
    .title {
        font-size: 60px;
        color: #ffffff;
    }

    .carousel-caption {
        background-color: rgba(0,0,0,.7) !important;
    }

    .carousel-item img {
        max-height: 400px;
    }
    .text-18 {
        font-size: 18px;
    }
</style>