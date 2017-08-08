require('./bootstrap');

window.Vue = require('vue');

Vue.component('sub-idx-languages', require('./components/SubIdxLanguages.vue'));

const app = new Vue({
    el: '#app'
});
