<template>
  <div>
    <h2>{{$t('snat.title')}}</h2>

    <div v-if="!view.isLoaded" class="spinner spinner-lg view-spinner"></div>
    <div v-if="snList.length == 0 && view.isLoaded" class="blank-slate-pf white">
      <div class="blank-slate-pf-icon">
        <span class="fa fa-random"></span>
      </div>
      <h1>{{$t('snat.no_aliases_found')}}</h1>
      <p>{{$t('snat.no_aliases_found_text')}}.</p>
      <div class="blank-slate-pf-main-action">
        <a
          target="_blank"
          href="/nethserver#/network"
          class="btn btn-primary btn-lg"
        >{{$t('snat.go_to_network')}}</a>
      </div>
    </div>

    <h3 v-if="snList.length > 0 && view.isLoaded">{{$t('list')}}</h3>
    <div
      v-if="snList.length > 0 && view.isLoaded"
      class="list-group list-view-pf list-view-pf-view no-mg-top"
    >
      <div class="list-group-item" v-for="(s,k) in snList" v-bind:key="k">
        <div class="list-view-pf-actions">
          <div v-if="s.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
          <button :disabled="s.isLoading" @click="saveSN(s)" class="btn btn-default">
            <span class="fa fa-check span-right-margin"></span>
            {{$t('save')}}
          </button>
        </div>
        <div class="list-view-pf-main-info">
          <div class="list-view-pf-left">
            <span class="fa fa-random list-view-pf-icon-sm"></span>
          </div>
          <div class="list-view-pf-body">
            <div class="list-view-pf-description">
              <div class="list-group-item-heading">
                <span>{{s.ipaddr}}</span>
              </div>
              <div class="list-group-item-text">
                <span>{{s.name}}</span>
              </div>
            </div>
            <div class="list-view-pf-additional-info">
              <div class="list-view-pf-additional-info-item">
                <span class="fa fa-cubes"></span>
                <div>
                  <select
                    class="combobox form-control"
                    v-model="objectToAdd"
                    @change="addObjectSnat(s, objectToAdd)"
                  >
                    <option
                      v-for="(h, k) in firewallObjects"
                      v-bind:key="k"
                      :value="h"
                    >{{h.name}} ({{$t('objects.' + h.type)}}) | {{h.textValue}}</option>
                  </select>
                  <div>
                    <span v-if="!s.firewallObjects.length" class="help-block float-left">
                      {{$t('snat.no_object')}}
                    </span>
                    <ul v-else class="list-inline compact mg-top-10">
                      <li v-for="(i, ki) in s.firewallObjects" v-bind:key="ki" class="selected-fw-object">
                        <span class="label label-info">
                          {{i.name}} ({{$t('objects.' + i.type)}})
                          <a @click="removeObjectSnat(s, ki)" class="remove-item-inline">
                            <span class="fa fa-times"></span>
                          </a>
                        </span>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "sNAT",
  mounted() {
    this.getFirewallObjects();

    var context = this;
    context.$parent.$on("changes-applied", function() {
      context.getFirewallObjects();
    });
  },
  beforeRouteLeave(to, from, next) {
    this.$parent.$off("changes-applied");
    $(".modal").modal("hide");
    next();
  },
  data() {
    return {
      view: {
        isLoaded: false
      },
      snList: [],
      hosts: [],
      hostGroups: [],
      cidrSubnets: [],
      ipRanges: [],
      maxLengthMembers: 40,
      objectToAdd: {},
      loaded: {
        hosts: false,
        cidrSubnets: false,
        ipRanges: false,
      },
    };
  },
  computed: {
    firewallObjects() {
      return this.hosts.concat(this.hostGroups, this.cidrSubnets, this.ipRanges);
    },
    firewallObjectsLoaded() {
      return this.loaded.hosts && this.loaded.cidrSubnets && this.loaded.ipRanges;
    }
  },
  watch: {
    firewallObjectsLoaded: function(loaded) {
      if (loaded) {
        this.getSN();
      }
    },
  },
  methods: {
    getFirewallObjects() {
      this.fwObjects = [];
      this.loaded = {hosts: false, cidrSubnets: false, ipRanges: false};
      this.getHosts();
      this.getCidrSubnets();
      this.getIpRanges();
    },
    addObjectSnat(snat, fwObj) {
      if (fwObj && fwObj.name) {
        if (!snat.firewallObjects.includes(fwObj)) {
          snat.firewallObjects.push(fwObj);
        }
      }
      this.objectToAdd = {};
    },
    removeObjectSnat(snat, index) {
      snat.firewallObjects.splice(index, 1);
    },
    getHosts() {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/objects/read"],
        {
          action: "hosts"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
            context.hosts = success.hosts.map( h => {
              return {name: h.name, type: 'host', textValue: h.IpAddress}
            });
            context.getHostGroups();
          } catch (e) {
            console.error(e);
          }
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getHostGroups() {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/objects/read"],
        {
          action: "host-groups"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
            context.hostGroups = success['host-groups'].map( h => {
              let membersText =  h.Members.join(", ");

              if (membersText.length > context.maxLengthMembers) {
                membersText = membersText.slice(0, context.maxLengthMembers) + "...";
              }
              return {name: h.name, type: 'host-group', textValue: membersText}
            });
            context.loaded.hosts = true;
          } catch (e) {
            console.error(e);
          }
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getCidrSubnets() {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/objects/read"],
        {
          action: "cidr-subs"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
            context.cidrSubnets = success['cidr-subs'].map( h => {
              return {name: h.name, type: 'cidr', textValue: h.Address}
            });
            context.loaded.cidrSubnets = true;
          } catch (e) {
            console.error(e);
          }
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getIpRanges() {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/objects/read"],
        {
          action: "ip-ranges"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
            context.ipRanges = success['ip-ranges'].map( h => {
              return {name: h.name, type: 'iprange', textValue: h.Start + " - " + h.End}
            });
            context.loaded.ipRanges = true;
          } catch (e) {
            console.error(e);
          }
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getSN() {
      var context = this;

      context.view.isLoaded = false;
      nethserver.exec(
        ["nethserver-firewall-base/snat/read"],
        {
          action: "list"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
            let snList = success.aliases;

            for (let snat of snList) {
              snat.isLoading = false;
              snat.firewallObjects = [];

              if (snat.FwObjectNat) {
                const fwObjects = snat.FwObjectNat.split(",");

                for (let fwObject of fwObjects) {
                  const [objType, objName] = fwObject.split(";");

                  switch (objType) {
                    case "host":
                      const host = context.hosts.find(h => h.name == objName);
                      snat.firewallObjects.push(host);
                      break;
                    case "host-group":
                      const hostGroup = context.hostGroups.find(hg => hg.name == objName);
                      snat.firewallObjects.push(hostGroup);
                      break;
                    case "cidr":
                      const cidr = context.cidrSubnets.find(c => c.name == objName);
                      snat.firewallObjects.push(cidr);
                      break;
                    case "iprange":
                      const ipRange = context.ipRanges.find(ir => ir.name == objName);
                      snat.firewallObjects.push(ipRange);
                      break;
                  }
                }
              }
            }
            context.snList = snList;
            context.view.isLoaded = true;
            context.$parent.getFirewallStatus();
          } catch (e) {
            console.error(e);
            context.view.isLoaded = true;
          }
        },
        function(error) {
          console.error(error);
        }
      );
    },
    saveSN(s) {
      var context = this;
      let fwObjectNatList = []

      for (let fwObject of s.firewallObjects) {
        fwObjectNatList.push(fwObject.type + ";" + fwObject.name);
      }

      var snObj = {
        action: "update",
        FwObjectNat: fwObjectNatList.join(),
        name: s.name
      };

      s.isLoading = true;
      context.$forceUpdate();
      nethserver.exec(
        ["nethserver-firewall-base/snat/validate"],
        snObj,
        null,
        function(success) {
          s.isLoading = false;

          // notifications
          nethserver.notifications.success = context.$i18n.t(
            "snat.source_nat_configured_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "snat.source_nat_configured_error"
          );

          // update values
          nethserver.exec(
            ["nethserver-firewall-base/snat/update"],
            snObj,
            function(stream) {
              console.info("firewall-base-update", stream);
            },
            function(success) {
              // reload data
              context.getFirewallObjects();
            },
            function(error, data) {
              console.error(error);
            }
          );
        },
        function(error, data) {
          s.isLoading = false;
          console.error(error, data);
        }
      );
    }
  }
};
</script>

<style>
.info-desc-local {
  min-width: 75px;
}

.small-icon {
  font-size: 14px !important;
  height: 25px !important;
  width: 25px !important;
}

.small-icon::before {
  line-height: 20px !important;
}

.flex-50 {
  flex: 1 0 calc(50% - 20px) !important;
}

.selected-fw-object {
  margin-bottom: 7px;
  float: left;
}

.float-left {
  float: left;
}
</style>