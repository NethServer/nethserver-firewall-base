<template>
  <div class="container-fluid container-cards-pf">
    <h3>{{ $t("troubleshooting.wans") }}</h3>
    <div class="row row-cards-pf">
      <!-- MULTIWAN -->
      <div class="col-sm-4 col-md-3">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <a href="#/wan" class="card-pf-link-with-icon card-action">
            <span class="fa fa-external-link"></span>
          </a>
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
            <span v-if="iface.nslabel"
              >{{ iface.nslabel }} ({{ iface.name }} -
              {{ iface.provider.name }})</span
            >
            <span v-else>{{ iface.name }} ({{ iface.provider.name }})</span>
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
                    <span v-else class="pficon pficon-error-circle-o"></span>
                  </div>
                  <div class="td">
                    <span v-if="wanProviders.status[iface.provider.name] == 1">
                      {{ $t("troubleshooting.up") }}
                    </span>
                    <span v-else>{{ $t("troubleshooting.down") }}</span>
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
    <!-- INTERFACE CHARTS -->
    <div v-show="!isLoaded.wanProviders" class="col-md-4">
      <div class="card-pf card-pf-accented card-pf-aggregate-status">
        <h2 class="card-pf-title mg-top-20"></h2>
        <div class="card-pf-body">
          <div class="spinner spinner-lg view-spinner"></div>
        </div>
      </div>
    </div>
    <div
      v-show="isLoaded.wanProviders"
      v-for="(iface, index) in wanProviders.configuration.interfaces"
      :key="index"
    >
      <h3>
        <span v-if="iface.nslabel"
          >{{ iface.nslabel }} ({{ iface.name }} -
          {{ iface.provider.name }})</span
        >
        <span v-else>{{ iface.name }} ({{ iface.provider.name }})</span>
      </h3>
      <div class="row row-cards-pf">
        <div class="col-md-12">
          <div class="card-pf card-pf-accented card-pf-aggregate-status">
            <h2 class="card-pf-title">
              <span>{{ $t("troubleshooting.traffic") }}</span>
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
                  <div :id="'traffic-chart-' + iface.name" class="chart"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- PING CHARTS -->
        <template v-if="isLoaded.ifacePingCharts">
          <div v-for="(ips, redName, index) in ifacePingCharts" :key="index">
            <div v-if="redName == iface.provider.name">
              <div
                v-for="(chart, ip, index) in ips"
                :key="index"
                class="col-md-6"
              >
                <div class="card-pf card-pf-accented card-pf-aggregate-status">
                  <h2 class="card-pf-title">
                    <span>{{ $t("troubleshooting.ping") }} {{ ip }}</span>
                  </h2>
                  <div class="card-pf-body">
                    <div
                      :id="`ping-legend-${redName}-${ip}`"
                      class="troubleshooting-chart-legend"
                    ></div>
                    <div
                      :id="`ping-chart-${redName}-${ip}`"
                      class="chart"
                    ></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>
      </div>
      <!-- END PING CHARTS -->
    </div>
    <!-- END INTERFACE CHARTS -->

    <h3>{{ $t("troubleshooting.status") }}</h3>
    <div class="row row-cards-pf">
      <!-- PING CHART -->
      <div class="col-md-6">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <div
            v-if="!view.isPingChartLoaded"
            class="spinner spinner-lg view-spinner  mg-top-20"
          ></div>
          <div class="card-pf-body">
            <div
              v-if="view.invalidChartsPingData"
              class="alert alert-warning alert-dismissable col-sm-12"
            >
              <span class="pficon pficon-warning-triangle-o"></span>
              <strong>{{ $t("warning") }}!</strong>
              {{ $t("troubleshooting.ping_charts_not_updated") }}.
            </div>
            <div v-if="view.isPingChartLoaded">
              <div v-for="(data, index) in charts.ping" :key="index">
                <h4 class="mg-top">
                  {{ $t("troubleshooting.ping") }}: {{ index }}
                </h4>
                <div
                  :id="'ping-legend-' + index"
                  class="troubleshooting-chart-legend"
                ></div>
                <div :id="'chart-ping-' + index" class="chart"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- END PING CHART -->
      <!-- PING DROPRATE -->
      <div class="col-md-6">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <div
            v-if="!view.isDroprateChartLoaded"
            class="spinner spinner-lg view-spinner  mg-top-20"
          ></div>
          <div class="card-pf-body">
            <div
              v-if="view.invalidChartsPingDroprateData"
              class="alert alert-warning alert-dismissable col-sm-12"
            >
              <span class="pficon pficon-warning-triangle-o"></span>
              <strong>{{ $t("warning") }}!</strong>
              {{ $t("troubleshooting.ping_droprate_charts_not_updated") }}.
            </div>
            <div v-if="view.isDroprateChartLoaded">
              <div v-for="(data, index) in charts.droprate" :key="index">
                <h4 class="mg-top">
                  {{ $t("troubleshooting.ping_droprate") }}: {{ index }}
                </h4>
                <div
                  :id="'ping-droprate-legend-' + index"
                  class="troubleshooting-chart-legend"
                ></div>
                <div :id="'chart-ping-droprate-' + index" class="chart"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- END PING DROPRATE -->
    </div>
  </div>
</template>

<script>
import Dygraph from "dygraphs";

export default {
  name: "TroubleshootingNetwork",
  data() {
    return {
      TRAFFIC_BY_INTERFACE_SAMPLES: 240,
      view: {
        multiwan: { status: "disabled", isLoaded: false },
        isPingChartLoaded: false,
        isDroprateChartLoaded: false,
      },
      wanProviders: { configuration: {}, status: {} },
      redInterfaces: [],
      trafficCharts: {},
      trafficDygraphs: {},
      ifacePingCharts: {},
      ifacePingDygraphs: {},
      trafficChartsInterval: null,
      ifacePingChartsInterval: null,
      pingChartInterval: null,
      pingDroprateChartInterval: null,
      charts: {
        ping: {},
        pingDygraphs: {},
        droprate: {},
        droprateDygraphs: {},
      },
      isLoaded: {
        wanProviders: false,
        ifacePingCharts: false,
      },
    };
  },
  mounted() {
    this.getServiceStatus("multiwan");
    this.getWanProviders();
    let context = this;

    this.updatePingChart();
    this.pingChartInterval = setInterval(function() {
      context.updatePingChart();
    }, 60000);

    this.updatePingDroprateChart();
    this.pingDroprateChartInterval = setInterval(function() {
      context.updatePingDroprateChart();
    }, 60000);

    this.updateIfacePingCharts();
    this.ifacePingChartsInterval = setInterval(function() {
      context.updateIfacePingCharts();
    }, 60000);
  },
  beforeDestroy() {
    clearInterval(this.trafficChartsInterval);
    clearInterval(this.ifacePingChartsInterval);
    clearInterval(this.pingChartInterval);
    clearInterval(this.pingDroprateChartInterval);
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
          }, 30000);

          setTimeout(function() {
            $('[data-toggle="tooltip"]').tooltip();
          }, 500);
        },
        function(error) {
          console.error(error);
        }
      );
    },
    updateIfacePingCharts() {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/troubleshooting/read"],
        { action: "wan-ping" },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }

          for (const [redName, ips] of Object.entries(success)) {
            // needed for reactivity (see https://vuejs.org/v2/guide/reactivity.html#For-Objects)
            context.$set(context.ifacePingCharts, redName, ips);

            for (const [ip, chart] of Object.entries(ips)) {
              for (var t in chart.data) {
                chart.data[t][0] = new Date(chart.data[t][0]);
              }
              const i18nLabels = chart.labels.map((label) =>
                context.$i18n
                  ? context.$i18n.t("troubleshooting." + label)
                  : label
              );
              context.isLoaded.ifacePingCharts = true;

              setTimeout(() => {
                let graph = context.ifacePingDygraphs[`${redName}-${ip}`];

                if (graph) {
                  // destroy previous graph to avoid memory leakage
                  graph.destroy();
                }

                graph = new Dygraph(
                  document.getElementById(`ping-chart-${redName}-${ip}`),
                  chart.data,
                  {
                    fillGraph: true,
                    stackedGraph: false,
                    labels: i18nLabels,
                    height: 200,
                    strokeWidth: 1,
                    strokeBorderWidth: 1,
                    ylabel: context.$i18n
                      ? context.$i18n.t("troubleshooting.latency_ms")
                      : "",
                    axisLineColor: "white",
                    labelsDiv: document.getElementById(
                      `ping-legend-${redName}-${ip}`
                    ),
                    labelsSeparateLines: true,
                    drawGrid: true,
                    axes: {
                      y: {
                        valueFormatter: function(y) {
                          return y.toFixed() + " ms";
                        },
                      },
                    },
                  }
                );
                graph.initialData = chart.data;
                context.$set(
                  context.ifacePingDygraphs,
                  `${redName}-${ip}`,
                  graph
                );
              }, 1000);
            }
          }
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
                context.$i18n
                  ? context.$i18n.t("troubleshooting." + label)
                  : label
              );

              let graph = context.trafficDygraphs[iface.name];

              if (graph) {
                // destroy previous graph to avoid memory leakage
                graph.destroy();
              }

              graph = new Dygraph(
                document.getElementById("traffic-chart-" + iface.name),
                chart.data,
                {
                  fillGraph: true,
                  stackedGraph: false,
                  labels: i18nLabels,
                  height: 200,
                  strokeWidth: 1,
                  strokeBorderWidth: 1,
                  ylabel: context.$i18n
                    ? context.$i18n.t("troubleshooting.traffic_mbps")
                    : "",
                  axisLineColor: "white",
                  labelsDiv: document.getElementById(
                    "traffic-legend-" + iface.name
                  ),
                  labelsSeparateLines: true,
                  drawGrid: true,
                  colors: ["blue", "green"],
                  axes: {
                    y: {
                      valueFormatter: function(y) {
                        return y.toFixed(2) + " mbit/s";
                      },
                    },
                  },
                }
              );
              graph.initialData = chart.data;
              context.$set(context.trafficDygraphs, iface.name, graph);
            });
          },
          function(error) {
            console.error(error);
          }
        );
      }
    },
    updatePingChart() {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/troubleshooting/read"],
        { action: "ping" },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.charts.ping = success;
          context.view.isPingChartLoaded = true;
          context.$nextTick(function() {
            for (const ip in context.charts.ping) {
              var chart = context.charts.ping[ip];

              for (var t in chart.data) {
                chart.data[t][0] = new Date(chart.data[t][0]);
              }

              const i18nLabels = chart.labels.map((label) =>
                context.$i18n
                  ? context.$i18n.t("troubleshooting." + label)
                  : label
              );

              let graph = context.charts.pingDygraphs[ip];

              if (graph) {
                // destroy previous graph to avoid memory leakage
                graph.destroy();
              }

              graph = new Dygraph(
                document.getElementById("chart-ping-" + ip),
                chart.data,
                {
                  fillGraph: true,
                  stackedGraph: false,
                  labels: i18nLabels,
                  height: 200,
                  strokeWidth: 1,
                  strokeBorderWidth: 1,
                  ylabel: context.$i18n
                    ? context.$i18n.t("troubleshooting.latency_ms")
                    : "",
                  axisLineColor: "white",
                  labelsDiv: document.getElementById("ping-legend-" + ip),
                  labelsSeparateLines: true,
                  drawGrid: true,
                  axes: {
                    y: {
                      valueFormatter: function(y) {
                        return y.toFixed() + " ms";
                      },
                    },
                  },
                }
              );
              graph.initialData = chart.data;
              context.$set(context.charts.pingDygraphs, ip, graph);
            }
          });
        },
        function(error) {
          console.error(error);
          context.view.isPingChartLoaded = true;
        }
      );
    },
    updatePingDroprateChart() {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/troubleshooting/read"],
        { action: "ping-droprate" },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.charts.droprate = success;
          context.view.isDroprateChartLoaded = true;
          context.$nextTick(function() {
            for (const ip in context.charts.droprate) {
              var chart = context.charts.droprate[ip];

              for (var t in chart.data) {
                chart.data[t][0] = new Date(chart.data[t][0]);
              }

              const i18nLabels = chart.labels.map((label) =>
                context.$i18n
                  ? context.$i18n.t("troubleshooting." + label)
                  : label
              );

              let graph = context.charts.droprateDygraphs[ip];

              if (graph) {
                // destroy previous graph to avoid memory leakage
                graph.destroy();
              }

              graph = new Dygraph(
                document.getElementById("chart-ping-droprate-" + ip),
                chart.data,
                {
                  fillGraph: true,
                  stackedGraph: false,
                  labels: i18nLabels,
                  height: 200,
                  strokeWidth: 1,
                  strokeBorderWidth: 1,
                  ylabel: context.$i18n
                    ? context.$i18n.t("troubleshooting.droprate_perc")
                    : "",
                  axisLineColor: "white",
                  labelsDiv: document.getElementById(
                    "ping-droprate-legend-" + ip
                  ),
                  labelsSeparateLines: true,
                  drawGrid: true,
                  axes: {
                    y: {
                      axisLabelFormatter: function(y) {
                        return (y * 100).toFixed(0) + "%";
                      },
                      valueFormatter: function(y) {
                        return (y * 100).toFixed(0) + "%";
                      },
                    },
                  },
                }
              );
              graph.initialData = chart.data;
              context.$set(context.charts.droprateDygraphs, ip, graph);
            }
          });
        },
        function(error) {
          console.error(error);
          context.view.isDroprateChartLoaded = true;
        }
      );
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
  margin-bottom: 0;
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

.chart {
  padding-left: 5px;
  padding-right: 5px;
  margin-bottom: 20px;
}

.card-pf-body .pficon,
.card-pf-body .fa {
  font-size: 22px;
}

.card-pf-aggregate-status .card-pf-title {
  font-size: 16px;
}

.card-pf-aggregate-status .card-action {
  float: right;
  margin-top: 10px;
}

.card-pf-aggregate-status .card-action span {
  margin-right: 0;
}

.card-pf-aggregate-status .card-action .fa {
  font-size: 14px;
  color: #0088ce !important;
}

.card-pf-aggregate-status .card-action .fa:hover {
  color: #0088ce !important;
}

.card-pf {
  min-height: 135px;
}
</style>
