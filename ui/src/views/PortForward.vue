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

    <div class="pf-container" v-if="pfList && view.isLoaded">
      <h3>{{$t('list')}}</h3>

      <form v-if="pfList" role="form" class="search-pf has-button search">
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
      </form>

      <div v-for="(data,host) in filteredPF" v-bind:key="host">
        <div
          id="pf-list-simple-expansion"
          class="list-group list-view-pf list-view-pf-view wizard-pf-contents-title white mg-top-10"
        >
          <div class="list-group-item list-view-pf-expand-active no-shadow mg-bottom-10">
            <div
              :class="[data.info.type == 'raw' ? 'ip-type-header': '', 'list-group-item-header cursor-initial']"
            >
              <div class="list-view-pf-main-info small-list">
                <div class="list-view-pf-left">
                  <span
                    :class="[data.info.type == 'raw' ? 'pficon pficon-warning-triangle-o ip-type' : 'pficon pficon-container-node', 'list-view-pf-icon-sm small-icon']"
                  ></span>
                </div>
                <div class="list-view-pf-body">
                  <div class="list-view-pf-description description-more-space">
                    <div class="list-group-item-heading flex-50">
                      <span class="normal">{{$t('port_forward.destination')}}:</span>
                      {{host | parseObj}}
                      <span
                        class="gray"
                        v-if="data.info.IpAddress && data.info.IpAddress.length > 0"
                      >({{data.info.IpAddress}})</span>
                    </div>
                    <div
                      v-if="data.info.type == 'host'"
                      class="list-group-item-text"
                    >{{data.info.Description}}</div>
                    <div v-if="data.info.type == 'raw'" class="list-group-item-text">
                      {{$t('port_forward.no_host_found')}}.
                      <a
                        @click="openCreateHost(host, data.rules)"
                      >{{$t('create')}}</a>
                    </div>
                  </div>
                  <div class="list-view-pf-additional-info additional-info-less-space">
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
                  <div class="col-sm-10">
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
                  <span class="col-sm-2">
                    <strong>{{$t('actions')}}</strong>
                  </span>
                </li>
                <li
                  v-for="p in data.rules"
                  v-bind:key="p"
                  :class="['list-group-item small-li', p.status == 'disabled' ? 'gray' : '']"
                >
                  <div class="col-sm-10">
                    <span class="col-sm-2">
                      {{p.Src || '-'}}
                      <span v-if="p.Service.length > 0">({{p.Service}})</span>
                    </span>
                    <span class="col-sm-2">{{p.Dst || '-'}}</span>
                    <span class="col-sm-2">{{$t('protocols.'+p.Proto)}}</span>
                    <span class="col-sm-2">{{p.Description || '-'}}</span>
                    <span class="col-sm-2">{{p.OriDst || '-'}}</span>
                    <span class="col-sm-2">{{p.Allow || '-' | prettyNewLine}}</span>
                  </div>
                  <div class="col-sm-2">
                    <button
                      @click="p.status == 'disabled' ? enablePF(p, host) : openEditPF(p, host, false)"
                      :class="['btn btn-default', p.status == 'disabled' ? 'btn-primary' : '']"
                    >
                      <span
                        :class="['fa', p.status == 'disabled' ? 'fa-check' : 'fa-edit', 'span-right-margin']"
                      ></span>
                      {{p.status == 'disabled' ? $t('enable') : $t('edit')}}
                    </button>
                    <div class="dropup pull-right dropdown-kebab-pf">
                      <button
                        class="btn btn-link dropdown-toggle"
                        type="button"
                        id="dropdownKebabRight9"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="true"
                      >
                        <span class="fa fa-ellipsis-v"></span>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                          <a @click="enablePF(p, host)">
                            <span
                              :class="['fa', p.status == 'enabled' ? 'fa-lock' : 'fa-check', 'span-right-margin']"
                            ></span>
                            {{p.status == 'enabled' ? $t('disable') : $t('enable')}}
                          </a>
                        </li>
                        <li>
                          <a @click="openEditPF(p, host, true)">
                            <span class="fa fa-clone span-right-margin"></span>
                            {{$t('port_forward.duplicate')}}
                          </a>
                        </li>
                        <li role="presentation" class="divider"></li>
                        <li>
                          <a @click="openDeletePF(p, host)">
                            <span class="fa fa-times span-right-margin"></span>
                            {{$t('delete')}}
                          </a>
                        </li>
                      </ul>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- MODALS -->
    <div class="modal" id="createPFModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{newPf.isEdit ? $t('port_forward.edit_pf') : newPf.isDuplicate ? $t('port_forward.duplicate_pf') : $t('port_forward.create_pf')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="savePF()">
            <div class="modal-body">
              <div :class="['form-group', newPf.errors.Proto.hasError ? 'has-error' : '']">
                <label class="col-sm-4 control-label">{{$t('port_forward.protocol')}}</label>
                <div class="col-sm-8">
                  <select
                    @change="handleProto()"
                    v-model="newPf.Proto"
                    required
                    class="form-control"
                  >
                    <option v-for="p in protocols" v-bind:key="p" :value="p">{{$t('protocols.'+p)}}</option>
                  </select>
                </div>
              </div>
              <div :class="['form-group', newPf.errors.Src.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-4 control-label"
                  for="textInput-modal-markup"
                >{{$t('port_forward.origin_port')}}</label>
                <div class="col-sm-8">
                  <suggestions
                    v-model="newPf.Src"
                    :disabled="newPf.SrcDisabled"
                    required
                    :options="autoOptions"
                    :onInputChange="filterSrcAuto"
                    :onItemSelected="selectSrcAuto"
                  >
                    <div slot="item" slot-scope="props" class="single-item">
                      <span>{{props.item.Ports.join(', ')}} ({{props.item.name}})</span>
                    </div>
                  </suggestions>
                  <span
                    v-if="newPf.SrcType && newPf.SrcType.length > 0"
                    class="help-block gray"
                  >{{newPf.SrcType}}</span>
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
                <div class="col-sm-8">
                  <suggestions
                    v-model="newPf.DstHost"
                    required
                    :options="autoOptions"
                    :onInputChange="filterHostsAuto"
                    :onItemSelected="selectHostsAuto"
                  >
                    <div slot="item" slot-scope="props" class="single-item">
                      <span>{{props.item.name}} ({{props.item.IpAddress}})</span>
                    </div>
                  </suggestions>
                  <span
                    v-if="newPf.DstHostIp && newPf.DstHostIp.length > 0"
                    class="help-block gray"
                  >{{newPf.DstHostIp}}</span>
                  <span v-if="newPf.errors.DstHost.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newPf.errors.DstHost.message)}}
                  </span>
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
                    :disabled="newPf.DstDisabled"
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
              <legend class="fields-section-header-pf" aria-expanded="true">
                <span
                  :class="['fa fa-angle-right field-section-toggle-pf', newPf.advanced ? 'fa-angle-down' : '']"
                ></span>
                <a
                  class="field-section-toggle-pf"
                  @click="toggleAdvancedMode()"
                >{{$t('advanced_mode')}}</a>
              </legend>

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
                  <textarea v-model="newPf.Allow" class="form-control textarea-mid-height"></textarea>
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
              >{{newPf.isEdit ? $t('edit') : newPf.isDuplicate ? $t('port_forward.duplicate') : $t('save')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal" id="createHostModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('port_forward.add_host')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveHost(newHost)">
            <div class="modal-body">
              <div :class="['form-group', newHost.errors.name.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('port_forward.hostname')}}</label>
                <div class="col-sm-9">
                  <input required type="text" v-model="newHost.name" class="form-control">
                  <span
                    v-if="newHost.errors.name.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newHost.errors.name.message)}}</span>
                </div>
              </div>
              <div :class="['form-group', newHost.errors.IpAddress.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('port_forward.ip_address')}}</label>
                <div class="col-sm-9">
                  <input required type="text" v-model="newHost.IpAddress" class="form-control">
                  <span
                    v-if="newHost.errors.IpAddress.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newHost.errors.IpAddress.message)}}</span>
                </div>
              </div>
              <div :class="['form-group', newHost.errors.Description.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('port_forward.description')}}</label>
                <div class="col-sm-9">
                  <input type="text" v-model="newHost.Description" class="form-control">
                  <span
                    v-if="newHost.errors.Description.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newHost.errors.Description.message)}}</span>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <div v-if="newHost.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-primary" type="submit">{{$t('save')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal" id="deletePFModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('port_forward.delete_pf')}} {{currentPf.Description}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deletePF(currentPf)">
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
    <!-- END MODALS -->
  </div>
</template>

<script>
var Mark = require("mark.js");

export default {
  name: "PortForward",
  mounted() {
    this.getPF();
    this.getHosts();
    this.getProtocols();
    this.getServices();
    this.getWans();

    var context = this;
    context.$parent.$on("changes-applied", function() {
      context.getPF();
      context.getHosts();
      context.getProtocols();
      context.getServices();
      context.getWans();
    });
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
      newHost: this.initHost(),
      currentPf: {},
      searchString: "",
      autoOptions: {
        inputClass: "form-control"
      },
      highlightInstance: null
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
    handleProto() {
      this.newPf.SrcDisabled = !(
        this.newPf.Proto.toLowerCase().includes("tcp") ||
        this.newPf.Proto.toLowerCase().includes("udp")
      );
      this.newPf.DstDisabled = !(
        this.newPf.Proto.toLowerCase().includes("tcp") ||
        this.newPf.Proto.toLowerCase().includes("udp")
      );

      this.newPf.Dst = null;
      this.newPf.DstDisabled = false;
      this.newPf.Src = null;
      this.newPf.SrcType = null;
    },
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
    filterHostsAuto(query) {
      this.newPf.DstHost = null;
      this.newPf.DstHostIp = null;
      this.newPf.DstHostFull = null;

      if (query.trim().length === 0) {
        return null;
      }

      return this.hosts.filter(function(host) {
        return (
          host.name.toLowerCase().includes(query.toLowerCase()) ||
          host.Description.toLowerCase().includes(query.toLowerCase()) ||
          host.IpAddress.toLowerCase().includes(query.toLowerCase())
        );
      });
    },
    selectHostsAuto(item) {
      this.newPf.DstHost = item.name;
      this.newPf.DstHostIp = item.IpAddress;

      this.newPf.DstHostFull = Object.assign({}, item);
      this.newPf.DstHostFull.name = this.newPf.DstHostFull.name.toLowerCase();
      this.newPf.DstHostFull.type = "host";
    },
    filterSrcAuto(query) {
      this.newPf.Src = null;
      this.newPf.SrcType = null;
      this.newPf.Proto = null;
      this.newPf.DstDisabled = false;

      if (query.trim().length === 0) {
        return null;
      }

      return this.services.filter(function(service) {
        return (
          service.name.toLowerCase().includes(query.toLowerCase()) ||
          service.Description.toLowerCase().includes(query.toLowerCase()) ||
          service.Ports.join(",")
            .toLowerCase()
            .includes(query.toLowerCase())
        );
      });
    },
    selectSrcAuto(item) {
      this.newPf.Src = item.Ports.join(", ");
      this.newPf.SrcType = item.name;
      this.newPf.Proto = item.Protocol;
      this.newPf.DstDisabled = this.newPf.Src.includes(",");
    },
    initHost() {
      return {
        name: null,
        Description: "",
        IpAddress: "",
        isLoading: false,
        errors: this.initHostErrors()
      };
    },
    initHostErrors() {
      return {
        name: {
          hasError: false,
          message: ""
        },
        Description: {
          hasError: false,
          message: ""
        },
        IpAddress: {
          hasError: false,
          message: ""
        }
      };
    },
    initPf() {
      return {
        name: null,
        Description: "",
        Src: null,
        SrcDisabled: false,
        SrcType: "",
        DstHost: "",
        DstHostIp: "",
        Dst: null,
        DstDisabled: false,
        Proto: "tcpudp",
        OriDst: "any",
        Allow: "",
        Log: false,
        isLoading: false,
        isEdit: false,
        isDuplicate: false,
        advanced: false,
        errors: this.initErrors()
      };
    },
    initErrors() {
      return {
        name: {
          hasError: false,
          message: ""
        },
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
            context.protocols = success.protocols.sort();
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
        ["nethserver-firewall-base/port-forward/read"],
        {
          action: "list"
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
    openCreateHost(host, rules) {
      this.newHost = this.initHost();
      this.newHost.IpAddress = host;
      this.newHost.rules = rules;
      $("#createHostModal").modal("show");
    },
    saveHost(newHost) {
      var context = this;

      var hostObj = {
        action: "create-host",
        name: context.newHost.name,
        IpAddress: context.newHost.IpAddress,
        Description: context.newHost.Description,
        portforwards: context.newHost.rules.map(function(pf) {
          return pf.name;
        })
      };

      context.newHost.isLoading = true;
      context.$forceUpdate();
      nethserver.exec(
        ["nethserver-firewall-base/objects/validate"],
        hostObj,
        null,
        function(success) {
          context.newHost.isLoading = false;
          $("#createHostModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "port_forward.host_created_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "port_forward.host_created_error"
          );

          // update values
          nethserver.exec(
            ["nethserver-firewall-base/objects/create"],
            hostObj,
            function(stream) {
              console.info("firewall-base-update", stream);
            },
            function(success) {
              // get tc
              context.getHosts();
              context.getPF();
            },
            function(error, data) {
              console.error(error, data);
            }
          );
        },
        function(error, data) {
          var errorData = {};
          context.newHost.isLoading = false;
          context.newHost.errors = context.initHostErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              context.newHost.errors[attr.parameter].hasError = true;
              context.newHost.errors[attr.parameter].message = attr.error;
            }
          } catch (e) {
            console.error(e);
          }
          context.$forceUpdate();
        }
      );
    },
    openCreatePF() {
      this.newPf = this.initPf();
      $("#createPFModal").modal("show");
    },
    openEditPF(pf, host, duplicate) {
      console.log(pf, host);
      this.newPf = Object.assign({}, pf);
      this.newPf.errors = this.initErrors();
      this.newPf.isLoading = false;
      this.newPf.isEdit = !duplicate;
      this.newPf.isDuplicate = duplicate;
      this.newPf.SrcType = this.newPf.Service;
      this.newPf.DstHostIp = "";
      this.newPf.DstDisabled = this.newPf.Src.includes(",");
      this.newPf.advanced = false;
      this.newPf.Log = this.newPf.Log == "info";
      this.newPf.Allow =
        this.newPf.Allow.length > 0
          ? this.newPf.Allow.split(",").join("\n")
          : "";
      this.newPf.OriDst = this.newPf.OriDst == "" ? "any" : this.newPf.OriDst;
      this.newPf.DstHost = this.newPf.DstHost.split(";")[1];
      this.newPf.name = duplicate ? "" : this.newPf.name;
      this.$forceUpdate();
      $("#createPFModal").modal("show");
    },
    savePF() {
      var context = this;

      var pfObj = {
        action: context.newPf.isEdit ? "update" : "create",
        name: context.newPf.isEdit ? context.newPf.name : null,
        Src: context.newPf.Src.split(",").map(function(item) {
          return parseInt(item.trim());
        }),
        DstHost: context.newPf.DstHostFull
          ? { name: context.newPf.DstHost, type: "host" }
          : { name: context.newPf.DstHost, type: "raw" },
        Dst: context.newPf.Dst ? context.newPf.Dst : "",
        Proto: context.newPf.Proto,
        Description: context.newPf.Description,
        OriDst: context.newPf.OriDst == "any" ? "" : context.newPf.OriDst,
        Allow: context.newPf.Allow.split("\n"),
        Log: context.newPf.Log ? "info" : "none",
        status: context.newPf.isEdit
          ? context.newPf.status
            ? "enabled"
            : "disabled"
          : "enabled"
      };

      context.newPf.isLoading = true;
      context.$forceUpdate();
      nethserver.exec(
        ["nethserver-firewall-base/port-forward/validate"],
        pfObj,
        null,
        function(success) {
          context.newPf.isLoading = false;
          $("#createPFModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "port_forward.pf_" +
              (context.newPf.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "port_forward.pf_" +
              (context.newPf.isEdit ? "updated" : "created") +
              "_error"
          );

          // update values
          nethserver.exec(
            [
              "nethserver-firewall-base/port-forward/" +
                (context.newPf.isEdit ? "update" : "create")
            ],
            pfObj,
            function(stream) {
              console.info("firewall-base-update", stream);
            },
            function(success) {
              // get tc
              context.getPF();
            },
            function(error, data) {
              console.error(error, data);
            }
          );
        },
        function(error, data) {
          var errorData = {};
          context.newPf.isLoading = false;
          context.newPf.errors = context.initErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              context.newPf.errors[attr.parameter].hasError = true;
              context.newPf.errors[attr.parameter].message = attr.error;
            }
          } catch (e) {
            console.error(e);
          }
          context.$forceUpdate();
        }
      );
    },
    enablePF(p, host) {
      var context = this;

      var pfObj = {
        action: "update",
        name: p.name,
        Src: p.Src,
        DstHost: host,
        Dst: p.Dst,
        Proto: p.Proto,
        Description: p.Description,
        OriDst: p.OriDst == "any" ? "" : p.OriDst,
        Allow: p.Allow,
        Log: p.Log ? "info" : "none",
        status: p.status == "enabled" ? "disabled" : "enabled"
      };

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "port_forward.pf_updated_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "port_forward.pf_updated_ok"
      );

      // update values
      nethserver.exec(
        ["nethserver-firewall-base/port-forward/update"],
        pfObj,
        function(stream) {
          console.info("firewall-base-update", stream);
        },
        function(success) {
          // get port forwards
          context.getPF();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    },
    openDeletePF(pf, host) {
      this.currentPf = Object.assign({}, pf);
      $("#deletePFModal").modal("show");
    },
    deletePF() {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "port_forward.pf_deleted_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "port_forward.pf_deleted_error"
      );

      $("#deletePFModal").modal("hide");
      nethserver.exec(
        ["nethserver-firewall-base/port-forward/delete"],
        {
          action: "delete",
          name: context.currentPf.name
        },
        function(stream) {
          console.info("nethserver-firewall-base", stream);
        },
        function(success) {
          // get port forwards
          context.getPF();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    },
    openCreateObject(object) {
      this.newObject = this.initObject();
      this.newObject.IpAddress = object.object == "host" ? object.name : null;
      this.newObject.Address = object.object == "cidr" ? object.name : null;
      this.newObject.rules = 1;
      this.newObject.type = object.object;
      $("#createObjectModal").modal("show");
    },
    saveObject(object) {
      var context = this;

      var objectObj = {
        action:
          context.newObject.type == "host" ? "create-host" : "create-cidr-sub",
        name: context.newObject.name,
        IpAddress: context.newObject.IpAddress,
        Address: context.newObject.Address,
        Description: context.newObject.Description,
        rules: context.newObject.rules
      };

      context.newObject.isLoading = true;
      context.$forceUpdate();
      nethserver.exec(
        ["nethserver-firewall-base/objects/validate"],
        objectObj,
        null,
        function(success) {
          context.newObject.isLoading = false;
          $("#createObjectModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            context.newObject.type == "host"
              ? "objects.host_created_ok"
              : "objects.cidr_sub_created_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            context.newObject.type == "host"
              ? "objects.host_created_error"
              : "objects.cidr_sub_created_error"
          );

          // update values
          nethserver.exec(
            ["nethserver-firewall-base/objects/create"],
            objectObj,
            function(stream) {
              console.info("firewall-base-update", stream);
            },
            function(success) {
              // get rules
              context.getRules();
            },
            function(error, data) {
              console.error(error, data);
            }
          );
        },
        function(error, data) {
          var errorData = {};
          context.newObject.isLoading = false;
          context.newObject.errors = context.initObjectErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              context.newObject.errors[attr.parameter].hasError = true;
              context.newObject.errors[attr.parameter].message = attr.error;
            }
          } catch (e) {
            console.error(e);
          }
          context.$forceUpdate();
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

.ip-type {
  border: 2px solid #ec7a08 !important;
}

.ip-type-header {
  background-color: #ececec;
}

.ip-type-header:hover {
  background-color: #f5f5f5;
}

.textarea-mid-height {
  min-height: 100px;
}

.description-more-space {
  width: 85% !important;
}
.additional-info-less-space {
  width: 15% !important;
}
</style>