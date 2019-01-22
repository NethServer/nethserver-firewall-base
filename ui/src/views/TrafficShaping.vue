<template>
  <div>
    <h2>{{$t('traffic_shaping.title')}}</h2>

    <h3 v-if="tc.length > 0">{{$t('charts')}}</h3>
    <div v-if="!view.isLoaded && tc.length > 0" class="spinner spinner-lg view-spinner"></div>
    <div
      v-if="view.invalidChartsData && tc.length > 0"
      class="alert alert-warning alert-dismissable col-sm-12"
    >
      <span class="pficon pficon-warning-triangle-o"></span>
      <strong>{{$t('warning')}}!</strong>
      {{$t('charts_not_updated')}}.
    </div>
    <div v-show="interfaces.length > 0 && view.isLoaded && tc.length > 0" class="row">
      <div v-for="i in interfaces" v-bind:key="i" class="col-sm-6 stats-divider">
        <h4>
          {{i.provider.name}}
          <span class="gray">({{$t('download_low')}})</span>
        </h4>
        <div :id="'chart-in-'+i.provider.name | sanitize" class="col-sm-12"></div>
        <h4>
          {{i.provider.name}}
          <span class="gray">({{$t('upload_low')}})</span>
        </h4>
        <div :id="'chart-out-'+i.provider.name | sanitize" class="col-sm-12"></div>
      </div>
    </div>

    <h3 v-if="tc.length > 0">{{$t('actions')}}</h3>
    <button
    v-if="tc.length > 0"
      @click="openCreateTc()"
      class="btn btn-primary btn-lg"
    >{{$t('traffic_shaping.create_class')}}</button>

    <h3 v-if="tc.length > 0">{{$t('list')}}</h3>
    <div v-if="!view.isLoaded" class="spinner spinner-lg view-spinner"></div>

    <div v-if="tc.length == 0 && view.isLoaded" class="blank-slate-pf white">
      <div class="blank-slate-pf-icon">
        <span class="fa fa-crosshairs"></span>
      </div>
      <h1>{{$t('traffic_shaping.no_tc_found')}}</h1>
      <p>{{$t('traffic_shaping.no_tc_found_text')}}.</p>
      <div class="blank-slate-pf-main-action">
        <button
          class="btn btn-primary"
          @click="openCreateTc()"
        >{{$t('traffic_shaping.create_class')}}</button>

        <button
          class="btn btn-default mg-left-5"
          @click="defaultTc()"
        >{{$t('traffic_shaping.create_default_class')}}</button>
      </div>
    </div>

    <div id="pf-list-simple-expansion" class="list-group list-view-pf list-view-pf-view no-mg-top">
      <div v-for="t in tc" v-bind:key="t" class="list-group-item">
        <div class="list-group-item-header cursor-initial">
          <div class="list-view-pf-actions">
            <button @click="openEditTc(t)" class="btn btn-default">
              <span class="fa fa-edit span-right-margin"></span>
              {{$t('edit')}}
            </button>
            <div class="dropdown pull-right dropdown-kebab-pf">
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
                  <a @click="openDeleteTc(t)">
                    <span class="fa fa-times span-right-margin"></span>
                    {{$t('delete')}}
                  </a>
                </li>
              </ul>
            </div>
          </div>
          <div class="list-view-pf-main-info">
            <div class="list-view-pf-left">
              <span class="fa fa-crosshairs list-view-pf-icon-sm"></span>
            </div>
            <div class="list-view-pf-body">
              <div class="list-view-pf-description">
                <div class="list-group-item-heading">
                  <a @click="openEditTc(t)">{{t.name}}</a>
                </div>
                <div class="list-group-item-text">{{t.Description}}</div>
              </div>
              <div class="list-view-pf-additional-info">
                <div class="list-view-pf-additional-info-item multi-line adjust-line">
                  <span>{{$t('download')}}</span>
                  <br>
                  <span>{{$t('upload')}}</span>
                </div>
                <div class="list-view-pf-additional-info-item multi-line">
                  <span
                    v-if="t.MinInputRate.length > 0"
                    data-toggle="tooltip"
                    data-placement="top"
                    :title="$t('traffic_shaping.desc_min_down_limit')"
                    class="fa fa-download"
                  ></span>
                  <strong
                    v-if="t.MinInputRate.length > 0"
                  >{{t.MinInputRate}} {{$t('traffic_shaping.'+t.Unit)}}</strong>
                  <span v-if="t.MinInputRate.length > 0">{{$t('traffic_shaping.min')}}</span>
                  <br>
                  <span
                    v-if="t.MinOutputRate.length > 0"
                    data-toggle="tooltip"
                    data-placement="top"
                    :title="$t('traffic_shaping.desc_min_up_limit')"
                    class="fa fa-upload"
                  ></span>
                  <strong
                    v-if="t.MinOutputRate.length > 0"
                  >{{t.MinOutputRate}} {{$t('traffic_shaping.'+t.Unit)}}</strong>
                  <span v-if="t.MinOutputRate.length > 0">{{$t('traffic_shaping.min')}}</span>
                </div>
                <div class="list-view-pf-additional-info-item multi-line">
                  <span
                    v-if="t.MaxInputRate.length > 0"
                    data-toggle="tooltip"
                    data-placement="top"
                    :title="$t('traffic_shaping.desc_max_down_limit')"
                    class="fa fa-download"
                  ></span>
                  <strong
                    v-if="t.MaxInputRate.length > 0"
                  >{{t.MaxInputRate}} {{$t('traffic_shaping.'+t.Unit)}}</strong>
                  <span v-if="t.MaxInputRate.length > 0">{{$t('traffic_shaping.max')}}</span>
                  <br>
                  <span
                    v-if="t.MaxOutputRate.length > 0"
                    data-toggle="tooltip"
                    data-placement="top"
                    :title="$t('traffic_shaping.desc_max_up_limit')"
                    class="fa fa-upload"
                  ></span>
                  <strong
                    v-if="t.MaxOutputRate.length > 0"
                  >{{t.MaxOutputRate}} {{$t('traffic_shaping.'+t.Unit)}}</strong>
                  <span v-if="t.MaxOutputRate.length > 0">{{$t('traffic_shaping.max')}}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- MODALS -->
    <div class="modal" id="createTc" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{newTc.isEdit ? ($t('traffic_shaping.edit_class') + ' ' + newTc.name) : $t('traffic_shaping.create_class')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveTc()">
            <div class="modal-body">
              <div :class="['form-group', newTc.errors.name.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-4 control-label"
                  for="textInput-modal-markup"
                >{{$t('traffic_shaping.class_name')}}</label>
                <div class="col-sm-8">
                  <input
                    :disabled="newTc.isEdit"
                    type="text"
                    v-model="newTc.name"
                    class="form-control"
                  >
                  <span v-if="newTc.errors.name.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newTc.errors.name.message)}}
                  </span>
                </div>
              </div>
              <div :class="['form-group', newTc.errors.Description.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-4 control-label"
                  for="textInput-modal-markup"
                >{{$t('traffic_shaping.class_description')}}</label>
                <div class="col-sm-8">
                  <input type="text" v-model="newTc.Description" class="form-control">
                  <span v-if="newTc.errors.Description.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newTc.errors.Description.message)}}
                  </span>
                </div>
              </div>
              <div class="form-group">
                <label
                  class="col-sm-4 control-label"
                  for="Bandwidth-Unit-1"
                >{{$t('traffic_shaping.bandwidth_unit')}}</label>
                <div class="col-sm-8">
                  <input
                    id="Bandwidth-Unit-1"
                    class="col-sm-2 col-xs-2"
                    type="radio"
                    v-model="newTc.Unit"
                    value="kbps"
                  >
                  <label
                    class="col-sm-10 col-xs-10 control-label text-align-left"
                    for="Bandwidth-Unit-2"
                  >{{$t('traffic_shaping.kbps')}}</label>
                  <input
                    id="Bandwidth-Unit-2"
                    class="col-sm-2 col-xs-2"
                    type="radio"
                    v-model="newTc.Unit"
                    value="%"
                  >
                  <label
                    class="col-sm-10 col-xs-10 control-label text-align-left"
                  >{{$t('traffic_shaping.%')}}</label>
                </div>
              </div>
              <div
                :class="['form-group', newTc.errors.MinInputRate.hasError || newTc.errors.MaxInputRate.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-4 control-label"
                  for="textInput-modal-markup"
                >{{$t('traffic_shaping.down_bandwidth_limit')}}</label>
                <div class="col-sm-4">
                  <label>{{$t('traffic_shaping.min')}} ({{$t('traffic_shaping.'+newTc.Unit)}})</label>
                  <input class="col-sm-3 form-control" type="number" v-model="newTc.MinInputRate">
                  <span v-if="newTc.errors.MinInputRate.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newTc.errors.MinInputRate.message)}}
                  </span>
                </div>
                <div class="col-sm-4">
                  <label>{{$t('traffic_shaping.max')}} ({{$t('traffic_shaping.'+newTc.Unit)}})</label>
                  <input class="col-sm-3 form-control" type="number" v-model="newTc.MaxInputRate">
                  <span v-if="newTc.errors.MaxInputRate.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newTc.errors.MaxInputRate.message)}}
                  </span>
                </div>
              </div>
              <div
                :class="['form-group', newTc.errors.MinOutputRate.hasError || newTc.errors.MaxOutputRate.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-4 control-label"
                  for="textInput-modal-markup"
                >{{$t('traffic_shaping.up_bandwidth_limit')}}</label>
                <div class="col-sm-4">
                  <label>{{$t('traffic_shaping.min')}} ({{$t('traffic_shaping.'+newTc.Unit)}})</label>
                  <input class="col-sm-3 form-control" type="number" v-model="newTc.MinOutputRate">
                  <span v-if="newTc.errors.MinOutputRate.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newTc.errors.MinOutputRate.message)}}
                  </span>
                </div>
                <div class="col-sm-4">
                  <label>{{$t('traffic_shaping.max')}} ({{$t('traffic_shaping.'+newTc.Unit)}})</label>
                  <input class="col-sm-3 form-control" type="number" v-model="newTc.MaxOutputRate">
                  <span v-if="newTc.errors.MaxOutputRate.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newTc.errors.MaxOutputRate.message)}}
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
                    :value="newTc.advanced"
                    :sync="true"
                    @change="toggleAdvancedMode()"
                  />
                </div>
              </div>
              <div
                v-if="newTc.advanced"
                :class="['form-group', newTc.errors.BindTo.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-4 control-label"
                  for="textInput-modal-markup"
                >{{$t('traffic_shaping.bind_to')}}</label>
                <div class="col-sm-8">
                  <select
                    @change="addIfaceToBind(newTc.ifaceToBind)"
                    v-model="newTc.ifaceToBind"
                    class="combobox form-control"
                  >
                    <option>-</option>
                    <option :value="i.name" v-for="(i, ki) in interfaces" v-bind:key="ki">{{i.name}}</option>
                  </select>
                  <span
                    v-if="newTc.errors.BindTo.hasError"
                    class="help-block"
                  >{{newTc.errors.BindTo.message}}</span>
                </div>
              </div>
              <div v-if="newTc.advanced" class="form-group">
                <label class="col-sm-4 control-label" for="textInput-modal-markup"></label>
                <div class="col-sm-8">
                  <ul class="list-inline compact">
                    <li v-for="(i, ki) in newTc.BindTo" v-bind:key="i">
                      <span class="label label-info">
                        {{i}}
                        <a @click="removeIfaceToBind(ki)" class="remove-item-inline">
                          <span class="fa fa-times"></span>
                        </a>
                      </span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="modal-footer no-mg-top">
              <div v-if="newTc.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button
                class="btn btn-primary"
                type="submit"
              >{{newTc.isEdit ? $t('edit') : $t('save')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal" id="deleteTc" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('traffic_shaping.delete_class')}} {{currentTc.name}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deleteTc(currentTc)">
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
export default {
  name: "TrafficShaping",
  data() {
    return {
      view: {
        isLoaded: false,
        invalidChartsData: false
      },
      tc: [],
      newTc: this.initTc(),
      currentTc: {},
      charts: {},
      pollingIntervalId: 0,
      interfaces: [],
      providers: []
    };
  },
  mounted() {
    this.getInterfaces();
    this.getTc();
    this.initCharts();
  },
  beforeRouteLeave(to, from, next) {
    $(".modal").modal("hide");
    clearInterval(this.pollingIntervalId);
    next();
  },
  methods: {
    toggleAdvancedMode() {
      this.newTc.advanced = !this.newTc.advanced;
      this.$forceUpdate();
    },
    initCharts() {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/traffic-shaping/read"],
        {
          action: "stats"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          for (var i in success) {
            var provider = success[i];

            if (provider) {
              context.view.invalidChartsData = false;

              // out classes
              var outColumns = [];
              for (var c in provider.out) {
                var classVal = provider.out[c];

                if (c != "time") {
                  outColumns.push([c].concat(classVal.reverse()));
                } else {
                  outColumns.push(
                    ["x"].concat(
                      classVal
                        .map(function(i) {
                          return moment.unix(i).format("HH:mm:ss");
                        })
                        .reverse()
                    )
                  );
                }
              }

              // in classes
              var inColumns = [];
              for (var c in provider.in) {
                var classVal = provider.in[c];

                if (c != "time") {
                  inColumns.push([c].concat(classVal.reverse()));
                } else {
                  inColumns.push(
                    ["x"].concat(
                      classVal
                        .map(function(i) {
                          return moment.unix(i).format("HH:mm:ss");
                        })
                        .reverse()
                    )
                  );
                }
              }

              var chart = c3.generate({
                bindto:
                  "#" + context.$options.filters.sanitize("chart-out-" + i),
                data: {
                  x: "x",
                  xFormat: "%H:%M:%S",
                  columns: outColumns
                  /* types: {
                    data1: "area-spline",
                    data2: "area-spline"
                  } */
                },
                axis: {
                  x: {
                    type: "timeseries",
                    tick: {
                      format: "%H:%M:%S"
                    }
                  }
                },
                size: {
                  height: 250
                }
              });

              var chart = c3.generate({
                bindto:
                  "#" + context.$options.filters.sanitize("chart-in-" + i),
                data: {
                  x: "x",
                  xFormat: "%H:%M:%S",
                  columns: inColumns
                  /* types: {
                    data1: "area-spline",
                    data2: "area-spline"
                  } */
                },
                axis: {
                  x: {
                    type: "timeseries",
                    tick: {
                      format: "%H:%M:%S"
                    }
                  }
                },
                size: {
                  height: 250
                }
              });
            } else {
              context.view.invalidChartsData = true;
            }
          }
        },
        function(error) {
          console.error(error);
        }
      );

      if (context.pollingIntervalId == 0) {
        context.pollingIntervalId = setInterval(function() {
          context.initCharts();
        }, 5000);
      }
    },

    groupAlreadyAdded(bind) {
      return this.newTc.BindTo.indexOf(bind) > -1;
    },
    addIfaceToBind(bindTo) {
      if (bindTo.length > 0 && bindTo != "-") {
        if (!this.groupAlreadyAdded(bindTo)) {
          this.newTc.BindTo.push(bindTo);
        }
      }
    },
    removeIfaceToBind(index) {
      this.newTc.BindTo.splice(index, 1);
    },

    initTc() {
      return {
        name: "",
        Description: "",
        MinInputRate: "",
        MaxInputRate: "",
        MinOutputRate: "",
        MaxOutputRate: "",
        Unit: "%",
        BindTo: [],
        advanced: false,
        isLoading: false,
        isEdit: false,
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
        MinInputRate: {
          hasError: false,
          message: ""
        },
        MaxInputRate: {
          hasError: false,
          message: ""
        },
        MinOutputRate: {
          hasError: false,
          message: ""
        },
        MaxOutputRate: {
          hasError: false,
          message: ""
        },
        BindTo: {
          hasError: false,
          message: ""
        }
      };
    },
    getInterfaces() {
      var context = this;

      context.view.isLoaded = false;
      nethserver.exec(
        ["nethserver-firewall-base/wan/read"],
        {
          action: "providers"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          var interfaces = [];
          for (var i in success.configuration.interfaces) {
            var iface = success.configuration.interfaces[i];
            interfaces.push(iface);
          }
          context.interfaces = interfaces;
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getTc() {
      var context = this;

      context.view.isLoaded = false;
      nethserver.exec(
        ["nethserver-firewall-base/traffic-shaping/read"],
        {
          action: "classes"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          var tc = [];
          for (var i in success.configuration.classes) {
            var tcItem = success.configuration.classes[i];
            tcItem.isLoading = false;
            tcItem.isEdit = false;
            tcItem.BindTo;
            tcItem.errors = context.initErrors();
            tc.push(tcItem);
          }
          context.tc = tc;
          context.view.isLoaded = true;

          setTimeout(function() {
            $('[data-toggle="tooltip"]').tooltip();
          }, 250);
        },
        function(error) {
          console.error(error);
          context.view.isLoaded = true;
        }
      );
    },
    openCreateTc() {
      this.newTc = this.initTc();
      this.newTc.isEdit = false;
      $("#createTc").modal("show");
    },
    openEditTc(t) {
      this.newTc = Object.assign({}, t);
      this.newTc.isEdit = true;
      this.newTc.errors = this.initErrors();
      $("#createTc").modal("show");
    },
    saveTc() {
      var context = this;

      var tcObj = {
        action: context.newTc.isEdit ? "update" : "create",
        name: context.newTc.name,
        Description: context.newTc.Description,
        MinInputRate: context.newTc.MinInputRate,
        MaxInputRate: context.newTc.MaxInputRate,
        MinOutputRate: context.newTc.MinOutputRate,
        MaxOutputRate: context.newTc.MaxOutputRate,
        Unit: context.newTc.Unit,
        BindTo: context.newTc.BindTo
      };

      context.newTc.isLoading = true;
      context.$forceUpdate();
      nethserver.exec(
        ["nethserver-firewall-base/traffic-shaping/validate"],
        tcObj,
        null,
        function(success) {
          context.newTc.isLoading = false;
          $("#createTc").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "traffic_shaping.tc_created_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "traffic_shaping.tc_created_error"
          );

          // update values
          nethserver.exec(
            [
              "nethserver-firewall-base/traffic-shaping/" +
                (context.newTc.isEdit ? "update" : "create")
            ],
            tcObj,
            function(stream) {
              console.info("firewall-base-update", stream);
            },
            function(success) {
              // get tc
              context.getTc();
            },
            function(error, data) {
              console.error(error, data);
            }
          );
        },
        function(error, data) {
          var errorData = {};
          context.newTc.isLoading = false;
          context.newTc.errors = context.initErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              context.newTc.errors[attr.parameter].hasError = true;
              context.newTc.errors[attr.parameter].message = attr.error;
            }
          } catch (e) {
            console.error(e);
          }
          context.$forceUpdate();
        }
      );
    },
    openDeleteTc(t) {
      this.currentTc = Object.assign({}, t);
      $("#deleteTc").modal("show");
    },
    deleteTc() {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "traffic_shaping.tc_deleted_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "traffic_shaping.tc_deleted_error"
      );

      $("#deleteTc").modal("hide");
      nethserver.exec(
        ["nethserver-firewall-base/traffic-shaping/delete"],
        {
          name: context.currentTc.name
        },
        function(stream) {
          console.info("nethserver-firewall-base", stream);
        },
        function(success) {
          // get tc
          context.getTc();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    },
    defaultTc() {
      // notification
      nethserver.notifications.success = context.$i18n.t(
        "traffic_shaping.tc_default_created_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "traffic_shaping.tc_default_created_error"
      );

      // update values
      nethserver.exec(
        ["nethserver-firewall-base/traffic-shaping/create"],
        { action: "create-default" },
        function(stream) {
          console.info("firewall-base-update", stream);
        },
        function(success) {
          // get tc
          context.getTc();
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
.multi-line {
  display: unset;
  text-align: unset;
}

.adjust-line {
  line-height: 26px;
}
</style>