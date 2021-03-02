<template>
    <div>
        <div class="d-flex justify-content-start px-4">
            <h3 class="small-uppercase text-success">&gt;&nbsp; Orders</h3>
        </div>

        <div class="d-flex justify-content-start mt-4 px-4">
            <data-tables-server :data="mydata" :table-props="tableProps" :pagination-props="mydata.length > 0 ? { pageSizes: [5, 10, 15] } : null" :total="total"> <!-- @query-change="loadData" -->
                <div slot="empty" style="color: red">emptyyyyyyyyyyyyyy</div>

                <el-table-column v-for="title in titles" :prop="title.prop" :label="title.label" :key="title.label" sortable="custom">
                </el-table-column>
            </data-tables-server>
        </div>
    </div>
</template>

<script>
	export default {
        data() {
            return {
                orders: [],
                mydata: [],
                total: 0,
                /*mydata: [...new Array(30)].reduce((previous) => {
                    return previous.concat(mydata);
                }, []),*/
                titles: [
                    {
                        prop: "id",
                        label: "Id"
                    }, 
                    {
                        prop: "user_id",
                        label: "User id"
                    },
                    {
                        prop: "total_price",
                        label: "Cost"
                    },
                    {
                        prop: "payment_status",
                        label: "Payment status"
                    },
                    {
                        prop: "shipping_address",
                        label: "Shipping address"
                    },
                    {
                        prop: "date",
                        label: "Date"
                    },
                    {
                        prop: "is_delivered",
                        label: "Is delivered"
                    }
                ],
                tableProps: {
                    border: true,
                    stripe: true,
                    defaultSort: {
                        prop: 'user_id',
                        order: 'descending'
                    }
                },
            }
        },
        beforeMount() {
            /*if (this.$route.path != '/userboard/orders') {
                this.$router.push({name : 'userboard-pages', params : {page: 'orders', id: null}});
                window.location.reload();
            }*/
            axios.get('/api/orders/')
            .then(response => {
                this.orders = response.data;     

                for (let item in this.orders) {
                    let order = this.orders[item];

                    this.mydata.push({
                        "id": order.id,
                        "user_id": order.user_id,
                        "total_price": order.total_price, // +" €"
                        "payment_status": order.payment_status,
                        "shipping_address": this.$options.filters.uppercaseFirst(order.shipping_address),
                        "date": order.created_at,
                        "is_delivered": order.is_delivered ? "Yes" : "No"
                    });

                    this.total = this.mydata.length;
                }
                console.log('total: ', this.total);
                // console.log('shipping_address: ', this.$options.filters.uppercaseFirst("shipping_address")); // ok
            })
            .catch(error => {
                console.error(error);
            }); 
        },
        /*mounted() {
            console.log(this.orders);
        },*/
        methods: {
            async loadData(queryInfo) {
                console.log('queryInfo: ', queryInfo);
                let { data, total } = await http(queryInfo);
                this.mydata = mydata;
                this.total = total;
            },
            deliver(index){
                let order = this.orders[index];

                axios.patch(`/api/orders/${order.id}/deliver`)
                .then(response => {
                    this.orders[index].is_delivered = 1;
                    this.$forceUpdate();
                })
                .catch(error => {
                    console.error(error);
                })   
            }
        }
    }
</script>

<style scoped>
    .sc-table {
        width: 100% !important;
    }
   /*@import url("https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css");*/
 </style>