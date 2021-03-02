<template>
	<div>
		<div class="container d-flex justify-content-start">
			<a href="/userboard"><h3 class="title pt-5">User dashboard</h3></a>
		</div>

		<div class="container row d-flex justify-content-center mx-auto mb-5 pb-5" id="userboard">
			<div class="col-md-12 bg-secondary py-2">
				<button class="btn text-uppercase small-text active" id="userboard-main" @click="setComponent('main')">Personal information</button>
				<button class="btn text-uppercase small-text" id="userboard-orders" @click="setComponent('orders')">Orders</button>
				<button class="btn text-uppercase small-text" id="userboard-payments" @click="setComponent('payments')">Payments</button>
			</div>

			<component :is="activeComponent" :user="user" :user_id="user_id" :order_id="orderId"></component>
		</div>
	</div>
</template>

<script>
	import Main from '../components/userboard/Main';
	import Orders from '../components/userboard/Orders';
	import Payments from '../components/userboard/Payments';
	import OrderDetails from '../components/userboard/OrderDetails';
	import { mapGetters, mapActions } from 'vuex';

	export default {
		data() {
			return {
				// orders: [],
				orderId: null,
				activeComponent : null,
			}
		},
		components : {
			Main, Payments, Orders, OrderDetails
		},
		computed: {
			...mapGetters([
				'user',
				'user_id',
				'order_id',
				'currentOrder'
			])
		},
		beforeMount() {
			this.setComponent(this.$route.params.page);
			this.setDefaults();
			
			axios.defaults.headers.common['Content-Type'] = 'application/json';
			axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('jwt');
		},
		mounted() {
			this.orderId = this.order_id;

			if (typeof this.$route.params.page != 'undefined') {
				this.setComponent(this.$route.params.page);
			} else {
				this.setComponent('main');
			}
		},
		methods: {
			setDefaults() {
                this.$store.dispatch('setDefaults', true);
            },
			setComponent(value) {
				$('#userboard button').removeClass("active");
				$('#userboard #userboard-'+value).addClass("active");
				
				switch(value) {
					case "orders":
						this.activeComponent = Orders;

						if (this.$route.path != '/userboard/orders') {
							this.$router.push({name : 'userboard-pages', params : {page: 'orders', id: null}});
							window.location.reload();
						}

						break;
					case "payments":
						this.activeComponent = Payments;

						if (this.$route.path != '/userboard/payments') {
							this.$router.push({name : 'userboard-pages', params : {page: 'payments'}});
							window.location.reload();
						}

						break;
					case "order-details":
						this.activeComponent = OrderDetails;

						if (this.$route.path != '/userboard/order-details') {
							this.$router.push({name : 'userboard-pages', params : {page: 'order-details'}});
						}

						break;
					default:
						this.activeComponent = Main;

						if (this.$route.name != 'userboard') {
							this.$router.push({name : 'userboard'});
						} 

						break;
				}
			}
		}
	}
</script>

<style>
	.small-text {
		font-size: 13px;
		letter-spacing: 0.07rem;
	}

	.active {
		color: #0ed230;
	}

	.btn:focus {
	    box-shadow: none;
	}

	.bg-secondary {
	    background-color: #d7d9da !important;
	}

	form label {
		margin-bottom: 0 !important;
		color: #aaa;
	}

	.text-grey {
		color: #aaa;
	}
</style>