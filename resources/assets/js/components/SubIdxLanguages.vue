<template>
    <div id="SubIdxLanguages">

        <div class="language header">
            <div class="country"></div>
            <div class="name">Language</div>
            <div class="status"><div></div>Status</div>
        </div>

        <div v-for="lang in languages" class="language">
            <div class="country">
                <span :class="'flag flag-' + lang.countryCode"></span>
            </div>
            <div class="name">{{ lang.language }}</div>

            <div v-if="lang.downloadUrl" class="status">
                <div></div>
                <download-link :url="lang.downloadUrl" text="Download"></download-link>
            </div>
            <div v-else class="status">
                <div>
                    <spinner v-show="!lang.hasError" size="extra-small"></spinner>
                </div>
                {{ lang.status }}
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

                if(arrayIndex !== -1) {
                    Vue.set(this.languages, arrayIndex, newLanguage);
                }
            });
        }
    }
</script>
