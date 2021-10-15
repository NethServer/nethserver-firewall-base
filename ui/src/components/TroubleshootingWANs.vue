<template>
  <div class="container-fluid container-cards-pf">
    <h3>{{ $t("troubleshooting.status") }}</h3>
    <div class="row row-cards-pf">
      <!-- MULTIWAN -->
      <div class="col-sm-4 col-md-3">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <h2 class="card-pf-title">
            <span class="pf-icon pficon-network"></span
            >{{ $t("troubleshooting.multiwan") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.multiwan.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.multiwan.isLoaded"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-row">
                <span
                  v-if="view.multiwan.status == 'disabled'"
                  class="fa fa-ban gray"
                ></span>
                <span
                  v-if="view.multiwan.status == 'running'"
                  class="pficon pficon-ok"
                ></span>
                <span
                  v-if="view.multiwan.status == 'warning'"
                  class="pficon pficon-warning-triangle-o"
                ></span>
                <span
                  v-if="view.multiwan.status == 'failed'"
                  class="pficon pficon-error-circle-o"
                ></span>
                <span
                  v-if="
                    isLoaded.wanProviders && view.multiwan.status !== 'disabled'
                  "
                >
                  {{
                    wanProviders.configuration.multiwan.WanMode == "balance"
                      ? $t("wan.balance")
                      : $t("wan.backup")
                  }}
                </span>
              </span>
            </p>
          </div>
        </div>
      </div>
      <!-- END MULTIWAN -->

      <!-- RED INTERFACES -->
      <div v-if="!isLoaded.wanProviders" class="col-sm-4 col-md-3">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <h2 class="card-pf-title mg-top-20"></h2>
          <div class="card-pf-body">
            <div class="spinner spinner-lg view-spinner"></div>
          </div>
        </div>
      </div>
      <div
        v-else
        v-for="(iface, index) in wanProviders.configuration.interfaces"
        :key="index"
        class="col-sm-4 col-md-3"
      >
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <h2 class="card-pf-title">
            <span class="pf-icon pficon-network"></span>
            <span>{{ iface.name }} ({{ iface.provider.name }})</span>
          </h2>
          <div class="card-pf-body mg-top-20">
            <div class="table-wrapper card-pf-aggregate-status-notifications">
              <div class="table">
                <!-- up/down -->
                <div class="tr">
                  <div class="td">
                    <span
                      v-if="wanProviders.status[iface.provider.name] == 1"
                      class="pficon pficon-ok row-icon"
                    ></span>
                    <span
                      v-else
                      class="pficon pficon-warning-triangle-o"
                    ></span>
                  </div>
                  <div class="td">
                    <span v-if="wanProviders.status[iface.provider.name] == 1">
                      {{ $t("troubleshooting.up") }}
                    </span>
                    <span v-else>{{ $t("troubleshooting.down") }}</span>
                  </div>
                </div>
                <!-- nslabel -->
                <div class="tr" v-if="iface.nslabel">
                  <div class="td">
                    <div
                      data-toggle="tooltip"
                      data-placement="bottom"
                      :title="$t('troubleshooting.label')"
                    >
                      <span class="pficon pficon-info row-icon"></span>
                    </div>
                  </div>
                  <div class="td">
                    {{ iface.nslabel }}
                  </div>
                </div>
                <!-- cidr -->
                <div class="tr">
                  <div class="td">
                    <div
                      data-toggle="tooltip"
                      data-placement="bottom"
                      :title="$t('troubleshooting.cidr')"
                    >
                      <span class="pficon pficon-screen row-icon"></span>
                    </div>
                  </div>
                  <div class="td">
                    {{ iface.cidr ? iface.cidr : "-" }}
                  </div>
                </div>
                <!-- gateway -->
                <div class="tr">
                  <div class="td">
                    <div
                      data-toggle="tooltip"
                      data-placement="bottom"
                      :title="$t('troubleshooting.gateway')"
                    >
                      <span class="pficon pficon-middleware row-icon"></span>
                    </div>
                  </div>
                  <div class="td">
                    {{ iface.gateway ? iface.gateway : "-" }}
                  </div>
                </div>
                <!-- weight -->
                <div class="tr">
                  <div class="td">
                    <div
                      data-toggle="tooltip"
                      data-placement="bottom"
                      :title="$t('troubleshooting.weight')"
                    >
                      <span class="pficon pficon-rebalance row-icon"></span>
                    </div>
                  </div>
                  <div class="td">
                    {{ iface.provider.weight ? iface.provider.weight : "-" }}
                  </div>
                </div>
                <!-- download bandwidth -->
                <div class="tr">
                  <div class="td">
                    <div
                      data-toggle="tooltip"
                      data-placement="bottom"
                      :title="$t('troubleshooting.download_bandwidth')"
                    >
                      <span class="fa fa-arrow-circle-o-down row-icon"></span>
                    </div>
                  </div>
                  <div class="td">{{ iface.FwInBandwidth }} kbps</div>
                </div>
                <!-- upload bandwidth -->
                <div class="tr">
                  <div class="td">
                    <div
                      data-toggle="tooltip"
                      data-placement="bottom"
                      :title="$t('troubleshooting.upload_bandwidth')"
                    >
                      <span class="fa fa-arrow-circle-o-up row-icon"></span>
                    </div>
                  </div>
                  <div class="td">{{ iface.FwOutBandwidth }} kbps</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- END RED INTERFACES -->
    </div>
    <h3>{{ $t("troubleshooting.traffic") }}</h3>
    <div class="row row-cards-pf">
      <!-- TRAFFIC CHARTS -->
      <div v-if="!isLoaded.wanProviders" class="col-sm-4 col-md-3">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <h2 class="card-pf-title mg-top-20"></h2>
          <div class="card-pf-body">
            <div class="spinner spinner-lg view-spinner"></div>
          </div>
        </div>
      </div>
      <div
        v-else
        v-for="(iface, index) in wanProviders.configuration.interfaces"
        :key="index"
        class="col-md-6"
      >
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <h2 class="card-pf-title">
            <span>{{ iface.name }} ({{ iface.provider.name }})</span>
          </h2>
          <div class="card-pf-body">
            <div v-if="!trafficCharts[iface.name]">
              <div class="spinner spinner-lg view-spinner"></div>
            </div>
            <div v-else>
              <div
                v-for="(data, index) in trafficCharts[iface.name]"
                :key="index"
              >
                <div
                  :id="'traffic-legend-' + iface.name"
                  class="troubleshooting-chart-legend"
                ></div>
                <div
                  :id="'traffic-chart-' + iface.name"
                  class="traffic-chart"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- END TRAFFIC CHARTS -->
    </div>
  </div>
</template>

<script>
import Dygraph from "dygraphs";

export default {
  name: "WANs",
  data() {
    return {
      view: {
        multiwan: { status: "disabled", isLoaded: false },
      },
      wanProviders: null,
      redInterfaces: [],
      trafficCharts: {},
      isLoaded: {
        wanProviders: false,
      },
    };
  },
  created() {
    this.getServiceStatus("multiwan");
    this.getWanProviders();
  },
  beforeDestroy() {
    clearInterval(this.trafficChartsInterval);
  },
  methods: {
    getServiceStatus(service) {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/troubleshooting/read"],
        { action: "service", service: service },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context["view"][service]["isLoaded"] = true;
          context["view"][service]["status"] = success.status;
          if ("details" in success) {
            context["view"][service]["details"] = success.details;
          }
        },
        function(error) {
          console.error(error);
          context["view"][service]["isLoaded"] = false;
        }
      );
    },
    getWanProviders() {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/troubleshooting/read"],
        { action: "wan-providers" },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.isLoaded.wanProviders = true;
          context.wanProviders = success;
          context.updateTrafficCharts();

          context.trafficChartsInterval = setInterval(function() {
            context.updateTrafficCharts();
          }, 5000);

          setTimeout(function() {
            $('[data-toggle="tooltip"]').tooltip();
          }, 500);
        },
        function(error) {
          console.error(error);
        }
      );
    },
    updateTrafficCharts() {
      var context = this;
      for (const iface of context.wanProviders.configuration.interfaces) {
        nethserver.exec(
          ["nethserver-firewall-base/troubleshooting/read"],
          { action: "interface", interface: iface.name },
          null,
          function(success) {
            try {
              success = JSON.parse(success);
            } catch (e) {
              console.error(e);
            }
            const chart = success;

            // needed for reactivity (see https://vuejs.org/v2/guide/reactivity.html#For-Objects)
            context.$set(context.trafficCharts, iface.name, chart);

            context.$nextTick(function() {
              for (var t in chart.data) {
                chart.data[t][0] = new Date(chart.data[t][0]);
              }

              const i18nLabels = chart.labels.map((label) =>
                context.$i18n.t("troubleshooting." + label)
              );

              var g = new Dygraph(
                document.getElementById("traffic-chart-" + iface.name),
                chart.data,
                {
                  fillGraph: true,
                  stackedGraph: true,
                  labels: i18nLabels,
                  height: 150,
                  strokeWidth: 1,
                  strokeBorderWidth: 1,
                  ylabel: context.$i18n.t("troubleshooting.traffic_mbps"),
                  axisLineColor: "white",
                  labelsDiv: document.getElementById(
                    "traffic-legend-" + iface.name
                  ),
                  labelsSeparateLines: true,
                  drawGrid: true,
                }
              );
              g.initialData = chart.data;
            });
          },
          function(error) {
            console.error(error);
          }
        );
      }
    },
  },
};
</script>

<style scoped>
.card-pf-aggregate-status .card-pf-title {
  padding-left: 20px;
  padding-right: 20px;
}

.card-pf-title > .pf-icon {
  margin-right: 7px;
}

.container-fluid.container-cards-pf {
  margin-left: auto !important;
}

.red {
  color: #cc0000;
}

.gray {
  color: #72767b !important;
}

.card-row {
  font-size: 14px;
  font-weight: normal;
}

.text-icon {
  font-size: 12px;
  margin-right: 7px;
}

.table-wrapper {
  display: flex;
  justify-content: center;
  margin-top: 0.5rem;
  font-size: 14px;
  font-weight: normal;
  text-align: start;
}

.table {
  display: table;
  width: auto;
}

.tr {
  display: table-row;
}

.td {
  display: table-cell;
}

.row-icon {
  margin-bottom: 10px;
  position: relative;
  top: 2px;
}

.traffic-chart {
  padding-left: 5px;
  padding-right: 5px;
  margin-bottom: 20px;
}
</style>
