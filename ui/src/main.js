import Vue from 'vue'

import VueI18n from "vue-i18n";

import App from './App.vue'
import router from './router'
import languages from "./i18n/lang";

Vue.config.productionTip = false
Vue.use(VueI18n);

import UtilService from "./services/util"
Vue.mixin(UtilService)

// configure i18n
var langConf = languages.initLang();
const i18n = new VueI18n({
  locale: langConf.locale,
  messages: langConf.messages
});
var moment = require("moment");
moment.locale(langConf.locale);

var ns = new Vue({
  router,
  i18n,
  currentLocale: langConf.locale,
  render: function (h) {
    return h(App)
  }
}).$mount('#app')
global.ns = ns;