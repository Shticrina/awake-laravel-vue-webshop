<template>
    <div>
        <div class="d-flex justify-content-start px-4">
            <h3 class="small-uppercase text-myblue">&gt;&nbsp; Dashboard</h3>
        </div>

    	<div class="d-flex justify-content-start my-4 px-4">
            <div class="col-md-4 product-box d-flex align-content-center justify-content-center flex-wrap big-text">
                <a href='/admin/orders'>Orders ({{ orders.length }})</a>
            </div>

            <div class="col-md-4 product-box d-flex align-content-center justify-content-center flex-wrap big-text">
               	<a href='/admin/products'>Products ({{ products.length }})</a>
            </div>

            <div class="col-md-4 product-box d-flex align-content-center justify-content-center flex-wrap big-text">
                <a href='/admin/users'>Users ({{ users.length  }})</a>
            </div>
        </div>
    </div>
</template>

<script>
	export default {
        data() {
            return {
                // user : null,
                orders : [],
                products : [],
                users : []
            }
        },
        mounted() {
            axios.get('/api/users/')
            .then(response => {
                this.users = response.data;
            })
            .catch(error => {
                console.error(error);
            })

            axios.get('/api/admin-products/')
            .then(response => {
                this.products = response.data.products;
            })
            .catch(error => {
                console.error(error);
            })

            axios.get('/api/orders/')
            .then(response => {
                this.orders = response.data;
            })
            .catch(error => {
                console.error(error);
            })         
        }
    }
</script>

<style scoped>
    .big-text {
        font-size: 28px;
    }
	.product-box {
        border: 1px solid #cccccc;
        padding: 10px 15px;
        height: 20vh
    }
</style>