<template>
  <div>
    <h2>{{$t('snat.title')}}</h2>

    <!-- error notification -->
    <div v-if="error.message" class="toast-pf alert alert-warning toast-pf-top-right alert-dismissable">
      <button type="button" class="close" aria-hidden="true" @click="clearError()">
        <span class="pficon pficon-close"></span>
      </button>
      <span class="pficon pficon-warning-triangle-o"></span>
      <div>
        <span>{{$t(error.message)}}</span><strong v-if="error.param">: {{error.param}}</strong>
      </div>
    </div>

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
      class="list-group list-view-pf list-view-pf-view no-mg-top snat-container"
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
            <!-- <span class="fa fa-random list-view-pf-icon-sm"></span> -->
            <span class="fa fa-cubes list-view-pf-icon-sm"></span>
          </div>
          <div class="list-view-pf-body">
            <div class="list-view-pf-description">
              <!-- <div class="list-group-item-heading">
              </div> -->
              <div class="fw-object-continer">
                <label>{{ $t('snat.source_objects') }}</label>
                <suggestions
                  v-model="s.selectedFwObject.name"
                  :options="autoOptions"
                  :onInputChange="filterFwObjAuto"
                  :onItemSelected="selectFwObjAuto"
                  @keydown.native="setCurrentSnat(s)"
                >
                  <div slot="item" slot-scope="props" class="single-item">
                    <span>
                      {{props.item.name}}
                      <span
                        v-show="props.item.IpAddress || props.item.Address"
                        class="gray"
                      >({{ props.item.IpAddress || props.item.Address }})</span>
                      <i class="mg-left-5">{{props.item.Description}}</i>
                      <b class="mg-left-5">{{props.item.type | capitalize}}</b>
                    </span>
                  </div>
                </suggestions>
                <div>
                  <span v-if="!s.firewallObjects.length" class="help-block float-left">
                    {{$t('snat.no_object')}}
                  </span>
                  <ul v-else class="list-inline compact mg-top-10">
                    <li v-for="(i, ki) in s.firewallObjects" v-bind:key="ki" class="selected-fw-object">
                      <span class="label label-info">
                        {{i.name}} ({{i.type | capitalize}})
                        <a @click="removeObjectSnat(s, ki)" class="remove-item-inline">
                          <span class="fa fa-times"></span>
                        </a>
                      </span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="list-view-pf-additional-info">
              <div class="list-view-pf-additional-info-item">
                <span class="list-group-item-text">{{ $t('snat.map_to') }}
                  <span class="fa fa-arrow-right mg-left-5"></span>
                </span>
              </div>
              <div class="list-view-pf-additional-info-item">
                <span class="list-group-item-text">{{s.ipaddr}}</span>
              </div>
              <div class="list-view-pf-additional-info-item">
                <span class="list-group-item-text">{{s.name}}</span>
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
      autoOptions: {
        inputClass: "form-control",
      },
      currentSnat: null,
      loaded: {
        hosts: false,
        cidrSubnets: false,
        ipRanges: false,
      },
      error: {
        message: "",
        param: "",
      }
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
            context.hosts = success.hosts.map(function(i) {
              i.type = context.$i18n.t("objects.host");
              i.typeId = "host";
              return i;
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
            context.hostGroups = success['host-groups'].map(function(i) {
              i.type = context.$i18n.t("objects.host_group");
              i.typeId = "host-group";
              return i;
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
            context.cidrSubnets = success['cidr-subs'].map(function(i) {
              i.type = context.$i18n.t("objects.cidr_sub");
              i.typeId = "cidr";
              return i;
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
            context.ipRanges = success['ip-ranges'].map(function(i) {
              i.type = context.$i18n.t("objects.ip_range");
              i.typeId = "iprange";
              return i;
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
    setCurrentSnat(snat) {
      this.currentSnat = snat;
    },
    filterFwObjAuto(query) {
      this.currentSnat.selectedFwObject.name = null;
      this.currentSnat.selectedFwObject.detail = null;

      if (query.trim().length === 0) {
        return [];
      }

      return this.firewallObjects.filter(function(service) {
        return (
          service.typeId.toLowerCase().includes(query.toLowerCase()) ||
          service.name.toLowerCase().includes(query.toLowerCase()) ||
          service.Description.toLowerCase().includes(query.toLowerCase()) ||
          (service.IpAddress &&
            service.IpAddress.toLowerCase().includes(query.toLowerCase()))
        );
      });
    },
    selectFwObjAuto(item) {
      this.currentSnat.selectedFwObject.name = "";

      this.currentSnat.selectedFwObject.detail = Object.assign({}, item);
      this.currentSnat.selectedFwObject.detail.type = this.currentSnat.selectedFwObject.detail.typeId;
      delete this.currentSnat.selectedFwObject.detail.typeId;

      if (!this.currentSnat.firewallObjects.includes(item)) {
        this.currentSnat.firewallObjects.push(item);
      }
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
              snat.selectedFwObject = { name: "", detail: ""};
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
    clearError() {
      this.error = {message: "", param: ""};
    },
    checkFwObjectsDuplicated(snat) {
      for (const fwObject of snat.firewallObjects) {
        for (const s of this.snList) {
          for (const otherFwObject of s.firewallObjects) {
            if (snat != s && fwObject.type == otherFwObject.type && fwObject.name == otherFwObject.name) {
              this.error = {message: "validation.snat_fw_object_duplicated", param: fwObject.name};
              return false;
            }
          }
        }
      }
      return true;
    },
    saveSN(s) {
      var context = this;
      let fwObjectNatList = []
      this.clearError();

      if (!context.checkFwObjectsDuplicated(s)) {
        return;
      }

      for (let fwObject of s.firewallObjects) {
        fwObjectNatList.push(fwObject.typeId + ";" + fwObject.name);
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

.snat-container {
  margin-bottom: 300px;
}

.fw-object-continer {
  flex-grow: 1;
  padding-right: 30px;
}
</style>