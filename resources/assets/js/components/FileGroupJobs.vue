<template>
    <div id="FileJobs" :class="this.fileJobs.length == 1 ? 'single-file' : 'multi-file'">

        <div v-for="fileJob in fileJobs" class="file-job">

            <div v-if="fileJob.errorMessage" class="status">
                {{ fileJob.errorMessage }}
            </div>
            <div v-else-if="fileJob.isFinished" class="status">
                <a :href="urlKey + '/' + fileJob.id" target="_blank">Download</a>
            </div>
            <div v-else class="status">
                Processing...
            </div>

            <div class="original-name">{{ fileJob.originalName }}</div>

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
                console.log(response);
                this.fileJobs = response.data;
            });

            Echo.channel(`file-group.${this.urlKey}.jobs`).listen('FileJobChanged', (newFileJob) => {
                let arrayIndex = _.findIndex(this.fileJobs, ['id', newFileJob.id]);

                if(arrayIndex !== -1) {
                    Vue.set(this.fileJobs, arrayIndex, newFileJob);
                }
            });
        }

    }
</script>
