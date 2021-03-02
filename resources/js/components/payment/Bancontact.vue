<template>
	<div class="container bg-dark-gray px-5" id="bancontact">
        <h4 class="my-4">Bancontact</h4>

        <div class="row justify-content-center align-items-center pb-4 px-4">
            <div class="col-8 text-left">
                <p class="kkk">You will be redirected to your Bancontact page to process the payment. Once the payment has been successfully processed, you will be redirected here to finish your order.</p>
                <p class="text-16 text-info font-italic">Click on "Pay" to proceed to payment.</p>
            </div>

            <div class="flex-fill text-center col-4 pb-4">
                <img class="w-50" src="/images/payment/bancontact.png" alt="bancontact">
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

	export default {
        props: ['order_id', 'shipping_price'],
		data: function() {
			return {
                bcWindow: null,
			}
		},
		mounted: function() {
            // console.log('bancontact component order_id:', this.order_id);
            // console.log('$parent.bc_status:', this.$parent.bc_status); // ok
		},
		methods: {
			initPayment: function() {
                console.log('init bancontact');
                let data = {
                    order_id: this.order_id,
                    payment_method: 2,
                    shipping_price: this.shipping_price
                };

                return axios.post('/api/pay', data)
                .then(response => {
                    if (response.error) {
                        return {
                            type: 'error',
                            code: response.error.code,
                            message: response.error.message
                        };
                    }
                    
                    return {
                        type: 'success',
                        response: response // data, code
                    }
                })
                .catch(error => {
                    return {
                        type: 'error',
                        response: error.response
                    }
                });
            },
            openWindow: function(link) {
                // console.log('link in openWindow Bancontact vue: ', link); //  https://hooks.stripe.com/redirect/authenticate/src_1GkopjIw4n6pDxhosF0S9c6Y?client_secret=src_client_secret_0OXqEimXzbv5txBGE7HTIyDC
                this.bcWindow = window.open(link, "_blank");
            }
		},
        /*watch: {
            '$parent.bc_status': function(value) {
                console.log('watcher triggered in Bancontact.vue');
                console.log(value);

                if (value === 'error') {
                    this.$parent.handleFeedback({
                        type: 'error',
                        message: 'Bancontact redirect failed'
                    });

                    return;
                }

                this.$parent.triggerPayment();
            }
        }*/
	}
</script>