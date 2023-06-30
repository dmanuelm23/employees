<template>
    <div v-if="message && showNotification" class="alert alert-success  d-flex align-items-center" role="alert">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
        </svg>
        <div>&nbsp;{{message}}</div>
    </div>
</template>
<script>
export default {
    props: {
        message:{
            type: String,
            required: true,
        },
    },
    data() {
        return {
            showNotification:true,
        }
    },
    mounted() {
        this.showNotification = true;
        setTimeout(()=>{
            let mgAlert=0;
            this.showNotification = false;
            this.$emit("statusAlertMsg", mgAlert);
        },
            4000
        );
    }
}
</script>