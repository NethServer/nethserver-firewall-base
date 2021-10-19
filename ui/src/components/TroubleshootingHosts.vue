<template>
  <div class="container">
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
    <div v-else>
      <div class="row">
        <!-- top local hosts -->
        <div class="col-md-6">
          <h3 class="no-mg-top">{{ $t("troubleshooting.top_local_hosts") }}</h3>
          <div class="host-table">
            <div
              v-if="!isLoaded.topLocalHosts"
              class="spinner spinner-lg mg-top-20"
            ></div>
            <div v-else-if="!topLocalHosts.length">
              <div class="blank-slate-pf">
                <div class="blank-slate-pf-icon">
                  <span class="fa fa-table"></span>
                </div>
                <h4 class="chart-title gray">
                  {{ $t("troubleshooting.no_data") }}
                </h4>
              </div>
            </div>
            <div v-else>
              <vue-good-table
                v-show="isLoaded.topLocalHosts"
                :columns="topHostsColumns"
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
                  <span
                    v-if="props.column.field == 'name'"
                    class="hostname-column"
                  >
                    <span class="semi-bold" :title="props.row.name">
                      <a @click="showHostModal(props.row)">{{
                        props.row.name
                      }}</a></span
                    >
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

        <!-- top remote hosts -->
        <div class="col-md-6">
          <h3 class="no-mg-top">
            {{ $t("troubleshooting.top_remote_hosts") }}
          </h3>
          <div class="host-table">
            <div
              v-if="!isLoaded.topRemoteHosts"
              class="spinner spinner-lg mg-top-20"
            ></div>
            <div v-else-if="!topRemoteHosts.length">
              <div class="blank-slate-pf">
                <div class="blank-slate-pf-icon">
                  <span class="fa fa-table"></span>
                </div>
                <h4 class="chart-title gray">
                  {{ $t("troubleshooting.no_data") }}
                </h4>
              </div>
            </div>
            <div v-else>
              <vue-good-table
                v-show="isLoaded.topRemoteHosts"
                :columns="topHostsColumns"
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
                  <span
                    v-if="props.column.field == 'name'"
                    class="hostname-column"
                  >
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
      </div>
      <div class="row">
        <!-- search -->
        <div class="col-md-12">
          <h3>
            {{ $t("troubleshooting.search_ip") }}
          </h3>
        </div>
        <form role="form" class="form-horizontal" v-on:submit.prevent="">
          <div class="col-sm-3">
            <div class="form-group no-mg-left">
              <input
                required
                type="text"
                :placeholder="$t('troubleshooting.ip_address')"
                v-model.trim="ipAddress"
                class="form-control ip-address"
              />
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <button
                class="btn btn-primary"
                type="submit"
                @click="showHostModalForIpAddress(ipAddress)"
              >
                <span class="fa fa-search"></span>
              </button>
            </div>
          </div>
        </form>
      </div>
      <div class="row">
        <!-- known hosts -->
        <div class="col-md-12">
          <h3>
            {{ $t("troubleshooting.known_hosts") }}
            <span v-if="isLoaded.knownHosts"> ({{ knownHosts.length }})</span>
          </h3>
          <div class="host-table">
            <div
              v-if="!isLoaded.knownHosts"
              class="spinner spinner-lg mg-top-20"
            ></div>
            <div v-else-if="!knownHosts.length">
              <div class="blank-slate-pf">
                <div class="blank-slate-pf-icon">
                  <span class="fa fa-table"></span>
                </div>
                <h4 class="chart-title gray">
                  {{ $t("troubleshooting.no_data") }}
                </h4>
              </div>
            </div>
            <div v-else>
              <vue-good-table
                v-show="isLoaded.knownHosts"
                :columns="knownHostsColumns"
                :rows="knownHosts"
                :lineNumbers="false"
                :sort-options="{
                  enabled: true,
                  initialSortBy: { field: 'name', type: 'asc' },
                }"
                :search-options="{
                  enabled: true,
                  placeholder: tableLangsTexts.globalSearchPlaceholder,
                }"
                :pagination-options="{
                  enabled: true,
                  perPageDropdown: [25, 50, 100],
                  perPage: 25,
                  nextLabel: tableLangsTexts.nextText,
                  prevLabel: tableLangsTexts.prevText,
                  ofLabel: tableLangsTexts.ofText,
                  rowsPerPageLabel: tableLangsTexts.rowsPerPageText,
                }"
                styleClass="table responsive vgt2"
              >
                <template slot="table-row" slot-scope="props">
                  <span
                    v-if="props.column.field == 'name'"
                    class="hostname-column"
                  >
                    <span class="semi-bold" :title="props.row.name">
                      <a @click="showHostModal(props.row)">{{
                        props.row.name
                      }}</a></span
                    >
                  </span>
                  <span v-else-if="props.column.field == 'ip'">
                    <span>{{ props.row.ip }}</span>
                  </span>
                  <span v-else-if="props.column.field == 'mac'">
                    <span class="semi-bold">{{ props.row.mac }}</span>
                  </span>
                  <span v-else-if="props.column.field == 'interface'">
                    <span class="semi-bold">{{ props.row.interface }}</span>
                  </span>
                </template>
              </vue-good-table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- host modal -->
    <div
      class="modal"
      id="hostModal"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
    >
      <div class="modal-dialog host-traffic">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">
              {{ $t("troubleshooting.host_traffic") }}
            </h4>
          </div>
          <div class="modal-body pad-20">
            <div v-if="!isLoaded.hostTraffic" class="spinner spinner-lg"></div>
            <div v-else-if="!hostTraffic" class="blank-slate-pf">
              <div class="blank-slate-pf-icon">
                <span class="fa fa-line-chart"></span>
              </div>
              <h4 class="chart-title gray">
                {{ $t("troubleshooting.no_data") }}
              </h4>
            </div>
            <div v-show="isLoaded.hostTraffic && hostTraffic">
              <h4 v-if="currentHost" class="text-center">
                {{ $t("troubleshooting.traffic_for_host") }}:
                {{ currentHost.name }}
              </h4>
              <div
                id="host-traffic-legend"
                class="troubleshooting-chart-legend"
              ></div>
              <div id="host-traffic-chart"></div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              class="btn btn-default"
              type="button"
              @click="hideHostModal"
            >
              {{ $t("close") }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Dygraph from "dygraphs";

export default {
  name: "TroubleshootingHosts",
  data() {
    return {
      ntopngStatus: "",
      topLocalHosts: [],
      topRemoteHosts: [],
      chartsInterval: null,
      currentHost: null,
      hostTraffic: null,
      knownHosts: [],
      topHostsColumns: [
        {
          label: this.$i18n.t("troubleshooting.name"),
          field: "name",
          sortable: true,
        },
        {
          label: this.$i18n.t("troubleshooting.ip_address"),
          field: "ip",
          sortable: true,
        },
        {
          label: this.$i18n.t("troubleshooting.throughput"),
          field: "throughput",
          type: "number",
          sortable: true,
        },
      ],
      knownHostsColumns: [
        {
          label: this.$i18n.t("troubleshooting.name"),
          field: "name",
          sortable: true,
        },
        {
          label: this.$i18n.t("troubleshooting.ip_address"),
          field: "ip",
          sortable: true,
        },
        {
          label: this.$i18n.t("troubleshooting.mac_address"),
          field: "mac",
          sortable: true,
        },
        {
          label: this.$i18n.t("troubleshooting.interface"),
          field: "interface",
          sortable: true,
        },
      ],
      tableLangsTexts: this.tableLangs(),
      ipAddress: "",
      isLoaded: {
        ntopngStatus: false,
        topLocalHosts: false,
        topRemoteHosts: false,
        hostTraffic: false,
        knownHosts: false,
      },
    };
  },
  created() {
    this.getNtopngStatus();
  },
  beforeDestroy() {
    $(".modal").modal("hide");
    clearInterval(this.chartsInterval);
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

          if (context.ntopngStatus == "running") {
            context.getTopLocalHosts();
            context.getTopRemoteHosts();
            context.getKnownHosts();

            context.chartsInterval = setInterval(function() {
              context.getTopLocalHosts();
              context.getTopRemoteHosts();
              context.getKnownHosts();
            }, 30000);
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
    getHostTraffic() {
      this.hostTraffic = null;
      this.isLoaded.hostTraffic = false;
      const context = this;
      nethserver.exec(
        ["nethserver-firewall-base/troubleshooting/read"],
        {
          action: "host-chart",
          host: this.currentHost.ip,
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.hostTraffic = success;
          context.isLoaded.hostTraffic = true;

          context.$nextTick(function() {
            var chart = context.hostTraffic;

            // sort rrd results by time (first element of array)
            chart.data.sort(this.sortByProperty(0));

            for (var t in chart.data) {
              chart.data[t][0] = new Date(chart.data[t][0]);
            }

            const i18nLabels = chart.labels.map((label) =>
              context.$i18n.t("troubleshooting." + label)
            );

            var g = new Dygraph(
              document.getElementById("host-traffic-chart"),
              chart.data,
              {
                fillGraph: true,
                stackedGraph: false,
                labels: i18nLabels,
                height: 300,
                strokeWidth: 1,
                strokeBorderWidth: 1,
                ylabel: context.$i18n.t("troubleshooting.traffic_mbps"),
                labelsDiv: document.getElementById("host-traffic-legend"),
                axisLineColor: "white",
                labelsSeparateLines: true,
                drawGrid: true,
                axes: {
                  y: {
                    valueFormatter: function(y) {
                      return y.toFixed(2) + " mbit/s";
                    },
                  },
                },
              }
            );
            g.initialData = chart.data;
          });
        },
        function(error) {
          console.error(error);
          context.hostTraffic = null;
          context.isLoaded.hostTraffic = true;
        }
      );
    },
    showHostModal(host) {
      this.currentHost = host;
      this.getHostTraffic();
      $("#hostModal").modal("show");
    },
    showHostModalForIpAddress(ipAddress) {
      if (!ipAddress) {
        return;
      }
      this.currentHost = { ip: ipAddress, name: ipAddress };
      this.getHostTraffic();
      $("#hostModal").modal("show");
    },
    hideHostModal() {
      $("#hostModal").modal("hide");
    },
    sortByProperty(property) {
      return function(a, b) {
        if (a[property] < b[property]) {
          return -1;
        }
        if (a[property] > b[property]) {
          return 1;
        }
        return 0;
      };
    },
    getKnownHosts() {
      const context = this;
      nethserver.exec(
        ["nethserver-firewall-base/troubleshooting/read"],
        {
          action: "host-list",
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.knownHosts = success;
          context.isLoaded.knownHosts = true;
        },
        function(error) {
          console.error(error);
          context.isLoaded.knownHosts = true;
        }
      );
    },
  },
};
</script>

<style scoped>
.container {
  padding: 20px;
}

.host-table {
  margin-bottom: 20px;
}

.text-center {
  text-align: center;
}

.pad-20 {
  padding: 25px !important;
}

@media (min-width: 1056px) {
  .modal-dialog.host-traffic {
    width: 800px !important;
  }
}
</style>
