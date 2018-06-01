import store from 'belt/core/js/store/index';

window.larabelt.elastic = _.get(window, 'larabelt.elastic', {});

export default class BeltElastic {

    constructor() {

        if ($('#belt-elastic').length > 0) {

            const router = new VueRouter({
                mode: 'history',
                base: '/admin/belt/elastic',
                routes: []
            });

            const app = new Vue({router, store}).$mount('#belt-elastic');
        }
    }

}