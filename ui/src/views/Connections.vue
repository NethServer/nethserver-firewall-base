<template>
  <div>
    <h2>{{$t('connections.title')}}</h2>

    <div v-if="!view.isLoaded" id="loader" class="spinner spinner-lg view-spinner"></div>
    <div class="pf-container" v-if="view.isLoaded">
      <h3>{{$t('list')}}</h3>

      <div class="mg-bottom-10">
        <label class="control-label">{{$t('connections.protocol')}}</label>
        <select v-model="searchProto" class="form-control quarter-width">
          <option v-for="(p, pk) in protocols" v-bind:key="pk" :value="pk">{{pk | uppercase}}</option>
        </select>
      </div>

      <div class="mg-bottom-10">
        <label class="control-label">{{$t('connections.state')}}</label>
        <select
          :disabled="searchProto == 'udp' || searchProto == 'icmp'"
          v-model="searchState"
          class="form-control quarter-width"
        >
          <option v-for="s in protocols[searchProto]" v-bind:key="s" :value="s">{{s | uppercase}}</option>
        </select>
      </div>

      <div class="mg-bottom-10">
        <label class="control-label">{{$t('connections.auto_refresh')}}</label>
        <input class="form-control adjust-check" type="checkbox" v-model="autoRefresh">
      </div>

      <h2 class="right mg-top-5 normal">{{filteredConnections.length}}</h2>

      <form v-if="connections.length > 0" role="form" class="search-pf has-button search">
        <div class="form-group has-clear">
          <div class="search-pf-input-group">
            <label class="sr-only">Search</label>
            <input
              v-focus
              type="search"
              v-model="searchString"
              class="form-control input-lg"
              :placeholder="$t('search')+'...'"
              @keyup="highlight"
              id="pf-search-list"
            >
          </div>
        </div>
        <div class="form-group">
          <button
            class="btn btn-danger btn-lg mg-left-10"
            type="button"
            data-toggle="modal"
            data-target="#deleteConnectionsModal"
          >{{$t('connections.flush')}}</button>
        </div>
      </form>

      <ul
        v-if="connections.length > 0 && view.isLoaded"
        class="list-group list-view-pf list-view-pf-view no-mg-top mg-top-10"
      >
        <li :class="['list-group-item']" v-for="c in filteredConnections" v-bind:key="c">
          <div class="list-view-pf-actions">
            <button @click="openDeleteConnection(c)" :class="['btn btn-danger']">
              <span :class="['fa', 'fa-times', 'span-right-margin']"></span>
              {{$t('delete')}}
            </button>
          </div>
          <div class="list-view-pf-main-info small-list">
            <div class="list-view-pf-body">
              <div class="list-view-pf-description">
                <div class="list-group-item-heading adjust-heading">
                  <span class="col-sm-6 mg-right-10">
                    <span>{{c.src}}</span>
                    <span class="gray">:{{c.sport}}</span>
                  </span>
                  <span class="fa fa-arrow-right mg-right-10"></span>
                  <span>
                    <span>{{c.dst}}</span>
                    <span class="gray">:{{c.dport}}</span>
                  </span>
                </div>
              </div>
              <div class="list-view-pf-additional-info">
                <div v-show="c.status" class="list-view-pf-additional-info-item col-sm-4">
                  <span class="fa fa-clock-o"></span>
                  <strong>{{c.timeout}}s</strong>
                  TIMEOUT
                </div>
                <div class="list-view-pf-additional-info-item col-sm-3">
                  <span class="fa fa-crosshairs"></span>
                  <strong>{{c.mark}}</strong>
                  MARK
                </div>
                <div v-show="c.nat" class="list-view-pf-additional-info-item col-sm-3">
                  <span class="pficon pficon-route"></span>
                  <strong>{{handleNAT(c)}}</strong>
                  NAT
                </div>
              </div>
            </div>
          </div>
        </li>
      </ul>
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

export default {
  name: "Connections",
  mounted() {
    this.getConnections();
    this.getProtocols();
  },
  beforeRouteLeave(to, from, next) {
    $(".modal").modal("hide");
    clearInterval(this.pollingIntervalId);
    next();
  },
  data() {
    return {
      view: {
        isLoaded: false
      },
      connections: [],
      protocols: {},
      searchString: "",
      highlightInstance: null,
      searchProto: "tcp",
      searchState: "ESTABLISHED",
      autoRefresh: false,
      currentConnection: {},
      pollingIntervalId: 0
    };
  },
  watch: {
    autoRefresh: function(val) {
      if (val) {
        // start polling
        var context = this;
        context.getConnections();
        context.pollingIntervalId = setInterval(function() {
          context.getConnections();
        }, 2500);
      } else {
        // stop polling
        clearInterval(this.pollingIntervalId);
      }
    }
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
    highlight() {
      if (!this.highlightInstance) {
        this.highlightInstance = new Mark("div.pf-container");
      }
      var options = {
        element: "span",
        className: "highlight-mark",
        accuracy: "partially"
      };
      this.highlightInstance.unmark(options);
      this.highlightInstance.mark(this.searchString.toLowerCase(), options);
    },
    handleNAT(c) {
      if (c.dst != c.src_reply) {
        return c.src_reply;
      }
      if (c.src != c.dst_reply) {
        return c.dst_reply;
      }
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
    getConnections() {
      var context = this;

      context.view.isLoaded = false;
      nethserver.exec(
        ["nethserver-firewall-base/connections/read"],
        {
          action: "conntrack",
          protocol: context.searchProto,
          state: context.searchState
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
        },
        function(error) {
          console.error(error);
          context.view.isLoaded = true;
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
</style>