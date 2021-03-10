<template>
    <div>
        <div class="d-flex justify-content-start px-4">
            <h3 class="small-uppercase text-myblue">&gt;&nbsp; Dashboard</h3>
        </div>

    	<div class="d-flex container justify-content-center align-items-center my-4">
            <div class="bg-secondary mx-5 px-5 py-3 big-text">
                <a href='/admin/orders'>Orders ({{ orders.length }})</a>
            </div>

            <div class="bg-secondary mx-5 px-5 py-3 big-text">
               	<a href='/admin/products'>Products ({{ products.length }})</a>
            </div>

            <div class="bg-secondary mx-5 px-5 py-3 big-text">
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
        beforeMount() {
            axios.get('/api/users/')
            .then(response => {
                console.log(response.data);
                this.users = response.data;
            })
            .catch(error => {
                console.error(error);
            });

            axios.get('/api/products/')
            .then(response => {
                console.log(response.data);
                this.products = response.data.products;
            })
            .catch(error => {
                console.error(error);
            });

            axios.get('/api/orders/')
            .then(response => {
                console.log(response.data);
                this.orders = response.data;
            })
            .catch(error => {
                console.error(error);
            });      
        },
        mounted() {
            console.log(this.users);
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