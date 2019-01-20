require('./bootstrap');

window.Vue = require('vue');

Vue.config.productionTip = false;
Vue.config.devtools = false;

Vue.component('sub-idx-languages', require('./components/SubIdxLanguages.vue').default);
Vue.component('file-group-jobs', require('./components/FileGroupJobs.vue').default);
Vue.component('sup-job', require('./components/SupJob.vue').default);
Vue.component('file-group-archive', require('./components/FileGroupArchive.vue').default);
Vue.component('download-link', require('./components/helpers/DownloadLink.vue').default);

const app = new Vue({
    el: '#app'
});
