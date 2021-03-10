import Vue from 'vue';
import Vuex from 'vuex';
import router from './router';

Vue.use(Vuex);

let store = new Vuex.Store({
	state: {
		inCart: [],
		products: [],
		product_details: [],
		categories: [],
		currentProduct: {}, // in show product page
		currentOrder: null,
		user: null,
		name: null,
		user_id: null,
		user_type: null,
		order_id: null, // if there is a shopping cart, get order_id 
		total_price: 0,
		cart_path: '/',
		nbItems: 0,
		current_route: null,
		orderDetailsId: null,
		currentOrderAdmin: null,
		orderDetailsItems: null,
		orderDetailsPaymentStatus: null,
		orderDetailsPayment: null,
		admin_dashboard: false
	},
	getters: {
		getCart: state => state.inCart,
		products: state => state.products,
		product_details: state => state.product_details,
		current_product_details: state => state.current_product_details,
		categories: state => state.categories,
		currentProduct: state => state.currentProduct,
		currentOrder: state => state.currentOrder,
		user: state => state.user,
		name: state => state.name,
		user_id: state => state.user_id,
		user_type: state => state.user_type,
		order_id: state => state.order_id,
		total_price: state => state.inCart ? state.inCart.reduce((acc, cur) => acc + cur.price * cur.quantity, 0) : 0,
		stripe: state => state.stripe,
		cart_path: state => state.cart_path,
		nbItems: state => state.nbItems,
		current_route: state => state.current_route,
		orderDetailsId: state => state.orderDetailsId,
		currentOrderAdmin: state => state.currentOrderAdmin,
		orderDetailsItems: state => state.orderDetailsItems,
		orderDetailsPaymentStatus: state => state.orderDetailsPaymentStatus,
		orderDetailsPayment: state => state.orderDetailsPayment,
		admin_dashboard: state => state.admin_dashboard
	},
	mutations: {
		initialiseStore(state) {
			// Check if the ID exists
			if (localStorage.getItem('store')) {
				// Replace the state object with the stored item
				this.replaceState(
					Object.assign(state, JSON.parse(localStorage.getItem('store')))
				);
			}
		},
		SET_PRODUCTS: (state, products) => {
	    	state.products = products;
	    },
	    SET_PRODUCT_DETAILS: (state, details) => {
	    	state.product_details = details; 
	    },
	    SET_CURRENT_PRODUCT_DETAILS: (state, current_details) => {
	    	state.current_product_details = current_details;
	    	// console.log('state.current_product_details: ', state.current_product_details); // ok
	    },
	    SET_CURRENT_PRODUCT: (state, currentProduct) => {
	    	state.currentProduct = currentProduct;
	    },
	    SET_CATEGORIES: (state, categories) => {
	    	state.categories = categories;
	    },
	    SET_USER: (state, user) => {
	    	state.user = user;
	    },
	    SET_ADMIN: (state, value) => {
	    	state.admin_dashboard = value;
	    },
	    SET_USER_NAME: (state, name) => {
	    	state.name = name;
	    },
	    SET_USER_TYPE: (state, user_type) => {
	    	state.user_type = user_type;
	    },
	    SET_USER_ID: (state, user_id) => {
	    	state.user_id = user_id;
	    },
		SET_ORDER_ID: (state, order_id) => {
	    	state.order_id = order_id;
	    },
	    SET_CURRENT_ORDER: (state, order) => {
	    	state.currentOrder = order;
	    },
	    SET_CURRENT_ROUTE: (state, currentRoute) => {
	    	state.current_route = currentRoute;
	    },
	    SET_ORDER_DETAILS_ID: (state, orderDetailsId) => {
	    	state.orderDetailsId = orderDetailsId;
	    },
	    SET_CURRENT_ORDER_ADMIN: (state, order) => {
	    	state.currentOrderAdmin = order;
	    },
	    SET_ORDER_DETAILS_ITEMS: (state, order_items) => {
	    	state.orderDetailsItems = order_items;
	    },
	    SET_ORDER_DETAILS_PAYMENT_STATUS: (state, status) => {
	    	state.orderDetailsPaymentStatus = status;
	    },
	    SET_ORDER_DETAILS_PAYMENT: (state, payment) => {
	    	state.orderDetailsPayment = payment;
	    },
	    UPDATE_CART: (state, items) => {
	    	// state.inCart = (items && items.length > 1) ? items : [];
	    	state.inCart = items;

	    	if (items && items.length > 0) {
	        	state.cart_path = '/cart';
	        } else {
	        	state.cart_path = '/';
	        }

	        state.nbItems = items ? items.length : 0;
	    }
	},
	actions: {
		setDefaults: ({ commit}, isLoggedIn) => {
			if (isLoggedIn) {
				let user = JSON.parse(localStorage.getItem('user'));

				axios.defaults.headers.common['Content-Type'] = 'application/json';
            	axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('jwt');
				
				commit('SET_USER_TYPE', user.is_admin);
				commit('SET_USER_ID', user.id);
				commit('SET_USER_NAME', user.name);
				commit('SET_USER', user);

				axios.post('/api/currentShoppingCart', {
	                isLoggedIn: isLoggedIn,
	                user_id: user.id,
	                user: user
	            })
	            .then(response => {
                    commit('UPDATE_CART', response.data.order_items);
                    commit('SET_CURRENT_ORDER', response.data.basket);
                    commit('SET_ORDER_ID', response.data.order_id);
	            });

	            if (router.currentRoute.path.includes("/admin")) {
			  		commit('SET_ADMIN', true);

			  		axios.get("/api/categories/")
			        .then((response) => {
			            commit('SET_CATEGORIES', response.data);
			        })
			        .catch(error => {
		                console.error(error);
		            });
			  	} else {
			  		commit('SET_ADMIN', false);
			  	}
			} else {
				commit('SET_USER_TYPE', null);
				commit('SET_USER_ID', null);
				commit('SET_USER', null);
				commit('SET_USER_NAME', null);
			}
		},
		getAllProducts: ({ commit, dispatch }) => {
			// headers: Access-Control-Allow-Origin
	        axios.get("/api/products/", {
				headers: { 
					'Access-Control-Allow-Origin' : '*',
        			'Access-Control-Allow-Methods' : 'GET,PUT,POST,DELETE,PATCH,OPTIONS'
				}
			})
	        .then(response => {
	        	dispatch('addDetailsToProducts', response.data.products);
	            commit('SET_CATEGORIES', response.data.categories);
	            commit('SET_PRODUCTS', response.data.products);
	            commit('SET_PRODUCT_DETAILS', response.data.productDetails);
	        })
	        .catch(error => {
                console.error(error);
            });
	    },
	    productsByCat: ({ commit, dispatch }, cat_id) => {
			axios.get('/api/byCategory/'+cat_id)
            .then(response => {
                dispatch('addDetailsToProducts', response.data.productsByCat);
            })
            .catch(error => {
                console.error(error);
            });
		},
		searchProducts: ({commit, dispatch}, search_item) => {
			axios.get('/api/product/search', { params: { query: search_item } })
            .then(response => {                    
                dispatch('addDetailsToProducts', response.data);
            })
            .catch(error => {
                console.error(error);
            });
		},
		addDetailsToProducts: ({commit}, my_object) => {
	    	for (let product in my_object) {
                let details = my_object[product].details;

                details.sort(function(a, b) { 
                    let aa = a['price'];
                    let bb = b['price'];

                    return aa-bb;
                });

                my_object[product]['image'] = details[0].image;
                my_object[product]['price'] = details[0].price;
                my_object[product]['detailsId'] = details[0].id;
            }

            commit('SET_PRODUCTS', my_object);
	    },
		updateShoppingCart: ({commit}, params) => {
			commit('UPDATE_CART', params.items);

			if (params.order_id) {
	    		commit('SET_ORDER_ID', params.order_id);
	    		commit('SET_CURRENT_ORDER', params.order);
	    	}
		},
	    mergeBasket: ({commit, state}) => {	// when logout
	    	axios.post('/api/basket/merge', {
	            user_id : state.user_id,
	            order_id : state.order_id,
	            loggedIn : true
	        })
	        .then(response => {
	        	if (response.data.success) {
					console.log(response.data.success);
	        	}
	        })
	        .catch(error => {
	            console.error(error);
	        });
		},
		updateUser: ({commit}, params) => {
			return axios.post('/api/users/update', params.formData)
            .then(response => {
                if (response.data.success) {
                    let user = response.data.user;

                    params.vm.$toastr.s(response.data.success, "Success!");
                    localStorage.setItem('user',JSON.stringify(user));
                    
	    			commit('SET_USER', user);
	    			commit('SET_USER_NAME', user.name);
                }
            })
            .catch(error => {
                var validation_errors = error.response.data.errors;

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
	    },
	    updateShippingAddress: ({commit}, params) => {
			axios.post('/api/order/updateShippingAddress', params.formData)
            .then(response => {
                if (response.data.success) {
                    params.vm.$toastr.s(response.data.success, "Success!");
                    $('#modifyShippingAddressModal').modal("hide");
	    			commit('SET_CURRENT_ORDER', response.data.order);
                } else {
                    params.vm.$toastr.e("ERRROR MESSAGE");
                }
            })
            .catch(error => {
                var validation_errors = error.response.data.errors;

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
	    },
		getCurrentProduct: ({commit}, product_id) => {
			return axios.get('/api/products/'+product_id)
            .then(response => {
            	commit('SET_CURRENT_PRODUCT', response.data);
            	commit('SET_CURRENT_PRODUCT_DETAILS', response.data.details);
            	return response.data;
            })
            .catch(error => {
                console.error(error);
            });
		},
		updateOrderDetails: ({commit}, order_id) => {
			if (!router.currentRoute.path.includes("/admin")) {
				commit('SET_CURRENT_ROUTE', 'order-details');
			}

	    	axios.get('/api/orders/'+order_id)
            .then(response => {
            	commit('SET_ORDER_DETAILS_ID', order_id);
            	commit('SET_CURRENT_ORDER_ADMIN', response.data.order);
            	commit('SET_ORDER_DETAILS_PAYMENT', response.data.payment);
            	commit('SET_ORDER_DETAILS_PAYMENT_STATUS', response.data.payment_status);
            	commit('SET_ORDER_DETAILS_ITEMS', response.data.orderItems);
            })
            .catch(error => {
                console.error(error);
            });
	    },
	    editOrAddCategory: ({commit}, params) => {
			return axios.post('/api/categories/storeOrUpdate', params.formData)
            .then(response => {
                if (response.data.success) {
                    if (response.data.success) {
                        params.vm.$toastr.s(response.data.success, "Success!");
                    } else {
                        params.vm.$toastr.e(response.data.error, "Error!");
                    }

	    			commit('SET_CATEGORIES', response.data.categories ? response.data.categories : []);
	    			return response.data.id;
                }
            })
            .catch(error => {
                var validation_errors = error.response.data.errors;

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
	    },
	    deleteCategory: ({commit}, cat_id) => {
			axios.post('/api/categories/delete', {cat_id: cat_id})
            .then(response => {
                if (response.data.success) {
    				commit('SET_CATEGORIES', response.data.categories ? response.data.categories : []);
                }
            })
            .catch(error => {
                console.error(error);
            });
	    },
	}
});

store.subscribe((mutation, state) => {
	// Store the state object as a JSON string
	localStorage.setItem('store', JSON.stringify(state));
});

export default store;