<template>
    <div>

        <div class="flex mb-2 font-bold">
            <div class="w-48">Language</div>
            <div>Status</div>
        </div>

        <div class="flex mb-2" v-for="lang in languages">
            <div class="w-48">{{ lang.language }}</div>

            <div class="w-24">
                <span v-if="lang.downloadUrl"><download-link :url="lang.downloadUrl" text="Download"></download-link></span>
                <span v-else-if="lang.hasError">{{ lang.status }}</span>
                <span v-else>{{ lang.status }}{{ loadingDots }}</span>
            </div>
        </div>

    </div>
</template>

<script>
    export default {

        data: () => ({
            languages: [],
            loadingDots: '...',
        }),

        props: [
            'pageId'
        ],

        mounted() {
            Echo.channel(`sub-idx.${this.pageId}`).listen('ExtractingSubIdxLanguageChanged', (newLanguage) => {
                let arrayIndex = _.findIndex(this.languages, ['index', newLanguage.index]);

                if(arrayIndex !== -1) {
                    Vue.set(this.languages, arrayIndex, newLanguage);
                }

                this.maybeDisconnectFromChannels();
            });

            axios.get(`/api/v1/sub-idx/languages/${this.pageId}`).then(response => {
                this.languages = response.data;

                this.maybeDisconnectFromChannels();
            });

            setInterval(() => {
                this.loadingDots += '.';

                if (this.loadingDots === '....') {
                    this.loadingDots = '';
                }
            }, 500);
        },

        methods: {
            maybeDisconnectFromChannels: function() {
                let allFinished = this.languages.every((element, index, array) => element.isFinished);

                if(allFinished) {
                    Echo.disconnect();
                }
            },
        }
    }
</script>
