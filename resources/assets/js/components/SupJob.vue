<template>
    <div class="w-full max-w-sm">
        <div v-if="this.supJob != null">

            <div class="flex items-center truncate">
                <img class="w-8 mr-4" src="/images/file-icon.png" alt="file" :title="supJob.originalName" />
                {{ shorten(supJob.originalName) }}
            </div>

            <div class="w-full mt-4">
                <strong v-if="supJob.errorMessage">
                    {{ supJob.errorMessage }}
                </strong>
                <strong v-else-if="supJob.isFinished">
                    <download-link :url="urlKey+'/download'" text="Download"></download-link>
                </strong>
                <strong v-else>
                    Processing... {{ statusMessage }}
                </strong>
            </div>

        </div>
    </div>
</template>

<script>

    export default {

        data: () => ({
            supJob: null,
            statusMessage: '',
        }),

        props: [
            'urlKey',
        ],

        mounted() {
            axios.get(`/api/v1/sup-job/${this.urlKey}`).then(response => {
                this.supJob = response.data.data;

                this.maybeDisconnectFromChannels();
            });

            Echo.channel(`sup-job.${this.urlKey}`).listen('SupJobChanged', (changedSupJob) => {
                this.supJob = changedSupJob;

                this.maybeDisconnectFromChannels();
            });

            Echo.channel(`sup-job.${this.urlKey}.progress`).listen('SupJobProgressChanged', (newProgress) => {
                this.statusMessage = newProgress.statusMessage;
            });
        },

        methods: {
            shorten: function(string) {
                let maxLength = 40;

                if(string.length < maxLength ) {
                    return string;
                }

                return string.substring(0, maxLength/2) + '...' + string.substring(string.length - maxLength/2);

            },

            maybeDisconnectFromChannels: function() {
                if(this.supJob.isFinished) {
                    Echo.disconnect();
                }
            },

        }

    }
</script>
