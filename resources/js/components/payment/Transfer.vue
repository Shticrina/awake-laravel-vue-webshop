<template>
	<div class="container bg-dark-gray px-5" id="transfer">
        <h4 class="my-4">Bank transfer</h4>

        <div class="text-left pb-4 pl-4"><b>Account</b>: BE37 0017 7183 3928</div>
    	<div class="text-left pb-4 pl-4"><b>BIC</b>: GEBEBEBB</div>

	    <form id="transferForm" class="row">
		    <div class="input-group pb-5 pl-4">
				<div class="input-group-prepend">
					<span class="input-group-text" id="basic-addon3">Communication</span>
				</div>
				<input class="form-control" type="text" name="" :value="transferComm">
		    </div>
	    </form>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

	export default {
		props: ['order_id', 'user', 'shipping_price'],
		data: function() {
			return {
			}
		},
		computed: {
            ...mapGetters([
                'getCart',
                'total_price',
                // 'user'
            ]),
            transferComm: function() {
		        let comm = '';
		        let now = new Date();

		        comm += this.user.name + ' ';
		        comm += now.getDate() + '-' + (now.getMonth()+1) + '-' + now.getFullYear() + ' ';
		        comm += this.order_id;

		        return comm;
        	}
        },
		mounted: function() {
			// console.log('transfer component:', this.order_id);
		},
		methods: {
			initPayment: function() {
                console.log('init transfer');
				let data = {
                    order_id: this.order_id,
                    payment_method: 3,
					shipping_price: this.shipping_price
                };

                return axios.post('/api/pay', data)
                .then(response => {
                    return {
	                    type: 'success',
	                    response: response
                    }
                })
                .catch(error => {
                    return {
                        type: 'error',
                        response: error.response
                    }
                });
            }
		}
	}
</script>