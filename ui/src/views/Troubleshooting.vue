<template>
  <div>
    <h2>{{ $t("troubleshooting.title") }}</h2>

    <div
      v-if="loading.isValidSubscription"
      class="spinner spinner-lg mg-top-20"
    ></div>
    <div v-else-if="!isValidSubscription" class="blank-slate-pf white">
      <div class="blank-slate-pf-icon">
        <span class="pficon pficon-key"></span>
      </div>
      <h1>{{ $t("troubleshooting.subscription_needed") }}</h1>
      <p>{{ $t("troubleshooting.subscription_needed_description") }}</p>
      <div class="blank-slate-pf-main-action">
        <a
          target="_blank"
          href="/nethserver#/subscription"
          class="btn btn-primary btn-lg"
          >{{ $t("troubleshooting.go_to_subscription") }}</a
        >
      </div>
    </div>
    <div v-else>
      <ul class="nav nav-tabs nav-tabs-pf">
        <li @click="selectServices()">
          <a
            class="nav-link"
            data-toggle="tab"
            href="#ts-services-tab"
            id="ts-services-tab-parent"
            >{{ $t("troubleshooting.status") }}</a
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
          <TroubleshootingServices
            v-if="view.isServicesSelected"
            @changeTab="onChangeTab"
          />
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
      isValidSubscription: false,
      loading: {
        isValidSubscription: true,
      },
    };
  },
  created() {
    this.getSubscription();
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
    getSubscription() {
      const context = this;
      nethserver.exec(
        ["nethserver-firewall-base/troubleshooting/read"],
        {
          action: "valid-subscription",
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.isValidSubscription = success;
          context.loading.isValidSubscription = false;
        },
        function(error) {
          console.error(error);
        }
      );
    },
    onChangeTab(tab) {
      if (tab == "services") {
        this.selectServices();
        $("#ts-services-tab-parent").click();
      } else if (tab == "network") {
        this.selectNetwork();
        $("#ts-network-tab-parent").click();
      } else if (tab == "hosts") {
        this.selectHosts();
        $("#ts-hosts-tab-parent").click();
      }
    },
  },
};
</script>

<style scoped>
.gray-bg {
  background-color: #f5f5f5;
}
</style>
