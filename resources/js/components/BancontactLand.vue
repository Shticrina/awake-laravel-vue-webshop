<template>
    <div>
        <h3>Close this window to proceed with your payment.</h3>
    </div>
</template>

<script>
    window.addEventListener('beforeunload', function(e) {
        console.log('result: ', $result); // $handler->source['redirect']['status']
        window.opener.checkBc("{{ $result }}");
    });

    window.close();
            
    export default {
        data() {
            return {
                
            }
        },
        mounted() {
            // console.log('user in home: ', this.user);
        },
        methods: {
            
        }
    }
</script>

<style scoped>
    
</style>