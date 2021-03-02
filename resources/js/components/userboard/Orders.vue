<template>
	<div class="col-md-12 bg-white mt-2 px-0">

        <div id="myOrders" class="px-5 py-4">
            <h4 class="text-success">My orders</h4>
            <h5 v-if="orders.length == 0">You don't have any orders yet.</h5>

            <table v-else class="table table-bordered mt-2 mb-4">
                <thead>
                    <tr class="text-center">
                        <th>Order #</th>
                        <th>Total price</th>
                        <th>Payment status</th>
                        <th>Delivery</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="(order,index) in orders" @key="index" class="text-center">
                        <td v-model="order.id">
                            <button class="btn text-info" @click="seeOrderDetails(order.id)">{{ index+1 }} (#{{ order.id }})</button>
                        </td>
                        <td v-model="order.total_price">{{ order.total_price }} &euro;</td>
                        <td v-model="order.payment_status">{{ payment_status_names[order.payment_status] }}</td>
                        <td v-model="order.is_delivered">{{ order.is_delivered == 1 ? "shipped" : "not shipped" }}</td>
                        <td v-model="order.created_at">{{ order.created_at }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <order-details :id="id" v-if="showDetails"></order-details>

       <!--  <div class="px-5 py-4 orderDetails" id="orderDetails">
            <h4 class="text-grey">Order details</h4>
            <p>...</p>

            <h4 class="text-grey">Order items</h4>
            <p>...</p>
        </div> -->
    </div>
</template>

<script>
    import OrderDetails from '../../components/userboard/OrderDetails';

	export default {
        props: ['user', 'user_id'],
        data() {
            return {
                orders: [],
                payment_status_names: {
                    0: 'No payment',
                    1: 'Not finished/Pending',
                    2: 'Paid'
                },
                id: null,
                showDetails: false
            }
        },
        components : {
            OrderDetails
        },
        beforeMount() {
            axios.get('/api/users/'+this.user_id+'/orders')
            .then(response => {
                this.orders = response.data;
            })
            .catch(error => {
                console.error(error);
            });
        },
        mounted() {
            $('#myOrders').removeClass('d-none');
            this.showDetails = false;
        },
        methods : {
            seeOrderDetails(orderId) {
                // console.log('seeOrderDetails: ', orderId);
                this.showDetails = true;
                this.id = orderId;
                
                $('#myOrders').addClass('d-none');
                $('#orderDetails').removeClass('d-none');
                $('#userboard button').removeClass('active');

                this.$store.dispatch('updateOrderDetails', orderId);
                this.$router.push({name: 'userboard-pages', params: {page: 'order-details'}});
            }
        }
    }
</script>