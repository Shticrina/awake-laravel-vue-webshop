<template>
	<div>
        <div class="d-flex justify-content-between" id="adminDash">
			<div class="flex-fill pt-3 sidebar">
        		<h3 class="small-uppercase text-grey pl-5 ml-1 mb-3">Admin panel</h3>

				<ul class="sidebar-list" id="adminPanel">
					<li><button class="btn text-white active" id="admin-main" @click="setComponent('main')">Dashboard</button></li>
		  			<li><button class="btn text-white" id="admin-categories" @click="setComponent('categories')">Categories</button></li>
		  			<li><button class="btn text-white" id="admin-products" @click="setComponent('products')">Products</button></li>
		  			<li><button class="btn text-white" id="admin-users" @click="setComponent('users')">Users</button></li>
		  			<li><button class="btn text-white" id="admin-orders" @click="setComponent('orders')">Orders</button></li>
		  			<li><button class="btn text-white" id="admin-payments" @click="setComponent('payments')">Payments</button></li>
				</ul>
			</div>

			<div class="flex-fill col-10 pt-3">
				<component :is="activeComponent" :user="user" :user_id="user_id"></component>
			</div>
		</div>
	</div>
</template>

<script>
	import Main from '../components/admin/Main';
	import Categories from '../components/admin/Categories';
	import Products from '../components/admin/Products';
	import Users from '../components/admin/Users';
	import Orders from '../components/admin/Orders';
	import Payments from '../components/admin/Payments';
	import { mapGetters, mapActions } from 'vuex';
	
	export default {
		data() {
			return {
				activeComponent : null,
			}
		},
		components : {
			Main, Users, Products, Orders, Categories, Payments
		},
		computed: {
			...mapGetters([
				'getCart',
				'user',
				'user_id',
				'user_type'
			])
		},
		beforeMount() {
			this.setComponent(this.$route.params.page);
			this.setDefaults();

			axios.defaults.headers.common['Content-Type'] = 'application/json';
			axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('jwt');
		},
		mounted() {
			// console.log('this.$route.params.page in admin: ', this.$route.params.page);
			if (typeof this.$route.params.page != 'undefined') {
				this.setComponent(this.$route.params.page);
			} else {
				this.setComponent('main');
			}
		},
		methods : {
			setDefaults() {
                this.$store.dispatch('setDefaults', true);
            },
			setComponent(value) {
				$('#adminPanel button.btn.text-white').removeClass("active");
				$('#adminPanel #admin-'+value).addClass("active");

				switch(value) {
					case "categories":
						this.activeComponent = Categories;

						if (this.$route.path != '/admin/categories') {
							this.$router.push({name : 'admin-pages', params : {page: 'categories'}});
							window.location.reload();
						}

						break;
					case "users":
						this.activeComponent = Users;

						if (this.$route.path != '/admin/users') {
							this.$router.push({name : 'admin-pages', params : {page: 'users'}});
							window.location.reload();
						}

						break;
					case "orders":
						this.activeComponent = Orders;

						if (this.$route.path != '/admin/orders') {
							this.$router.push({name : 'admin-pages', params : {page: 'orders'}});
							window.location.reload();
						}

						break;
					case "products":
						this.activeComponent = Products;

						if (this.$route.path != '/admin/products') {
							this.$router.push({name : 'admin-pages', params : {page: 'products'}});
							window.location.reload();
						}

						break;
					case "products":
						this.activeComponent = Payments;

						if (this.$route.path != '/admin/payments') {
							this.$router.push({name : 'admin-pages', params : {page: 'payments'}});
							window.location.reload();
						}

						break;
					default:
						this.activeComponent = Main;

						if (this.$route.name != 'admin') {
							this.$router.push({name : 'admin'});
						} 

						break;
				}
			}
		}
	}
</script>

<style>
	.sc-table {
        width: 100% !important;
    }

    .el-button--text {
        color: #f00 !important;
    }

    .dropbtn {
        -webkit-appearance: none;
        background-color: #FFF;
        background-image: none;
        border-radius: 4px;
        border: 1px solid #DCDFE6;
        box-sizing: border-box;
        color: #606266;
        display: inline-block;
        font-size: inherit;
        height: 40px;
        line-height: 40px;
        outline: 0;
        padding: 0 15px;
        -webkit-transition: border-color .2s cubic-bezier(.645,.045,.355,1);
        transition: border-color .2s cubic-bezier(.645,.045,.355,1);
        width: 100%;
    }

    .dropbtn:hover, .dropbtn:focus {
      border-color: #ddd;
    }

    #myInput:focus {outline: 3px solid #ddd;}

    .dropdown {
      position: relative;
      display: inline-block;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      background-color: #f6f6f6;
      min-width: 230px;
      overflow: auto;
      border: 1px solid #ddd;
      z-index: 1;
    }

    .dropdown-content a {
      color: black;
      padding: 12px 16px;
      text-decoration: none;
      display: block;
    }

    .dropdown a:hover {background-color: #ddd;}

    .show {display: block;}

	#adminDash {
		min-height: 100vh;
	}

	.el-table td, .el-table th {
	    text-align: center !important;
	}

	.active {
		color: #87ceeb !important;
		/*color: #0ed230 !important;*/
	}

	.el-button--info {
	    background-color: #87ceeb !important;
	    border-color: #87ceeb !important;
	}

	h3.small-uppercase.text-grey {
		font-size: 16px !important;
	}

	.hero-section {
		height: 20vh;
		background: #ababab;
		align-items: center;
		margin-bottom: 20px;
		margin-top: -20px;
	}

	.sidebar {
		background-color: #555;
	}

	.sidebar-list {
		color: #fff !important;
	}

	@media (min-width: 576px) {
		#viewOrderModal .modal-dialog,
		#editProductModal .modal-dialog {
		    max-width: 900px !important;
		}
	}
</style>