<template>
	<div class="container bg-dark-gray px-5" id="credit">
		<h4 class="my-4">Credit card</h4>

		<form id="creditCardForm" class="row">
			<div class="col-10 d-flex justify-content-center flex-column mx-auto" id="stripe-container">
				<div id="card-element" class="w-100">
				<!-- Elements will create input elements here -->
				</div>

				<!-- We'll put the error messages in this element -->
				<div id="card-errors" role="alert"></div>
				<!-- <button id="submitCreditPaymentBtn" type="button">Pay</button> -->
			</div>
		</form>

		<div class="d-flex col-4 justify-content-center mx-auto pt-3">
			<img src="/images/payment/visa.svg" alt="visa" class="w-25 mx-3"> 
			<img src="/images/payment/mastercard.svg" alt="mastercard" class="w-25 mx-3">
		</div>

		<div class="col-10 d-flex align-items-center justify-content-between mx-auto">
			<p class="m-0 pl-3 text-left flex-fill">Click on the following link to access <a href="https://stripe.com/fr-BE/privacy" target="_blank">Stripe privacy policies</a>.</p>

			<a href="https://stripe.com/fr-BE/privacy" target="_blank" class="flex-fill col-2">
				<img src="/images/payment/powered_by_stripe.svg" class="w-100" alt="Powered by Stripe">
			</a>
		</div>
	</div>
</template>

<script>
	import { mystripe } from '../../mystripe.js';
	import { mapGetters } from 'vuex';

	export default {
		props: ['order_id', 'shipping_price'],
		data: function() {
			return {
				cardElement: null,
				intentSecret: null,
				stripe: null,
				stripePublic: 'pk_test_vwUeU2SOiAQHXl6RPOa7lX7j00AR9Ts6dq'
			}
		},
		mounted: function() {
        	this.stripe = Stripe(this.stripePublic, {
			    betas: ['payment_intent_beta_3']
			});

			this.cardElement = mystripe(this.stripe);
			// console.log('cardElement: ', this.cardElement);
		},
		methods: {
			initPayment: function() {
				console.log('init credit card');
				return this.paymentRequest();
			},
			paymentRequest: function() {
				let vm = this;
				let data = {
					order_id: this.order_id,
					payment_method: 1,
					shipping_price: this.shipping_price
				};

				return axios.post('/api/pay2', data)
		        .then(response => {
					// console.log('response.data after pay2: ', response.data); // $this->intent->client_secret

					// response.data has to be a string
					if (typeof response.data !== 'string') {
						return {
							type: 'success',
							response: response
						}
					}

					vm.intentSecret = response.data;
					return vm.stripeRequest();
				})
				.catch(error => {
					console.log('paymentRequest catch'); // here
					console.log(error);

					return {
						type: 'error',
						response: error.response
					};
				});
			},
			stripeRequest: function() {
				console.log('Card stripeRequest');

				return this.stripe.handleCardPayment(this.intentSecret, this.cardElement)
				.then(function(result) {
					console.log('result in handleCardPayment: ', result); // paymentIntent object

					if (result.error) {
						return {
							type: 'error',
							code: result.error.code,
							message: result.error.message
						};
					} else {
						return {
							type: 'success',
							response: {
								data: {
									code: 'intent_success',
									message: 'Your credit card payment has been accepted.'
								}
							},
						};
					}
				});
			}
		}
	}
</script>

<style scoped>
	*, label {
	  font-family: "Helvetica Neue", Helvetica, sans-serif;
	  font-size: 16px;
	  font-variant: normal;
	  padding: 0;
	  margin: 0;
	  -webkit-font-smoothing: antialiased;
	}

	#card-errors {
	  height: 20px;
	  padding: 4px 0;
	  color: #fa755a;
	}

	.token {
	  color: #32325d;
	  font-family: 'Source Code Pro', monospace;
	  font-weight: 500;
	}

	.wrapper {
	  width: 90%;
	  margin: 0 auto;
	  height: 100%;
	}

	#stripe-token-handler {
	  position: absolute;
	  top: 0;
	  left: 25%;
	  right: 25%;
	  padding: 20px 30px;
	  border-radius: 0 0 4px 4px;
	  box-sizing: border-box;
	  box-shadow: 0 50px 100px rgba(50, 50, 93, 0.1),
		0 15px 35px rgba(50, 50, 93, 0.15),
		0 5px 15px rgba(0, 0, 0, 0.1);
	  -webkit-transition: all 500ms ease-in-out;
	  transition: all 500ms ease-in-out;
	  transform: translateY(0);
	  opacity: 1;
	  background-color: white;
	}

	#stripe-token-handler.is-hidden {
	  opacity: 0;
	  transform: translateY(-80px);
	}

	/**
	 * The CSS shown here will not be introduced in the Quickstart guide, but shows
	 * how you can use CSS to style your Element's container.
	 */
	.StripeElement {
	  /*min-width: 300px;*/
	  background-color: white;
	  padding: 8px 12px;
	  border-radius: 4px;
	  border: 1px solid transparent;
	  box-shadow: 0 1px 3px 0 #e6ebf1;
	  -webkit-transition: box-shadow 150ms ease;
	  transition: box-shadow 150ms ease;
	}

	.StripeElement--focus {
	  box-shadow: 0 1px 3px 0 #cfd7df;
	}

	.StripeElement--invalid {
	  border-color: #fa755a;
	}

	.StripeElement--webkit-autofill {
	  background-color: #fefde5 !important;
	}
</style>