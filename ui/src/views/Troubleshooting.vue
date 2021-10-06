<template>
  <div>
    <h2>{{$t('troubleshooting.title')}}</h2>

    <ul class="nav nav-tabs nav-tabs-pf">
     <li>
        <a
          class="nav-link"
          data-toggle="tab"
          href="#ts-services-tab"
          id="ts-services-tab-parent"
        >{{$t('troubleshooting.services')}}</a>
      </li>
      <li>
        <a
          class="nav-link"
          data-toggle="tab"
          href="#ts-wans-tab"
          id="ts-wans-tab-parent"
        >{{$t('troubleshooting.wans')}}</a>
      </li>
      <li>
        <a
          class="nav-link"
          data-toggle="tab"
          href="#ts-hosts-tab"
          id="ts-hosts-tab-parent"
        >{{$t('troubleshooting.hosts')}}</a>
      </li>
    </ul>

    <div class="tab-content gray-bg" id="troubleshootingTabContent">
      <!-- SERVICES -->
      <div class="tab-pane fade active" id="ts-services-tab" role="tabpanel" aria-labelledby="ts-services-tab">

        <div class="container-fluid container-cards-pf">
          <h3>{{$t('Global')}}</h3>

          <div class="row row-cards-pf">

            <!-- INTERNET -->
            <div class="col-xs-3 col-sm-2 col-md-2">
                <div class="card-pf card-pf-accented card-pf-aggregate-status">
                <h2 class="card-pf-title">
                <span class="fa fa-shield"></span>{{ $t('troubleshooting.internet') }}
                </h2>
                <div class="card-pf-body">
                  <div v-if="!view.internet.isLoaded" class="spinner spinner-lg view-spinner"></div>
                  <p v-if="view.internet.isLoaded" class="card-pf-aggregate-status-notifications">
                  <span class="card-pf-aggregate-status-notification">
                    <span v-if="view.internet.status == 'disabled'" class="fa fa-ban gray"></span>
                    <span v-if="view.internet.status == 'running'" class="pficon pficon-ok"></span>
                    <span v-if="view.internet.status == 'warning'" class="pficon pficon-warning-triangle-o"></span>
                    <span v-if="view.internet.status == 'failed'" class="pficon pficon-error-circle-o"></span>
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
                <span class="fa fa-shield"></span>{{ $t('troubleshooting.multiwan') }}
                </h2>
                <div class="card-pf-body">
                  <div v-if="!view.multiwan.isLoaded" class="spinner spinner-lg view-spinner"></div>
                  <p v-if="view.multiwan.isLoaded" class="card-pf-aggregate-status-notifications">
                  <span class="card-pf-aggregate-status-notification">
                    <span v-if="view.multiwan.status == 'disabled'" class="fa fa-ban gray"></span>
                    <span v-if="view.multiwan.status == 'running'" class="pficon pficon-ok"></span>
                    <span v-if="view.multiwan.status == 'warning'" class="pficon pficon-warning-triangle-o"></span>
                    <span v-if="view.multiwan.status == 'failed'" class="pficon pficon-error-circle-o"></span>
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
                <span class="fa fa-shield"></span>{{ $t('troubleshooting.shorewall') }}
                </h2>
                <div class="card-pf-body">
                  <div v-if="!view.shorewall.isLoaded" class="spinner spinner-lg view-spinner"></div>
                  <p v-if="view.shorewall.isLoaded" class="card-pf-aggregate-status-notifications">
                  <span class="card-pf-aggregate-status-notification">
                    <span v-if="view.shorewall.status == 'disabled'" class="fa fa-ban gray"></span>
                    <span v-if="view.shorewall.status == 'running'" class="pficon pficon-ok"></span>
                    <span v-if="view.shorewall.status == 'warning'" class="pficon pficon-warning-triangle-o"></span>
                    <span v-if="view.shorewall.status == 'failed'" class="pficon pficon-error-circle-o"></span>
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
                <span class="fa fa-shield"></span>{{ $t('troubleshooting.systemd') }}
                </h2>
                <div class="card-pf-body">
                  <div v-if="!view.systemd.isLoaded" class="spinner spinner-lg view-spinner"></div>
                  <p v-if="view.systemd.isLoaded" class="card-pf-aggregate-status-notifications">
                  <span class="card-pf-aggregate-status-notification">
                    <span v-if="view.systemd.status == 'running'" class="pficon pficon-ok"></span>
                    <span v-if="view.systemd.status == 'warning'" class="pficon pficon-warning-triangle-o"></span>
                  </span>
                  </p>
                </div>
              </div>
            </div>
            <!-- END SYSTEMD -->

          </div>

          <h3>{{$t('Services')}}</h3>

          <div class="row row-cards-pf">

            <!-- PROXY -->
            <div class="col-xs-6 col-sm-4 col-md-4">
                <div class="card-pf card-pf-accented card-pf-aggregate-status">
                <h2 class="card-pf-title">
                <span class="fa fa-shield"></span>{{ $t('troubleshooting.proxy') }}
                </h2>
                <div class="card-pf-body">
                  <div v-if="!view.squid.isLoaded" class="spinner spinner-lg view-spinner"></div>
                  <p v-if="view.squid.isLoaded && view.squid.status == 'running'" class="card-pf-aggregate-status-notifications">
                    <span class="card-pf-aggregate-status-notification">
                      <div class="row">
                        <div class="col-xs-1 col-sm-1 col-md-1">
                          <span class="pficon pficon-ok"></span>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1">
                          <p class="green small">{{ $t('troubleshooting.'+view.squid.details.green)}}</p>
                          <p class="blue small">{{ $t('troubleshooting.'+view.squid.details.blue)}}</p>
                        </div>
                      </div>
                    </span>
                  </p>
                  <p v-if="view.squid.isLoaded && view.squid.status == 'disabled' " class="card-pf-aggregate-status-notifications">
                    <span class="card-pf-aggregate-status-notification">
                      <span class="fa fa-ban gray"></span>
                    </span>
                  </p>
                  <p v-if="view.squid.isLoaded && view.squid.status == 'failed' " class="card-pf-aggregate-status-notifications">
                    <span class="card-pf-aggregate-status-notification">
                      <span class="pficon pficon-error-circle-o"></span>
                    </span>
                  </p>
                  <p>
                    <a href="#" class="card-pf-link-with-icon">
                      <span class="fa fa-external-link"></span>{{ $t('troubleshooting.details') }}
                    </a>
                  </p>
                </div>
              </div>
            </div>
            <!-- END PROXY -->

          </div>

          <h3>{{$t('Security')}}</h3>

          <div class="row row-cards-pf">

            <!-- IP BLACKLIST -->
            <div class="col-xs-3 col-sm-2 col-md-2">
                <div class="card-pf card-pf-accented card-pf-aggregate-status">
                <h2 class="card-pf-title">
                <span class="fa fa-shield"></span>{{ $t('troubleshooting.ipblacklist') }}
                </h2>
                <div class="card-pf-body">
                  <div v-if="!view.ipblacklist.isLoaded" class="spinner spinner-lg view-spinner"></div>
                  <p v-if="view.ipblacklist.isLoaded" class="card-pf-aggregate-status-notifications">
                  <span class="card-pf-aggregate-status-notification">
                    <span v-if="view.ipblacklist.status == 'disabled'" class="fa fa-ban gray"></span>
                    <span v-if="view.ipblacklist.status == 'enabled'" class="pficon pficon-ok"></span>
                  </span>
                  </p>
                  <p>
                    <a href="#" class="card-pf-link-with-icon">
                      <span class="fa fa-external-link"></span>{{ $t('troubleshooting.details') }}
                    </a>
                  </p>
                </div>
              </div>
            </div>
            <!-- END IP BLACKLIST -->

          </div>



        <div class="row row-cards-pf">
          <h3>{{$t('troubleshooting.charts')}}</h3>

          <div class="col-xs-6 col-sm-4 col-md-4">
            <div class="card-pf">
              <div v-if="!view.graphLoaded && !view.isChartLoaded " class="spinner spinner-lg view-spinner"></div>
              <div class="panel panel-default" id="network-graph"></div>

              <div v-if="!view.graphLoaded && !view.isChartLoaded " class="spinner spinner-lg view-spinner"></div>
              <div>
                <div
                  v-if="view.invalidChartsPingData"
                  class="alert alert-warning alert-dismissable col-sm-12"
                >
                  <span class="pficon pficon-warning-triangle-o"></span>
                  <strong>{{$t('warning')}}!</strong>
                  {{$t('troubleshooting.ping_charts_not_updated')}}.
                </div>
                <div
                  v-show="view.isChartLoaded && !view.invalidChartsPingData"
                  class="row"
                >
                  <div class="col-sm-11">
                    <h4 class="col-sm-12">
                      {{$t('troubleshooting.ping')}}
                      <div id="chart-status" class="legend"></div>
                    </h4>
                    <div id="chart-ping" class="col-sm-12"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>

        <h3>{{$t('Other')}}</h3>

        <div class="row row-cards-pf">

          <!-- TEMPLATES -->
          <div class="col-xs-3 col-sm-2 col-md-2">
              <div class="card-pf card-pf-accented card-pf-aggregate-status">
              <h2 class="card-pf-title">
              <span class="fa fa-shield"></span>{{ $t('troubleshooting.templates') }}
              </h2>
              <div class="card-pf-body">
                <div v-if="!view.templates.isLoaded" class="spinner spinner-lg view-spinner"></div>
                <p v-if="view.templates.isLoaded" class="card-pf-aggregate-status-notifications">
                <span class="card-pf-aggregate-status-notification">
                  <span v-if="view.templates.status == 'disabled'" class="fa fa-ban gray"></span>
                  <span v-if="view.templates.status == 'warning'" class="pficon pficon-warning-triangle-o"></span>
                </span>
                </p>
                <p>
                  <a href="#" class="card-pf-link-with-icon">
                    <span class="fa fa-external-link"></span>{{ $t('troubleshooting.details') }}
                  </a>
                </p>
              </div>
            </div>
          </div>
          <!-- END TEMPLATES -->
        </div>

        </div> <!-- END cards container -->


      </div>
      <!-- END SERVICES -->

      <!-- WANS -->
      <div class="tab-pane fade active" id="ts-wans-tab" role="tabpanel" aria-labelledby="ts-wans-tab">
        <h3>{{$t('WANS')}}</h3>
      </div>
      <!-- END WANS -->

      <!-- HOSTS -->
      <div class="tab-pane fade active" id="ts-hosts-tab" role="tabpanel" aria-labelledby="ts-hosts-tab">
        <h3>{{$t('Hosts')}}</h3>
      </div>
      <!-- END HOSTS -->
    </div>

  </div>
</template>

<script>
var Mark = require("mark.js");
import Dygraph from "dygraphs";

export default {
  name: "Troubleshooting",
  beforeRouteLeave(to, from, next) {
    $(".modal").modal("hide");
    clearInterval(this.pollingIntervalIdChart);
    next();
  },
  mounted() {
    var context = this;

    context.updatePingChart();
    context.charts.ping_interval = setInterval(function() {
      context.updatePingChart(900);
    }, 5000);

    //context.getSquidStatus();
    const services = ["internet", "shorewall", "multiwan", "systemd", "ipblacklist", "templates", "squid"]
    services.forEach(function (item, index) {
      context.getServiceStatus(item);
    });
    $("#ts-services-tab-parent").click();

  },
  data() {
    return {
      view: {
        isChartLoaded: false,
        invalidChartsPingData: false,
        squid: {status: 'disabled', isLoaded: false, details: {}},
        internet: {status: "disabled", isLoaded: false},
        shorewall: {status: "disabled", isLoaded: false},
        multiwan: {status: "disabled", isLoaded: false},
        systemd: {status: "disabled", isLoaded: false},
        ipblacklist: {status: "disabled", isLoaded: false},
        dnsblacklist: {status: "disabled", isLoaded: false},
        templates: {status: "disabled", isLoaded: false, details: []},

      },
      charts: {
      },
      pollingIntervalIdChart: 0
    };
  },
  methods: {
    getServiceStatus(service) {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/troubleshooting/read"],
        { action: "service",
          service: service
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context["view"][service]["isLoaded"] = true;
          context["view"][service]["status"] = success.status;
          if ('details' in success) {
            context["view"][service]["details"] = success.details;
          }
        },
        function(error) {
          console.error(error);
          context["view"][service]["isLoaded"] = false;
        }
      );
    },
    getSquidStatus() {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/troubleshooting/read"],
        { action: "service",
          service: "squid"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.view.squid.isLoaded = true;
          context.view.squid.status = success.proxy.status;
        },
        function(error) {
          console.error(error);
          this.view.squid.isLoaded = false;
        }
      );
    },
    updatePingChart() {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/troubleshooting/read"],
        { action: "ping",
          time: 900
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          if (success.data.length > 0) {
            context.view.invalidChartsPingData = false;
            for (var t in success.data) {
              success.data[t][0] = new Date(success.data[t][0] * 1000);
            }
            context.charts["chart-ping"] = new Dygraph(
              document.getElementById("chart-ping"),
              success.data,
              {
                fillGraph: true,
                stackedGraph: true,
                labels: success.labels,
                height: 150,
                width: 400,
                strokeWidth: 1,
                strokeBorderWidth: 1,
                ylabel: context.$i18n.t("troubleshooting.latency"),
                axisLineColor: "white",
                labelsDiv: document.getElementById("chart-status"),
                labelsSeparateLines: true,
                drawGrid: false,
                legend: "always"
              }
            );
            context.charts["chart-ping"].initialData = success.data;
          } else {
            context.view.invalidChartsPingData = true;
            context.$forceUpdate();
          }
          context.view.isChartLoaded = true;
        },
        function(error) {
          console.error(error);
          context.view.isChartLoaded = true;
        }
      );
    }
  }
};
</script>

<style>
.gray-bg {
  background-color: #f5f5f5;
}

.green {
  color: green
}

.blue {
  color: blue
}
</style>
