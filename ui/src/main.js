import Vue from 'vue'
import VueI18n from "vue-i18n";
import VueGoodTable from "vue-good-table";
import VueToggleButton from 'vue-js-toggle-button';
import Sortable from 'sortablejs'

import "v-suggestions/dist/v-suggestions.css";
import VueSuggestions from 'v-suggestions'

import DocInfo from "./directives/DocInfo.vue";

import App from './App.vue'
import router from './router'
import "./filters/filters";

window.c3 = require('c3');
window.d3 = require('d3');
window.moment = require("moment");

Vue.config.productionTip = false
Vue.use(VueI18n);
Vue.use(VueGoodTable);
Vue.use(VueToggleButton);
Vue.component('suggestions', VueSuggestions)
Vue.component('doc-info', DocInfo)
Vue.directive('sortable', {
  inserted: function (el, binding) {
    new Sortable(el, binding.value || {})
  }
})
Vue.directive('focus', {
  inserted: function (el) {
    el.focus()
  }
})

import UtilService from "./services/util"
Vue.mixin(UtilService)

// configure i18n
const i18n = new VueI18n();
// FIXME: invoke lang API
moment.locale('en');

var ns = new Vue({
  router,
  i18n,
  currentLocale: 'en',
  render: function (h) {
    return h(App)
  }
})

nethserver.fetchTranslatedStrings(function (data) {
    i18n.setLocaleMessage('cockpit', data);
    i18n.locale = 'cockpit';
    ns.$mount('#app'); // Start VueJS application after language strings are loaded
})
