<template>
  <div>
    <h2>{{ $t("troubleshooting.title") }}</h2>

    <ul class="nav nav-tabs nav-tabs-pf">
      <li @click="selectServices()">
        <a
          class="nav-link"
          data-toggle="tab"
          href="#ts-services-tab"
          id="ts-services-tab-parent"
          >{{ $t("troubleshooting.services") }}</a
        >
      </li>
      <li @click="selectNetwork()">
        <a
          class="nav-link"
          data-toggle="tab"
          href="#ts-network-tab"
          id="ts-network-tab-parent"
          >{{ $t("troubleshooting.network") }}</a
        >
      </li>
      <li @click="selectHosts()">
        <a
          class="nav-link"
          data-toggle="tab"
          href="#ts-hosts-tab"
          id="ts-hosts-tab-parent"
          >{{ $t("troubleshooting.hosts") }}</a
        >
      </li>
    </ul>

    <div class="tab-content gray-bg" id="troubleshootingTabContent">
      <!-- SERVICES -->
      <div
        class="tab-pane fade active"
        id="ts-services-tab"
        role="tabpanel"
        aria-labelledby="ts-services-tab"
      >
        <TroubleshootingServices v-if="view.isServicesSelected" />
      </div>
      <!-- END SERVICES -->

      <!-- NETWORK -->
      <div
        class="tab-pane fade active"
        id="ts-network-tab"
        role="tabpanel"
        aria-labelledby="ts-network-tab"
      >
        <TroubleshootingNetwork v-if="view.isNetworkSelected" />
      </div>
      <!-- END NETWORK -->

      <!-- HOSTS -->
      <div
        class="tab-pane fade active"
        id="ts-hosts-tab"
        role="tabpanel"
        aria-labelledby="ts-hosts-tab"
      >
        <TroubleshootingHosts v-if="view.isHostsSelected" />
      </div>
      <!-- END HOSTS -->
    </div>
  </div>
</template>

<script>
import TroubleshootingServices from "@/components/TroubleshootingServices";
import TroubleshootingNetwork from "@/components/TroubleshootingNetwork";
import TroubleshootingHosts from "@/components/TroubleshootingHosts";

export default {
  name: "Troubleshooting",
  components: {
    TroubleshootingServices,
    TroubleshootingNetwork,
    TroubleshootingHosts,
  },
  data() {
    return {
      view: {
        isServicesSelected: true,
        isNetworkSelected: false,
        isHostsSelected: false,
      },
    };
  },
  methods: {
    selectServices() {
      this.view.isServicesSelected = true;
      this.view.isNetworkSelected = false;
      this.view.isHostsSelected = false;
    },
    selectNetwork() {
      this.view.isServicesSelected = false;
      this.view.isNetworkSelected = true;
      this.view.isHostsSelected = false;
    },
    selectHosts() {
      this.view.isServicesSelected = false;
      this.view.isNetworkSelected = false;
      this.view.isHostsSelected = true;
    },
  },
};
</script>

<style scoped>
.gray-bg {
  background-color: #f5f5f5;
}
</style>
