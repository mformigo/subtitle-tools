<template>
    <div id="GroupArchive">

        <img src="/images/archive-icon.png" alt="Archive">



        <span class="status">
            <div v-if="this.requestArchiveUrl">
                <a href="javascript:" @click="requestArchive(requestArchiveUrl)">{{ this.archiveStatus }}</a>
            </div>
            <div v-else-if="this.downloadArchiveUrl">
                <form :action="this.downloadArchiveUrl"
                      method="post"
                      enctype="multipart/form-data"
                      target="_blank">
                    <input type="hidden" name="_token" :value="this.csrfToken">

                    <button type="submit">{{ this.archiveStatus }}</button>
                </form>
            </div>
            <div v-else>
                {{ this.archiveStatus }}
            </div>
        </span>

    </div>
</template>

<script>
    export default {

        data: () => ({
            archiveStatus: '',
            requestArchiveUrl: false,
            downloadArchiveUrl: false,
        }),

        props: [
            'urlKey'
        ],

        mounted() {
            axios.get(`/api/v1/file-group/archive/${this.urlKey}`).then(response => {
                this.archiveStatus = response.data.archiveStatus;
                this.requestArchiveUrl = response.data.requestArchiveUrl;
                this.downloadArchiveUrl = response.data.downloadArchiveUrl;
            });

//            Echo.channel(`file-group.${this.urlKey}.jobs`).listen('FileJobChanged', (newFileJob) => {
//                let arrayIndex = _.findIndex(this.fileJobs, ['id', newFileJob.id]);
//
//                if(arrayIndex !== -1) {
//                    Vue.set(this.fileJobs, arrayIndex, newFileJob);
//                }
//            });
        },

        methods: {

            requestArchive: (requestUrl) => {
              console.log(requestUrl);
                axios.post(requestUrl);
            },


        },

        computed: {
            csrfToken: () => { return window.Laravel.csrf_token; }
        }



    }
</script>
