<template>
    <div id="SubIdxLanguages">

        <div class="language header">
            <div class="country"></div>
            <div class="name">Language</div>
            <div class="status">Status</div>
        </div>

        <div v-for="lang in languages" class="language">
            <div class="country">
                <span :class="'flag flag-' + lang.countryCode"></span>
            </div>
            <div class="name">{{ lang.language }}</div>

            <div v-if="lang.downloadUrl" class="status">
                <a :href="lang.downloadUrl">Download</a>
            </div>
            <div v-else class="status">
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
