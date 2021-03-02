<template>
	<div class="col-md-12 bg-white mt-2 px-0">
        <div id="myPayments" class="px-5 py-4">
            <h4 class="text-success">My payments</h4>
            <h5 v-if="payments.length == 0">You don't have any payments yet.</h5>

            <table v-else class="table table-bordered mt-2 mb-4">
                <thead>
                    <tr class="text-center">
                        <td class="">Payment #</td>
                        <td class="">Order #</td>
                        <td class="">Type</td>
                        <td class="">Amount</td>
                        <td class="">Status</td>
                        <td class="">Date</td>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="(payment,index) in payments" @key="index" class="text-center">
                        <td class="">{{ index+1 }} ({{ payment.id }})</td>
                        <td class="">
                            <button class="btn text-info" @click="seeOrderDetails(payment.order_id)">#{{ payment.order_id }}</button>
                        </td>
                        <td class="">{{ payment.type_name }}</td>
                        <td class="">{{ payment.amount }} &euro;</td>
                        <td class="">{{ payment_status_names[payment.status] }}</td>
                        <td class="">{{ payment.created_at }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <order-details :id="id" v-if="showDetails"></order-details>
    </div>
</template>

<script>
    import OrderDetails from '../../components/userboard/OrderDetails';

	export default {
        props : ['user', 'user_id'],
        data() {
            return {
                payments : [],
                payment_status_names: {
                    0: 'Error during payment',
                    1: 'In process/Not finished',
                    2: 'Pending',
                    4: 'Paid',
                    6: 'Canceled',
                    7: 'Failed'
                },
                id: null,
                showDetails: false
            }
        },
        components : {
            OrderDetails
        },
        beforeMount() {
            axios.get('/api/users/'+this.user_id+'/payments')
            .then(response => {
                this.payments = response.data;
            })
            .catch(error => {
                console.error(error);
            });  
        },
        mounted() {
            $('#myPayments').removeClass('d-none');
            this.showDetails = false;
        },
        methods : {
            seeOrderDetails(orderId) {
                this.showDetails = true;
                this.id = orderId;
                $('#myPayments').addClass('d-none');
                $('#orderDetails').removeClass('d-none');
                $('#userboard button').removeClass('active');
                this.$store.dispatch('setComponent', orderId);
                this.$router.push({name: 'userboard-pages', params: {page: 'order-details'}});
            }
        }
    }
</script>