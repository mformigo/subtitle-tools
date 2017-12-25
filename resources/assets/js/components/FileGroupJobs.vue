<template>
    <div class="w-full max-w-sm">
        <div v-for="fileJob in fileJobs" class="mb-4">

        <div class="flex items-center">
            <div class="w-24 min-w-24 mr-2 text-sm">
                <strong v-if="fileJob.errorMessage">Failed</strong>
                <strong v-else-if="fileJob.isFinished"><download-link :url="urlKey+'/'+fileJob.id" text="Download"></download-link></strong>
                <strong v-else>{{ processingText }}</strong>
            </div>

            <div class="flex items-center truncate">
                <img class="w-6 mr-4" src="/images/file-icon.png" alt="file" :title="fileJob.originalName" />
                {{ shorten(fileJob.originalName) }}
            </div>
        </div>
        <div class="mt-2 mb-2" v-if="fileJob.errorMessage">
            {{ fileJob.errorMessage }}
        </div>

        </div>
    </div>
</template>

<script>
    export default {

        data: () => ({
            fileJobs: [],
            apiUpdateInterval: null,
            processingText: 'Processing'
        }),

        props: [
            'urlKey'
        ],

        mounted() {
            Echo.channel(`file-group.${this.urlKey}.jobs`).listen('FileJobChanged', (newFileJob) => {
                let arrayIndex = _.findIndex(this.fileJobs, ['id', newFileJob.id]);

                if (arrayIndex !== -1) {
                    Vue.set(this.fileJobs, arrayIndex, newFileJob);
                }

                this.maybeClearUpdateInterval();
            });

            let updateFromApi = () => {
                axios.get(`/api/v1/file-group/result/${this.urlKey}`).then(response => {
                    this.fileJobs = response.data;

                    this.maybeClearUpdateInterval();
                });
            };

            setInterval(() => {
                this.processingText += '.';

                if (this.processingText.endsWith('....')) {
                    this.processingText = 'Processing';
                }
            }, 500);

            // Sometimes we don't properly receive the pusher message when
            // all files are done. So we manually check with an interval
            this.apiUpdateInterval = setInterval(updateFromApi, 3000);

            updateFromApi();
        },

        methods: {
            shorten: function(string) {
                let maxLength = 36;

                if (string.length < maxLength ) {
                    return string;
                }

                return string.substring(0, maxLength/2) + '...' + string.substring(string.length - maxLength/2);
            },
            
            maybeClearUpdateInterval: function() {
                let allFinished = this.fileJobs.every((element, index, array) => element.isFinished);
                
                if (allFinished && this.apiUpdateInterval !== null) {
                    // We can't disconnect Pusher/Echo here because the
                    // archive Vue Component still needs it
                    clearInterval(this.apiUpdateInterval);
                }
            },
        }

    }
</script>
