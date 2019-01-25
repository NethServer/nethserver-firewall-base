<template>
  <div>
    <h2>{{$t('port_forward.title')}}</h2>

    <div v-if="!view.isLoaded" class="spinner spinner-lg view-spinner"></div>

    <div v-if="!pfList && view.isLoaded" class="blank-slate-pf white">
      <div class="blank-slate-pf-icon">
        <span class="fa fa-exchange"></span>
      </div>
      <h1>{{$t('port_forward.no_pf_found')}}</h1>
      <p>{{$t('port_forward.no_pf_found_text')}}.</p>
      <div class="blank-slate-pf-main-action">
        <button
          @click="openCreatePF()"
          class="btn btn-primary btn-lg"
        >{{$t('port_forward.create_pf')}}</button>
      </div>
    </div>

    <div v-if="pfList && view.isLoaded">
      <h3>{{$t('actions')}}</h3>
      <button
        @click="openCreatePF()"
        class="btn btn-primary btn-lg"
      >{{$t('port_forward.create_pf')}}</button>
    </div>
    <div v-if="pfList && view.isLoaded">
      <h3>{{$t('list')}}</h3>

      <form v-if="pfList" role="form" class="search-pf has-button search">
        <div class="form-group has-clear">
          <div class="search-pf-input-group">
            <label class="sr-only">Search</label>
            <input
              type="search"
              v-model="searchString"
              class="form-control input-lg"
              :placeholder="$t('search')+'...'"
            >
          </div>
        </div>
      </form>

      <div v-for="(data,host) in filteredPF" v-bind:key="host">
        <div
          id="pf-list-simple-expansion"
          class="list-group list-view-pf list-view-pf-view wizard-pf-contents-title white mg-top-10"
        >
          <div class="list-group-item list-view-pf-expand-active no-shadow mg-bottom-10">
            <div
              :class="[data.info.type == 'ip' ? 'ip-type-header': '', 'list-group-item-header cursor-initial']"
            >
              <div class="list-view-pf-main-info small-list">
                <div class="list-view-pf-left">
                  <span
                    :class="[data.info.type == 'ip' ? 'pficon pficon-warning-triangle-o ip-type' : 'pficon pficon-container-node', 'list-view-pf-icon-sm small-icon']"
                  ></span>
                </div>
                <div class="list-view-pf-body">
                  <div class="list-view-pf-description">
                    <div class="list-group-item-heading flex-50">
                      {{$t('port_forward.destination')}}: {{host | parseObj}}
                      <span
                        class="gray"
                        v-if="data.info.IpAddress && data.info.IpAddress.length > 0"
                      >({{data.info.IpAddress}})</span>
                    </div>
                    <div
                      v-if="data.info.type == 'object'"
                      class="list-group-item-text"
                    >{{data.info.Description}}</div>
                    <div v-if="data.info.type == 'ip'" class="list-group-item-text">
                      {{$t('port_forward.no_host_found')}}.
                      <a
                        href="#"
                        data-toggle="modal"
                        data-target="#createHostModal"
                      >{{$t('create')}}</a>
                    </div>
                  </div>
                  <div class="list-view-pf-additional-info">
                    <div class="list-view-pf-additional-info-item">
                      <span class="fa fa-exchange"></span>
                      <strong>{{data.rules.length}}</strong>
                      {{$t('port_forward.forwards')}}
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="list-group-item-container container-fluid no-pd-bottom">
              <ul class="list-group no-border-top no-mg-bottom">
                <li class="list-group-item transparent">
                  <div class="col-sm-11">
                    <span class="col-sm-2">
                      <strong>{{$t('port_forward.origin_port')}}</strong>
                    </span>
                    <span class="col-sm-2">
                      <strong>{{$t('port_forward.destination_port')}}</strong>
                    </span>
                    <span class="col-sm-2">
                      <strong>{{$t('port_forward.protocol')}}</strong>
                    </span>
                    <span class="col-sm-2">
                      <strong>{{$t('port_forward.description')}}</strong>
                    </span>
                    <span class="col-sm-2">
                      <strong>{{$t('port_forward.wan_ip')}}</strong>
                    </span>
                    <span class="col-sm-2">
                      <strong>{{$t('port_forward.allow_only_short')}}</strong>
                    </span>
                  </div>
                  <span class="col-sm-1">
                    <strong>{{$t('actions')}}</strong>
                  </span>
                </li>
                <li v-for="p in data.rules" v-bind:key="p" class="list-group-item small-li">
                  <div class="col-sm-11">
                    <span class="col-sm-2">
                      {{p.Src || '-'}}
                      <span v-if="p.Service.length > 0">({{p.Service}})</span>
                    </span>
                    <span class="col-sm-2">{{p.Dst || '-'}}</span>
                    <span class="col-sm-2">{{p.Proto | uppercase}}</span>
                    <span class="col-sm-2">{{p.Description || '-'}}</span>
                    <span class="col-sm-2">{{p.OriDst || '-'}}</span>
                    <span class="col-sm-2">{{p.Allow || '-' | prettyNewLine}}</span>
                  </div>
                  <span class="col-sm-1">
                    <button
                      @click="openEditPF(p, host)"
                      class="btn btn-default btn-sm"
                    >{{$t('edit')}}</button>
                  </span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal" id="createPFModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{newPf.isEdit ? $t('port_forward.edit_pf') : $t('port_forward.create_pf')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="savePF()">
            <div class="modal-body">
              <div :class="['form-group', newPf.errors.Src.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-4 control-label"
                  for="textInput-modal-markup"
                >{{$t('port_forward.origin_port')}}</label>
                <div class="col-sm-8">
                  <input required type="number" v-model="newPf.Src" class="form-control">
                  <span v-if="newPf.errors.Src.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newPf.errors.Src.message)}}
                  </span>
                </div>
              </div>
              <div :class="['form-group', newPf.errors.DstHost.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-4 control-label"
                  for="textInput-modal-markup"
                >{{$t('port_forward.destination_host')}}</label>
                <div class="col-sm-6">
                  <select required type="text" v-model="newPf.DstHost" class="form-control">
                    <option>-</option>
                    <option
                      v-for="h in hosts"
                      v-bind:key="h"
                      :value="h.name"
                    >{{h.name}} | {{h.IpAddress}}</option>
                  </select>
                  <span v-if="newPf.errors.DstHost.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newPf.errors.DstHost.message)}}
                  </span>
                </div>
                <div class="col-sm-2">
                  <button class="btn btn-primary">
                    <span class="fa fa-plus"></span>
                  </button>
                </div>
              </div>
              <div :class="['form-group', newPf.errors.Dst.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-4 control-label"
                  for="textInput-modal-markup"
                >{{$t('port_forward.destination_port')}}</label>
                <div class="col-sm-8">
                  <input
                    v-model="newPf.Dst"
                    :placeholder="newPf.Src"
                    onfocus="this.type='number';"
                    class="form-control"
                  >
                  <span v-if="newPf.errors.Dst.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newPf.errors.Dst.message)}}
                  </span>
                </div>
              </div>
              <div :class="['form-group', newPf.errors.Proto.hasError ? 'has-error' : '']">
                <label class="col-sm-4 control-label">{{$t('port_forward.protocol')}}</label>
                <div class="col-sm-8">
                  <input
                    id="protocol-1"
                    class="col-sm-2 col-xs-2"
                    type="radio"
                    v-model="newPf.Proto"
                    value="tcp"
                  >
                  <label
                    class="col-sm-10 col-xs-10 control-label text-align-left"
                    for="protocol-1"
                  >{{$t('port_forward.tcp')}}</label>
                  <input
                    id="protocol-2"
                    class="col-sm-2 col-xs-2"
                    type="radio"
                    v-model="newPf.Proto"
                    value="udp"
                  >
                  <label
                    class="col-sm-10 col-xs-10 control-label text-align-left"
                    for="protocol-2"
                  >{{$t('port_forward.udp')}}</label>
                  <input
                    id="protocol-3"
                    class="col-sm-2 col-xs-2"
                    type="radio"
                    v-model="newPf.Proto"
                    value="tcp,udp"
                  >
                  <label
                    class="col-sm-10 col-xs-10 control-label text-align-left"
                    for="protocol-3"
                  >{{$t('port_forward.tcp_udp')}}</label>
                  <!-- <input
                    id="protocol-3"
                    class="col-sm-2 col-xs-2"
                    type="radio"
                    v-model="newPf.Proto"
                    value="other"
                  >
                  <label
                    class="col-sm-10 col-xs-10 control-label text-align-left"
                    for="protocol-3"
                  >{{$t('port_forward.other')}}</label>

                  <label class="col-sm-2"></label>
                  <input class="col-sm-5" v-model="newPf.ProtoOther">-->
                </div>
              </div>
              <div :class="['form-group', newPf.errors.Description.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-4 control-label"
                  for="textInput-modal-markup"
                >{{$t('port_forward.description')}}</label>
                <div class="col-sm-8">
                  <input type="text" v-model="newPf.Description" class="form-control">
                  <span v-if="newPf.errors.Description.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newPf.errors.Description.message)}}
                  </span>
                </div>
              </div>
              <div class="form-group">
                <label
                  class="col-sm-4 control-label"
                  for="textInput-modal-markup"
                >{{$t('advanced_mode')}}</label>
                <div class="col-sm-8">
                  <toggle-button
                    class="min-toggle"
                    :width="40"
                    :height="20"
                    :color="{checked: '#0088ce', unchecked: '#bbbbbb'}"
                    :value="newPf.advanced"
                    :sync="true"
                    @change="toggleAdvancedMode()"
                  />
                </div>
              </div>
              <div
                v-show="newPf.advanced"
                :class="['form-group', newPf.errors.OriDst.hasError ? 'has-error' : '']"
              >
                <label class="col-sm-4 control-label">{{$t('port_forward.wan_ip')}}</label>
                <div class="col-sm-8">
                  <span v-for="(a,i) in wans" v-bind:key="i">
                    <input
                      :id="'wan_ip-'+i"
                      class="col-sm-2 col-xs-2"
                      type="radio"
                      v-model="newPf.OriDst"
                      :value="a"
                    >
                    <label
                      class="col-sm-10 col-xs-10 control-label text-align-left"
                      :for="'wan_ip-'+i"
                    >{{$t(a)}}</label>
                  </span>
                </div>
              </div>
              <div
                v-show="newPf.advanced"
                :class="['form-group', newPf.errors.Allow.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-4 control-label"
                  for="textInput-modal-markup"
                >{{$t('port_forward.allow_only')}}</label>
                <div class="col-sm-8">
                  <textarea v-model="newPf.Allow" class="form-control"></textarea>
                  <span v-if="newPf.errors.Allow.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newPf.errors.Allow.message)}}
                  </span>
                </div>
              </div>
              <div
                v-show="newPf.advanced"
                :class="['form-group', newPf.errors.Log.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-4 control-label"
                  for="textInput-modal-markup"
                >{{$t('port_forward.write_log')}}</label>
                <div class="col-sm-8">
                  <input type="checkbox" v-model="newPf.Log" class="form-control">
                  <span v-if="newPf.errors.Log.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newPf.errors.Log.message)}}
                  </span>
                </div>
              </div>
            </div>
            <div class="modal-footer no-mg-top">
              <div v-if="newPf.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button
                class="btn btn-primary"
                type="submit"
              >{{newPf.isEdit ? $t('edit') : $t('save')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "NAT",
  mounted() {
    this.getPF();
    this.getHosts();
    this.getProtocols();
    this.getServices();
    this.getWans();
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
      pfList: null,
      snList: [],
      wans: ["any", "192.168.5.59", "93.57.48.70"],
      hosts: [],
      protocols: [],
      services: [],
      newPf: this.initPf(),
      searchString: ""
    };
  },
  computed: {
    filteredPF() {
      var returnObj = {};
      for (var p in this.pfList) {
        var forwards = this.pfList[p].rules;
        var found = false;

        for (var f = 0; f < forwards.length; f++) {
          var forward = forwards[f];
          var values = Object.values(forward);
          found =
            found ||
            values
              .join(" ")
              .toLowerCase()
              .includes(this.searchString.toLowerCase());
        }

        forwards = forwards.sort(function(a, b) {
          if (parseInt(a.Src) < parseInt(b.Src)) return -1;
          if (parseInt(a.Src) > parseInt(b.Src)) return 1;
          return 0;
        });

        if (
          p.toLowerCase().includes(this.searchString.toLowerCase()) ||
          found
        ) {
          returnObj[p] = this.pfList[p];
        }
      }

      return returnObj;
    }
  },
  methods: {
    initPf() {
      return {
        Description: "",
        Src: 0,
        DstHost: "",
        Dst: null,
        Proto: "tcp",
        ProtoOther: "",
        OriDst: "any",
        Allow: "",
        Log: false,
        isLoading: false,
        isEdit: false,
        advanced: false,
        errors: this.initErrors()
      };
    },
    initErrors() {
      return {
        Description: {
          hasError: false,
          message: ""
        },
        Src: {
          hasError: false,
          message: ""
        },
        DstHost: {
          hasError: false,
          message: ""
        },
        Dst: {
          hasError: false,
          message: ""
        },
        Proto: {
          hasError: false,
          message: ""
        },
        OriDst: {
          hasError: false,
          message: ""
        },
        Allow: {
          hasError: false,
          message: ""
        },
        Log: {
          hasError: false,
          message: ""
        }
      };
    },
    toggleAdvancedMode() {
      this.newPf.advanced = !this.newPf.advanced;
      this.$forceUpdate();
    },
    getWans() {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/objects/read"],
        {
          action: "wan"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
            context.wans = ["any"].concat(success.wans);
          } catch (e) {
            console.error(e);
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
        ["nethserver-firewall-base/objects/read"],
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
    getServices() {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/objects/read"],
        {
          action: "services"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
            context.services = success.services;
          } catch (e) {
            console.error(e);
          }
        },
        function(error) {
          console.error(error);
        }
      );
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
    getPF() {
      var context = this;

      context.view.isLoaded = false;
      nethserver.exec(
        ["nethserver-firewall-base/nat/read"],
        {
          action: "portforward"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
            context.view.isLoaded = true;
            context.pfList = success.portforward;
          } catch (e) {
            console.error(e);
            context.view.isLoaded = true;
          }
        },
        function(error) {
          console.error(error);
          context.view.isLoaded = true;
        }
      );
    },
    openCreatePF() {
      this.newPf = this.initPf();
      $("#createPFModal").modal("show");
    },
    openEditPF(pf, host) {
      this.newPf = pf;
      this.newPf.errors = this.initErrors();
      this.newPf.isLoading = false;
      this.newPf.isEdit = true;
      this.newPf.advanced = false;
      //this.newPf.Allow = this.newPf.Allow.join("\n");
      $("#createPFModal").modal("show");
    },
    savePF() {
      this.newPf.isLoading = true;
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

.ip-type {
  border: 2px solid #ec7a08 !important;
}

.ip-type-header {
  background-color: #ececec;
}
.ip-type-header:hover {
  background-color: #f5f5f5;
}
</style>