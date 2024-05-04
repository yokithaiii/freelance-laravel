<script>
export default {
    name: 'Show',

    props: [
        'user'
    ],

    data() {
        return {
            like_str: ''
        }
    },

    created() {
        window.Echo.channel(`send_like_${this.$page.props.auth.user.id}`)
            .listen('.send_like', res => {
                console.log(res)
                this.like_str = res.like_str
            })
    },

    methods: {
        sendLike() {
            axios.post(`/user/${this.user.id}`, {from_id: this.$page.props.auth.user.id})
                .then(res => {
                    this.like_str = res.data.like_str;
                })
        }
    }
}
</script>

<template>
    <div class="p-6 w-1/3 mx-auto">
        <div class="mb-4">
            User {{ user.login }}
        </div>
        <div class="mb-4">
            <a @click.prevent="sendLike" href="#" class="rounded-lg block w-48 bg-gray-500 text-white text-center py-2">Send like</a>
        </div>

        <div v-if="like_str">
            {{ like_str }}
        </div>
    </div>
</template>

<style scoped>

</style>