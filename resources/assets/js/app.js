require('./bootstrap');
window.Vue = require('vue');

Vue.component('ami-grid', require('./components/Grid.vue'));

const amiGrid = new Vue({
    el: '#ami-grid',
    component: [
        'ami-grid'
    ],
    data: {
        list: [],
    },
});
window.amiGrid = amiGrid;