<template>
  <div>
    <h3>{{ $t("troubleshooting.hosts") }}</h3>
    <div v-if="!isLoaded.ntopngStatus" class="spinner spinner-lg"></div>
    <div v-else-if="ntopngStatus == 'disabled'">
      <div class="blank-slate-pf">
        <div class="blank-slate-pf-icon">
          <span class="fa fa-ban"></span>
        </div>
        <h4 class="chart-title gray">
          {{ $t("troubleshooting.bandwidth_monitor_disabled") }}
        </h4>
      </div>
    </div>
    <div v-else class="row">
      <!-- top local hosts -->
      <div class="col-md-6">
        <h3>{{ $t("troubleshooting.top_local_hosts") }}</h3>
        <div v-show="!isLoaded.topLocalHosts">
          <div class="blank-slate-pf">
            <div class="blank-slate-pf-icon">
              <span class="fa fa-table"></span>
            </div>
            <h4 class="chart-title gray">
              {{ $t("troubleshooting.no_data") }}
            </h4>
          </div>
        </div>
        <vue-good-table
          v-show="isLoaded.topLocalHosts"
          :columns="columns"
          :rows="topLocalHosts"
          :lineNumbers="false"
          :sort-options="{
            enabled: true,
            initialSortBy: { field: 'throughput', type: 'desc' },
          }"
          :search-options="{
            enabled: false,
          }"
          :pagination-options="{
            enabled: false,
          }"
          styleClass="table responsive vgt2"
        >
          <template slot="table-row" slot-scope="props">
            <span v-if="props.column.field == 'name'" class="hostname-column">
              <span class="semi-bold" :title="props.row.name">{{
                props.row.name
              }}</span>
            </span>
            <span v-else-if="props.column.field == 'ip'">
              <span>{{ props.row.ip }}</span>
            </span>
            <span v-else-if="props.column.field == 'throughput'">
              <span class="semi-bold">{{
                props.row.throughput | bpsFormat
              }}</span>
            </span>
          </template>
        </vue-good-table>
      </div>

      <!-- top remote hosts -->
      <div class="col-md-6">
        <h3>{{ $t("troubleshooting.top_remote_hosts") }}</h3>
        <div v-show="!isLoaded.topRemoteHosts">
          <div class="blank-slate-pf">
            <div class="blank-slate-pf-icon">
              <span class="fa fa-table"></span>
            </div>
            <h4 class="chart-title gray">
              {{ $t("troubleshooting.no_data") }}
            </h4>
          </div>
        </div>
        <vue-good-table
          v-show="isLoaded.topRemoteHosts"
          :columns="columns"
          :rows="topRemoteHosts"
          :lineNumbers="false"
          :sort-options="{
            enabled: true,
            initialSortBy: { field: 'throughput', type: 'desc' },
          }"
          :search-options="{
            enabled: false,
          }"
          :pagination-options="{
            enabled: false,
          }"
          styleClass="table responsive vgt2"
        >
          <template slot="table-row" slot-scope="props">
            <span v-if="props.column.field == 'name'" class="hostname-column">
              <span class="semi-bold" :title="props.row.name">{{
                props.row.name
              }}</span>
            </span>
            <span v-else-if="props.column.field == 'ip'">
              <span>{{ props.row.ip }}</span>
            </span>
            <span v-else-if="props.column.field == 'throughput'">
              <span class="semi-bold">{{
                props.row.throughput | bpsFormat
              }}</span>
            </span>
          </template>
        </vue-good-table>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "TroubleshootingHosts",
  data() {
    return {
      ntopngStatus: "",
      topLocalHosts: [],
      topRemoteHosts: [],
      columns: [
        {
          label: this.$i18n.t("troublehsooting.name"),
          field: "name",
          sortable: true,
        },
        {
          label: this.$i18n.t("troublehsooting.ip_address"),
          field: "ip",
          sortable: true,
        },
        {
          label: this.$i18n.t("troublehsooting.throughput"),
          field: "throughput",
          type: "number",
          sortable: true,
        },
      ],
      isLoaded: {
        ntopngStatus: false,
        topLocalHosts: false,
        topRemoteHosts: false,
      },
    };
  },
  created() {
    console.log("hosts created"); ////
    this.getNtopngStatus();
  },
  mounted() {
    console.log("hosts mounted"); ////
  },
  beforeDestroy() {
    console.log("hosts beforeDestroy"); ////
  },
  methods: {
    getNtopngStatus() {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/troubleshooting/read"],
        { action: "service", service: "ntopng" },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.ntopngStatus = success.status;
          context.isLoaded.ntopngStatus = true;

          if (context.ntopngStatus == "enabled") {
            this.getTopLocalHosts();
            this.getTopRemoteHosts();
          }
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getTopLocalHosts() {
      const context = this;
      nethserver.exec(
        ["nethserver-firewall-base/troubleshooting/read"],
        {
          action: "top-local-hosts",
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.topLocalHosts = success.topLocalHosts;
          context.isLoaded.topLocalHosts = true;
        },
        function(error) {
          console.error(error);
          context.isLoaded.topLocalHosts = true;
        }
      );
    },
    getTopRemoteHosts() {
      const context = this;
      nethserver.exec(
        ["nethserver-firewall-base/troubleshooting/read"],
        {
          action: "top-remote-hosts",
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.topRemoteHosts = success.topRemoteHosts;
          context.isLoaded.topRemoteHosts = true;
        },
        function(error) {
          console.error(error);
          context.isLoaded.topRemoteHosts = true;
        }
      );
    },
  },
};
</script>

<style scoped></style>
