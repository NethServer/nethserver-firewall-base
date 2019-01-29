import Vue from 'vue'
import VueI18n from "vue-i18n";
import VueToggleButton from 'vue-js-toggle-button';
import VueSuggestions from 'v-suggestions'

import App from './App.vue'
import router from './router'
import languages from "./i18n/lang";
import "./filters/filters";

window.c3 = require('c3');
window.d3 = require('d3');
window.moment = require("moment");

Vue.config.productionTip = false
Vue.use(VueI18n);
Vue.use(VueToggleButton)
Vue.component('suggestions', VueSuggestions)

import UtilService from "./services/util"
Vue.mixin(UtilService)

// configure i18n
var langConf = languages.initLang();
const i18n = new VueI18n({
  locale: langConf.locale,
  messages: langConf.messages
});
moment.locale(langConf.locale);

var ns = new Vue({
  router,
  i18n,
  currentLocale: langConf.locale,
  render: function (h) {
    return h(App)
  }
}).$mount('#app')