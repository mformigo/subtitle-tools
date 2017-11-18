<template>
    <div id="FileJobs" class="single-file">

        <div v-if="supJob !== null">
        <div class="file-job">

            <div v-if="supJob.errorMessage" class="status">
                <strong>Failed</strong>
            </div>
            <div v-else-if="supJob.isFinished" class="status">

                <i class='material-icons'>file_download</i>

                <strong>
                    <download-link :url="urlKey + '/download'" text="Download"></download-link>
                </strong>
            </div>
            <div v-else class="sup-job status">

                <spinner size="extra-small"></spinner>

                <strong>Processing...</strong>

                <span>{{ statusMessage }}</span>

            </div>

            <div class="original-name">
                <img src="/images/file-icon.png" alt="file" :title="supJob.originalName" />
                {{ shorten(supJob.originalName) }}

            </div>

            <div class="ocr-language original-name">
                <br/>
                <br/>
                <strong>OCR language: </strong> {{ ocrLanguage }}
            </div>

            <div v-if="supJob.errorMessage" class="error-message">
                Error: {{ supJob.errorMessage }}
            </div>

        </div>
        </div>

    </div>
</template>

<script>

    /*
     * This component is a copy of FileGroupJobs.vue
     */

    export default {

        data: () => ({
            supJob: null,
            statusMessage: '',
        }),

        props: [
            'urlKey',
            'ocrLanguage'
        ],

        mounted() {
            Echo.channel(`sup-job.${this.urlKey}`).listen('SupJobChanged', (changedSupJob) => {
                this.supJob = changedSupJob;

                this.maybeDisconnectFromChannels();
            });

            Echo.channel(`sup-job.${this.urlKey}.progress`).listen('SupJobProgressChanged', (newProgress) => {
                this.statusMessage = newProgress.statusMessage;
            });

            axios.get(`/api/v1/sup-job/${this.urlKey}`).then(response => {
                this.supJob = response.data.data;

                this.maybeDisconnectFromChannels();
            });
        },

        methods: {
            shorten: function(string) {
                let maxLength = 80;

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
