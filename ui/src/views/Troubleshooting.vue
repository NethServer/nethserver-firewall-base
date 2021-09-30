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

    <div class="tab-content" id="troubleshootingTabContent">
      <!-- SERVICES -->
      <div class="tab-pane fade active" id="ts-services-tab" role="tabpanel" aria-labelledby="ts-services-tab">
        <h3>{{$t('Services')}}</h3>

        <div class="container-fluid container-cards-pf">
          <div class="row row-cards-pf">

            <!-- PROXY -->
            <div class="col-xs-3 col-sm-2 col-md-2">
                <div class="card-pf card-pf-accented card-pf-aggregate-status">
                <h2 class="card-pf-title">
                <span class="fa fa-shield"></span>{{ $t('troubleshooting.proxy') }}
                </h2>
                <div class="card-pf-body">
                  <div v-if="!view.squid.isLoaded" class="spinner spinner-lg view-spinner"></div>
                  <p v-if="view.squid.isLoaded" class="card-pf-aggregate-status-notifications">
                  <span class="card-pf-aggregate-status-notification">
                    <span v-if="view.squid.status == 'disabled'" class="fa fa-ban gray"></span>
                    <span v-if="view.squid.status == 'enabled'" class="pficon pficon-ok"></span>
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
        </div>

        <div class="row">
          <div class="col-sm-4">
            <div v-if="!view.graphLoaded && !view.isChartLoaded " class="spinner spinner-lg view-spinner"></div>
            <div class="panel panel-default" id="network-graph"></div>

            <h3>{{$t('troubleshooting.charts')}}</h3>
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

    context.getSquidStatus();
    $("#ts-services-tab-parent").click();

  },
  data() {
    return {
      view: {
        isChartLoaded: false,
        invalidChartsPingData: false,
        squid: {status: '', isLoaded: false}
      },
      charts: {
      },
      pollingIntervalIdChart: 0
    };
  },
  methods: {
    getSquidStatus(service) {
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
            console.log(success);
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
</style>
