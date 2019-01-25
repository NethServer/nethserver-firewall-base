<template>
  <div id="app">
    <nav
      id="navbar-left"
      class="nav-pf-vertical nav-pf-vertical-with-sub-menus nav-pf-persistent-secondary panel-group"
    >
      <ul class="list-group panel">
        <li id="dashboard-item" :class="[getCurrentPath('') ? 'active' : '', 'list-group-item']">
          <a href="#/">
            <span class="fa fa-cube"></span>
            <span class="list-group-item-value">{{$t('dashboard.app')}}</span>
          </a>
        </li>

        <li class="li-empty"></li>

        <li id="wan-item" :class="[getCurrentPath('wan') ? 'active' : '', 'list-group-item']">
          <a href="#/wan">
            <span class="fa fa-globe"></span>
            <span class="list-group-item-value">{{$t('wan.title')}}</span>
          </a>
        </li>
        <li
          id="traffic-shaping-item"
          :class="[getCurrentPath('traffic-shaping') ? 'active' : '', 'list-group-item']"
        >
          <a href="#/traffic-shaping">
            <span class="fa fa-crosshairs"></span>
            <span class="list-group-item-value">{{$t('traffic_shaping.title')}}</span>
          </a>
        </li>
        <li id="snat-item" :class="[getCurrentPath('snat') ? 'active' : '', 'list-group-item']">
          <a href="#/snat">
            <span class="fa fa-random"></span>
            <span class="list-group-item-value">{{$t('snat.title')}}</span>
          </a>
        </li>
        <li
          id="port-forward-item"
          :class="[getCurrentPath('port-forward') ? 'active' : '', 'list-group-item']"
        >
          <a href="#/port-forward">
            <span class="fa fa-exchange"></span>
            <span class="list-group-item-value">{{$t('port_forward.title')}}</span>
          </a>
        </li>

        <li class="li-empty"></li>

        <li id="logs-item" :class="[getCurrentPath('logs') ? 'active' : '', 'list-group-item']">
          <a href="#/logs">
            <span class="fa fa-list"></span>
            <span class="list-group-item-value">{{$t('logs.title')}}</span>
          </a>
        </li>

        <li class="li-empty"></li>

        <li id="about-item" :class="[getCurrentPath('about') ? 'active' : '', 'list-group-item']">
          <a href="#/about">
            <span class="fa fa-info"></span>
            <span class="list-group-item-value">{{$t('about.title')}}</span>
          </a>
        </li>
      </ul>
    </nav>
    <div class="container-fluid container-cards-pf main-container">
      <router-view/>
    </div>
  </div>
</template>

<script>
export default {
  name: "App",
  mounted() {},
  data() {
    return {
      title: ""
    };
  },
  methods: {
    getCurrentPath(route, offset) {
      if (offset) {
        return this.$route.path.split("/")[offset] === route;
      } else {
        return this.$route.path.split("/")[1] === route;
      }
    }
  }
};
</script>

<style>
.span-right-margin {
  margin-right: 4px;
}

.no-pd-bottom {
  padding-bottom: 0px !important;
}

.no-mg-top {
  margin-top: 0px !important;
}

.no-mg-bottom {
  margin-bottom: 0px !important;
}

.no-mg-left {
  margin-left: 0px !important;
}

.mg-top-5 {
  margin-top: 5px !important;
}

.mg-left-5 {
  margin-left: 5px !important;
}
.mg-right-5 {
  margin-right: 5px !important;
}

.mg-top-10 {
  margin-top: 10px !important;
}

.mg-bottom-10 {
  margin-bottom: 10px !important;
}

.no-shadow {
  box-shadow: none !important;
}

.no-border-top {
  border-top: 0px !important;
}

.text-align-left {
  text-align: left !important;
}

.popover-content {
  max-height: 350px;
  overflow: auto;
}

.cursor-initial {
  cursor: initial;
}

.remove-item-inline {
  color: white;
  margin-left: 4px;
}

.compact {
  margin-bottom: 0px !important;
}

#providerDetails {
  padding: 10px;
  border-left: 1px solid #ddd;
  border-right: 1px solid #ddd;
  border-bottom: 1px solid #ddd;
}

.provider-details {
  color: #0088ce;
  text-decoration: none;
}

#change-provider-btn {
  float: right;
}

#provider-markup {
  border: none;
  box-shadow: none;
}

.provider-details:hover {
  text-decoration: underline;
  cursor: pointer;
}

.list-view-pf .list-group-item {
  border-top: 1px solid #ededec;
}

.list-group.list-view-pf {
  border-top: 0px;
}

.transparent:hover {
  background-color: transparent !important;
}

.search-pf {
  width: 50%;
}

.small-list {
  padding-top: 5px;
  padding-bottom: 5px;
}

.small-li {
  padding-top: 3px !important;
  padding-bottom: 3px !important;
}

.multi-line {
  display: unset;
  text-align: unset;
}

.adjust-line {
  line-height: 26px;
}
</style>