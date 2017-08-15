require('./bootstrap');

window.Vue = require('vue');

Vue.component('sub-idx-languages', require('./components/SubIdxLanguages.vue'));
Vue.component('file-group-result', require('./components/FileGroupResult.vue'));

const app = new Vue({
    el: '#app'
});
