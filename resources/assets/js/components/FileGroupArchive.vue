<template>
    <div id="GroupArchive">

        <img src="/images/archive-icon.png" alt="Archive">

        <span class="status">
            <div v-if="this.archiveStatus === false">
                Loading...
            </div>
            <div v-else-if="this.archiveRequestUrl">
                <a href="javascript:" @click="requestArchive(archiveRequestUrl);archiveStatus = false;">{{ this.archiveStatus }}</a>
            </div>
            <div v-else-if="this.archiveDownloadUrl">

                <download-link :url="this.archiveDownloadUrl" :text="this.archiveStatus"></download-link>

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
            archiveStatus: false,
            archiveRequestUrl: false,
            archiveDownloadUrl: false,
        }),

        props: [
            'urlKey'
        ],

        mounted() {
            axios.get(`/api/v1/file-group/archive/${this.urlKey}`).then(response => {
                this.archiveRequestUrl = response.data.archiveRequestUrl;
                this.archiveDownloadUrl = response.data.archiveDownloadUrl;
                this.archiveStatus = response.data.archiveStatus;
            });

            Echo.channel(`file-group.${this.urlKey}`).listen('FileGroupChanged', (newFileGroup) => {
                this.archiveRequestUrl = newFileGroup.archiveRequestUrl;
                this.archiveDownloadUrl = newFileGroup.archiveDownloadUrl;
                this.archiveStatus = newFileGroup.archiveStatus;
            });
        },

        methods: {
            requestArchive: (requestUrl) => axios.post(requestUrl)
        },

    }
</script>
