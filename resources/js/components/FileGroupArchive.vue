<template>
    <div class="flex items-center mb-8">
        <img class="mr-4" src="/images/archive-icon.png" alt="Archive">

        <div v-if="this.archiveStatus === false">
            Loading...
        </div>
        <div v-else-if="this.archiveRequestUrl">
            <a href="javascript:" @click="requestArchive(archiveRequestUrl)">{{ archiveStatus }}</a>
        </div>
        <div v-else-if="this.archiveDownloadUrl">
            <download-link :url="this.archiveDownloadUrl" :text="archiveStatus" :instant-download="userHasRequested"></download-link>
        </div>
        <div v-else>
            {{ archiveStatus }}
        </div>
    </div>
</template>

<script>
    export default {

        data: () => ({
            archiveStatus: false,
            archiveRequestUrl: false,
            archiveDownloadUrl: false,
            apiUpdateInterval: null,

            userHasRequested: false,
        }),

        props: {
            'urlKey': String,
        },

        mounted() {
            let updateFromApi = () => {
                axios.get(`/api/v1/file-group/archive/${this.urlKey}`).then(response => {
                    this.archiveRequestUrl = response.data.archiveRequestUrl;
                    this.archiveDownloadUrl = response.data.archiveDownloadUrl;
                    this.archiveStatus = response.data.archiveStatus;
                });
            };

            this.apiUpdateInterval = setInterval(updateFromApi, 2000);

            updateFromApi();
        },

        methods: {
            requestArchive(requestUrl) {
                this.userHasRequested = true;

                this.archiveStatus = false;

                return axios.post(requestUrl);
            },

        },

    }
</script>
