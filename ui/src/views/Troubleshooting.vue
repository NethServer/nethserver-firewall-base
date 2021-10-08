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
      <li @click="selectWans()">
        <a
          class="nav-link"
          data-toggle="tab"
          href="#ts-wans-tab"
          id="ts-wans-tab-parent"
          >{{ $t("troubleshooting.wans") }}</a
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

      <!-- WANS -->
      <div
        class="tab-pane fade active"
        id="ts-wans-tab"
        role="tabpanel"
        aria-labelledby="ts-wans-tab"
      >
        <TroubleshootingWANs v-if="view.isWansSelected" />
      </div>
      <!-- END WANS -->

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
import TroubleshootingWANs from "@/components/TroubleshootingWANs";
import TroubleshootingHosts from "@/components/TroubleshootingHosts";

export default {
  name: "Troubleshooting",
  components: {
    TroubleshootingServices,
    TroubleshootingWANs,
    TroubleshootingHosts,
  },
  data() {
    return {
      view: {
        isServicesSelected: true,
        isWansSelected: false,
        isHostsSelected: false,
      },
    };
  },
  methods: {
    selectServices() {
      this.view.isServicesSelected = true;
      this.view.isWansSelected = false;
      this.view.isHostsSelected = false;
    },
    selectWans() {
      this.view.isServicesSelected = false;
      this.view.isWansSelected = true;
      this.view.isHostsSelected = false;
    },
    selectHosts() {
      this.view.isServicesSelected = false;
      this.view.isWansSelected = false;
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
