<template>
	<div class="col-md-12 bg-white mt-2 px-0">
        <div v-bind:class="{'px-5 py-4': !admin_dashboard}" id="orderDetails" v-if="currentOrder">
            <h4 class="text-success mb-4" v-bind:class="{'text-14 small-uppercase': admin_dashboard}">Order details <span v-if="!admin_dashboard"  class="text-grey">(#{{ orderDetailsId }})</span></h4>

            <p><b>Status</b>: {{ payment_status ? payment_status : "N/A" }}</p>
            <p v-if="payment"><b>Payment type</b>: {{ payment ? payment.type_name : "" | uppercaseFirst }}</p>
            <p><b>Account</b>: BE88 1111 5546 1234</p>
            <p><b>BIC</b>: GEBEBEBB</p>

            <p v-if="payment && payment.type == 2"><b>Communication</b>: {{ payment.transfer_comm ? payment.transfer_comm : "" }}</p>

            <p><b>Amount</b>: <b>{{ currentOrderAdmin ? currentOrderAdmin.total_price : '' }} €</b></p>
            <p v-if="payment"><b>Date</b>: {{ currentOrderAdmin ? currentOrderAdmin.created_at : '' }}</p>
            <p v-if="admin_dashboard"><b>User name</b>: {{ order ? order.user.name : '' }}</p>

            <h4 class="text-success mb-4 mt-5" v-bind:class="{'text-14 small-uppercase': admin_dashboard}">Order items</h4>

            <table class="table mt-2 mb-4">
                <thead>
                    <tr class="text-center">
                        <th>Item #</th>
                        <th>Name</th>
                        <th>Size</th>
                        <th>Color</th>
                        <th>Unit price</th>
                        <th>Quantity</th>
                        <th>Total price</th>
                    </tr>
                </thead>
                
                <tbody>
                    <tr v-for="(item, index) in orderItems" @key="index"  class="text-center">
                        <td class=""># {{ item.id }}</td>
                        <td class="align-middle">{{ item.name }}</td>
                        <td class="align-middle">{{ item.size ? item.size : '-' }}</td>
                        <td class="align-middle">{{ item.color ? item.color : '-' }}</td>
                        <td class="align-middle">{{ item.price }} €</td>
                        <td class="align-middle">{{ item.quantity }}</td>
                        <td class="align-middle">{{ item.price*item.quantity }} €</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    import { mapGetters, mapActions } from 'vuex';

	export default {
        name: "OrderDetails",
        props: ['id'],
        data() {
            return {
                order: null,
                payment: null,
                orderItems: null,
                payment_status: null
            }
        },
        computed: {
            ...mapGetters([
                'orderDetailsId',
                'currentOrderAdmin',
                'orderDetailsItems',
                'orderDetailsPayment',
                'orderDetailsPaymentStatus',
                'admin_dashboard'
            ]),
            currentOrder() {
                this.getCurrentOrderDetails();
                return true;
            }
        },
        mounted() {
            console.log('this.currentOrderAdmin: ', this.currentOrderAdmin);
        },
        methods : {
            getCurrentOrderDetails() {
                this.order = this.currentOrderAdmin;
                this.payment = this.orderDetailsPayment;
                this.payment_status = this.orderDetailsPaymentStatus;
                this.orderItems = this.orderDetailsItems;
            }
        }
    }
</script>

<style scoped>
    #orderDetails p {
        margin-bottom: 10px;
    }
</style>