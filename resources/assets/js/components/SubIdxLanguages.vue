<template>
    <div>
        <div id="SubIdxLanguages">

            <div v-for="lang in languages" class="language">
                <div class="flag">X</div>
                <div class="name">{{ lang.language }}</div>

                <div v-if="lang.hasStarted == false" class="status">
                    Queued...
                </div>
                <div v-else-if="lang.hasFinished == false" class="status">
                    Processing...
                </div>
                <div v-else-if="lang.hasError == true" class="status">
                    Failed
                </div>
                <div v-else class="status">
                    <a :href="pageId + '/' + lang.index">Download</a>
                </div>

            </div>

        </div>
    </div>
</template>

<script>
    export default {

        data: () => ({
            languages: [],
        }),

        props: [
            'pageId'
        ],

        mounted() {
            axios.get(`/api/v1/sub-idx/languages/${this.pageId}`).then(response => {
                this.languages = response.data;
            });

            Echo.channel(`sub-idx.${this.pageId}`).listen('ExtractingSubIdxLanguageChanged', (newLanguage) => {
                let arrayIndex = _.findIndex(this.languages, ['index', newLanguage.index]);

                Vue.set(this.languages, arrayIndex, newLanguage);
            });
        }
    }
</script>
