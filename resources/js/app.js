/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

 // devtools disable config
Vue.config.devtools = false;
Vue.config.productionTip = false;

import Vue from 'vue';
// import VueRouter from 'vue-router';
import router from './router';
import store from './store';
import VueToastr from "vue-toastr";
import moment from "moment";

// add vue-data-tables plugin ******************************************

/*import ElementUI from 'element-ui';
import locale from 'element-ui/lib/locale/lang/en';
Vue.use(ElementUI, { locale });*/

import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
Vue.use(ElementUI);

// set language to EN
import lang from 'element-ui/lib/locale/lang/en';
import locale from 'element-ui/lib/locale';

locale.use(lang);

// import DataTables and DataTablesServer separately
import { DataTables, DataTablesServer } from 'vue-data-tables';
Vue.use(DataTables);
Vue.use(DataTablesServer);
// **********************************************************************

Vue.use(VueToastr, {
    /* OverWrite Plugin Options if you need */
    /*defaultProgressBar: false,
    defaultProgressBarValue: 0,
    defaultType: "error",*/
    // defaultCloseOnHover: false,
    // defaultStyle: { "background-color": "red" },
    defaultTimeout: 3000,
    defaultPosition: "toast-top-full-width",
    defaultClassNames: ["animated", "zoomInUp"]
});

Vue.filter('uppercaseFirst', function (value) {
    if (!value) return '';
    value = value.toString();

    return value.charAt(0).toUpperCase() + value.slice(1);
});

Vue.filter('formatDate', function(value) {
    if (!value) return '';
    value = value.toString();

    return moment(value).format('DD MMM YYYY');

    /*if (value) {
        return moment(String(value)).format('MM/DD/YYYY hh:mm')
    }*/
});

// Vue.use(VueRouter);

import App from './components/App';

Vue.prototype.$gblData = "something";

const app = new Vue({
    el: '#app',
    store,
    beforeCreate() {
        this.$store.commit('initialiseStore');
    },
    components: { App },
    router
});