<template>
  <div>
    <h2>{{$t('snat.title')}}</h2>

    <div v-if="!view.isLoaded" class="spinner spinner-lg view-spinner"></div>

    <h3 v-if="view.isLoaded">{{$t('list')}}</h3>
    <div v-if="view.isLoaded" class="list-group list-view-pf list-view-pf-view no-mg-top">
      <div class="list-group-item" v-for="s in snList" v-bind:key="s">
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
                <span class="pficon pficon-screen panel-icon"></span>
                <select class="combobox form-control" v-model="s.FwObjectNat">
                  <option>-</option>
                  <option
                    v-for="h in hosts"
                    v-bind:key="h"
                    :value="h.name"
                  >{{h.name}} | {{h.IpAddress}}</option>
                </select>
                <span class="mg-left-5 info-desc-local">{{$t('snat.local_host')}}</span>
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
    this.getSN();
    this.getHosts();
  },
  beforeRouteLeave(to, from, next) {
    $(".modal").modal("hide");
    next();
  },
  data() {
    return {
      view: {
        isLoaded: false
      },
      snList: [],
      hosts: []
    };
  },
  methods: {
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
            context.hosts = success.hosts;
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
            context.snList = success.aliases;

            for (var s in context.snList) {
              context.snList[s].isLoading = false;
              context.snList[s].FwObjectNat = context.snList[s].FwObjectNat
                ? context.snList[s].FwObjectNat.split(";")[1]
                : "-";
            }

            context.view.isLoaded = true;
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

      var snObj = {
        action: "update",
        FwObjectNat: s.FwObjectNat == "-" ? "" : "host;" + s.FwObjectNat,
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
              // get interfaces
              context.getSN();
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
</style>