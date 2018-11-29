<template>
  <div id="app">
    <nav id="navbar-left" class="nav-pf-vertical nav-pf-vertical-with-sub-menus nav-pf-persistent-secondary panel-group">
      <ul class="list-group panel">

        <li id="dashboard-item" :class="[getCurrentPath('') ? 'active' : '', 'list-group-item']">
          <a href="#/">
            <span class="fa fa-cube"></span>
            <span class="list-group-item-value">{{$t('dashboard.title')}}</span>
          </a>
        </li>

        <li class="li-empty"></li>

        <li id="wan-item" :class="[getCurrentPath('wan') ? 'active' : '', 'list-group-item']">
          <a href="#/wan">
            <span class="fa fa-globe"></span>
            <span class="list-group-item-value">{{$t('wan.title')}}</span>
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
</style>