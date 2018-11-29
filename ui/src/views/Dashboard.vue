<template>
  <div>
    <h2>{{$t('dashboard.title')}}</h2>
    <div v-if="!view.isLoaded" class="spinner spinner-lg view-spinner"></div>
    <div id="network-graph"></div>
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
  },
  data() {
    return {
      view: {
        isLoaded: false
      },
      nodes: [
        {
          id: 0,
          label: "Firewall",
          group: "source",
          level: 2
        }
      ],
      edges: [
        { from: "green", to: 0 },
        { from: "blue", to: 0 },
        { from: "orange", to: 0 },
        { from: "other", to: 0, dashes: true },
        { from: "empty", to: 0, dashes: true },
        { from: "free", to: 0, dashes: true },
        { from: "missing", to: 0, dashes: true },
        { from: 0, to: "red" }
      ],
      options: {
        nodes: {
          shape: "box",
          size: 20,
          font: {
            size: 25,
            align: "left",
            color: "white",
            face: "Monospace"
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
          greenInt: {
            color: "#3f9c35"
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
          redInt: {
            color: "#cc0000"
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
          blueInt: {
            color: "#0088ce"
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
          orangeInt: {
            color: "#ec7a08"
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
          otherInt: {
            color: "#703fec"
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
      context.view.isLoaded = false;
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
          context.view.isLoaded = true;

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
                  eth.name +
                  "\nIP:   " +
                  (eth.ipaddr ||
                    (success.status[eth.name] &&
                      success.status[eth.name].ippaddr) ||
                    "-") +
                  "\nMASK: " +
                  (eth.netmask ||
                    (success.status[eth.name] &&
                      success.status[eth.name].netmask) ||
                    "-") +
                  "\nGW:   " +
                  (eth.gateway ||
                    (success.status[eth.name] &&
                      success.status[eth.name].gateway) ||
                    "-"),
                group: role + "Int",
                level: context.levelMap(role, false)
              });
              context.edges.push({
                from: eth.name,
                to: role,
                dashes: role == "other" ? true : false
              });
            }
          }

          callback ? callback() : null;
        },
        function(error) {
          console.error(error);
        }
      );
    }
  }
};
</script>

<style>
#network-graph {
  width: 100%;
  border-bottom: 1px solid #d1d1d1;
}
</style>