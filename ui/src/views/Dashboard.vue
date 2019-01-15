<template>
  <div>
    <h2>{{$t('dashboard.title')}}</h2>

    <h3>{{$t('dashboard.topology')}}</h3>
    <div v-if="!view.graphLoaded" class="spinner spinner-lg view-spinner"></div>
    <div id="network-graph" class="divider"></div>

    <h3>{{$t('dashboard.providers')}}</h3>
    <div v-if="!view.statsLoaded" class="spinner spinner-lg view-spinner"></div>
    <div class="row divider row-status" v-if="view.statsLoaded">
      <div
        v-for="(s,i) in providers"
        :key="i"
        class="stats-container col-xs-12 col-sm-4 col-md-3 col-lg-2"
      >
        <span
          :class="['card-pf-utilization-card-details-count stats-count', s ? 'pficon pficon-ok' : 'pficon-error-circle-o']"
          data-toggle="tooltip"
          data-placement="top"
          :title="$t('dashboard.status')+': '+ (s ? $t('up') : $t('down'))"
        ></span>
        <span class="card-pf-utilization-card-details-description stats-description">
          <span class="card-pf-utilization-card-details-line-2 stats-text">{{i}}</span>
        </span>
      </div>
      <div class="stats-container" v-if="!providers">{{$t('dashboard.no_info_found')}}</div>
    </div>

    <h3>{{$t('dashboard.statistics')}}</h3>
    <div v-if="!view.statsLoaded" class="spinner spinner-lg view-spinner"></div>
    <div class="row divider row-stat" v-if="view.statsLoaded">
      <div class="row-inline-block">
        <div
          v-for="(s,i) in stats"
          :key="i"
          v-if="i != 'objects'"
          class="stats-container col-xs-12 col-sm-4 col-md-3 col-lg-2"
        >
          <span class="card-pf-utilization-card-details-count stats-count">{{s}}</span>
          <span class="card-pf-utilization-card-details-description stats-description">
            <span class="card-pf-utilization-card-details-line-2 stats-text">{{$t('dashboard.'+i)}}</span>
          </span>
        </div>
        <div class="stats-container" v-if="!stats">{{$t('dashboard.no_info_found')}}</div>
      </div>
      <h3>{{$t('dashboard.objects')}}</h3>
      <div class="row-inline-block">
        <div
          v-for="(o,i) in stats.objects"
          :key="i"
          class="stats-container col-xs-12 col-sm-4 col-md-3 col-lg-2"
        >
          <span class="card-pf-utilization-card-details-count stats-count">{{o}}</span>
          <span class="card-pf-utilization-card-details-description stats-description">
            <span class="card-pf-utilization-card-details-line-2 stats-text">{{$t('dashboard.'+i)}}</span>
          </span>
        </div>
        <div class="stats-container" v-if="!stats.objects">{{$t('dashboard.no_info_found')}}</div>
      </div>
    </div>

    <h3>{{$t('dashboard.services')}}</h3>
    <div v-if="!view.statsLoaded" class="spinner spinner-lg view-spinner"></div>
    <div class="row divider row-status" v-if="view.statsLoaded">
      <div
        v-for="(s,i) in services"
        :key="i"
        class="stats-container col-xs-12 col-sm-4 col-md-3 col-lg-2"
      >
        <span
          :class="['card-pf-utilization-card-details-count stats-count', s ? 'pficon pficon-ok' : 'pficon-error-circle-o']"
          data-toggle="tooltip"
          data-placement="top"
          :title="$t('dashboard.status')+': '+ (s ? $t('enabled') : $t('disabled'))"
        ></span>
        <span class="card-pf-utilization-card-details-description stats-description">
          <span class="card-pf-utilization-card-details-line-2 stats-text">{{$t('dashboard.'+i)}}</span>
        </span>
      </div>
      <div class="stats-container" v-if="!services">{{$t('dashboard.no_info_found')}}</div>
    </div>

    <h3>{{$t('dashboard.connections')}}</h3>
    <div v-if="!view.statsLoaded" class="spinner spinner-lg view-spinner"></div>
    <div class="row row-stat" v-if="view.statsLoaded">
      <div
        v-for="(s,i) in connections"
        :key="i"
        v-if="i != 'total'"
        class="stats-container col-xs-12 col-sm-4 col-md-3 col-lg-2"
      >
        <span class="card-pf-utilization-card-details-count stats-count">{{s}}</span>
        <span class="card-pf-utilization-card-details-description stats-description">
          <span class="card-pf-utilization-card-details-line-2 stats-text">{{$t('dashboard.'+i)}}</span>
        </span>
      </div>
      <div class="stats-container" v-if="!connections">{{$t('dashboard.no_info_found')}}</div>
    </div>
  </div>
</template>

<script>
import vis from "vis";

export default {
  name: "Dashboard",
  mounted() {
    $("#network-graph").height(window.innerHeight / 2);

    var context = this;
    context.getInterfaces(function() {
      context.initGraph();
    });
    context.getStats();
  },
  data() {
    return {
      view: {
        graphLoaded: false,
        statsLoaded: false
      },
      stats: {},
      services: {},
      providers: {},
      connections: {},
      nodes: [
        {
          id: 0,
          label: "Firewall",
          group: "source",
          level: 2
        }
      ],
      edges: [
        {
          from: "green",
          to: 0
        },
        {
          from: "blue",
          to: 0
        },
        {
          from: "orange",
          to: 0
        },
        {
          from: "other",
          to: 0,
          dashes: true
        },
        {
          from: "empty",
          to: 0,
          dashes: true
        },
        {
          from: "free",
          dashes: true
        },
        {
          from: "missing",
          dashes: true
        },
        {
          from: 0,
          to: "red"
        }
      ],
      options: {
        nodes: {
          shape: "box",
          font: {
            multi: "html",
            size: 25,
            align: "left",
            color: "white"
          },
          borderWidth: 2,
          color: "#363636"
        },
        layout: {
          hierarchical: {
            direction: "LR",
            levelSeparation: 400
          }
        },
        edges: {
          width: 2,
          arrows: {
            to: {
              enabled: true,
              type: "circle"
            }
          }
        },
        groups: {
          green: {
            shape: "icon",
            icon: {
              face: "FontAwesome",
              code: "\uf041",
              size: 75,
              color: "#3f9c35"
            },
            font: {
              color: "#363636"
            },
            margin: {
              top: 20
            }
          },
          greenIntUP: {
            color: {
              background: "#3f9c35",
              border: "#2d7623",
              highlight: { background: "#6ec664", border: "#2d7623" }
            }
          },
          greenIntDOWN: {
            color: { background: "#363636", border: "#3f9c35" }
          },
          red: {
            shape: "icon",
            icon: {
              face: "FontAwesome",
              code: "\uf0ac",
              size: 75,
              color: "#cc0000"
            },
            font: {
              color: "#363636"
            },
            margin: {
              top: 20
            }
          },
          redIntUP: {
            color: {
              background: "#cc0000",
              border: "#8b0000",
              highlight: { background: "#470000", border: "#8b0000" }
            }
          },
          redIntDOWN: {
            color: { background: "#363636", border: "#cc0000" }
          },
          blue: {
            shape: "icon",
            icon: {
              face: "FontAwesome",
              code: "\uf0c0",
              size: 75,
              color: "#0088ce"
            },
            font: {
              color: "#363636"
            },
            margin: {
              top: 20
            }
          },
          blueIntUP: {
            color: {
              background: "#0088ce",
              border: "#008bad",
              highlight: { background: "#39a5dc", border: "#008bad" }
            }
          },
          blueIntDOWN: {
            color: { background: "#363636", border: "#0088ce" }
          },
          orange: {
            shape: "icon",
            icon: {
              face: "FontAwesome",
              code: "\uf132",
              size: 75,
              color: "#ec7a08"
            },
            font: {
              color: "#363636"
            },
            margin: {
              top: 20
            }
          },
          orangeIntUP: {
            color: {
              background: "#ec7a08",
              border: "#b35c00",
              highlight: { background: "#f39d3c", border: "#b35c00" }
            }
          },
          orangeIntDOWN: {
            color: { background: "#363636", border: "#ec7a08" }
          },
          other: {
            shape: "icon",
            icon: {
              face: "FontAwesome",
              code: "\uf128",
              size: 75,
              color: "#703fec"
            },
            font: {
              color: "#363636"
            },
            margin: {
              top: 20
            }
          },
          otherIntUP: {
            color: {
              background: "#703fec",
              border: "#582fc0",
              highlight: { background: "#a18fff", border: "#582fc0" }
            }
          },
          otherIntDOWN: {
            color: { background: "#363636", border: "#703fec" }
          },
          empty: {
            shape: "icon",
            icon: {
              face: "FontAwesome",
              code: "\uf128",
              size: 75,
              color: "#703fec"
            },
            font: {
              color: "#363636"
            },
            margin: {
              top: 20
            }
          },
          emptyInt: {
            color: "#703fec"
          },
          missing: {
            shape: "icon",
            icon: {
              face: "FontAwesome",
              code: "\uf128",
              size: 75,
              color: "#703fec"
            },
            font: {
              color: "#363636"
            },
            margin: {
              top: 20
            }
          },
          missingInt: {
            color: "#703fec"
          },
          free: {
            shape: "icon",
            icon: {
              face: "FontAwesome",
              code: "\uf128",
              size: 75,
              color: "#703fec"
            },
            font: {
              color: "#363636"
            },
            margin: {
              top: 20
            }
          },
          freeInt: {
            color: "#703fec"
          },
          source: {
            shape: "icon",
            icon: {
              face: "FontAwesome",
              code: "\uf06d",
              size: 100,
              color: "#363636"
            },
            font: {
              color: "#363636"
            },
            margin: {
              top: 20
            }
          }
        },
        interaction: {
          zoomView: false
        }
      }
    };
  },
  methods: {
    levelMap(role, master) {
      switch (role) {
        case "green":
          return master ? 1 : 0;
          break;

        case "blue":
          return master ? 1 : 0;
          break;

        case "orange":
          return master ? 1 : 0;
          break;

        case "red":
          return master ? 3 : 4;
          break;

        case "other":
        case "empty":
        case "free":
        case "missing":
          return master ? 1 : 0;
          break;

        default:
          return 2;
          break;
      }
    },
    initGraph() {
      var container = document.getElementById("network-graph");
      var network = new vis.Network(
        container,
        {
          nodes: this.nodes,
          edges: this.edges
        },
        this.options
      );
    },
    getInterfaces(callback) {
      var context = this;
      context.view.graphLoaded = false;
      nethserver.exec(
        ["system-network/read"],
        {
          action: "list"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.view.graphLoaded = true;

          for (var role in success.configuration) {
            if (success.configuration[role].length > 0)
              context.nodes.push({
                id: role,
                label: context.$i18n.t("dashboard." + role),
                group: role,
                level: context.levelMap(role, true)
              });
            for (var i in success.configuration[role]) {
              var eth = success.configuration[role][i];
              context.nodes.push({
                id: eth.name,
                label:
                  "<b>" +
                  eth.name +
                  "</b>" +
                  (eth.nslabel && eth.nslabel.length > 0
                    ? " (" + eth.nslabel + ")"
                    : "") +
                  "\n<code>CIDR:   " +
                  (eth.cidr ||
                    (success.status[eth.name] &&
                      success.status[eth.name].cidr) ||
                    "-") +
                  "\n<code>LINK:   " +
                  (success.status[eth.name] &&
                  success.status[eth.name].link == 1
                    ? "UP"
                    : "DOWN"),
                group:
                  role +
                  "Int" +
                  (success.status[eth.name].link == 1 ? "UP" : "DOWN"),
                level: context.levelMap(role, false)
              });
              context.edges.push({
                from: eth.name,
                to: role,
                dashes:
                  role == "other" ||
                  role == "empty" ||
                  role == "free" ||
                  role == "missing"
                    ? true
                    : false
              });
            }
          }

          callback ? callback() : null;
        },
        function(error) {
          console.error(error);
          context.view.graphLoaded = true;
        }
      );
    },
    getStats() {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/read"],
        {
          action: "info"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.stats =
            success.statistics && Object.keys(success.statistics).length > 0
              ? success.statistics
              : false;
          context.providers =
            success.providers && Object.keys(success.providers).length > 0
              ? success.providers
              : false;
          context.services =
            success.services && Object.keys(success.services).length > 0
              ? success.services
              : false;
          context.connections =
            success.connections && Object.keys(success.connections).length > 0
              ? success.connections
              : false;

          context.view.statsLoaded = true;

          setTimeout(function() {
            $('[data-toggle="tooltip"]').tooltip();
          }, 250);
        },
        function(error) {
          console.error(error);
          context.view.statsLoaded = true;
        }
      );
    }
  }
};
</script>

<style>
#network-graph {
  width: 100%;
}

.divider {
  border-bottom: 1px solid #d1d1d1;
}

.stats-container {
  padding: 20px !important;
  border-width: initial !important;
  border-style: none !important;
  border-color: initial !important;
  border-image: initial !important;
}

.stats-text {
  margin-top: 10px !important;
  display: block;
}

.stats-description {
  float: left;
  line-height: 1;
}

.stats-count {
  font-size: 26px;
  font-weight: 300;
  margin-right: 10px;
  float: left;
  line-height: 1;
}

.row-stat {
  margin-left: 0px;
  margin-right: 0px;
}

.row-status {
  margin-left: -5px;
  margin-right: 0px;
}

.row-inline-block {
  display: inline-block;
  width: 100%;
}
</style>