<template>
    <div>
        <div>{{ token }}</div>
    </div>
</template>

<script>
export default {
    created() {
        this.token = "Bearer " + window.Laravel.access_token;
    },

    data() {
        return {
            token: "",
        };
    },
};
</script>

<style></style>
