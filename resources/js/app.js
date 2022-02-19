require('./bootstrap');

window.Vue = require('vue').default;

Vue.component('game', require('./components/Game.vue').default);

const app = new Vue({
    el: '#app',
});
