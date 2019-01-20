<template>
    <div>
        <h2 class="mt-0 mb-4" v-if="hasSelectedLanguages">Selected languages</h2>

        <div class="flex items-center w-80 mb-6" v-for="lang in downloadableLanguages">
            <!-- File icon -->
            <svg class="w-5 mr-3" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1596 380q28 28 48 76t20 88v1152q0 40-28 68t-68 28h-1344q-40 0-68-28t-28-68v-1600q0-40 28-68t68-28h896q40 0 88 20t76 48zm-444-244v376h376q-10-29-22-41l-313-313q-12-12-41-22zm384 1528v-1024h-416q-40 0-68-28t-28-68v-416h-768v1536h1280zm-1024-864q0-14 9-23t23-9h704q14 0 23 9t9 23v64q0 14-9 23t-23 9h-704q-14 0-23-9t-9-23v-64zm736 224q14 0 23 9t9 23v64q0 14-9 23t-23 9h-704q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h704zm0 256q14 0 23 9t9 23v64q0 14-9 23t-23 9h-704q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h704z"></path></svg>

            <div class="flex flex-grow justify-between">
                <div class="text-lg font-semibold">{{ lang.language }}.srt</div>
                <download-link :url="lang.downloadUrl" text="Download"></download-link>
            </div>
        </div>

        <div class="flex items-center w-80 mb-6" v-for="lang in processingLanguages">
            <!-- Cog spinner -->
            <svg class="spin w-5 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M24 13.616v-3.232c-1.651-.587-2.694-.752-3.219-2.019v-.001c-.527-1.271.1-2.134.847-3.707l-2.285-2.285c-1.561.742-2.433 1.375-3.707.847h-.001c-1.269-.526-1.435-1.576-2.019-3.219h-3.232c-.582 1.635-.749 2.692-2.019 3.219h-.001c-1.271.528-2.132-.098-3.707-.847l-2.285 2.285c.745 1.568 1.375 2.434.847 3.707-.527 1.271-1.584 1.438-3.219 2.02v3.232c1.632.58 2.692.749 3.219 2.019.53 1.282-.114 2.166-.847 3.707l2.285 2.286c1.562-.743 2.434-1.375 3.707-.847h.001c1.27.526 1.436 1.579 2.019 3.219h3.232c.582-1.636.75-2.69 2.027-3.222h.001c1.262-.524 2.12.101 3.698.851l2.285-2.286c-.744-1.563-1.375-2.433-.848-3.706.527-1.271 1.588-1.44 3.221-2.021zm-12 2.384c-2.209 0-4-1.791-4-4s1.791-4 4-4 4 1.791 4 4-1.791 4-4 4z"></path></svg>

            <div>
                <div class="text-lg font-semibold mb-1">{{ lang.language }}.srt</div>
                Processing{{ loadingDots }}
            </div>
        </div>

        <div class="flex items-center w-80 mb-6" v-for="lang in queuedLanguages">
            <!-- Loading spinner -->
            <svg class="spin-steps-8 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M13.75 22c0 .966-.783 1.75-1.75 1.75s-1.75-.784-1.75-1.75.783-1.75 1.75-1.75 1.75.784 1.75 1.75zm-1.75-22c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm10 10.75c.689 0 1.249.561 1.249 1.25 0 .69-.56 1.25-1.249 1.25-.69 0-1.249-.559-1.249-1.25 0-.689.559-1.25 1.249-1.25zm-22 1.25c0 1.105.896 2 2 2s2-.895 2-2c0-1.104-.896-2-2-2s-2 .896-2 2zm19-8c.551 0 1 .449 1 1 0 .553-.449 1.002-1 1-.551 0-1-.447-1-.998 0-.553.449-1.002 1-1.002zm0 13.5c.828 0 1.5.672 1.5 1.5s-.672 1.501-1.502 1.5c-.826 0-1.498-.671-1.498-1.499 0-.829.672-1.501 1.5-1.501zm-14-14.5c1.104 0 2 .896 2 2s-.896 2-2.001 2c-1.103 0-1.999-.895-1.999-2s.896-2 2-2zm0 14c1.104 0 2 .896 2 2s-.896 2-2.001 2c-1.103 0-1.999-.895-1.999-2s.896-2 2-2z"></path></svg>

            <div>
                <div class="text-lg font-semibold mb-1">{{ lang.language }}.srt</div>
                <div>{{ lang.isBeingQueued ? 'Adding to queue' : 'Waiting to be processed' }}{{ loadingDots }}</div>
                <div v-if="!lang.isBeingQueued" class="text-xs mt-2">Position in queue: {{ lang.queuePosition }}</div>
            </div>
        </div>

        <div class="flex items-center w-80 mb-6" v-for="lang in failedLanguages">
            <!-- Cross -->
            <svg class="w-5 mr-3 text-red-light fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M23.954 21.03l-9.184-9.095 9.092-9.174-2.832-2.807-9.09 9.179-9.176-9.088-2.81 2.81 9.186 9.105-9.095 9.184 2.81 2.81 9.112-9.192 9.18 9.1z"></path></svg>

            <div>
                <div class="text-lg font-semibold mb-1">{{ lang.language }}.srt</div>
                <div class="text-red-light">Failed to convert this language to srt</div>
            </div>
        </div>


        <h2 v-if="hasAvailableLanguages" class="mt-8 mb-2">Available languages</h2>
        <p v-if="hasAvailableLanguages" class="mb-6">
            Choose the languages you want to download below.
        </p>

        <div class="flex items-center w-80 mb-4 cursor-pointer" @click="requestLanguage(lang)" v-for="lang in requestableLanguages">
            <!-- Download arrow -->
            <svg class="w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M16 11h5l-9 10-9-10h5v-11h8v11zm1 11h-10v2h10v-2z"></path></svg>

            <div class="font-semibold">{{ lang.language }}.srt</div>
        </div>


    </div>
</template>

<script>
    export default {

        props: {
            'urlKey': String,
        },

        data: () => ({
            languages: [],
            loadingDots: '...',
            updateInterval: null,
        }),

        mounted() {
            this.update();

            this.refreshUpdateInterval();

            setInterval(() => {
                this.loadingDots += '.';

                if (this.loadingDots === '....') {
                    this.loadingDots = '';
                }
            }, 500);
        },

        methods: {
            update() {
                axios.get(`/api/v1/sub-idx/${this.urlKey}/languages`).then(response => {
                    this.languages = response.data.data.map(i => ({...i, isBeingQueued: false}));
                });
            },

            refreshUpdateInterval() {
                clearInterval(this.updateInterval);

                this.updateInterval = setInterval(() => this.update(), 4000);
            },

            requestLanguage(language) {
                if (! language.canBeRequested) {
                    return;
                }

                this.refreshUpdateInterval();

                axios.post(`/api/v1/sub-idx/${this.urlKey}/languages/${language.id}`);

                language.canBeRequested = false;

                language.isBeingQueued = true;
            },
        },

        computed: {
            requestableLanguages() {
                return this.languages.filter(i => i.canBeRequested);
            },

            queuedLanguages() {
                return this.languages.filter(i => i.isQueued || i.isBeingQueued);
            },

            processingLanguages() {
                return this.languages.filter(i => i.isProcessing);
            },

            downloadableLanguages() {
                return this.languages.filter(i => i.downloadUrl);
            },

            failedLanguages() {
                return this.languages.filter(i => i.hasError);
            },

            hasAvailableLanguages() {
                return this.requestableLanguages.length > 0;
            },

            hasSelectedLanguages() {
                return this.requestableLanguages.length !== this.languages.length;
            },

        },
    }
</script>
