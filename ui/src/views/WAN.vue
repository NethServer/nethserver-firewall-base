<template>
  <div>
    <h2>{{$t('wan.title')}}</h2>

    <h3 v-if="view.isChartLoaded && interfaces.length > 0">{{$t('charts')}}</h3>
    <a
      v-if="view.isChartLoaded && interfaces.length > 0"
      @click="toggleCharts()"
    >{{view.chartsShowed ? $t('hide_charts') : $t('show_charts')}}</a>
    <div
      v-if="!view.isChartLoaded && interfaces.length > 0"
      class="spinner spinner-lg view-spinner"
    ></div>
    <div :class="view.chartsShowed ? '' : 'hidden'">
      <div
        v-if="view.invalidChartsData && interfaces.length > 0"
        class="alert alert-warning alert-dismissable col-sm-12"
      >
        <span class="pficon pficon-warning-triangle-o"></span>
        <strong>{{$t('warning')}}!</strong>
        {{$t('charts_not_updated')}}.
      </div>
      <div
        v-show="interfaces.length > 0 && view.isChartLoaded && !view.invalidChartsData"
        class="row"
      >
        <div v-for="i in interfaces" v-bind:key="i" class="col-sm-4">
          <h4>
            {{i.nslabel}}
            <span class="gray">({{i.provider.name}})</span>
          </h4>
          <div :id="'chart-in-'+i.name | sanitize" class="col-sm-12"></div>
          <div :id="'chart-out-'+i.name | sanitize" class="col-sm-12"></div>
        </div>
      </div>
    </div>

    <div v-if="view.isLoadedInterface">
      <h3>{{$t('wan.configuration')}}</h3>
      <div class="panel panel-default" id="provider-markup">
        <div class="panel-heading">
          <button
            id="change-provider-btn"
            data-toggle="modal"
            data-target="#configureWAN"
            class="btn btn-primary"
          >{{$t('configure')}}</button>
          <span class="panel-title">
            <span>{{$t('wan.mode')}}: {{wan.WanMode == 'balance' ? $t('wan.balance') : $t('wan.backup')}}</span>
          </span>
          <a
            class="mg-left-5"
            data-toggle="collapse"
            data-parent="#provider-markup"
            href="#providerDetails"
          >{{$t('details')}}</a>
        </div>
        <div id="providerDetails" class="panel-collapse collapse in">
          <div v-show="!view.isLoadedInterface" class="spinner spinner-lg view-spinner"></div>
          <div
            v-show="interfaces.length == 0 && view.isLoadedInterface"
            class="blank-slate-pf white"
          >
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
            v-if="interfaces.length > 0 && view.isLoadedInterface"
            id="pf-list-simple-expansion"
            class="list-group list-view-pf list-view-pf-view wizard-pf-contents-title white no-mg-top"
          >
            <div
              v-for="i in interfaces"
              v-bind:key="i"
              class="list-group-item wan-list list-view-pf-expand-active no-shadow mg-bottom-10"
            >
              <div class="list-group-item-header">
                <div class="list-view-pf-actions">
                  <a
                    tabindex="0"
                    @click="speedTest(i)"
                    :id="'popover-'+i.name | sanitize"
                    data-placement="left"
                    data-toggle="popover"
                    data-html="true"
                    :title="$t('wan.speed_info')"
                    class="btn btn-default"
                  >
                    <span class="fa fa-bolt span-right-margin"></span>
                    {{$t('wan.speedtest')}}
                  </a>
                </div>
                <div @click="openInterfaceDetails(i)" class="list-view-pf-main-info">
                  <div class="list-view-pf-left">
                    <span class="fa fa-globe list-view-pf-icon-sm border-red"></span>
                  </div>
                  <div class="list-view-pf-body">
                    <div class="list-view-pf-description">
                      <div class="list-group-item-heading red">
                        {{i.name}}
                        <span class="gray">({{i.provider.name}})</span>
                      </div>
                      <div class="list-group-item-text more-space-description">{{i.nslabel || '-'}}</div>
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
                      <div
                        v-if="i.FwInBandwidth == 0 || i.FwOutBandwidth == 0"
                        class="list-view-pf-additional-info-item"
                      >
                        <span
                          data-toggle="tooltip"
                          data-placement="top"
                          data-html="true"
                          :title="i.FwInBandwidth == 0 && i.FwOutBandwidth == 0 ? $t('wan.in_out_bound_zero') : i.FwOutBandwidth == 0 && i.FwInBandwidth != 0 ? $t('wan.outbound_zero') : $t('wan.inbound_zero')"
                          class="pficon pficon-warning-triangle-o"
                        ></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div
                :class="['list-group-item-container container-fluid', i.opened ? 'active':'hidden']"
              >
                <div class="row">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <form class="form-horizontal" v-on:submit.prevent="saveInterface(i)">
                      <div class="modal-body">
                        <div :class="['form-group', i.errors.nslabel.hasError ? 'has-error' : '']">
                          <label
                            class="col-sm-3 control-label"
                            for="textInput-modal-markup"
                          >{{$t('wan.provider')}}</label>
                          <div class="col-sm-9">
                            <input required type="text" v-model="i.nslabel" class="form-control">
                            <span v-if="i.errors.nslabel.hasError" class="help-block">
                              {{$t('validation.validation_failed')}}:
                              {{$t('validation.'+i.errors.nslabel.message)}}
                            </span>
                          </div>
                        </div>
                        <div
                          :class="['form-group', i.errors.weight.hasError ? 'has-error' : '']"
                        >
                          <label
                            class="col-sm-3 control-label"
                            for="textInput-modal-markup"
                          >{{$t('wan.weight')}}</label>
                          <div class="col-sm-9">
                            <input
                              required
                              type="number"
                              v-model="i.provider.weight"
                              class="form-control"
                            >
                            <span v-if="i.errors.weight.hasError" class="help-block">
                              {{$t('validation.validation_failed')}}:
                              {{$t('validation.'+i.errors.weight.message)}}
                            </span>
                          </div>
                        </div>
                        <div
                          :class="['form-group', i.errors.FwInBandwidth.hasError ? 'has-error' : '']"
                        >
                          <label
                            class="col-sm-3 control-label"
                            for="textInput-modal-markup"
                          >{{$t('wan.inbound_bandwidth')}}</label>
                          <div class="col-sm-9">
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
                        <div
                          :class="['form-group', i.errors.FwOutBandwidth.hasError ? 'has-error' : '']"
                        >
                          <label
                            class="col-sm-3 control-label"
                            for="textInput-modal-markup"
                          >{{$t('wan.outbound_bandwidth')}}</label>
                          <div class="col-sm-9">
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
        </div>
      </div>
      <div class="divider"></div>
    </div>

    <h3 v-if="view.isLoaded">{{$t('rules.title')}}</h3>
    <button
      v-if="view.isLoaded && rules.length > 0"
      @click="openCreateRule()"
      class="btn btn-primary btn-lg"
    >{{$t('rules.create_divert_rule')}}</button>
    <div v-if="!view.isLoaded" class="spinner spinner-lg view-spinner"></div>
    <div v-if="rules.length == 0 && view.isLoaded" class="blank-slate-pf white">
      <div class="blank-slate-pf-icon">
        <span class="fa fa-share"></span>
      </div>
      <div v-if="interfaces.length > 1">
        <h1>{{$t('rules.no_rules_found')}}</h1>
        <p>{{$t('rules.no_rules_found_text')}}.</p>
        <div  class="blank-slate-pf-main-action">
          <button @click="openCreateRule()" class="btn btn-primary">{{$t('rules.create_divert_rule')}}</button>
        </div>
      </div>
      <div v-if="interfaces.length <= 1">
        <h3>{{$t('rules.two_providers_needed')}}</h3>
      </div>
    </div>
    <ul
      v-if="rules.length > 0 && view.isLoaded"
      v-sortable="{onEnd: reorder, handle: '.drag-here'}"
      class="list-group list-view-pf list-view-pf-view no-mg-top mg-top-10"
    >
      <li
        :class="[r.status == 'disabled' ? 'gray-list' : mapList(r.Action), 'list-group-item', r.status == 'disabled' ? 'gray' : '']"
        v-for="r in rules"
        v-bind:key="r"
      >
        <div class="drag-size">
          <span class="gray mg-right-5">{{r.Action.split(';')[1] | uppercase}}</span>
        </div>
        <div class="list-view-pf-checkbox drag-here">
          <span class="fa fa-bars"></span>
        </div>
        <div class="list-view-pf-actions">
          <button
            @click="r.status == 'disabled' ? enableRule(r) : openEditRule(r, false)"
            :class="['btn btn-default', r.status == 'disabled' ? 'btn-primary' : '']"
          >
            <span
              :class="['fa', r.status == 'disabled' ? 'fa-check' : 'fa-edit', 'span-right-margin']"
            ></span>
            {{r.status == 'disabled' ? $t('enable') : $t('edit')}}
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
            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownKebabRight9">
              <li>
                <a @click="enableRule(r)">
                  <span
                    :class="['fa', r.status == 'enabled' ? 'fa-lock' : 'fa-check', 'span-right-margin']"
                  ></span>
                  {{r.status == 'enabled' ? $t('disable') : $t('enable')}}
                </a>
              </li>
              <li @click="openEditRule(r, true)">
                <a>
                  <span class="fa fa-clone span-right-margin"></span>
                  {{$t('rules.duplicate')}}
                </a>
              </li>
              <li role="separator" class="divider"></li>
              <li @click="openDeleteRule(r)">
                <a>
                  <span class="fa fa-times span-right-margin"></span>
                  {{$t('delete')}}
                </a>
              </li>
            </ul>
          </div>
        </div>
        <div class="list-view-pf-main-info small-list">
          <div class="list-view-pf-left">
            <span
              data-toggle="tooltip"
              data-placement="top"
              data-html="true"
              :title="mapTitleAction(r)"
              :class="[mapIcon(r.Action, r.status), 'list-view-pf-icon-sm']"
            ></span>
          </div>
          <div class="list-view-pf-body">
            <div class="list-view-pf-description rules-src-dst">
              <div class="list-group-item-heading">
                <span
                  data-toggle="tooltip"
                  data-placement="top"
                  data-html="true"
                  :title="mapTitleSrc(r)"
                  class="handle-overflow"
                >
                  <span :class="mapObjectIcon(r.Src, r.status)"></span>
                  <span
                    :class="[r.status == 'disabled' ? 'gray' : r.Src.name.toLowerCase(),'mg-left-5']"
                  >
                    <span
                      v-show="r.Src.type == 'raw'"
                      class="pficon pficon-warning-triangle-o mg-right-5"
                    ></span>
                    {{r.Src.type == 'fw' || r.Src.type == 'role' || r.Src.type == 'any' ? (r.Src.name.toUpperCase()): r.Src.name}}
                    <a
                      v-show="r.Src.type == 'raw'"
                      @click="openCreateObject(r.Src)"
                    >{{$t('create')}} {{$t('objects.'+r.Src.object)}}</a>
                  </span>
                </span>
              </div>
              <div class="list-group-item-text">
                <span :class="[mapArrow(r.Action), 'mg-right-10 big-icon']"></span>
                <span
                  data-toggle="tooltip"
                  data-placement="top"
                  data-html="true"
                  :title="mapTitleDst(r)"
                  class="handle-overflow"
                >
                  <span :class="mapObjectIcon(r.Dst, r.status)"></span>
                  <span
                    :class="[r.status == 'disabled' ? 'gray' : r.Dst.name.toLowerCase(),'mg-left-5']"
                  >
                    <span
                      v-show="r.Dst.type == 'raw'"
                      class="pficon pficon-warning-triangle-o mg-right-5"
                    ></span>
                    {{r.Dst.type == 'fw' || r.Dst.type == 'role' || r.Dst.type == 'any' ? (r.Dst.name.toUpperCase()): r.Dst.name}}
                    <a
                      v-show="r.Dst.type == 'raw'"
                      @click="openCreateObject(r.Src)"
                    >{{$t('create')}} {{$t('objects.'+r.Src.object)}}</a>
                  </span>
                </span>
              </div>
            </div>
            <div class="list-view-pf-additional-info rules-info">
              <div
                v-show="r.Service"
                data-toggle="tooltip"
                data-placement="top"
                data-html="true"
                :title="mapTitleService(r)"
                class="list-view-pf-additional-info-item"
              >
                <span class="fa fa-cogs"></span>
                <strong>{{r.Service && r.Service.name}}</strong>
              </div>
              <div
                v-show="r.Time"
                data-toggle="tooltip"
                data-placement="top"
                data-html="true"
                :title="mapTitleTime(r)"
                class="list-view-pf-additional-info-item"
              >
                <span class="fa fa-clock-o"></span>
                <strong>{{r.Time && r.Time.name}}</strong>
              </div>
              <div class="list-view-pf-additional-info-item">{{r.Description}}</div>
            </div>
          </div>
        </div>
      </li>
    </ul>

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
                <label class="col-sm-3 control-label">{{$t('wan.mode')}}</label>
                <div class="col-sm-9">
                  <input
                    id="WanMode-1"
                    class="col-sm-2 col-xs-2"
                    type="radio"
                    v-model="wan.WanMode"
                    value="balance"
                  >
                  <label
                    class="col-sm-10 col-xs-10 control-label text-align-left"
                    for="WanMode-1"
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
                    for="WanMode-2"
                  >{{$t('wan.backup')}}</label>
                </div>
              </div>
              <div :class="['form-group', wan.errors.NotifyWan.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('wan.notify_status_change')}}</label>
                <div class="col-sm-9">
                  <input type="checkbox" v-model="wan.NotifyWan" class="form-control">
                  <small v-if="wan.EmailAddress">{{wan.EmailAddress}}</small>
                  <span v-if="wan.errors.NotifyWan.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+wan.errors.NotifyWan.message)}}
                  </span>
                </div>
              </div>
              <legend class="fields-section-header-pf" aria-expanded="true">
                <span
                  :class="['fa fa-angle-right field-section-toggle-pf', wan.advanced ? 'fa-angle-down' : '']"
                ></span>
                <a
                  class="field-section-toggle-pf"
                  @click="toggleAdvancedMode()"
                >{{$t('advanced_mode')}}</a>
              </legend>

              <div
                v-show="wan.advanced"
                :class="['form-group', wan.errors.CheckIP.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('wan.check_ip')}}</label>
                <div class="col-sm-9">
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
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('wan.max_number_packet_loss')}}</label>
                <div class="col-sm-9">
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
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('wan.max_percent_packet_loss')}}</label>
                <div class="col-sm-9">
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
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('wan.ping_interval')}}</label>
                <div class="col-sm-9">
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

    <div class="modal" id="createRuleModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{newRule.isEdit ? $t('rules.edit_rule') : newRule.isDuplicate ? $t('rules.duplicate_rule') : $t('rules.create_divert_rule')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveRule()">
            <div class="modal-body">
              <div :class="['form-group', newRule.errors.Action.hasError ? 'has-error' : '']">
                <label class="col-sm-3 control-label">{{$t('rules.provider')}}</label>
                <div class="col-sm-9">
                  <select v-model="newRule.Action" class="form-control" required>
                    <option
                      v-for="i in interfaces"
                      v-bind:key="i"
                      :value="'provider;'+i.provider.name"
                    >{{i.provider.name}}</option>
                  </select>
                  <span v-if="newRule.errors.Action.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newRule.errors.Action.message)}}
                  </span>
                </div>
              </div>
              <div :class="['form-group', newRule.errors.Src.hasError ? 'has-error' : '']">
                <label class="col-sm-3 control-label">
                  {{$t('rules.source')}}
                  <doc-info
                    :placement="'top'"
                    :title="$t('rules.source')"
                    :chapter="'rules_source'"
                    :inline="true"
                  ></doc-info>
                </label>
                <div class="col-sm-9">
                  <suggestions
                    v-model="newRule.Src"
                    required
                    :options="autoOptions"
                    :onInputChange="filterSrcAuto"
                    :onItemSelected="selectSrcAuto"
                  >
                    <div slot="item" slot-scope="props" class="single-item">
                      <span>
                        <span
                          v-show="props.item.typeId == 'role'"
                          :class="['square-'+ props.item.name]"
                        ></span>
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
                  <span
                    v-if="newRule.SrcType && newRule.SrcType.length > 0"
                    class="help-block gray"
                  >{{newRule.SrcType}}</span>
                  <span v-if="newRule.errors.Src.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newRule.errors.Src.message)}}
                  </span>
                </div>
              </div>

              <div :class="['form-group', newRule.errors.Dst.hasError ? 'has-error' : '']">
                <label class="col-sm-3 control-label">
                  {{$t('rules.destination')}}
                  <doc-info
                    :placement="'top'"
                    :title="$t('rules.destination')"
                    :chapter="'rules_destination'"
                    :inline="true"
                  ></doc-info>
                </label>
                <div class="col-sm-9">
                  <suggestions
                    v-model="newRule.Dst"
                    required
                    :options="autoOptions"
                    :onInputChange="filterDstAuto"
                    :onItemSelected="selectDstAuto"
                  >
                    <div slot="item" slot-scope="props" class="single-item">
                      <span>
                        <span
                          v-show="props.item.typeId == 'role'"
                          :class="['square-'+ props.item.name]"
                        ></span>
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
                  <span
                    v-if="newRule.DstType && newRule.DstType.length > 0"
                    class="help-block gray"
                  >{{newRule.DstType}}</span>
                  <span v-if="newRule.errors.Dst.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newRule.errors.Dst.message)}}
                  </span>
                </div>
              </div>

              <div :class="['form-group', newRule.errors.Service.hasError ? 'has-error' : '']">
                <label class="col-sm-3 control-label">{{$t('rules.service')}}</label>
                <div class="col-sm-9">
                  <suggestions
                    v-model="newRule.Service"
                    :options="autoOptions"
                    :onInputChange="filterServiceAuto"
                    :onItemSelected="selectServiceAuto"
                  >
                    <div slot="item" slot-scope="props" class="single-item">
                      <span>
                        {{props.item.name}}
                        <span
                          v-show="props.item.Ports"
                          class="gray"
                        >({{props.item.Ports.join(', ')}})</span>
                        <i class="mg-left-5">{{props.item.Description}}</i>
                      </span>
                    </div>
                  </suggestions>
                  <span
                    v-if="newRule.ServiceType && newRule.ServiceType.length > 0"
                    class="help-block gray"
                  >{{newRule.ServiceType}}</span>
                  <span v-if="newRule.errors.Service.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newRule.errors.Service.message)}}
                  </span>
                </div>
              </div>

              <legend class="fields-section-header-pf" aria-expanded="true">
                <span
                  :class="['fa fa-angle-right field-section-toggle-pf', newRule.advanced ? 'fa-angle-down' : '']"
                ></span>
                <a
                  class="field-section-toggle-pf"
                  @click="toggleAdvancedRuleMode()"
                >{{$t('advanced_mode')}}</a>
              </legend>

              <div
                v-show="newRule.advanced"
                :class="['form-group', newRule.errors.Description.hasError ? 'has-error' : '']"
              >
                <label class="col-sm-3 control-label">{{$t('rules.description')}}</label>
                <div class="col-sm-9">
                  <input class="form-control" type="text" v-model="newRule.Description">
                  <span v-if="newRule.errors.Description.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newRule.errors.Description.message)}}
                  </span>
                </div>
              </div>

              <div
                v-show="newRule.advanced"
                :class="['form-group', newRule.errors.Time.hasError ? 'has-error' : '']"
              >
                <label class="col-sm-3 control-label">{{$t('rules.time_condition')}}</label>
                <div class="col-sm-9">
                  <suggestions
                    v-model="newRule.Time"
                    :options="autoOptions"
                    :onInputChange="filterTimeAuto"
                    :onItemSelected="selectTimeAuto"
                  >
                    <div slot="item" slot-scope="props" class="single-item">
                      <span>
                        {{props.item.name}}
                        <i class="mg-left-5">{{props.item.Description}}</i>
                      </span>
                    </div>
                  </suggestions>
                  <span
                    v-if="newRule.TimeType && newRule.TimeType.length > 0"
                    class="help-block gray"
                  >{{newRule.TimeType}}</span>
                  <span v-if="newRule.errors.Time.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newRule.errors.Time.message)}}
                  </span>
                </div>
              </div>
            </div>
            <div class="modal-footer no-mg-top">
              <div v-if="newRule.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button
                class="btn btn-primary"
                type="submit"
              >{{newRule.isEdit ? $t('edit') : newRule.isDuplicate ? $t('rules.duplicate') : $t('save')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal" id="deleteRuleModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('rules.delete_rule')}} {{currentRule.id}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deleteRule(currentRule)">
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
    <div class="modal" id="createObjectModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('objects.add_'+newObject.type)}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveObject(newObject)">
            <div class="modal-body">
              <div :class="['form-group', newObject.errors.name.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.name')}}</label>
                <div class="col-sm-9">
                  <input required type="text" v-model="newObject.name" class="form-control">
                  <span
                    v-if="newObject.errors.name.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newObject.errors.name.message)}}</span>
                </div>
              </div>
              <div
                v-if="newObject.IpAddress"
                :class="['form-group', newObject.errors.IpAddress.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.ip_address')}}</label>
                <div class="col-sm-9">
                  <input required type="text" v-model="newObject.IpAddress" class="form-control">
                  <span
                    v-if="newObject.errors.IpAddress.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newObject.errors.IpAddress.message)}}</span>
                </div>
              </div>
              <div
                v-if="newObject.Address"
                :class="['form-group', newObject.errors.Address.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.network')}}</label>
                <div class="col-sm-9">
                  <input required type="text" v-model="newObject.Address" class="form-control">
                  <span
                    v-if="newObject.errors.Address.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newObject.errors.Address.message)}}</span>
                </div>
              </div>
              <div
                :class="['form-group', newObject.errors.Description.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.description')}}</label>
                <div class="col-sm-9">
                  <input type="text" v-model="newObject.Description" class="form-control">
                  <span
                    v-if="newObject.errors.Description.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newObject.errors.Description.message)}}</span>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <div v-if="newObject.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
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
        isLoadedInterface: false,
        isChartLoaded: false,
        invalidChartsData: false,
        chartsShowed: false
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
      pollingIntervalId: 0,
      rules: [],
      hosts: [],
      hostGroups: [],
      ipRanges: [],
      cidrSubs: [],
      zones: [],
      timeConditions: [],
      services: [],
      roles: [],
      autoOptions: {
        inputClass: "form-control"
      },
      newRule: this.initRule(),
      currentRule: {},
      status: {},
      newObject: this.initObject()
    };
  },
  mounted() {
    this.getProviders();
    this.getRules();
    this.getHosts();
    this.getHostGroups();
    this.getIPRanges();
    this.getCIDRSubs();
    this.getZones();
    this.getTimeConditions();
    this.getServices();
    this.getRoles();

    var context = this;
    context.$parent.$on("changes-applied", function() {
      context.getProviders();
      context.getRules();
      context.getHosts();
      context.getHostGroups();
      context.getIPRanges();
      context.getCIDRSubs();
      context.getZones();
      context.getTimeConditions();
      context.getServices();
      context.getApplications();
      context.getRoles();
    });
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
    reorder({ oldIndex, newIndex }) {
      const movedItem = this.rules.splice(oldIndex, 1)[0];
      this.rules.splice(newIndex, 0, movedItem);

      var ids = this.rules.map(function(i) {
        return i.id;
      });

      // notification
      nethserver.notifications.success = this.$i18n.t("rules.rule_updated_ok");
      nethserver.notifications.error = this.$i18n.t("rules.rule_updated_error");

      nethserver.exec(
        ["nethserver-firewall-base/wan/update"],
        {
          action: "reorder",
          rules: ids
        },
        function(stream) {
          console.info("firewall-base-update", stream);
        },
        function(success) {},
        function(error, data) {
          console.error(error, data);
        }
      );
    },
    mapTitleAction(rule) {
      var html = "<b>" + rule.Action.split(";")[1].toUpperCase() + "</b><br>";

      return html;
    },
    mapTitleSrc(rule) {
      var html =
        "<b>" +
        (rule.Src.type == "fw" ||
        rule.Src.type == "role" ||
        rule.Src.type == "any"
          ? rule.Src.name.toUpperCase()
          : rule.Src.name) +
        "</b>";

      // host zone
      if (rule.Src.zone && rule.Src.zone.length > 0) {
        html +=
          ' | <span class="' +
          this.mapZoneIcon(rule.Src.zone) +
          ' mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          rule.Src.zone.toUpperCase() +
          "</span>";
      }

      html += "<br>";

      // host
      if (rule.Src.IpAddress) {
        html +=
          '<div><span class="pficon pficon-screen mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          rule.Src.IpAddress +
          "</span></div>";
      }

      // cidr
      if (rule.Src.Address) {
        html +=
          '<div><span class="pficon pficon-screen mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          rule.Src.Address +
          "</span></div>";
      }

      // ip range
      if (rule.Src.Start || rule.Src.End) {
        html +=
          '<div><span class="pficon pficon-screen mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          rule.Src.Start +
          " - " +
          rule.Src.End +
          "</span></div>";
      }

      // zone
      if (rule.Src.Network || rule.Src.Interface) {
        html +=
          '<div><span class="pficon pficon-screen mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          rule.Src.Network +
          "</span></div>";

        html +=
          '<div><span class="pficon pficon-plugged mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          rule.Src.Interface +
          "</span></div>";
      }

      // role
      if (rule.Src.type == "role") {
        if (rule.Src.Interfaces && rule.Src.Interfaces.length > 0) {
          html +=
            '<div><span class="pficon pficon-plugged mg-right-5 mg-top-5 detail-icon"></span>' +
            "<span>" +
            rule.Src.Interfaces.join(", ") +
            "</span></div>";
        }
      }

      html +=
        '<span class="type-info"><b>' +
        this.$i18n.t("objects." + rule.Src.type) +
        "</b></span>";

      return html;
    },
    mapTitleDst(rule) {
      var html =
        "<b>" +
        (rule.Dst.type == "fw" ||
        rule.Dst.type == "role" ||
        rule.Dst.type == "any"
          ? rule.Dst.name.toUpperCase()
          : rule.Dst.name) +
        "</b>";

      // host zone
      if (rule.Dst.zone && rule.Dst.zone.length > 0) {
        html +=
          ' | <span class="' +
          this.mapZoneIcon(rule.Dst.zone) +
          ' mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          rule.Dst.zone.toUpperCase() +
          "</span>";
      }

      html += "<br>";

      // host
      if (rule.Dst.IpAddress) {
        html +=
          '<div><span class="pficon pficon-screen mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          rule.Dst.IpAddress.split(",").join("\n") +
          "</span></div>";
      }

      // cidr
      if (rule.Dst.Address) {
        html +=
          '<div><span class="pficon pficon-screen mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          rule.Dst.Address +
          "</span></div>";
      }

      // ip range
      if (rule.Dst.Start || rule.Dst.End) {
        html +=
          '<div><span class="pficon pficon-screen mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          rule.Dst.Start +
          " - " +
          rule.Dst.End +
          "</span></div>";
      }

      // zone
      if (rule.Dst.Network || rule.Dst.Interface) {
        html +=
          '<div><span class="pficon pficon-screen mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          rule.Dst.Network +
          "</span></div>";

        html +=
          '<div><span class="pficon pficon-plugged mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          rule.Dst.Interface +
          "</span></div>";
      }

      // role
      if (rule.Dst.type == "role") {
        if (rule.Dst.Interfaces && rule.Dst.Interfaces.length > 0) {
          html +=
            '<div><span class="pficon pficon-plugged mg-right-5 mg-top-5 detail-icon"></span>' +
            "<span>" +
            rule.Dst.Interfaces.join(", ") +
            "</span></div>";
        }
      }

      html +=
        '<span class="type-info"><b>' +
        this.$i18n.t("objects." + rule.Dst.type) +
        "</b></span>";

      return html;
    },
    mapTitleService(rule) {
      if (!rule.Service) {
        return "";
      }
      var html =
        "<b>" +
        rule.Service.name +
        (rule.Service.Description && rule.Service.Description.length > 0
          ? " | " + rule.Service.Description
          : "") +
        "</b><br>";

      if (rule.Service.Protocol) {
        html +=
          '<div><span class="fa fa-arrows-h mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          (rule.Service.Protocol ? rule.Service.Protocol.toUpperCase() : "") +
          "</span></div>";
      }

      if (rule.Service.Ports) {
        html +=
          '<div><span class="pficon pficon-template mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          (rule.Service.Ports ? rule.Service.Ports.join(", ") : "") +
          "</span></div>";
      }

      html +=
        "<span class='type-info'><b>" +
        this.$i18n.t("objects.service") +
        "</b></span>";

      return html;
    },
    mapTitleTime(rule) {
      if (!rule.Time) {
        return "";
      }
      var html =
        "<b>" +
        rule.Time.name +
        (rule.Time.Description && rule.Time.Description.length > 0
          ? " | " + rule.Time.Description
          : "") +
        "</b><br>";

      if (rule.Time.TimeStart && rule.Time.TimeSto) {
        html +=
          '<div><span class="fa fa-clock-o mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          rule.Time.TimeStart +
          " - " +
          rule.Time.TimeStop +
          "</span></div>";
      }

      if (rule.Time.WeekDays && rule.Time.WeekDays.length > 0) {
        html +=
          '<div><span class="fa fa-calendar mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          rule.Time.WeekDays.join(", ") +
          "</span></div>";
      }

      html +=
        "<span class='type-info'><b>" +
        this.$i18n.t("objects.time_condition") +
        "</b></span>";

      return html;
    },
    mapZoneIcon(zone) {
      switch (zone) {
        case "red":
        case "green":
        case "orange":
        case "blue":
          return "square-" + zone.toUpperCase();
          break;
        default:
          return "square-OTHER";
          break;
      }
    },
    mapObjectIcon(obj, status) {
      switch (obj.type) {
        case "host":
          return "fa fa-desktop";
          break;
        case "host-group":
          return "fa fa-cubes";
          break;
        case "iprange":
          return "pficon pficon-network";
          break;
        case "cidr":
          return "pficon pficon-network";
          break;
        case "zone":
          return "pficon pficon-zone";
          break;
        case "role":
          return (
            "square-" + (status == "disabled" ? "GRAY" : obj.name.toUpperCase())
          );
          break;
        case "any":
          return "fa fa-globe";
          break;
        case "fw":
          return "fa fa-fire";
          break;
      }
    },
    mapArrow(action) {
      return "gray fa fa-arrow-right";
    },
    mapList(action) {
      return "black-list";
    },
    mapIcon(action, status) {
      return (
        "fa fa-share " +
        (status == "disabled" ? "gray border-gray" : "black border-black")
      );
    },
    toggleAdvancedRuleMode() {
      this.newRule.advanced = !this.newRule.advanced;
      this.$forceUpdate();
    },
    initObject() {
      return {
        name: null,
        Description: "",
        IpAddress: "",
        Address: "",
        isLoading: false,
        errors: this.initObjectErrors()
      };
    },
    initObjectErrors() {
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
        },
        Address: {
          hasError: false,
          message: ""
        }
      };
    },
    initRule() {
      return {
        Src: "",
        SrcType: "",
        Dst: "",
        DstType: "",
        Service: "",
        ServiceType: "",
        Action: "accept",
        Quick: false,
        Time: "",
        Description: "",
        fwTarget: "to-fw",
        isLoading: false,
        isEdit: false,
        isDuplicate: false,
        advanced: false,
        errors: this.initRuleErrors()
      };
    },
    initRuleErrors() {
      return {
        Src: {
          hasError: false,
          message: ""
        },
        Dst: {
          hasError: false,
          message: ""
        },
        Service: {
          hasError: false,
          message: ""
        },
        Action: {
          hasError: false,
          message: ""
        },
        Quick: {
          hasError: false,
          message: ""
        },
        Time: {
          hasError: false,
          message: ""
        },
        Description: {
          hasError: false,
          message: ""
        }
      };
    },
    filterSrcAuto(query) {
      this.newRule.Src = null;
      this.newRule.SrcFull = null;
      this.newRule.SrcType = "";

      if (query.trim().length === 0) {
        return null;
      }

      var roles = this.roles.filter(function(i) {
        return i.name != "RED";
      });

      var objects = roles.concat(
        this.hosts.concat(
          this.hostGroups.concat(
            this.ipRanges.concat(this.cidrSubs.concat(this.zones))
          )
        )
      );

      return objects.filter(function(service) {
        return (
          service.typeId.toLowerCase().includes(query.toLowerCase()) ||
          service.name.toLowerCase().includes(query.toLowerCase()) ||
          service.Description.toLowerCase().includes(query.toLowerCase()) ||
          (service.IpAddress &&
            service.IpAddress.toLowerCase().includes(query.toLowerCase()))
        );
      });
    },
    selectSrcAuto(item) {
      this.newRule.Src = item.name;

      this.newRule.SrcFull = Object.assign({}, item);
      this.newRule.SrcFull.name = this.newRule.SrcFull.name.toLowerCase();
      this.newRule.SrcFull.type = this.newRule.SrcFull.typeId;
      delete this.newRule.SrcFull.typeId;

      this.newRule.SrcType =
        item.name +
        " " +
        (item.IpAddress ? item.IpAddress + " " : "") +
        (item.Address ? item.Address + " " : "") +
        (item.Start && item.End ? item.Start + " - " + item.End + " " : "") +
        "(" +
        item.type +
        ")";
    },
    filterDstAuto(query) {
      this.newRule.Dst = null;
      this.newRule.DstFull = null;
      this.newRule.DstType = "";

      if (query.trim().length === 0) {
        return null;
      }

      var roles = this.roles.filter(function(i) {
        return i.name == "RED";
      });

      var objects = roles.concat(
        this.hosts.concat(
          this.ipRanges.concat(this.cidrSubs.concat(this.zones))
        )
      );

      return objects.filter(function(service) {
        return (
          service.typeId.toLowerCase().includes(query.toLowerCase()) ||
          (service.IpAddress &&
            service.IpAddress.toLowerCase().includes(query.toLowerCase())) ||
          service.name.toLowerCase().includes(query.toLowerCase()) ||
          service.Description.toLowerCase().includes(query.toLowerCase())
        );
      });
    },
    selectDstAuto(item) {
      this.newRule.Dst = item.name;

      this.newRule.DstFull = Object.assign({}, item);
      this.newRule.DstFull.name = this.newRule.DstFull.name.toLowerCase();
      this.newRule.DstFull.type = this.newRule.DstFull.typeId;
      delete this.newRule.DstFull.typeId;

      this.newRule.DstType =
        item.name +
        " " +
        (item.IpAddress ? item.IpAddress + " " : "") +
        (item.Address ? item.Address + " " : "") +
        (item.Start && item.End ? item.Start + " - " + item.End + " " : "") +
        "(" +
        item.type +
        ")";
    },
    filterServiceAuto(query) {
      this.newRule.Service = null;
      this.newRule.ServiceFull = null;
      this.newRule.ServiceType = "";

      if (query.trim().length === 0) {
        return null;
      }

      var objects = this.services;

      return objects.filter(function(service) {
        return (
          service.typeId.toLowerCase().includes(query.toLowerCase()) ||
          (service.Ports &&
            service.Ports.join(" ")
              .toLowerCase()
              .includes(query.toLowerCase())) ||
          service.name.toLowerCase().includes(query.toLowerCase()) ||
          service.Description.toLowerCase().includes(query.toLowerCase())
        );
      });
    },
    selectServiceAuto(item) {
      this.newRule.Service = item.name;

      this.newRule.ServiceFull = Object.assign({}, item);
      this.newRule.ServiceFull.type = this.newRule.ServiceFull.typeId;
      delete this.newRule.ServiceFull.typeId;

      this.newRule.ServiceType = item.name + " (" + item.Ports.join(", ") + ")";
    },
    filterTimeAuto(query) {
      this.newRule.Time = null;
      this.newRule.TimeFull = null;
      this.newRule.TimeType = "";

      if (query.trim().length === 0) {
        return null;
      }

      var objects = this.timeConditions;

      return objects.filter(function(service) {
        return (
          service.name.toLowerCase().includes(query.toLowerCase()) ||
          service.Description.toLowerCase().includes(query.toLowerCase())
        );
      });
    },
    selectTimeAuto(item) {
      this.newRule.Time = item.name;

      this.newRule.TimeFull = Object.assign({}, item);
      this.newRule.TimeFull.type = this.newRule.TimeFull.typeId;
      delete this.newRule.TimeFull.typeId;

      this.newRule.TimeType = item.name + " (" + item.Description + ")";
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
          } catch (e) {
            console.error(e);
          }
          context.hosts = success["hosts"];
          context.hosts = context.hosts.map(function(i) {
            i.type = context.$i18n.t("objects.host");
            i.typeId = "host";
            return i;
          });
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
          } catch (e) {
            console.error(e);
          }
          context.hostGroups = success["host-groups"];
          context.hostGroups = context.hostGroups.map(function(i) {
            i.type = context.$i18n.t("objects.host_group");
            i.typeId = "host-group";
            return i;
          });
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getIPRanges() {
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
          } catch (e) {
            console.error(e);
          }
          context.ipRanges = success["ip-ranges"];
          context.ipRanges = context.ipRanges.map(function(i) {
            i.type = context.$i18n.t("objects.ip_range");
            i.typeId = "iprange";
            return i;
          });
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getCIDRSubs() {
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
          } catch (e) {
            console.error(e);
          }
          context.cidrSubs = success["cidr-subs"];
          context.cidrSubs = context.cidrSubs.map(function(i) {
            i.type = context.$i18n.t("objects.cidr_sub");
            i.typeId = "cidr";
            return i;
          });
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getZones() {
      var context = this;

      nethserver.exec(
        ["nethserver-firewall-base/objects/read"],
        {
          action: "zones"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.zones = success["zones"];
          context.zones = context.zones.map(function(i) {
            i.type = context.$i18n.t("objects.zone");
            i.typeId = "zone";
            return i;
          });
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getTimeConditions() {
      var context = this;

      nethserver.exec(
        ["nethserver-firewall-base/objects/read"],
        {
          action: "time-conditions"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.timeConditions = success["time-conditions"];
          context.timeConditions = context.timeConditions.map(function(i) {
            i.type = context.$i18n.t("objects.time_condition");
            i.typeId = "time";
            return i;
          });
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
          } catch (e) {
            console.error(e);
          }
          context.services = success["services"];
          context.services = context.services.map(function(i) {
            i.type = context.$i18n.t("objects.service");
            i.typeId = "fwservice";
            return i;
          });
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getRoles() {
      var context = this;

      nethserver.exec(
        ["nethserver-firewall-base/rules/read"],
        {
          action: "roles"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.roles = success["roles"];
          context.roles = context.roles.map(function(r) {
            var i = {};
            i.name = r.toUpperCase();
            i.Description =
              r.toUpperCase() + " " + context.$i18n.t("objects.role");
            i.type = context.$i18n.t("objects.role");
            i.typeId = "role";
            return i;
          });
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getRules() {
      var context = this;

      context.view.isLoaded = false;
      nethserver.exec(
        ["nethserver-firewall-base/wan/read"],
        {
          action: "rules",
          expand: true
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
            context.rules = success.rules;
            context.status = success.status;

            context.view.isLoaded = true;

            setTimeout(function() {
              $('[data-toggle="tooltip"]').tooltip();
            }, 750);
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
    openCreateRule() {
      this.newRule = this.initRule();
      $("#createRuleModal").modal("show");
    },
    toggleCharts() {
      this.view.chartsShowed = !this.view.chartsShowed;
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
              max: iface.FwInBandwidth <= 0 ? 1 : iface.FwInBandwidth,
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
              height: 100,
              width: window.innerWidth / 3 - 100
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
              max: iface.FwOutBandwidth <= 0 ? 1 : iface.FwOutBandwidth,
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
              height: 100,
              width: window.innerWidth / 3 - 100
            }
          });
        }

        this.view.isChartLoaded = true;
        this.updateCharts();
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
            if (iface && (iface.in && iface.out)) {
              var inName = context.$i18n.t("wan.inbound_bandwidth");
              var outName = context.$i18n.t("wan.outbound_bandwidth");

              context.view.invalidChartsData = false;

              context.charts[i].in.load({
                columns: [[inName, iface.in]]
              });
              context.charts[i].out.load({
                columns: [[outName, iface.out]]
              });

              // start polling
              if (context.pollingIntervalId == 0) {
                context.pollingIntervalId = setInterval(function() {
                  context.updateCharts();
                }, 2000);
              }
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
        weight: {
          hasError: false,
          message: ""
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

      context.view.isLoadedInterface = false;
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
            iface.opened = false;
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

          context.view.isLoadedInterface = true;

          setTimeout(function() {
            $("[data-toggle=popover]")
              .popovers()
              .popovers()
              .on("hidden.bs.popover", function(e) {
                $(e.target).data("bs.popover").inState.click = false;
              });
            context.initCharts();
          }, 250);

          setTimeout(function() {
            $('[data-toggle="tooltip"]').tooltip();
          }, 500);
        },
        function(error) {
          console.error(error);
          context.view.isLoadedInterface = true;
        }
      );
    },
    speedTest(iface) {
      var popover = $(
        "#" + this.$options.filters.sanitize("popover-" + iface.name)
      ).data("bs.popover");

      if (!iface.speedtest.isLoaded && popover) {
        popover.options.content = '<div class="spinner spinner-sm"></div><small>'+this.$i18n.t('wan.fireqos_temporary_disabled')+'</small>';
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
            popover.options.content = '<div class="alert alert-warning alert-dismissable"><span class="pficon pficon-warning-triangle-o"></span><strong>' +
              context.$i18n.t('warning') + '.</strong>'+context.$i18n.t('wan.speedtest_error') +
              '</div>';
            popover.show();
            iface.speedtest.isLoaded = true;
            console.error(error);
          }
        );
      }
    },
    openInterfaceDetails(iface) {
      iface.opened = !iface.opened;
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
    },
    openCreateRule() {
      this.newRule = this.initRule();
      $("#createRuleModal").modal("show");
    },
    openEditRule(r, duplicate) {
      this.newRule = Object.assign({}, r);
      this.newRule.errors = this.initRuleErrors();
      this.newRule.isLoading = false;
      this.newRule.isEdit = !duplicate;
      this.newRule.isDuplicate = duplicate;

      // handle src
      this.newRule.Src = r.Src.name;
      this.newRule.SrcFull = Object.assign({}, r.Src);
      this.newRule.SrcType =
        r.Src.type +
        " " +
        (r.Src.IpAddress ? r.Src.IpAddress + " " : "") +
        "(" +
        r.Src.type +
        ")";

      // handle dst
      this.newRule.Dst = r.Dst.name;
      this.newRule.DstFull = Object.assign({}, r.Dst);
      this.newRule.DstType =
        r.Dst.name +
        " " +
        (r.Dst.IpAddress ? r.Dst.IpAddress + " " : "") +
        "(" +
        r.Dst.type +
        ")";

      // handle service
      if (r.Service) {
        this.newRule.Service = r.Service.name;
        this.newRule.ServiceFull = Object.assign({}, r.Service);
        this.newRule.ServiceType =
          r.Service.name +
          (r.Service.Ports ? " (" + r.Service.Ports.join(", ") + ")" : "");
      }

      // handle time
      if (r.Time) {
        this.newRule.Time = r.Time.name;
        this.newRule.TimeFull = Object.assign({}, r.Time);
        this.newRule.TimeType =
          r.Time.name +
          (r.Time.Description ? " (" + r.Time.Description + ")" : "");
      }

      $("#createRuleModal").modal("show");
    },
    enableRule(r) {
      var context = this;

      var ruleObj = {
        action: "update-rule",
        Log: r.Log ? "info" : " none",
        Time: r.Time ? r.Time : null,
        Position: r.Position,
        status: r.status == "enabled" ? "disabled" : "enabled",
        Service: r.Service ? r.Service : null,
        Action: r.Action ? r.Action : null,
        Dst: r.Dst ? r.Dst : null,
        id: r.id,
        Src: r.Src ? r.Src : null,
        type: "rule"
      };

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "rules.rule_updated_ok"
      );
      nethserver.notifications.error = context.$i18n.t("rules.rule_updated_ok");

      // update values
      nethserver.exec(
        ["nethserver-firewall-base/wan/update"],
        ruleObj,
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
    openDeleteRule(r) {
      this.currentRule = Object.assign({}, r);
      $("#deleteRuleModal").modal("show");
    },
    saveRule() {
      var context = this;

      var ruleObj = {
        action: context.newRule.isEdit ? "update-rule" : "create-rule",
        Log: context.newRule.Log ? "info" : "none",
        Time: context.newRule.TimeFull ? context.newRule.TimeFull : null,
        Position: context.newRule.isEdit
          ? context.newRule.Position
          : context.status.next,
        status: context.newRule.isEdit ? context.newRule.status : "enabled",
        Service: context.newRule.ServiceFull
          ? context.newRule.ServiceFull
          : null,
        Action: context.newRule.Action ? context.newRule.Action : null,
        Dst: context.newRule.DstFull
          ? context.newRule.DstFull
          : { name: context.newRule.Dst, type: "raw" },
        id: context.newRule.isEdit ? context.newRule.id : null,
        Src: context.newRule.SrcFull
          ? context.newRule.SrcFull
          : { name: context.newRule.Src, type: "raw" },
        type: "rule",
        Description: context.newRule.Description
      };

      context.newRule.isLoading = true;
      context.$forceUpdate();
      nethserver.exec(
        ["nethserver-firewall-base/wan/validate"],
        ruleObj,
        null,
        function(success) {
          context.newRule.isLoading = false;
          $("#createRuleModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "rules.rule_" +
              (context.newRule.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "rules.rule_" +
              (context.newRule.isEdit ? "updated" : "created") +
              "_error"
          );

          // update values
          nethserver.exec(
            [
              "nethserver-firewall-base/wan/" +
                (context.newRule.isEdit ? "update" : "create")
            ],
            ruleObj,
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
          context.newRule.isLoading = false;
          context.newRule.errors = context.initRuleErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              context.newRule.errors[attr.parameter].hasError = true;
              context.newRule.errors[attr.parameter].message = attr.error;
            }
          } catch (e) {
            console.error(e);
          }
          context.$forceUpdate();
        }
      );
    },
    deleteRule(r) {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "rules.rule_deleted_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "rules.rule_deleted_error"
      );

      $("#deleteRuleModal").modal("hide");
      nethserver.exec(
        ["nethserver-firewall-base/wan/delete"],
        {
          name: r.id
        },
        function(stream) {
          console.info("nethserver-firewall-base", stream);
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
.red {
  color: #cc0000;
}

.wan-list {
  border-left: 3px solid #cc0000 !important;
  border-bottom: 1px solid #bbbbbb !important;
  border-right: 1px solid #bbbbbb !important;
}

.gray {
  color: #72767b !important;
}

.border-red {
  border: 2px solid #cc0000 !important;
}

.white {
  background-color: #fff !important;
}

.more-space {
  flex: 1 0 20% !important;
}

.more-space-description {
  width: calc(40% - 40px) !important;
}

.spinner-speed {
  float: left;
  margin-top: 5px;
}

.semi-bold {
  font-weight: 600;
}

.normal {
  font-weight: 500;
}

.small-font {
  font-size: 12px;
}
</style>
