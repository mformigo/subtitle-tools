<template>
    <div id="FileJobs" :class="this.fileJobs.length == 1 ? 'single-file' : 'multi-file'">

        <div v-for="fileJob in fileJobs" class="file-job">

            <div v-if="fileJob.errorMessage" class="status">
                <strong>Failed</strong>
            </div>
            <div v-else-if="fileJob.isFinished" class="status">


                <i v-show="fileJobs.length == 1" class='material-icons'>file_download</i>

                <strong>
                    <download-link :url="urlKey + '/' + fileJob.id" text="Download"></download-link>
                </strong>
            </div>
            <div v-else class="status">

                <spinner size="extra-small"></spinner>

                <strong>Processing...</strong>
            </div>

            <div class="original-name">
                <img src="/images/file-icon.png" alt="file" :title="fileJob.originalName" />
                {{ shorten(fileJob.originalName) }}
            </div>

            <div v-if="fileJob.errorMessage" class="error-message">
                Error: {{ fileJob.errorMessage }}
            </div>

        </div>

    </div>
</template>

<script>
    export default {

        data: () => ({
            fileJobs: [],
        }),

        props: [
            'urlKey'
        ],

        mounted() {
            axios.get(`/api/v1/file-group/result/${this.urlKey}`).then(response => {
                this.fileJobs = response.data;
            });

            Echo.channel(`file-group.${this.urlKey}.jobs`).listen('FileJobChanged', (newFileJob) => {
                let arrayIndex = _.findIndex(this.fileJobs, ['id', newFileJob.id]);

                if(arrayIndex !== -1) {
                    Vue.set(this.fileJobs, arrayIndex, newFileJob);
                }
            });
        },

        methods: {
            shorten: function(string) {
                let maxLength = 80;

                if(string.length < maxLength ) {
                    return string;
                }

                return string.substring(0, maxLength/2) + '...' + string.substring(string.length - maxLength/2);

            }
        }

    }
</script>
