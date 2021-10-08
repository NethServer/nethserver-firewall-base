<template>
  <div class="container-fluid container-cards-pf">
    <!-- //// fix i18n keys -->
    <h3>{{ $t("Global") }}</h3>

    <div class="row row-cards-pf">
      <!-- INTERNET -->
      <div class="col-xs-3 col-sm-2 col-md-2">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <h2 class="card-pf-title">
            <span class="fa fa-globe"></span
            >{{ $t("troubleshooting.internet") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.internet.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.internet.isLoaded"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span
                  v-if="view.internet.status == 'disabled'"
                  class="fa fa-ban gray"
                ></span>
                <span
                  v-if="view.internet.status == 'running'"
                  class="pficon pficon-ok"
                ></span>
                <span
                  v-if="view.internet.status == 'warning'"
                  class="pficon pficon-warning-triangle-o"
                ></span>
                <span
                  v-if="view.internet.status == 'failed'"
                  class="pficon pficon-error-circle-o"
                ></span>
              </span>
            </p>
          </div>
        </div>
      </div>
      <!-- END INTERNET -->

      <!-- MULTIWAN -->
      <div class="col-xs-3 col-sm-2 col-md-2">
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
              <span class="card-pf-aggregate-status-notification">
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
              </span>
            </p>
          </div>
        </div>
      </div>
      <!-- END MULTIWAN -->

      <!-- SHOREWALL -->
      <div class="col-xs-3 col-sm-2 col-md-2">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <h2 class="card-pf-title">
            <span class="fa fa-shield"></span
            >{{ $t("troubleshooting.shorewall") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.shorewall.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.shorewall.isLoaded"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span
                  v-if="view.shorewall.status == 'disabled'"
                  class="fa fa-ban gray"
                ></span>
                <span
                  v-if="view.shorewall.status == 'running'"
                  class="pficon pficon-ok"
                ></span>
                <span
                  v-if="view.shorewall.status == 'warning'"
                  class="pficon pficon-warning-triangle-o"
                ></span>
                <span
                  v-if="view.shorewall.status == 'failed'"
                  class="pficon pficon-error-circle-o"
                ></span>
              </span>
            </p>
          </div>
        </div>
      </div>
      <!-- END SHOREWALL -->

      <!-- SYSTEMD -->
      <div class="col-xs-3 col-sm-2 col-md-2">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <h2 class="card-pf-title">
            <span class="pf-icon pficon-services"></span
            >{{ $t("troubleshooting.systemd") }}
            <a href="#" class="card-pf-link-with-icon">
              <span class="fa fa-external-link pl-21"></span>
            </a>
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.systemd.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.systemd.isLoaded"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span
                  v-if="view.systemd.status == 'running'"
                  class="pficon pficon-ok"
                ></span>
                <span
                  v-if="view.systemd.status == 'warning'"
                  class="pficon pficon-warning-triangle-o"
                ></span>
              </span>
            </p>
          </div>
        </div>
      </div>
      <!-- END SYSTEMD -->
    </div>

    <h3>{{ $t("Services") }}</h3>

    <div class="row row-cards-pf">
      <!-- PROXY -->
      <div class="col-xs-3 col-sm-2 col-md-2">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <h2 class="card-pf-title">
            <span class="pf-icon pficon-filter"></span
            >{{ $t("troubleshooting.proxy") }}
            <a href="#" class="card-pf-link-with-icon">
              <span class="fa fa-external-link pl-21"></span>
            </a>
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.squid.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.squid.isLoaded && view.squid.status == 'running'"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span class="pficon pficon-ok"></span>
              </span>
            </p>
            <div v-if="view.squid.isLoaded && view.squid.status == 'running'">
              <p class="green small">
                {{ $t("troubleshooting." + view.squid.details.green) }}
              </p>
              <p class="blue small">
                {{ $t("troubleshooting." + view.squid.details.blue) }}
              </p>
            </div>
            <p
              v-if="view.squid.isLoaded && view.squid.status == 'disabled'"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span class="fa fa-ban gray"></span>
              </span>
            </p>
            <p
              v-if="view.squid.isLoaded && view.squid.status == 'failed'"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span class="pficon pficon-error-circle-o"></span>
              </span>
            </p>
          </div>
        </div>
      </div>
      <!-- END PROXY -->

      <!-- ufdbGuard FILTER -->
      <div class="col-xs-3 col-sm-2 col-md-2">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <h2 class="card-pf-title">
            <span class="pf-icon pficon-filter"></span
            >{{ $t("troubleshooting.ufdbGuard") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.ufdbGuard.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.ufdbGuard.isLoaded"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span
                  v-if="view.ufdbGuard.status == 'disabled'"
                  class="fa fa-ban gray"
                ></span>
                <span
                  v-if="view.ufdbGuard.status == 'running'"
                  class="pficon pficon-ok"
                ></span>
                <span
                  v-if="view.ufdbGuard.status == 'failed'"
                  class="pficon pficon-error-circle-o"
                ></span>
              </span>
            </p>
          </div>
        </div>
      </div>
      <!-- END FILTER -->

      <!-- ANTIVIRUS -->
      <div class="col-xs-3 col-sm-2 col-md-2">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <h2 class="card-pf-title">
            <span class="pf-icon pficon-filter"></span
            >{{ $t("troubleshooting.antivirus") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.antivirus.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.antivirus.isLoaded"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span
                  v-if="view.antivirus.status == 'disabled'"
                  class="fa fa-ban gray"
                ></span>
                <span
                  v-if="view.antivirus.status == 'running'"
                  class="pficon pficon-ok"
                ></span>
                <span
                  v-if="view.antivirus.status == 'failed'"
                  class="pficon pficon-error-circle-o"
                ></span>
              </span>
            </p>
          </div>
        </div>
      </div>
      <!-- END ANTIVIRUS -->

      <!-- NTOPNG -->
      <div class="col-xs-3 col-sm-2 col-md-2">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <h2 class="card-pf-title">
            <span class="pf-icon pficon-storage-domain"></span
            >{{ $t("troubleshooting.ntopng") }}
            <a href="#" class="card-pf-link-with-icon">
              <span class="fa fa-external-link pl-21"></span>
            </a>
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.ntopng.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <div
              v-if="view.ntopng.isLoaded && view.ntopng.status == 'running'"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="pficon pficon-ok"></span>
            </div>
            <p
              v-if="view.ntopng.isLoaded && view.ntopng.status == 'disabled'"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="fa fa-ban gray"></span>
            </p>
            <p
              v-if="view.ntopng.isLoaded && view.ntopng.status == 'failed'"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span class="pficon pficon-error-circle-o"></span>
              </span>
            </p>
            <div class="h10">
              <div class="dropdown">
                <a href="#" data-toggle="dropdown" class="right">
                  <span class="pficon pficon-info"></span>
                </a>
                <div class="dropdown-menu infotip bottom-right">
                  <div class="arrow"></div>
                  <ul class="list-group">
                    <li
                      class="list-group-item"
                      v-if="view.ntopng.details"
                      v-for="(role, name) in view.ntopng.details"
                      :key="name"
                    >
                      <span class="pficon pficon-network pr-5"></span>
                      <span :class="role">{{ name }}</span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- END NTOPNG -->
    </div>

    <h3>{{ $t("Security") }}</h3>

    <div class="row row-cards-pf">
      <!-- IP BLACKLIST -->
      <div class="col-xs-3 col-sm-2 col-md-2">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <h2 class="card-pf-title">
            <span class="pf-icon pficon-cloud-security"></span
            >{{ $t("troubleshooting.ipblacklist") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.ipblacklist.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.ipblacklist.isLoaded"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span
                  v-if="view.ipblacklist.status == 'disabled'"
                  class="fa fa-ban gray"
                ></span>
                <span
                  v-if="view.ipblacklist.status == 'enabled'"
                  class="pficon pficon-ok"
                ></span>
              </span>
            </p>
          </div>
        </div>
      </div>
      <!-- END IP BLACKLIST -->

      <!-- FTL DNS BLACKLIST -->
      <div class="col-xs-3 col-sm-2 col-md-2">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <h2 class="card-pf-title">
            <span class="pf-icon pficon-cloud-security"></span
            >{{ $t("troubleshooting.ftl") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.ftl.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.ftl.isLoaded"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span
                  v-if="view.ftl.status == 'disabled'"
                  class="fa fa-ban gray"
                ></span>
                <span
                  v-if="view.ftl.status == 'enabled'"
                  class="pficon pficon-ok"
                ></span>
              </span>
            </p>
          </div>
        </div>
      </div>
      <!-- END FTL -->

      <!-- FAIL2BAN -->
      <div class="col-xs-3 col-sm-2 col-md-2">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <h2 class="card-pf-title">
            <span class="pf-icon pficon-locked"></span
            >{{ $t("troubleshooting.fail2ban") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.fail2ban.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.fail2ban.isLoaded"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span
                  v-if="view.fail2ban.status == 'disabled'"
                  class="fa fa-ban gray"
                ></span>
                <span
                  v-if="view.fail2ban.status == 'enabled'"
                  class="pficon pficon-ok"
                ></span>
              </span>
            </p>
          </div>
        </div>
      </div>
      <!-- END FAIL2BAN -->

      <!-- SURICATA -->
      <div class="col-xs-3 col-sm-2 col-md-2">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <h2 class="card-pf-title">
            <span class="pf-icon pficon-security"></span
            >{{ $t("troubleshooting.suricata") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.suricata.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.suricata.isLoaded"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span
                  v-if="view.suricata.status == 'disabled'"
                  class="fa fa-ban gray"
                ></span>
                <span
                  v-if="view.suricata.status == 'enabled'"
                  class="pficon pficon-ok"
                ></span>
              </span>
            </p>
          </div>
        </div>
      </div>
      <!-- END SURICATA -->
    </div>

    <h3>{{ $t("Network") }}</h3>

    <div class="row row-cards-pf">
      <!-- PING CHART -->
      <div class="col-xs-6 col-sm-4 col-md-4">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <div
            v-if="!view.graphLoaded && !view.isChartLoaded"
            class="spinner spinner-lg view-spinner"
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
            <div
              v-if="view.isChartLoaded"
              v-for="(data, index) in charts"
              :key="index"
            >
              <h4>{{ $t("troubleshooting.ping") }}: {{ index }}</h4>
              <div :id="'chart-ping-' + index"></div>
            </div>
          </div>
        </div>
      </div>
      <!-- END PING CHART -->
    </div>

    <h3>{{ $t("Other") }}</h3>

    <div class="row row-cards-pf">
      <!-- TEMPLATES -->
      <div class="col-xs-3 col-sm-2 col-md-2">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <h2 class="card-pf-title">
            <span class="pf-icon pficon-settings"></span
            >{{ $t("troubleshooting.templates") }}
            <a href="#" class="card-pf-link-with-icon">
              <span class="pf-icon pficon-arrow fa-external-link"></span>
            </a>
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.templates.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.templates.isLoaded"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span
                  v-if="view.templates.status == 'disabled'"
                  class="fa fa-ban gray"
                ></span>
                <span
                  v-if="view.templates.status == 'warning'"
                  class="pficon pficon-warning-triangle-o"
                ></span>
              </span>
            </p>
          </div>
        </div>
      </div>
      <!-- END TEMPLATES -->
    </div>
  </div>
</template>

<script>
import Dygraph from "dygraphs";

export default {
  name: "TroubleshootingServices",
  data() {
    return {
      view: {
        isChartLoaded: false,
        invalidChartsPingData: false,

        internet: { status: "disabled", isLoaded: false },
        shorewall: { status: "disabled", isLoaded: false },
        multiwan: { status: "disabled", isLoaded: false },
        systemd: { status: "disabled", isLoaded: false },

        squid: { status: "disabled", isLoaded: false, details: {} },
        antivirus: { status: "disabled", isLoaded: false },
        ufdbGuard: { status: "disabled", isLoaded: false },

        ipblacklist: { status: "disabled", isLoaded: false },
        ftl: { status: "disabled", isLoaded: false },

        ntopng: { status: "disabled", isLoaded: false },

        fail2ban: { status: "disabled", isLoaded: false },
        suricata: { status: "disabled", isLoaded: false },
        templates: { status: "disabled", isLoaded: false, details: [] },
      },
      charts: {},
      pollingIntervalIdChart: 0,
    };
  },
  created() {
    console.log("services created"); ////
  },
  mounted() {
    console.log("services mounted"); ////

    var context = this;

    context.updatePingChart();
    context.charts.ping_interval = setInterval(function() {
      context.updatePingChart();
    }, 5000);

    const services = [
      "internet",
      "shorewall",
      "multiwan",
      "systemd",
      "ipblacklist",
      "ftl",
      "templates",
      "squid",
      "ufdbGuard",
      "antivirus",
      "fail2ban",
      "suricata",
      "ntopng",
    ];
    services.forEach(function(item, index) {
      context.getServiceStatus(item);
    });

    $("#ts-services-tab-parent").click(); //// needed?
  },
  beforeDestroy() {
    console.log("services beforeDestroy"); ////

    //// todo needed?
    $(".modal").modal("hide");
    clearInterval(this.pollingIntervalIdChart);

    clearInterval(this.charts.ping_interval);
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
    updatePingChart() {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/troubleshooting/read"],
        { action: "ping", time: 900 },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.charts = success;
          context.view.invalidChartsPingData = false;
          context.view.isChartLoaded = true;
          context.$nextTick(function() {
            for (const ip in context.charts) {
              var chart = context.charts[ip];
              for (var t in chart.data) {
                chart.data[t][0] = new Date(chart.data[t][0]);
              }
              var g = new Dygraph(
                document.getElementById("chart-ping-" + ip),
                chart.data,
                {
                  fillGraph: true,
                  stackedGraph: true,
                  labels: chart.labels,
                  height: 150,
                  width: 400,
                  strokeWidth: 1,
                  strokeBorderWidth: 1,
                  ylabel: context.$i18n.t("troubleshooting.latency"),
                  axisLineColor: "white",
                  labelsSeparateLines: true,
                  drawGrid: false,
                }
              );
              g.initialData = chart.data;
            }
          });
        },
        function(error) {
          console.error(error);
          context.view.isChartLoaded = true;
        }
      );
    },
  },
};
</script>

<style scoped>
.green {
  color: green;
}

.blue {
  color: blue;
}

.card-pf-title > .pf-icon {
  margin-right: 7px;
}

.pl-21 {
  padding-left: 21px;
}

.pr-5 {
  padding-right: 21px;
}

.h10 {
  height: 10px;
  padding: 2px;
}
</style>
