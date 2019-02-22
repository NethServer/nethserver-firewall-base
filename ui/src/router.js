import Vue from 'vue'
import Router from 'vue-router'

import Dashboard from './views/Dashboard.vue'
import WAN from './views/WAN.vue'
import TrafficShaping from './views/TrafficShaping.vue'
import sNAT from './views/sNAT.vue'
import Objects from './views/Objects.vue'
import PortForward from './views/PortForward.vue'

import Rules from './views/Rules.vue'
import LocalRules from './views/LocalRules.vue'

import Settings from './views/Settings.vue'
import Logs from './views/Logs.vue'
import About from './views/About.vue'

Vue.use(Router)

export default new Router({
  routes: [{
      path: '/',
      name: 'dashboard',
      component: Dashboard
    },
    {
      path: '/wan',
      name: 'wan',
      component: WAN
    },
    {
      path: '/traffic-shaping',
      name: 'traffic-shaping',
      component: TrafficShaping
    },
    {
      path: '/objects',
      name: 'objects',
      component: Objects
    },
    {
      path: '/port-forward',
      name: 'port-forward',
      component: PortForward
    },
    {
      path: '/rules',
      name: 'rules',
      component: Rules
    },
    {
      path: '/local-rules',
      name: 'local-rules',
      component: LocalRules
    },
    {
      path: '/snat',
      name: 'snat',
      component: sNAT
    },
    {
      path: '/settings',
      name: 'settings',
      component: Settings
    },
    {
      path: '/logs',
      name: 'logs',
      component: Logs
    },
    {
      path: '/about',
      name: 'about',
      component: About
    }
  ]
})