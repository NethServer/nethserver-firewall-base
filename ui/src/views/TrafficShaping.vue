<template>
  <div>
    <h2>{{$t('traffic_shaping.title')}}</h2>

    <h3 v-if="view.isChartLoaded && tc.length > 0">{{$t('charts')}}</h3>
    <a
      v-if="view.isChartLoaded && tc.length > 0"
      @click="toggleCharts()"
    >{{view.chartsShowed ? $t('hide_charts') : $t('show_charts')}}</a>
    <div v-if="!view.isChartLoaded && tc.length > 0" class="spinner spinner-lg view-spinner"></div>

    <div :class="view.chartsShowed ? '' : 'hidden'">
      <div
        v-if="view.invalidChartsData && tc.length > 0"
        class="alert alert-warning alert-dismissable col-sm-12 mg-bottom-5"
      >
        <span class="pficon pficon-warning-triangle-o"></span>
        <strong>{{$t('warning')}}!</strong>
        {{$t('charts_not_updated')}}.
      </div>
      <div
        v-show="interfaces.length > 0 && view.isChartLoaded && tc.length > 0 && !view.invalidChartsData"
        class="row"
      >
        <div v-for="i in interfaces" v-bind:key="i" class="col-sm-6">
          <h4>
            {{i.nslabel}}
            <span class="gray">({{$t('download_low')}})</span>
          </h4>
          <div :id="'chart-in-'+i.provider.name | sanitize" class="col-sm-12"></div>
          <h4>
            {{i.nslabel}}
            <span class="gray">({{$t('upload_low')}})</span>
          </h4>
          <div :id="'chart-out-'+i.provider.name | sanitize" class="col-sm-12"></div>
        </div>
      </div>
    </div>

    <div v-if="view.isLoadedTc">
      <h3>{{$t('traffic_shaping.configuration')}}</h3>
      <div class="panel panel-default" id="provider-markup">
        <div class="panel-heading">
          <button
            id="change-provider-btn"
            v-if="tc.length > 0"
            @click="openCreateTc()"
            class="btn btn-primary"
          >{{$t('traffic_shaping.create_class')}}</button>
          <span class="panel-title">
            <span>{{$t('traffic_shaping.classes')}}: {{tc.length}}</span>
          </span>
          <a
            class="mg-left-5 provider-details"
            data-toggle="collapse"
            data-parent="#provider-markup"
            href="#providerDetails"
          >{{$t('details')}}</a>
        </div>
        <div id="providerDetails" class="panel-collapse collapse in">
          <div v-if="!view.isLoadedTc" class="spinner spinner-lg view-spinner"></div>

          <div v-if="tc.length == 0 && view.isLoadedTc" class="blank-slate-pf white">
            <div class="blank-slate-pf-icon">
              <span class="fa fa-balance-scale"></span>
            </div>
            <h1>{{$t('traffic_shaping.no_tc_found')}}</h1>
            <p>{{$t('traffic_shaping.no_tc_found_text')}}.</p>
            <div class="blank-slate-pf-main-action">
              <button
                class="btn btn-primary btn-lg"
                @click="openCreateTc()"
              >{{$t('traffic_shaping.create_class')}}</button>

              <button
                class="btn btn-default btn-lg mg-left-5"
                @click="defaultTc()"
              >{{$t('traffic_shaping.create_default_class')}}</button>
            </div>
          </div>

          <div
            id="pf-list-simple-expansion"
            class="list-group list-view-pf list-view-pf-view no-mg-top"
          >
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
                <div class="list-view-pf-main-info small-list">
                  <div class="list-view-pf-left">
                    <span class="fa fa-balance-scale list-view-pf-icon-sm"></span>
                  </div>
                  <div class="list-view-pf-body">
                    <div class="list-view-pf-description">
                      <div class="list-group-item-heading">
                        <a @click="openEditTc(t)">
                          {{t.name}}
                          <span
                            v-if="t.BindTo && t.BindTo.length > 0"
                            class="gray"
                          >({{t.BindTo.join(',')}})</span>
                        </a>
                      </div>
                      <div class="list-group-item-text more-space-description">{{t.Description}}</div>
                    </div>
                    <div class="list-view-pf-additional-info">
                      <div class="list-view-pf-additional-info-item multi-line adjust-line">
                        <span>{{$t('download')}}</span>
                        <br>
                        <span>{{$t('upload')}}</span>
                      </div>
                      <div
                        v-show="t.MinInputRate.length > 0 || t.MinOutputRate.length > 0"
                        class="list-view-pf-additional-info-item multi-line"
                      >
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
                      <div
                        v-show="t.MaxInputRate.length > 0 || t.MaxOutputRate.length > 0"
                        class="list-view-pf-additional-info-item multi-line"
                      >
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
        </div>
      </div>
      <div class="divider"></div>
    </div>

    <h3 v-if="view.isLoaded">{{$t('rules.title')}}</h3>
    <button
      v-if="view.isLoaded && rules.length > 0"
      @click="openCreateRule()"
      class="btn btn-primary btn-lg"
    >{{$t('rules.create_routing_rule')}}</button>
    <div v-if="!view.isLoaded" class="spinner spinner-lg view-spinner"></div>
    <div v-if="rules.length == 0 && view.isLoaded" class="blank-slate-pf white">
      <div class="blank-slate-pf-icon">
        <span class="fa fa-crosshairs"></span>
      </div>
      <h1>{{$t('rules.no_rules_found')}}</h1>
      <p>{{$t('rules.no_rules_found_text')}}.</p>
      <div class="blank-slate-pf-main-action">
        <button
          @click="openCreateRule()"
          class="btn btn-primary"
        >{{$t('rules.create_routing_rule')}}</button>
      </div>
    </div>
    <ul
      v-if="rules.length > 0 && view.isLoaded"
      v-sortable="{onEnd: reorder, handle: '.drag-here'}"
      class="list-group list-view-pf list-view-pf-view no-mg-top mg-top-10"
    >
      <li :class="[mapList(r.Action), 'list-group-item']" v-for="r in rules" v-bind:key="r">
        <div class="drag-size">
          <span class="gray mg-right-5">{{r.Action.split(';')[1] | uppercase}}</span>
        </div>
        <div class="list-view-pf-checkbox drag-here">
          <span class="fa fa-bars"></span>
        </div>
        <div class="list-view-pf-actions">
          <button class="btn btn-default">
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
            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownKebabRight9">
              <li>
                <a href="#">
                  <span class="fa fa-lock span-right-margin"></span>
                  {{$t('disable')}}
                </a>
              </li>
              <li>
                <a href="#">
                  <span class="fa fa-clone span-right-margin"></span>
                  {{$t('rules.duplicate')}}
                </a>
              </li>
              <li role="separator" class="divider"></li>
              <li>
                <a href="#">
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
              :class="[mapIcon(r.Action), 'list-view-pf-icon-sm']"
            ></span>
            <span
              data-toggle="tooltip"
              data-placement="top"
              data-html="true"
              :title="$t('rules.log_enabled')"
              v-show="r.Log"
              class="fa fa-bookmark-o log-icon"
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
                  <span :class="mapObjectIcon(r.Src)"></span>
                  <span
                    :class="[r.Src.name.toLowerCase(),'mg-left-5']"
                  >{{r.Src.type == 'fw' || r.Src.type == 'role' || r.Src.type == 'any' ? (r.Src.name.toUpperCase()): r.Src.name}}</span>
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
                  <span :class="mapObjectIcon(r.Dst)"></span>
                  <span
                    :class="[r.Dst.name.toLowerCase(),'mg-left-5']"
                  >{{r.Dst.type == 'fw' || r.Dst.type == 'role' || r.Dst.type == 'any' ? (r.Dst.name.toUpperCase()): r.Dst.name}}</span>
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
                <span class="fa fa-fighter-jet"></span>
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
    <div class="modal" id="createRuleModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{newRule.isEdit ? $t('rules.edit_rule') : newRule.isDuplicate ? $t('rules.duplicate_rule') : $t('rules.create_routing_rule')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveRule()">
            <div class="modal-body">
              <div :class="['form-group', newRule.errors.Action.hasError ? 'has-error' : '']">
                <label class="col-sm-4 control-label">{{$t('rules.class')}}</label>
                <div class="col-sm-8">
                  <select v-model="newRule.Action" class="form-control" required>
                    <option v-for="i in tc" v-bind:key="i" :value="'class;'+i.name">{{i.name}}</option>
                  </select>
                  <span v-if="newRule.errors.Action.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newRule.errors.Action.message)}}
                  </span>
                </div>
              </div>
              <div :class="['form-group', newRule.errors.Src.hasError ? 'has-error' : '']">
                <label class="col-sm-4 control-label">{{$t('rules.source')}}</label>
                <div class="col-sm-8">
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
                          v-show="props.item.IpAddress"
                          class="gray"
                        >({{props.item.IpAddress}})</span>
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
                <label class="col-sm-4 control-label">{{$t('rules.destination')}}</label>
                <div class="col-sm-8">
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
                          v-show="props.item.IpAddress"
                          class="gray"
                        >({{props.item.IpAddress}})</span>
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
                <label class="col-sm-4 control-label">{{$t('rules.service')}}</label>
                <div class="col-sm-8">
                  <suggestions
                    v-model="newRule.Service"
                    required
                    :options="autoOptions"
                    :onInputChange="filterServiceAuto"
                    :onItemSelected="selectServiceAuto"
                  >
                    <div slot="item" slot-scope="props" class="single-item">
                      <span>
                        <span
                          v-show="props.item.typeId == 'application'"
                          :class="['square-'+ props.item.name]"
                        ></span>
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
                    :value="newRule.advanced"
                    :sync="true"
                    @change="toggleAdvancedRuleMode()"
                  />
                </div>
              </div>

              <div
                v-show="newRule.advanced"
                :class="['form-group', newRule.errors.Description.hasError ? 'has-error' : '']"
              >
                <label class="col-sm-4 control-label">{{$t('rules.description')}}</label>
                <div class="col-sm-8">
                  <input class="form-control" type="text" v-model="newRule.Description">
                  <span v-if="newRule.errors.Description.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newRule.errors.Description.message)}}
                  </span>
                </div>
              </div>

              <div
                v-show="newRule.advanced"
                :class="['form-group', newRule.errors.Log.hasError ? 'has-error' : '']"
              >
                <label class="col-sm-4 control-label">{{$t('rules.log')}}</label>
                <div class="col-sm-8">
                  <input class="form-control" type="checkbox" v-model="newRule.Log">
                  <span v-if="newRule.errors.Log.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newRule.errors.Log.message)}}
                  </span>
                </div>
              </div>

              <div
                v-show="newRule.advanced"
                :class="['form-group', newRule.errors.Time.hasError ? 'has-error' : '']"
              >
                <label class="col-sm-4 control-label">{{$t('rules.time_condition')}}</label>
                <div class="col-sm-8">
                  <suggestions
                    v-model="newRule.Time"
                    required
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
        isLoadedTc: false,
        isChartLoaded: false,
        invalidChartsData: false,
        chartsShowed: false
      },
      tc: [],
      newTc: this.initTc(),
      currentTc: {},
      charts: {},
      pollingIntervalId: 0,
      interfaces: [],
      providers: [],
      rules: [],
      hosts: [],
      hostGroups: [],
      ipRanges: [],
      cidrSubs: [],
      zones: [],
      timeConditions: [],
      services: [],
      applications: [],
      roles: [],
      autoOptions: {
        inputClass: "form-control"
      },
      newRule: this.initRule()
    };
  },
  mounted() {
    this.getInterfaces();
    this.getTc();
    this.getRules();
    this.getHosts();
    this.getHostGroups();
    this.getIPRanges();
    this.getCIDRSubs();
    this.getZones();
    this.getTimeConditions();
    this.getServices();
    this.getApplications();
    this.getRoles();
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
    toggleAdvancedRuleMode() {
      this.wan.advanced = !this.wan.advanced;
      this.$forceUpdate();
    },
    reorder({ oldIndex, newIndex }) {
      const movedItem = this.rules.splice(oldIndex, 1)[0];
      this.rules.splice(newIndex, 0, movedItem);

      this.rules = this.rules.map(function(r, i) {
        r.Position = i + 1;
        return r;
      });

      console.log(this.rules);
    },
    mapTitleAction(rule) {
      var html = "<b>" + rule.Action.split(";")[1].toUpperCase() + "</b><br>";

      if (rule.Log) {
        html +=
          '<div class="type-info"><span class="fa fa-bookmark-o mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          this.$i18n.t("rules.log_enabled") +
          "</span></div>";
      }

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
    mapObjectIcon(obj) {
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
          return "square-" + obj.name.toUpperCase();
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
      return "gray-list";
    },
    mapIcon(action) {
      return "fa fa-crosshairs gray border-gray";
    },
    toggleAdvancedRuleMode() {
      this.newRule.advanced = !this.newRule.advanced;
      this.$forceUpdate();
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
        Log: false,
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
        Log: {
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
      if (query.trim().length === 0) {
        return null;
      }

      var objects = this.roles.concat(
        this.hosts.concat(
          this.hostGroups.concat(
            this.ipRanges.concat(this.cidrSubs.concat(this.zones))
          )
        )
      );

      this.newRule.Src = null;
      this.newRule.SrcType = "";

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
      this.newRule.SrcType =
        item.name +
        " " +
        (item.IpAddress ? item.IpAddress + " " : "") +
        "(" +
        item.type +
        ")";
    },
    filterDstAuto(query) {
      if (query.trim().length === 0) {
        return null;
      }

      var objects = this.roles.concat(
        this.hosts.concat(
          this.hostGroups.concat(
            this.ipRanges.concat(this.cidrSubs.concat(this.zones))
          )
        )
      );

      this.newRule.Dst = null;
      this.newRule.DstType = "";

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
      this.newRule.DstType =
        item.name +
        " " +
        (item.IpAddress ? item.IpAddress + " " : "") +
        "(" +
        item.type +
        ")";
    },
    filterServiceAuto(query) {
      if (query.trim().length === 0) {
        return null;
      }

      var objects = this.services.concat(this.applications);

      this.newRule.Service = null;
      this.newRule.ServiceType = "";

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
      this.newRule.ServiceType = item.name + " (" + item.Ports.join(", ") + ")";
    },
    filterTimeAuto(query) {
      if (query.trim().length === 0) {
        return null;
      }

      var objects = this.timeConditions;

      this.newRule.Time = null;
      this.newRule.TimeType = "";

      return objects.filter(function(service) {
        return (
          service.name.toLowerCase().includes(query.toLowerCase()) ||
          service.Description.toLowerCase().includes(query.toLowerCase())
        );
      });
    },
    selectTimeAuto(item) {
      this.newRule.Time = item.name;
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
            i.typeId = "service";
            return i;
          });
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getApplications() {
      var context = this;

      nethserver.exec(
        ["nethserver-firewall-base/objects/read"],
        {
          action: "applications"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.applications = success["applications"];
          context.applications = context.applications.map(function(i) {
            i.type = context.$i18n.t("objects.application");
            i.typeId = "application";
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
        ["nethserver-firewall-base/traffic-shaping/read"],
        {
          action: "rules",
          expand: true
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
            var rules = success.rules.map(function(r) {
              r.Log = r.Log == "none" ? false : true;
              return r;
            });
            context.rules = rules;

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
      if (this.view.chartsShowed) {
        this.initCharts();
      }
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
              // out classes
              var outColumns = [];
              var typesOutClass = {};
              if (provider.out && provider.out.time) {
                context.view.invalidChartsData = false;
                for (var c in provider.out) {
                  var classVal = provider.out[c];

                  if (c != "time") {
                    typesOutClass[c] = "area-spline";
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
              } else {
                context.view.invalidChartsData = true;
              }

              // in classes
              var inColumns = [];
              var typesInClass = {};
              if (provider.in && provider.in.time) {
                context.view.invalidChartsData = false;

                for (var c in provider.in) {
                  var classVal = provider.in[c];

                  if (c != "time") {
                    typesInClass[c] = "area-spline";
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
              } else {
                context.view.invalidChartsData = true;
              }

              if (context.charts["chart-out-" + i]) {
                context.charts["chart-out-" + i].load({
                  columns: outColumns
                });
              } else {
                context.charts["chart-out-" + i] = c3.generate({
                  bindto:
                    "#" + context.$options.filters.sanitize("chart-out-" + i),
                  transition: {
                    duration: 0
                  },
                  data: {
                    x: "x",
                    xFormat: "%H:%M:%S",
                    columns: outColumns,
                    types: typesOutClass
                  },
                  axis: {
                    x: {
                      type: "timeseries",
                      tick: {
                        format: "%H:%M:%S",
                        count: 7
                      }
                    },
                    y: {
                      tick: {
                        format: function(y) {
                          return context.$options.filters.byteFormat(
                            (Math.round(y * 100) / 100) * 1000
                          );
                        },
                        count: 5
                      }
                    }
                  },
                  size: {
                    height: 150,
                    width: window.innerWidth / 2 - 100
                  }
                });
              }

              if (context.charts["chart-in-" + i]) {
                context.charts["chart-in-" + i].load({
                  columns: inColumns
                });
              } else {
                context.charts["chart-in-" + i] = c3.generate({
                  bindto:
                    "#" + context.$options.filters.sanitize("chart-in-" + i),
                  transition: {
                    duration: 0
                  },
                  data: {
                    x: "x",
                    xFormat: "%H:%M:%S",
                    columns: inColumns,
                    types: typesInClass
                  },
                  axis: {
                    x: {
                      type: "timeseries",
                      tick: {
                        format: "%H:%M:%S",
                        count: 7
                      }
                    },
                    y: {
                      tick: {
                        format: function(y) {
                          return context.$options.filters.byteFormat(
                            (Math.round(y * 100) / 100) * 1000
                          );
                        },
                        count: 5
                      }
                    }
                  },
                  size: {
                    height: 150,
                    width: window.innerWidth / 2 - 100
                  }
                });
              }

              context.view.isChartLoaded = true;
            } else {
              context.view.invalidChartsData = true;
              context.view.isChartLoaded = true;
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
        }, 2500);
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

      context.view.isLoadedTc = false;
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

      context.view.isLoadedTc = false;
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
          context.view.isLoadedTc = true;

          setTimeout(function() {
            $('[data-toggle="tooltip"]').tooltip();
          }, 250);
        },
        function(error) {
          console.error(error);
          context.view.isLoadedTc = true;
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
            "traffic_shaping.tc_" + context.newTc.isEdit
              ? "updated"
              : "created" + "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "traffic_shaping.tc_" + context.newTc.isEdit
              ? "updated"
              : "created" + "_error"
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
      var context = this;

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
</style>