<template>
  <div>
    <h2>{{$t('wan.title')}}</h2>

    <h3 v-if="interfaces.length > 0">{{$t('charts')}}</h3>
    <div v-if="!view.isLoaded && interfaces.length > 0" class="spinner spinner-lg view-spinner"></div>

    <div
      v-if="view.invalidChartsData && interfaces.length > 0"
      class="alert alert-warning alert-dismissable col-sm-12"
    >
      <span class="pficon pficon-warning-triangle-o"></span>
      <strong>{{$t('warning')}}!</strong>
      {{$t('charts_not_updated')}}.
    </div>
    <div v-show="interfaces.length > 0 && view.isLoaded" class="row">
      <div v-for="i in interfaces" v-bind:key="i" class="col-sm-4 stats-divider">
        <h4>
          {{i.name}}
          <span class="gray">({{i.provider.name}})</span>
        </h4>
        <div :id="'chart-in-'+i.name | sanitize" class="col-sm-12"></div>
        <div :id="'chart-out-'+i.name | sanitize" class="col-sm-12"></div>
      </div>
    </div>

    <h3 v-if="interfaces.length > 0">{{$t('actions')}}</h3>
    <button
      v-if="interfaces.length > 0"
      data-toggle="modal"
      data-target="#configureWAN"
      class="btn btn-primary btn-lg"
    >{{$t('wan.configure_wan')}}</button>

    <h3 v-if="interfaces.length > 0">{{$t('list')}}</h3>
    <div v-if="!view.isLoaded" class="spinner spinner-lg view-spinner"></div>
    <div v-if="interfaces.length == 0 && view.isLoaded" class="blank-slate-pf white">
      <div class="blank-slate-pf-icon">
        <span class="fa fa-globe"></span>
      </div>
      <h1>{{$t('wan.no_interfaces_found')}}</h1>
      <p>{{$t('wan.no_interfaces_found_text')}}.</p>
      <div class="blank-slate-pf-main-action">
        <a
          target="_blank"
          href="/nethserver#/network"
          class="btn btn-primary btn-lg"
        >{{$t('wan.go_to_network')}}</a>
      </div>
    </div>

    <div
      v-if="interfaces.length > 0 && view.isLoaded"
      id="pf-list-simple-expansion"
      class="list-group list-view-pf list-view-pf-view wizard-pf-contents-title white no-mg-top"
    >
      <div
        v-for="i in interfaces"
        v-bind:key="i"
        class="list-group-item red-list list-view-pf-expand-active no-shadow mg-bottom-10"
      >
        <div class="list-group-item-header cursor-initial">
          <div class="list-view-pf-actions">
            <a
              tabindex="0"
              @click="speedTest(i)"
              :id="'popover-'+i.name | sanitize"
              data-placement="left"
              data-toggle="popover"
              data-html="true"
              data-trigger="focus"
              :title="$t('wan.speed_info')"
              class="btn btn-default"
            >
              <span class="fa fa-bolt span-right-margin"></span>
              {{$t('wan.speedtest')}}
            </a>
          </div>
          <div class="list-view-pf-main-info">
            <div class="list-view-pf-left">
              <span class="fa fa-globe list-view-pf-icon-sm border-red"></span>
            </div>
            <div class="list-view-pf-body">
              <div class="list-view-pf-description">
                <div class="list-group-item-heading red">
                  {{i.name}}
                  <span class="gray">({{i.provider.name}})</span>
                </div>
                <div class="list-group-item-text">{{i.nslabel}}</div>
              </div>
              <div class="list-view-pf-additional-info">
                <div class="list-view-pf-additional-info-item">
                  <span class="pficon pficon-screen"></span>
                  <strong>{{i.cidr}}</strong> CIDR
                </div>
                <div class="list-view-pf-additional-info-item">
                  <span class="pficon pficon-middleware"></span>
                  <strong>{{i.gateway}}</strong> GW
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="list-group-item-container container-fluid active">
          <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <form class="form-horizontal" v-on:submit.prevent="saveInterface(i)">
                <div class="modal-body">
                  <div :class="['form-group', i.errors.nslabel.hasError ? 'has-error' : '']">
                    <label
                      class="col-sm-3 control-label"
                      for="textInput-modal-markup"
                    >{{$t('wan.provider')}}</label>
                    <div class="col-sm-8">
                      <input required type="text" v-model="i.nslabel" class="form-control">
                      <span v-if="i.errors.nslabel.hasError" class="help-block">
                        {{$t('validation.validation_failed')}}:
                        {{$t('validation.'+i.errors.nslabel.message)}}
                      </span>
                    </div>
                  </div>
                  <div
                    :class="['form-group', i.errors.provider.weight.hasError ? 'has-error' : '']"
                  >
                    <label
                      class="col-sm-3 control-label"
                      for="textInput-modal-markup"
                    >{{$t('wan.weight')}}</label>
                    <div class="col-sm-8">
                      <input
                        required
                        type="number"
                        v-model="i.provider.weight"
                        class="form-control"
                      >
                      <span v-if="i.errors.provider.weight.hasError" class="help-block">
                        {{$t('validation.validation_failed')}}:
                        {{$t('validation.'+i.errors.provider.weight.message)}}
                      </span>
                    </div>
                  </div>
                  <div :class="['form-group', i.errors.FwInBandwidth.hasError ? 'has-error' : '']">
                    <label
                      class="col-sm-3 control-label"
                      for="textInput-modal-markup"
                    >{{$t('wan.inbound_bandwidth')}}</label>
                    <div class="col-sm-8">
                      <input
                        :id="i.name + '-FwInBandwidth' | sanitize"
                        required
                        type="number"
                        class="form-control"
                        v-model="i.FwInBandwidth"
                      >
                      <span v-if="i.errors.FwInBandwidth.hasError" class="help-block">
                        {{$t('validation.validation_failed')}}:
                        {{$t('validation.'+i.errors.FwInBandwidth.message)}}
                      </span>
                    </div>
                  </div>
                  <div :class="['form-group', i.errors.FwOutBandwidth.hasError ? 'has-error' : '']">
                    <label
                      class="col-sm-3 control-label"
                      for="textInput-modal-markup"
                    >{{$t('wan.outbound_bandwidth')}}</label>
                    <div class="col-sm-8">
                      <input
                        :id="i.name + '-FwOutBandwidth' | sanitize"
                        required
                        type="number"
                        class="form-control"
                        v-model="i.FwOutBandwidth"
                      >
                      <span v-if="i.errors.FwOutBandwidth.hasError" class="help-block">
                        {{$t('validation.validation_failed')}}:
                        {{$t('validation.'+i.errors.FwOutBandwidth.message)}}
                      </span>
                    </div>
                  </div>
                </div>
                <div class="modal-footer no-mg-top">
                  <div v-if="i.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
                  <button class="btn btn-primary" type="submit">{{$t('save')}}</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- MODALS -->
    <div class="modal" id="configureWAN" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('wan.configure_wan')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="configureWAN()">
            <div class="modal-body">
              <div :class="['form-group', wan.errors.WanMode.hasError ? 'has-error' : '']">
                <label class="col-sm-4 control-label" for="WanMode-1">{{$t('wan.mode')}}</label>
                <div class="col-sm-8">
                  <input
                    id="WanMode-1"
                    class="col-sm-2 col-xs-2"
                    type="radio"
                    v-model="wan.WanMode"
                    value="balance"
                  >
                  <label
                    class="col-sm-10 col-xs-10 control-label text-align-left"
                    for="WanMode-2"
                  >{{$t('wan.balance')}}</label>
                  <input
                    id="WanMode-2"
                    class="col-sm-2 col-xs-2"
                    type="radio"
                    v-model="wan.WanMode"
                    value="backup"
                  >
                  <label
                    class="col-sm-10 col-xs-10 control-label text-align-left"
                  >{{$t('wan.backup')}}</label>
                </div>
              </div>
              <div :class="['form-group', wan.errors.NotifyWan.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-4 control-label"
                  for="textInput-modal-markup"
                >{{$t('wan.notify_status_change')}}</label>
                <div class="col-sm-8">
                  <input type="checkbox" v-model="wan.NotifyWan" class="form-control">
                  <span v-if="wan.errors.NotifyWan.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+wan.errors.NotifyWan.message)}}
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
                    :value="wan.advanced"
                    :sync="true"
                    @change="toggleAdvancedMode()"
                  />
                </div>
              </div>
              <div
                v-show="wan.advanced"
                :class="['form-group', wan.errors.CheckIP.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-4 control-label"
                  for="textInput-modal-markup"
                >{{$t('wan.check_ip')}}</label>
                <div class="col-sm-8">
                  <textarea v-model="wan.CheckIP" class="form-control"></textarea>
                  <span v-if="wan.errors.CheckIP.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+wan.errors.CheckIP.message)}}
                  </span>
                </div>
              </div>
              <div
                v-show="wan.advanced"
                :class="['form-group', wan.errors.MaxNumberPacketLoss.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-4 control-label"
                  for="textInput-modal-markup"
                >{{$t('wan.max_number_packet_loss')}}</label>
                <div class="col-sm-8">
                  <input type="number" v-model="wan.MaxNumberPacketLoss" class="form-control">
                  <span v-if="wan.errors.MaxNumberPacketLoss.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+wan.errors.MaxNumberPacketLoss.message)}}
                  </span>
                </div>
              </div>
              <div
                v-show="wan.advanced"
                :class="['form-group', wan.errors.MaxPercentPacketLoss.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-4 control-label"
                  for="textInput-modal-markup"
                >{{$t('wan.max_percent_packet_loss')}}</label>
                <div class="col-sm-8">
                  <input type="number" v-model="wan.MaxPercentPacketLoss" class="form-control">
                  <span v-if="wan.errors.MaxPercentPacketLoss.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+wan.errors.MaxPercentPacketLoss.message)}}
                  </span>
                </div>
              </div>
              <div
                v-show="wan.advanced"
                :class="['form-group', wan.errors.PingInterval.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-4 control-label"
                  for="textInput-modal-markup"
                >{{$t('wan.ping_interval')}}</label>
                <div class="col-sm-8">
                  <input type="number" v-model="wan.PingInterval" class="form-control">
                  <span v-if="wan.errors.PingInterval.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+wan.errors.PingInterval.message)}}
                  </span>
                </div>
              </div>
            </div>
            <div class="modal-footer no-mg-top">
              <div v-if="wan.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-primary" type="submit">{{$t('save')}}</button>
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
  name: "WAN",
  data() {
    return {
      view: {
        isLoaded: false,
        invalidChartsData: false
      },
      interfaces: [],
      wan: {
        WanMode: "balance",
        CheckIP: "",
        MaxNumberPacketLoss: 0,
        MaxPercentPacketLoss: 0,
        PingInterval: 0,
        NotifyWan: false,
        errors: this.initWANErrors(),
        advanced: false,
        isLoading: false
      },
      charts: {},
      pollingIntervalId: 0
    };
  },
  mounted() {
    this.getProviders();
  },
  beforeRouteLeave(to, from, next) {
    $(".modal").modal("hide");
    clearInterval(this.pollingIntervalId);
    next();
  },
  methods: {
    toggleAdvancedMode() {
      this.wan.advanced = !this.wan.advanced;
      this.$forceUpdate();
    },
    initCharts() {
      for (var i in this.interfaces) {
        var iface = this.interfaces[i];
        this.charts[iface.name] = {};

        if (!this.charts[iface.name].in) {
          var inName = this.$i18n.t("wan.inbound_bandwidth");

          this.charts[iface.name].in = c3.generate({
            bindto:
              "#" + this.$options.filters.sanitize("chart-in-" + iface.name),
            data: {
              columns: [[inName, 0]],
              type: "gauge"
            },
            gauge: {
              max: iface.FwInBandwidth <= 0 ? 100000 : iface.FwInBandwidth,
              units: ""
            },
            color: {
              pattern: ["#60B044", "#F97600", "#FF0000"],
              threshold: {
                values: [
                  iface.FwInBandwidth / 3,
                  iface.FwInBandwidth / 1.5,
                  iface.FwInBandwidth / 1.25
                ]
              }
            },
            size: {
              height: 100
            }
          });
        }

        if (!this.charts[iface.name].out) {
          var outName = this.$i18n.t("wan.outbound_bandwidth");

          this.charts[iface.name].out = c3.generate({
            bindto:
              "#" + this.$options.filters.sanitize("chart-out-" + iface.name),
            data: {
              columns: [[outName, 0]],
              type: "gauge"
            },
            gauge: {
              max: iface.FwOutBandwidth <= 0 ? 100000 : iface.FwOutBandwidth,
              units: ""
            },
            color: {
              pattern: ["#60B044", "#F97600", "#FF0000"],
              threshold: {
                values: [
                  iface.FwOutBandwidth / 3,
                  iface.FwOutBandwidth / 1.5,
                  iface.FwOutBandwidth / 1.25
                ]
              }
            },
            size: {
              height: 100
            }
          });
        }
      }

      // start polling
      var context = this;
      if (context.pollingIntervalId == 0) {
        context.pollingIntervalId = setInterval(function() {
          context.updateCharts();
        }, 2500);
      }
    },
    updateCharts() {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/wan/read"],
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
            var iface = success[i];
            if (iface) {
              var inName = context.$i18n.t("wan.inbound_bandwidth");
              var outName = context.$i18n.t("wan.outbound_bandwidth");

              context.view.invalidChartsData = false;

              context.charts[i].in.load({
                columns: [[inName, iface.in]]
              });
              context.charts[i].out.load({
                columns: [[outName, iface.out]]
              });
            } else {
              context.view.invalidChartsData = true;
              context.$forceUpdate();
            }
          }
        },
        function(error) {
          console.error(error);
        }
      );
    },
    initErrors() {
      return {
        FwOutBandwidth: {
          hasError: false,
          message: ""
        },
        FwInBandwidth: {
          hasError: false,
          message: ""
        },
        nslabel: {
          hasError: false,
          message: ""
        },
        provider: {
          weight: {
            hasError: false,
            message: ""
          }
        }
      };
    },
    initWANErrors() {
      return {
        WanMode: {
          hasError: false,
          message: ""
        },
        CheckIP: {
          hasError: false,
          message: ""
        },
        MaxNumberPacketLoss: {
          hasError: false,
          message: ""
        },
        MaxPercentPacketLoss: {
          hasError: false,
          message: ""
        },
        PingInterval: {
          hasError: false,
          message: ""
        },
        NotifyWan: {
          hasError: false,
          message: ""
        }
      };
    },
    getProviders() {
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
            iface.isLoading = false;
            iface.speedtest = {
              isLoaded: false
            };
            iface.errors = context.initErrors();
            $(
              "#" +
                context.$options.filters.sanitize(iface.name) +
                "-FwOutBandwidth"
            ).val(iface.FwOutBandwidth);
            $(
              "#" +
                context.$options.filters.sanitize(iface.name) +
                "-FwInBandwidth"
            ).val(iface.FwInBandwidth);
            interfaces.push(iface);
          }
          context.interfaces = interfaces;

          context.wan = success.configuration.multiwan;
          context.wan.NotifyWan =
            context.wan.NotifyWan == "enabled" ? true : false;
          context.wan.CheckIP = context.wan.CheckIP.join("\n");
          context.wan.advanced = false;
          context.wan.isLoading = false;
          context.wan.errors = context.initWANErrors();

          context.view.isLoaded = true;

          setTimeout(function() {
            $("[data-toggle=popover]")
              .popovers()
              .popovers()
              .on("hidden.bs.popover", function(e) {
                $(e.target).data("bs.popover").inState.click = false;
              });
            context.initCharts();
          }, 250);
        },
        function(error) {
          console.error(error);
          context.view.isLoaded = true;
        }
      );
    },
    speedTest(iface) {
      var popover = $(
        "#" + this.$options.filters.sanitize("popover-" + iface.name)
      ).data("bs.popover");

      if (!iface.speedtest.isLoaded && popover) {
        popover.options.content = '<div class="spinner spinner-sm"></div>';
        popover.show();

        var context = this;
        nethserver.exec(
          ["nethserver-firewall-base/wan/read"],
          {
            action: "speedtest",
            interface: iface.name
          },
          null,
          function(success) {
            try {
              success = JSON.parse(success);
            } catch (e) {
              console.error(e);
            }

            popover.options.content =
              '<b class="col-sm-6">' +
              context.$i18n.t("download") +
              '</b><span class="col-sm-6">' +
              ((success.download &&
                context.$options.filters.byteFormat(success.download)) ||
                "-") +
              "</span>";

            popover.options.content +=
              '<b class="col-sm-6">' +
              context.$i18n.t("upload") +
              '</b><span class="col-sm-6">' +
              ((success.upload &&
                context.$options.filters.byteFormat(success.upload)) ||
                "-") +
              "</span>";

            popover.options.content +=
              '<b class="col-sm-6">' +
              context.$i18n.t("wan.ping") +
              '</b><span class="col-sm-6">' +
              (success.ping ? success.ping + " ms" : "-") +
              "</span>";

            popover.options.content +=
              '<span class="col-sm-6">' +
              "<button onclick=\"setSpeedValues('" +
              context.$options.filters.sanitize(iface.name) +
              "'," +
              Math.round(success.download / 1024) +
              "," +
              Math.round(success.upload / 1024) +
              ')" class="btn btn-primary btn-sm no-mg-left mg-top-5">' +
              context.$i18n.t("wan.use_this_set") +
              "</button>" +
              "</span><script>" +
              "function setSpeedValues(iface, down, up) {" +
              "$('#'+iface+'-FwInBandwidth').val(down);" +
              "$('#'+iface+'-FwOutBandwidth').val(up);" +
              "}";

            iface.speedtest.isLoaded = true;
            popover.show();
          },
          function(error) {
            iface.speedtest.isLoaded = true;
            console.error(error);
          }
        );
      }
    },
    saveInterface(iface) {
      var context = this;

      iface.FwInBandwidth = parseInt(
        $(
          "#" + this.$options.filters.sanitize(iface.name) + "-FwInBandwidth"
        ).val()
      );
      iface.FwOutBandwidth = parseInt(
        $(
          "#" + this.$options.filters.sanitize(iface.name) + "-FwOutBandwidth"
        ).val()
      );

      var providerObj = {
        action: "provider",
        FwOutBandwidth: iface.FwOutBandwidth,
        FwInBandwidth: iface.FwInBandwidth,
        nslabel: iface.nslabel,
        weight: iface.provider.weight,
        name: iface.name
      };

      iface.isLoading = true;
      nethserver.exec(
        ["nethserver-firewall-base/wan/validate"],
        providerObj,
        null,
        function(success) {
          iface.isLoading = false;

          // notifications
          nethserver.notifications.success = context.$i18n.t(
            "wan.provider_configured_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "wan.provider_configured_error"
          );

          // update values
          nethserver.exec(
            ["nethserver-firewall-base/wan/update"],
            providerObj,
            function(stream) {
              console.info("firewall-base-update", stream);
            },
            function(success) {
              // get interfaces
              context.getProviders();
            },
            function(error, data) {
              console.error(error);
            }
          );
        },
        function(error, data) {
          var errorData = {};
          iface.isLoading = false;
          iface.errors = context.initErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              iface.errors[attr.parameter].hasError = true;
              iface.errors[attr.parameter].message = attr.error;
            }
          } catch (e) {
            console.error(e);
          }
        }
      );
    },
    configureWAN() {
      var context = this;

      var wanObj = {
        action: "wan",
        WanMode: context.wan.WanMode,
        CheckIP: context.wan.CheckIP.split("\n"),
        NotifyWan: context.wan.NotifyWan ? "enabled" : "disabled",
        MaxNumberPacketLoss: context.wan.MaxNumberPacketLoss,
        MaxPercentPacketLoss: context.wan.MaxPercentPacketLoss,
        PingInterval: context.wan.PingInterval
      };

      context.wan.isLoading = true;
      context.$forceUpdate();
      nethserver.exec(
        ["nethserver-firewall-base/wan/validate"],
        wanObj,
        null,
        function(success) {
          context.wan.isLoading = false;
          $("#configureWAN").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "wan.multiwan_configured_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "wan.multiwan_configured_error"
          );

          // update values
          nethserver.exec(
            ["nethserver-firewall-base/wan/update"],
            wanObj,
            function(stream) {
              console.info("firewall-base-update", stream);
            },
            function(success) {
              // get interfaces
              context.getProviders();
            },
            function(error, data) {
              console.error(error, data);
            }
          );
        },
        function(error, data) {
          var errorData = {};
          context.wan.isLoading = false;
          context.wan.errors = context.initWANErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              context.wan.errors[attr.parameter].hasError = true;
              context.wan.errors[attr.parameter].message = attr.error;
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
.red {
  color: #cc0000;
}

.red-list {
  border-left: 3px solid #cc0000 !important;
}

.green {
  color: #3f9c35;
}

.green-list {
  border-left: 3px solid #3f9c35 !important;
}

.yellow {
  color: #f0ab00;
}

.yellow-list {
  border-left: 3px solid #f0ab00 !important;
}

.free {
  color: #72767b;
}

.free-list {
  border-left: 3px solid #72767b !important;
}

.missing {
  color: #703fec;
}

.missing-list {
  border-left: 3px solid #703fec !important;
}

.orange {
  color: #ec7a08;
}

.orange-list {
  border-left: 3px solid #ec7a08 !important;
}

.blue {
  color: #0088ce;
}

.blue-list {
  border-left: 3px solid #0088ce !important;
}

.other {
  color: black;
}

.other-list {
  border-left: 3px solid black !important;
}

.gray {
  color: #72767b !important;
}

.border-red {
  border: 2px solid #cc0000 !important;
}

.border-gray {
  border: 2px solid #72767b !important;
}

.border-free {
  border: 2px solid #72767b !important;
}

.border-missing {
  border: 2px solid #703fec !important;
}

.border-empty {
  border: 2px solid black !important;
}

.border-blue {
  border: 2px solid #0088ce !important;
}

.border-orange {
  border: 2px solid #ec7a08 !important;
}

.white {
  background-color: #fff !important;
}

.spinner-speed {
  float: left;
  margin-top: 5px;
}

.stats-divider:not(:last-child) {
  border-right: 1px solid #d1d1d1;
}
</style>