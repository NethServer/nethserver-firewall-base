<template>
  <div>
    <h2>{{$t('connections.title')}}</h2>

    <h3 v-if="view.isChartLoaded && connections.length > 0">{{$t('charts')}}</h3>
    <a
      v-if="view.isChartLoaded && connections.length > 0"
      @click="toggleCharts()"
    >{{view.chartsShowed ? $t('hide_charts') : $t('show_charts')}}</a>
    <div
      v-if="!view.isChartLoaded && connections.length > 0"
      class="spinner spinner-lg view-spinner"
    ></div>
    <div :class="view.chartsShowed ? '' : 'hidden'">
      <div
        v-if="view.invalidChartsData && connections.length > 0"
        class="alert alert-warning alert-dismissable col-sm-12"
      >
        <span class="pficon pficon-warning-triangle-o"></span>
        <strong>{{$t('warning')}}!</strong>
        {{$t('charts_not_updated')}}.
      </div>
      <div
        v-show="connections.length > 0 && view.isChartLoaded && !view.invalidChartsData"
        class="row"
      >
        <div class="col-sm-11">
          <h4 class="col-sm-12">
            {{$t('connections.title')}}
            <div id="chart-status" class="legend"></div>
          </h4>
          <div id="chart-connections" class="col-sm-12"></div>
        </div>
      </div>
    </div>

    <div v-if="!view.isLoaded" id="loader" class="spinner spinner-lg view-spinner"></div>

    <h3>{{$t('filter')}}</h3>
    <form class="form-horizontal">
      <div class="form-group">
        <label class="col-sm-2">{{$t('connections.protocol')}}</label>
        <div class="col-sm-6">
          <select
            @change="getConnections(true)"
            v-model="searchProto"
            class="form-control quarter-width"
          >
            <option v-for="(p, pk) in protocols" v-bind:key="pk" :value="pk">{{pk | uppercase}}</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2">{{$t('connections.state')}}</label>
        <div class="col-sm-6">
          <select
            :disabled="searchProto == 'udp' || searchProto == 'icmp'"
            @change="getConnections(true)"
            v-model="searchState"
            class="form-control quarter-width"
          >
            <option
              v-for="(s,k) in protocols[searchProto]"
              v-bind:key="k"
              :value="s"
            >{{s ? s : $t("all") | uppercase}}</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2">{{$t('connections.limit')}}</label>
        <div class="col-sm-6">
          <select
            @change="getConnections(true)"
            v-model="searchLimit"
            class="form-control quarter-width"
          >
            <option value="25">25</option>
            <option value="100">100</option>
            <option value>{{ $t("connections.all") }}</option>
          </select>
        </div>
      </div>
    </form>

    <div class="pf-container" v-if="view.isLoaded">
      <h3>{{$t('actions')}}</h3>
      <form v-if="connections.length > 0" role="form" class="search-pf has-button search">
        <div class="form-group">
          <button
            class="btn btn-primary btn-lg mg-left-10"
            type="button"
            v-on:click="getConnections(true)"
          >{{$t('connections.refresh')}}</button>
          <button
            class="btn btn-danger btn-lg mg-left-10"
            type="button"
            data-toggle="modal"
            data-target="#deleteConnectionsModal"
          >{{$t('connections.flush')}}</button>
        </div>
      </form>

      <div v-show="connections.length == 0 && view.isLoaded" class="blank-slate-pf white">
        <div class="blank-slate-pf-icon">
          <span class="fa fa-link"></span>
        </div>
        <h1>{{$t('connections.no_connections_found')}}</h1>
        <p>{{$t('connections.no_connections_found_text')}}.</p>
      </div>

      <div
        v-if="!view.isLoadedAutoRefresh"
        id="loader"
        class="spinner spinner-lg view-spinner mg-top-10"
      ></div>

      <div v-if="connections.length > 0 && view.isLoaded && view.isLoadedAutoRefresh">
        <h3 class="pull-left">{{$t('list')}}</h3>
        <h3
          class="pull-right table-counter"
        >{{$t('connections.total')}}: {{filteredConnections.length}}</h3>
        <vue-good-table
          :customRowsPerPageDropdown="[25,50,100]"
          :perPage="25"
          :columns="columns"
          :rows="connections"
          :lineNumbers="false"
          :sort-options="{ enabled: false }"
          :globalSearch="true"
          :globalSearchFn="searchFn"
          :paginate="true"
          styleClass="table condensed"
          :nextText="tableLangsTexts.nextText"
          :prevText="tableLangsTexts.prevText"
          :rowsPerPageText="tableLangsTexts.rowsPerPageText"
          :globalSearchPlaceholder="tableLangsTexts.globalSearchPlaceholder"
          :ofText="tableLangsTexts.ofText"
        >
          <template slot="table-row" slot-scope="props">
            <td class="fancy">
              <span class="semi-bold">{{props.row.src}}</span>
              <span v-if="props.row.sport">: {{props.row.sport}}</span>
            </td>
            <td class="fancy">
              <span class="semi-bold">{{props.row.dst}}</span>
              <span v-if="props.row.dport">: {{props.row.dport}}</span>
            </td>
            <td class="fancy">{{props.row.state ? props.row.state : '-'}}</td>
            <td class="fancy">{{props.row.bytes_total | byteFormat}}</td>
            <td class="fancy">{{props.row.timeout + " s"}}</td>
            <td
              class="fancy"
            >{{props.row['delta-time'] ? props.row['delta-time'] : null | secondsInHour }}</td>
            <td class="fancy">{{formatNatField(props.row)}}</td>
            <td class="fancy">{{props.row.provider ? props.row.provider : "-" }}</td>
            <td class="fancy">{{props.row.ndpi ? props.row.ndpi : "-" }}</td>
            <td class="fancy">{{props.row.prio ? props.row.prio : "-" }}</td>
            <td class="fancy">
              <button @click="openDeleteConnection(props.row)" :class="['btn btn-danger']">
                <span :class="['fa', 'fa-times', 'span-right-margin']"></span>
                {{$t('delete')}}
              </button>
            </td>
          </template>
        </vue-good-table>
      </div>
    </div>

    <div
      class="modal"
      id="deleteConnectionModal"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{$t('rules.delete_connection')}} {{currentConnection.src}} -> {{currentConnection.dst}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deleteConnection(currentConnection)">
            <div class="modal-body">
              <div class="form-group">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('are_you_sure')}}?</label>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-danger" type="submit">{{$t('delete')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div
      class="modal"
      id="deleteConnectionsModal"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('connections.flush_connections')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="flushConnections()">
            <div class="modal-body">
              <div class="alert alert-warning alert-dismissable">
                <span class="pficon pficon-warning-triangle-o"></span>
                <strong>{{$t('warning')}}</strong>
                : {{$t('connections.flush_connections_warning')}}.
              </div>
              <div class="form-group">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('are_you_sure')}}?</label>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-danger" type="submit">{{$t('delete')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
var Mark = require("mark.js");
import Dygraph from "dygraphs";

export default {
  name: "Connections",
  mounted() {
    this.getConnections();
    this.getProtocols();
    this.initCharts();
  },
  beforeRouteLeave(to, from, next) {
    $(".modal").modal("hide");
    clearInterval(this.pollingIntervalId);
    clearInterval(this.pollingIntervalIdChart);
    next();
  },
  data() {
    return {
      view: {
        isLoaded: false,
        isLoadedAutoRefresh: false,
        isChartLoaded: false,
        invalidChartsData: false,
        chartsShowed: false
      },
      connections: [],
      protocols: {},
      searchString: "",
      searchProto: "tcp",
      searchState: "",
      searchLimit: 25,
      currentConnection: {},
      pollingIntervalId: 0,
      pollingIntervalIdChart: 0,
      tableLangsTexts: this.tableLangs(),
      charts: {
        connections: null
      },
      columns: [
        {
          label: this.$i18n.t("connections.source"),
          field: "src",
          sortable: false
        },
        {
          label: this.$i18n.t("connections.destination"),
          field: "dst",
          sortable: false
        },
        {
          label: this.$i18n.t("connections.state"),
          field: "state",
          sortable: false
        },
        {
          label: this.$i18n.t("connections.bytes"),
          field: "bytes_total",
          sortable: false
        },
        {
          label: this.$i18n.t("connections.timeout"),
          field: "timeout",
          sortable: false
        },
        {
          label: this.$i18n.t("connections.duration"),
          field: "delta-time",
          sortable: false
        },
        {
          label: this.$i18n.t("connections.nat"),
          field: "nat",
          sortable: false
        },
        {
          label: this.$i18n.t("connections.provider"),
          field: "provider",
          sortable: false
        },
        {
          label: this.$i18n.t("connections.application"),
          field: "ndpi",
          sortable: false
        },
        {
          label: this.$i18n.t("connections.priority"),
          field: "prio",
          sortable: false
        },
        {
          label: "",
          field: "",
          filterable: true,
          sortable: false
        }
      ]
    };
  },
  computed: {
    filteredConnections() {
      var returnObj = [];
      for (var c in this.connections) {
        var conn = JSON.stringify(this.connections[c]);
        if (conn.toLowerCase().includes(this.searchString.toLowerCase())) {
          returnObj.push(this.connections[c]);
        }
      }

      return returnObj;
    }
  },
  methods: {
    searchFn(row, col, cellValue, searchTerm) {
      return JSON.stringify(row)
        .toLowerCase()
        .includes(searchTerm.toLowerCase());
    },
    toggleCharts() {
      this.view.chartsShowed = !this.view.chartsShowed;
      if (this.view.chartsShowed) {
        this.initCharts();
      }
    },
    initCharts() {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/connections/read"],
        {
          action: "stats",
          time: 120
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }

          if (success.data.length > 0) {
            context.view.invalidChartsData = false;

            for (var t in success.data) {
              success.data[t][0] = new Date(success.data[t][0] * 1000);
            }

            context.charts["chart-connections"] = new Dygraph(
              document.getElementById("chart-connections"),
              success.data.reverse(),
              {
                fillGraph: true,
                stackedGraph: true,
                labels: success.labels,
                height: 150,
                strokeWidth: 1,
                strokeBorderWidth: 1,
                ylabel: context.$i18n.t("connections.total"),
                axisLineColor: "white",
                labelsDiv: document.getElementById("chart-status"),
                labelsSeparateLines: true,
                legend: "always",
                axes: {
                  y: {
                    axisLabelFormatter: function(y) {
                      return Math.ceil(y);
                    }
                  }
                }
              }
            );
            context.charts["chart-connections"].initialData = success.data;

            // start polling
            if (context.pollingIntervalIdChart == 0) {
              context.pollingIntervalIdChart = setInterval(function() {
                context.updateCharts(120);
              }, 1000);
            }
          } else {
            context.view.invalidChartsData = true;
            context.$forceUpdate();
          }

          context.view.isChartLoaded = true;
        },
        function(error) {
          console.error(error);
          context.view.isChartLoaded = true;
        }
      );
    },
    updateCharts(time) {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/connections/read"],
        {
          action: "stats",
          time: time
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          if (success.data.length > 0) {
            context.view.invalidChartsData = false;

            for (var t in success.data) {
              success.data[t][0] = new Date(success.data[t][0] * 1000);
            }

            context.charts["chart-connections"].updateOptions({
              file: success.data.reverse()
            });
          } else {
            context.view.invalidChartsData = true;
            context.$forceUpdate();
          }
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getProtocols() {
      var context = this;

      nethserver.exec(
        ["nethserver-firewall-base/connections/read"],
        {
          action: "protocols"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
            context.protocols = success.protocols;
          } catch (e) {
            console.error(e);
          }
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getConnections(change) {
      var context = this;

      if (change) {
        context.view.isLoadedAutoRefresh = false;
      } else {
        context.view.isLoaded = false;
      }
      nethserver.exec(
        ["nethserver-firewall-base/connections/read"],
        {
          action: "conntrack",
          protocol: context.searchProto,
          state: context.searchState,
          limit: context.searchLimit
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
            context.connections = success.connections;
          } catch (e) {
            console.error(e);
          }

          context.view.isLoaded = true;
          context.view.isLoadedAutoRefresh = true;

          context.$parent.getFirewallStatus();
        },
        function(error) {
          console.error(error);
          context.view.isLoaded = true;
          context.view.isLoadedAutoRefresh = true;
        }
      );
    },
    openDeleteConnection(c) {
      this.currentConnection = Object.assign({}, c);
      $("#deleteConnectionModal").modal("show");
    },
    deleteConnection(c) {
      var context = this;

      var connObj = {
        action: "connection",
        protocol: c.protocol,
        dport: c.dport,
        sport: c.sport,
        src: c.src,
        dst: c.dst
      };

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "connections.connection_deleted_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "connections.connection_deleted_error"
      );

      // update values
      $("#deleteConnectionModal").modal("hide");
      nethserver.exec(
        ["nethserver-firewall-base/connections/delete"],
        connObj,
        function(stream) {
          console.info("firewall-base-update", stream);
        },
        function(success) {
          // get connections
          context.getConnections();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    },
    flushConnections(c) {
      var context = this;

      var connObj = {
        action: "flush"
      };

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "connections.connections_flushed_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "connections.connections_flushed_error"
      );

      // update values
      $("#deleteConnectionsModal").modal("hide");
      nethserver.exec(
        ["nethserver-firewall-base/connections/delete"],
        connObj,
        function(stream) {
          console.info("firewall-base-update", stream);
        },
        function(success) {
          // get connections
          context.getConnections();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    },
    formatNatField(row) {
      if (!row.nat) {
        return "-";
      }
      if (row.dst != row.src_reply) {
        return row.src_reply;
      }
      if (row.src != row.dst_reply) {
        return row.dst_reply;
      }
    }
  }
};
</script>

<style>
.quarter-width {
  width: 25% !important;
}

.adjust-heading {
  width: calc(50% - 20px) !important;
}

@media (max-width: 992px) {
  .adjust-heading {
    width: calc(100% - 20px) !important;
  }
}

.adjust-check {
  margin-top: -5px !important;
}

.table-counter {
  position: absolute;
  right: 10px;
  margin-top: 56px;
  z-index: 999;
  font-size: 20px;
}
</style>
