<template>
    <div class="block">
        <button class='block w-full btn btn-default text-white  bg-primary'
                @click.prevent="loginAs"
        >
            {{ (panel.buttonTitle || 'Login') }}
        </button>
        <iframe ref="login-iframe" :src='iframeUrl' style="display: none"></iframe>
    </div>
</template>

<script>
export default {
    props: ['resourceName', 'resourceId', 'panel'],

    mounted() {
        //
    },
    computed: {
        resourceID() {
            return (this.panel.fields[0].resourceID || this.resourceId)
        },
        postUrl() {
            return (this.panel.fields[0].postUrl || "http://localhost:3000")
        },
        iframeUrl() {
            return this.postUrl + (this.panel.fields[0].iframePath || '/external-login')
        },
        redirectUrl() {
            return this.postUrl + (this.panel.fields[0].redirectPath || '/login')
        }
    },
    methods: {
        postCrossDomainMessage(msg) {
            this.$refs['login-iframe']
                .contentWindow
                .postMessage(msg, this.postUrl);
        },
        saveToken(data) {
            this.$toasted.show(this.__('Token generated. Saving token. Please do not refresh the page'), {type: 'success'})
            this.postCrossDomainMessage(data.data)
            // We use timeout without profile generation
            setTimeout(() => {
                this.$toasted.show(this.__('Redirecting...'), {type: 'success'})
                window.open(this.redirectUrl, '_blank');
            }, 3000);
        },
        loginAs() {
            this.$toasted.show('Request new token.  Please do not refresh the page', {type: 'success'})
            Nova.request()
                .post(`/nova-vendor/nova-external-login/${this.resourceName}/${this.resourceID}/create-token`, {
                    unique_key: this.panel.uniqueKey
                })
                .then(response => {
                    this.saveToken(response.data)
                })

        }
    }
}
</script>
