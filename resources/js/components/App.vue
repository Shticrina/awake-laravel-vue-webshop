<template>
	<div>
		<nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm navbar-laravel">
			<div class="container-fluid">
				<router-link :to="{name: 'home'}" v-if="!admin_dashboard" class="navbar-brand">My Store</router-link>
				<router-link :to="{name: 'home'}" v-else class="navbar-brand">Back to website</router-link>

				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
	
				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<!-- Left Side Of Navbar -->
					<ul class="navbar-nav mr-auto"></ul>

					<!-- Right Side Of Navbar -->
					<ul class="navbar-nav ml-auto">
						<li class="nav-item dropdown" id="basket">
							<router-link id="basket01" class="nav-link bg-transparent d-flex justify-content-center" :to="{path: cart_path}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="fa fa-shopping-cart"></i>
								<span id="js-nav-basket-nbitems" class="ml-1">({{ nbItems }})</span>
							</router-link>

							<div class="shopping-cart dropdown-menu" id="basketDropdownBlock" aria-labelledby="basket01">
								<div id="cartContent">
									<ul>Items list...</ul>
									<h5>Total price...</h5>
								</div>

								<div id="cartButtons" class="d-none">
									<div class="d-flex justify-content-between mt-4">
										<router-link :to="{name: 'cart'}" class="flex-fill p-2 mr-3 btn btn-lg btn-success">Shopping cart</router-link>
										<router-link :to="{name: 'checkout'}" class="flex-fill p-2 btn btn-lg btn-secondary">Checkout</router-link>
									</div>
								</div>
							</div>
						</li>

						<li class="nav-item" v-if="!isLoggedIn">
							<router-link :to="{name: 'login'}" class="nav-link">Login</router-link>
						</li>

						<li class="nav-item" v-if="!isLoggedIn">
							<router-link :to="{name: 'register'}" class="nav-link">Register</router-link>
						</li>

						<li class="nav-item dropdown" v-if="isLoggedIn">
							<a id="dropdownProfile" class="nav-link dropdown-toggle bg-transparent" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Hi, {{ name }}</a>

							<div class="dropdown-menu" aria-labelledby="dropdownProfile">
								<router-link :to="{name: user_type == 0 ? 'userboard' : 'admin'}" class="dropdown-item">{{ user_type == 0 ? 'Userboard' : 'Admin' }}</router-link>
								<a class="dropdown-item" href="#" @click="logout">Logout</a>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</nav>

		<main class="">
			<router-view @loggedIn="change"></router-view>
		</main>

		<footer v-if="!admin_dashboard" class="mt-5 py-3 bg-dark text-success text-center">
			<h3>This is the footer</h3>

			<div class="p-3 d-flex justify-content-center">
				follows the links
			</div>
		</footer>
	</div>
</template>

<script>
	import { refreshShoppingCart } from '../functions.js';
	import { mapGetters, mapActions } from 'vuex';

	export default {
	  data() {
		  return {
			isLoggedIn: localStorage.getItem('jwt') != null,
		  }
	  },
	  computed: {
		...mapGetters([
			'user_id',
			'user',
			'user_type',
			'order_id',
			'currentOrder',
			'name',
			'getCart',
			'nbItems',
			'cart_path',
			'admin_dashboard'
		])
	  },
	  beforeMount() {
        axios.defaults.headers.common['Content-Type'] = 'application/json';
        axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('jwt');
      },
	  async mounted() {
    	await this.setDefaults();
	  	refreshShoppingCart(this.getCart);

	  	/*if (this.$route.path.includes("/admin")) {
	  		this.admin_dashboard = true;
	  	}*/
	  	
	  	// console.log('admin_dashboard: ', this.admin_dashboard); // true
	  },
	  methods : {
		setDefaults() {
			this.$store.dispatch('setDefaults', this.isLoggedIn);
		},
		change() {
			this.isLoggedIn = localStorage.getItem('jwt') != null;
			this.setDefaults();
		},
		logout() {
			this.$store.dispatch('mergeBasket');

			localStorage.removeItem('jwt');
			localStorage.removeItem('user');
			this.change();
			this.setDefaults();

			if (this.$route.name != 'home') {
				this.$router.push('/');
			}
		}
	  }
	}
</script>

<style>
	.text-skyblue {
		color: #87ceeb !important;
	}

	.text-success {
		color: #02ce25 !important;
	}

	.btn-success {
	    background-color: #37d13bd1 !important;
	    border-color: #37d13bd1 !important;
	}

	.text-16 {
        font-size: 16px;
    }

    .small-uppercase {
    	text-transform: uppercase;
    	font-family: "Montserrat";
    	letter-spacing: 1px;
    }

    .text-12 {
        font-size: 12px;
    }

    .text-14 {
        font-size: 14px;
    }

    .text-18 {
        font-size: 18px;
    }

    .text-20 {
        font-size: 20px;
    }

    .text-22 {
        font-size: 22px;
    }

    .w-40 {
        width: 40% !important;
    }

    .w-30 {
        width: 30% !important;
    }
    
    .bg-dark-gray {
        background-color: #f6f6f8;
    }

    .text-light-grey {
        color: #C0C4CC !important;
    }

	.btn-skyblue {
		background-color: #73c4e5 !important;
		color: #fff;
	}

	.text-myblue {
		color: #06adf1 !important;
	}

	.title {
		font-size: 30px;
		font-style: italic;
		color: #777;
	}

	#basket.dropdown:hover>#basketDropdownBlock {
		display: block !important;
	}

	#basket01 .fa-shopping-cart {
	    margin-top: 0.15rem !important;
	}

	ul {
	    list-style-type: none;
	}

	#basket .bor-bot {
	    border-bottom: 1px solid #ebebeb;
	}

	#basket .dropdown-menu {
	    position: absolute;
	    padding: 1rem;
	    color: #707070;
	    font-family: Open Sans;
	    font-size: .85rem;
	    max-height: 70vh;
	    overflow-y: scroll;
	    width: 25rem;
	    left: -17rem!important;
	    top: 2.3rem;
	    z-index: 370 !important;
	}
</style>