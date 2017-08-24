require('./bootstrap');

window.Vue = require('vue');

Vue.component('sub-idx-languages', require('./components/SubIdxLanguages.vue'));
Vue.component('file-group-jobs', require('./components/FileGroupJobs.vue'));
Vue.component('file-group-archive', require('./components/FileGroupArchive.vue'));
Vue.component('spinner', require('./components/helpers/Spinner.vue'));
Vue.component('download-link', require('./components/helpers/DownloadLink.vue'));

const app = new Vue({
    el: '#app'
});
