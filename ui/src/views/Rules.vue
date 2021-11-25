<template>
  <div>
    <h2>{{$t('rules.title')}}</h2>

    <div v-if="!view.isLoaded" class="spinner spinner-lg view-spinner"></div>
    <div v-if="view.isLoaded">
      <h3>
        <span v-if="rules.length > 0">
          {{$t('actions')}}
        </span>
        <a
          id="routing-info"
          data-toggle="modal"
          data-target="#policiesModal"
          :class="['right', policies.length == 0 ? 'disabled' : '']"
        >
          <span class="fa fa-shield starred-marging"></span>
          {{$t('rules.policies')}}
        </a>
      </h3>
      <div v-if="rules.length == 0" class="blank-slate-pf white">
        <div class="blank-slate-pf-icon">
          <span class="fa fa-ban"></span>
        </div>
        <h1>{{$t('rules.no_rules_found')}}</h1>
        <p>{{$t('rules.no_rules_found_text')}}.</p>
        <div class="blank-slate-pf-main-action">
          <button class="btn btn-primary btn-lg" @click="openCreateRule()">{{$t('rules.create_rule')}}</button>
        </div>
      </div>
      <button v-if="rules.length > 0" @click="openCreateRule()" class="btn btn-primary btn-lg">{{$t('rules.create_rule')}}</button>
      <button v-if="rules.length > 0" @click="openCreateSeparator()" class="btn btn-default btn-lg mg-left-5">{{$t('rules.create_separator')}}</button>
    </div>

    <div class="pf-container" v-if="rules.length > 0 && view.isLoaded">
      <h3>{{$t('rules.list')}}</h3>
      <form v-if="rules.length > 0" role="form" class="search-pf has-button search">
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

      <ul
        v-if="rules.length > 0 && view.isLoaded"
        v-sortable="{onEnd: reorder, handle: '.drag-here'}"
        class="list-group list-view-pf list-view-pf-view no-mg-top mg-top-10"
      >
        <li 
          :class="[r.type === 'separator' ? r.color+'-background' : r.status == 'disabled' ? 'gray-list' : mapList(r.Action), 'list-group-item', r.status == 'disabled' ? 'gray' : '']"
          v-for="(r,k) in filteredRules"
          v-bind:key="k"
        >
          <template v-if="r.type === 'rule'">
          <div class="drag-size">
            <span class="gray mg-right-5">{{r.id}}</span>
          </div>
          <div v-show="searchString.length == 0" class="list-view-pf-checkbox drag-here">
            <span class="fa fa-bars"></span>
          </div>
          <div class="list-view-pf-actions">
            <button
              @click="r.status == 'disabled' ? toggleEnableRule(r) : openEditRule(r, false)"
              :class="['btn btn-default', r.status == 'disabled' ? 'btn-primary' : '']"
            >
              <span
                :class="['fa', r.status == 'disabled' ? 'fa-check' : 'fa-edit', 'span-right-margin']"
              ></span>
              {{r.status == 'disabled' ? $t('enable') : $t('edit')}}
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
              <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownKebabRight9">
                <li>
                  <a @click="r.status == 'enabled' ? toggleEnableRule(r) : openEditRule(r, false)">
                    <span
                      :class="['fa', r.status == 'enabled' ? 'fa-lock' : 'fa-edit', 'span-right-margin']"
                    ></span>
                    {{r.status == 'enabled' ? $t('disable') : $t('edit')}}
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
              <span
                data-toggle="tooltip"
                data-placement="top"
                data-html="true"
                :title="$t('rules.log_enabled')"
                v-show="r.Log"
                class="fa fa-bookmark-o log-icon"
              ></span>
              <span
                data-toggle="tooltip"
                data-placement="top"
                data-html="true"
                :title="$t('rules.state_enabled')"
                v-show="r.State"
                class="pficon pficon-security pf-orange state-icon"
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
                        @click="openCreateObject(r.Dst)"
                      >{{$t('create')}} {{$t('objects.'+r.Dst.object)}}</a>
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
                  <span
                    :class="['fa', r.Service && r.Service.type == 'application' ? r.Service.icon : 'fa-cogs']"
                  ></span>
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
          </template>
          <template v-if="r.type === 'separator'">
          <div v-show="searchString.length == 0" class="list-view-pf-checkbox drag-here">
            <span class="fa fa-bars"></span>
          </div>
          <div class="list-view-pf-actions">
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
              <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownKebabRight9">
                <li @click="openEditSeparator(r)">
                  <a>
                    <span class="fa fa-edit span-right-margin"></span>
                    {{$t('edit')}}
                  </a>
                </li>
                <li @click="openDeleteSeparator(r)">
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
            </div>
            <div class="list-view-pf-body">
              <div class="list-view-pf-description rules-src-dst">
                <div class="list-group-item-heading">
                <span>{{r.Description.toUpperCase()}}</span>
                </div>
                <div class="list-group-item-text">
                </div>
              </div>
              <div class="list-view-pf-additional-info rules-info">
              </div>
            </div>
          </div>
          </template>
        </li>
      </ul>
    </div>

    <div class="modal" id="policiesModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('rules.policies')}}</h4>
          </div>
          <form class="form-horizontal">
            <div class="modal-body">
              <div class="form-group">
                <div class="col-sm-12">
                  <ul
                    v-if="policies.length > 0 && view.isLoaded"
                    v-sortable="{onEnd: reorder, handle: '.drag-here'}"
                    class="list-group list-view-pf list-view-pf-view no-mg-top mg-top-10"
                  >
                    <li
                      :class="[r.status == 'disabled' ? 'gray-list' : mapList(r.Action), 'list-group-item', r.status == 'disabled' ? 'gray' : '']"
                      v-for="(r,k) in policies"
                      v-bind:key="k"
                    >
                      <div class="drag-size-large">
                        <span class="gray mg-right-5">{{r.id}}</span>
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
                                >{{r.Src.type == 'role' || r.Src.type == 'any' ? (r.Src.name.toUpperCase()): r.Src.name}}</span>
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
                                >{{r.Dst.type == 'role' || r.Dst.type == 'any' ? (r.Dst.name.toUpperCase()): r.Dst.name}}</span>
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
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
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
            >{{newRule.isEdit ? $t('rules.edit_rule') : newRule.isDuplicate ? $t('rules.duplicate_rule') : $t('rules.create_rule')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveRule()">
            <div class="modal-body">
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
                        <span
                          v-show="props.item.typeId == 'application'"
                          :class="['fa', props.item.icon]"
                        ></span>
                        {{props.item.name}}
                        <span
                          v-show="props.item.Ports"
                          class="gray"
                        >({{props.item.Ports && props.item.Ports.join(', ')}})</span>
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

              <div :class="['form-group', newRule.errors.Action.hasError ? 'has-error' : '']">
                <label class="col-sm-3 control-label">{{$t('rules.action')}}</label>
                <div class="col-sm-9">
                  <select v-model="newRule.Action" class="form-control">
                    <option value="accept">{{$t('rules.accept')}}</option>
                    <option :disabled="newRule.ServiceFull.type == 'application'" value="reject">{{$t('rules.reject')}}</option>
                    <option value="drop">{{$t('rules.drop')}}</option>
                  </select>
                  <span v-if="newRule.errors.Action.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newRule.errors.Action.message)}}
                  </span>
                </div>
              </div>

              <legend class="fields-section-header-pf" aria-expanded="true">
                <span
                  :class="['fa fa-angle-right field-section-toggle-pf', newRule.advanced ? 'fa-angle-down' : '']"
                ></span>
                <a
                  class="field-section-toggle-pf"
                  @click="toggleAdvancedMode()"
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
                v-show="newRule.advanced && (newRule.ServiceFull.type != 'application' || newRule.Service == '')"
                :class="['form-group', newRule.errors.Log.hasError ? 'has-error' : '']"
              >
                <label class="col-sm-3 control-label">{{$t('rules.log')}}</label>
                <div class="col-sm-9">
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

              <div
                v-show="newRule.advanced"
                :class="['form-group', newRule.errors.State.hasError ? 'has-error' : '']"
              >
                <label class="col-sm-3 control-label">
                  {{$t('rules.state')}}
                  <doc-info
                    :placement="'top'"
                    :title="$t('rules.state_enabled')"
                    :chapter="'rules_state'"
                    :inline="true"
                  ></doc-info>
                </label>
                <div class="col-sm-9">
                  <input class="form-control" type="checkbox" v-model="newRule.State">
                  <span v-if="newRule.errors.State.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newRule.errors.State.message)}}
                  </span>
                </div>
              </div>
              <div 
                v-show="newRule.advanced && (!newRule.isEdit || newRule.isDuplicate)"
                :class="['form-group', newRule.errors.order.hasError ? 'has-error' : '']">
                <label class="col-sm-3 control-label">{{$t('rules.order')}}</label>
                <div class="col-sm-9">
                  <select v-model="newRule.order" class="form-control">
                    <option value="top">{{$t('rules.create_first_label')}}</option>
                    <option value="bottom">{{$t('rules.create_last_label')}}</option>
                  </select>
                  <span v-if="newRule.errors.order.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newRule.errors.order.message)}}
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

    <div class="modal" id="createSeparatorModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{newSeparator.isEdit ? $t('rules.edit_separator') : $t('rules.create_separator')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveSeparator()">
            <div class="modal-body">
              <div
                :class="['form-group', newSeparator.errors.Description.hasError ? 'has-error' : '']"
              >
                <label class="col-sm-3 control-label">{{$t('rules.description')}}</label>
                <div class="col-sm-9">
                  <input class="form-control" type="text" v-model="newSeparator.Description">
                  <span v-if="newSeparator.errors.Description.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newSeparator.errors.Description.message)}}
                  </span>
                </div>
              </div>
              <div :class="['form-group', newSeparator.errors.color.hasError ? 'has-error' : '']">
                <label class="col-sm-3 control-label">{{$t('rules.color')}}</label>
                <div class="col-sm-9">
                  <select v-model="newSeparator.color" class="form-control">
                    <option value="green">{{$t('rules.green')}}</option>
                    <option value="blue">{{$t('rules.blue')}}</option>
                    <option value="red">{{$t('rules.red')}}</option>
                  </select>
                  <span v-if="newSeparator.errors.color.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newSeparator.errors.color.message)}}
                  </span>
                </div>
              </div>
            </div>
            <div class="modal-footer no-mg-top">
              <div v-if="newSeparator.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button
                class="btn btn-primary"
                type="submit"
              >{{newSeparator.isEdit ? $t('edit') : $t('save')}}</button>
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
  
    <div class="modal" id="deleteSeparatorModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('rules.delete_Separator')}} {{currentRule.id}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deleteSeparator(currentRule)">
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
  </div>
</template>

<script>
var Mark = require("mark.js");

export default {
  name: "Rules",
  mounted() {
    this.getRules();
    this.getPolicies();
    this.getHosts();
    this.getHostGroups();
    this.getIPRanges();
    this.getCIDRSubs();
    this.getZones();
    this.getTimeConditions();
    this.getServices();
    this.getApplications();

    var context = this;
    context.$parent.$on("changes-applied", function() {
      context.getRules();
      context.getPolicies();
      context.getHosts();
      context.getHostGroups();
      context.getIPRanges();
      context.getCIDRSubs();
      context.getZones();
      context.getTimeConditions();
      context.getServices();
      context.getApplications();
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
        isLoaded: false,
        macAddresses: {
          isLoaded: false
        },
      },
      rules: [],
      policies: [],
      hosts: [],
      hostGroups: [],
      ipRanges: [],
      cidrSubs: [],
      zones: [],
      timeConditions: [],
      services: [],
      applications: [],
      roles: [],
      macAddresses: [],
      accessZones: [],
      autoOptions: {
        inputClass: "form-control"
      },
      newRule: this.initRule(),
      newSeparator: this.initSeparator(),
      currentRule: {},
      searchString: "",
      highlightInstance: null,
      expandInfo: true,
      status: {},
      newObject: this.initObject(),
      any: {
        name: "any",
        type: "any",
        typeId: "any",
        Description: ""
      }
    };
  },
  computed: {
    filteredRules() {
      var returnObj = [];
      for (var r in this.rules) {
        var rule = JSON.stringify(this.rules[r]);
        if (rule.toLowerCase().includes(this.searchString.toLowerCase())) {
          returnObj.push(this.rules[r]);
        }
      }

      return returnObj;
    }
  },
  methods: {
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
    reorder({ oldIndex, newIndex }) {
      const movedItem = this.rules.splice(oldIndex, 1)[0];
      this.rules.splice(newIndex, 0, movedItem);

      var ids = this.rules.map(function(i) {
        return i.id;
      });

      // notification
      nethserver.notifications.success = this.$i18n.t("rules.rule_updated_ok");
      nethserver.notifications.error = this.$i18n.t("rules.rule_updated_error");

      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/rules/update"],
        {
          action: "reorder",
          rules: ids,
          type: movedItem.type
        },
        function(stream) {
          console.info("firewall-base-update", stream);
        },
        function(success) {
          context.getRules();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    },
    moveRuleTop(index, type) {
      // retrieve the array order of indexes.
      var ids = this.rules.map(function(i) {
        return i.id;
      });

      ids.unshift(index);
      // notification
      nethserver.notifications.success = this.$i18n.t("rules.rule_updated_ok");
      nethserver.notifications.error = this.$i18n.t("rules.rule_updated_error");

      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/rules/update"],
        {
          action: "reorder",
          rules: ids,
          type: type
        },
        function(stream) {
          console.info("firewall-base-update", stream);
        },
        function(success) {
          context.getRules();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    },
    mapTitleAction(rule) {
      var html = "<b>" + rule.Action.toUpperCase() + "</b><br>";

      if (rule.Log) {
        html +=
          '<div class="type-info"><span class="fa fa-bookmark-o mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          this.$i18n.t("rules.log_enabled") +
          "</span></div>";
      }

      if (rule.State) {
        html +=
          '<div class="type-info"><span class="pficon pficon-security mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          this.$i18n.t("rules.state") +
          "</span></div>";
      }

      return html;
    },
    mapTitleSrc(rule) {
      var html =
        "<b>" +
        (rule.Src.type == "role" || rule.Src.type == "any"
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
        (rule.Dst.type == "role" || rule.Dst.type == "any"
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
        case "mac":
          return "pficon pficon-memory";
          break;
        case "any":
          return "fa fa-globe";
          break;
      }
    },
    mapArrow(action) {
      switch (action) {
        case "accept":
          return "gray fa fa-arrow-right";
          break;
        case "reject":
          return "gray fa fa-arrow-right";
          break;
        case "drop":
          return "gray fa fa-arrow-right";
          break;
      }
    },
    mapList(action) {
      switch (action) {
        case "accept":
          return "green-list";
          break;
        case "reject":
          return "orange-list";
          break;
        case "drop":
          return "red-list";
          break;
      }
    },
    mapIcon(action, status) {
      switch (action) {
        case "accept":
          return (
            "fa fa-check " +
            (status == "disabled" ? "gray border-gray" : "green border-green")
          );
          break;
        case "reject":
          return (
            "fa fa-shield " +
            (status == "disabled" ? "gray border-gray" : "orange border-orange")
          );
          break;
        case "drop":
          return (
            "fa fa-ban " +
            (status == "disabled" ? "gray border-gray" : "red border-red")
          );
          break;
      }
    },
    toggleAdvancedMode() {
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
        ServiceFull: {
          type: "fwservice"
        },
        Action: "accept",
        Log: false,
        Quick: false,
        Time: "",
        Description: "",
        State: false,
        isLoading: false,
        isEdit: false,
        isDuplicate: false,
        advanced: false,
        order: "bottom",
        errors: this.initRuleErrors()
      };
    },
    initSeparator() {
      return {
        Dst: "",
        Description: "",
        order: "top",
        color: "blue",
        errors: this.initSeparatorErrors()
      };
    },
    initSeparatorErrors() {
      return {
        Dst: {
          hasError: false,
          message: ""
        },
        Description: {
          hasError: false,
          message: ""
        },
        Position: {
          hasError: false,
          message: ""
        },
        order: {
          hasError: false,
          message: ""
        },
        color: {
          hasError: false,
          message: ""
        }
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
        },
        State: {
          hasError: false,
          message: ""
        },
        Position: {
          hasError: false,
          message: ""
        },
        order: {
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

      var objects = this.roles.concat(
        this.hosts.concat(
          this.hostGroups.concat(
            this.ipRanges.concat(this.cidrSubs.concat(this.zones.concat(this.macAddresses.concat(this.any))))
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
      this.newRule.SrcFull.name = this.newRule.SrcFull.typeId == 'role' ? this.newRule.SrcFull.name.toLowerCase() : this.newRule.SrcFull.name;
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

      var objects = this.roles.concat(
        this.hosts.concat(
          this.hostGroups.concat(
            this.ipRanges.concat(this.cidrSubs.concat(this.zones.concat(this.any)))
          )
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
      this.newRule.DstFull.name = this.newRule.DstFull.typeId == 'role' ? this.newRule.DstFull.name.toLowerCase() : this.newRule.DstFull.name;
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
      this.newRule.ServiceFull = {
        type: "fwservice"
      }
      this.newRule.ServiceType = "";

      if (query.trim().length === 0) {
        return null;
      }

      var objects = this.services.concat(this.applications);

      return objects.filter(function(service) {
        return (
          service.typeId.toLowerCase().includes(query.toLowerCase()) ||
          (service.Ports &&
            service.Ports.join(" ")
              .toLowerCase()
              .includes(query.toLowerCase())) ||
          service.name.toLowerCase().includes(query.toLowerCase()) ||
          (service.Description &&
            service.Description.toLowerCase().includes(query.toLowerCase()))
        );
      });
    },
    selectServiceAuto(item) {
      this.newRule.Service = item.name;

      this.newRule.ServiceFull = Object.assign({}, item);
      this.newRule.ServiceFull.type = this.newRule.ServiceFull.typeId;
      delete this.newRule.ServiceFull.typeId;

      this.newRule.ServiceType =
        item.name + (item.Ports ? " (" + item.Ports.join(", ") + ")" : "");
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
          context.getRoles();
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
          context.accessZones = context.roles.concat(context.zones);

          if (!context.view.macAddresses.isLoaded) {
            context.getMacAddresses();
          }
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
        ["nethserver-firewall-base/rules/read"],
        {
          action: "list",
          expand: context.expandInfo
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
            var rules = success.rules.map(function(r) {
              r.Log = r.Log == "none" ? false : true;
              r.State = (r.State == "new" || r.State == "" || !r.State) ? false : true;
              return r;
            });
            context.rules = rules;
            context.status = success.status;

            context.view.isLoaded = true;

            setTimeout(function() {
              $('[data-toggle="tooltip"]').tooltip();
            }, 500);

            context.$parent.getFirewallStatus();
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
    getPolicies() {
      var context = this;

      nethserver.exec(
        ["nethserver-firewall-base/rules/read"],
        {
          action: "policies"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
            context.policies = success.policies;
          } catch (e) {
            console.error(e);
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
    openCreateSeparator() {
      this.newSeparator = this.initSeparator();
      $("#createSeparatorModal").modal("show");
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
      this.newRule.order = 'bottom';

      $("#createRuleModal").modal("show");
    },
    openEditSeparator(r) {
      this.newSeparator = Object.assign({}, r);
      this.newSeparator.errors = this.initSeparatorErrors();
      this.newSeparator.isLoading = false;
      this.newSeparator.isEdit = true;

      $("#createSeparatorModal").modal("show");
    },
    toggleEnableRule(r) {
      var context = this;

      var ruleObj = {
        action: "update-rule",
        Log: r.Log ? "info" : " none",
        Time: r.Time ? r.Time : null,
        Position: r.Position,
        status: r.status == "enabled" ? "disabled" : "enabled",
        Service: r.Service ? r.Service : {
          "name": "any",
          "type": "fwservice"
        },
        Action: r.Action ? r.Action : null,
        Dst: r.Dst ? r.Dst : null,
        id: r.id,
        Src: r.Src ? r.Src : null,
        Description: r.Description,
        State: r.State ? "all" : "new",
        type: "rule"
      };

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "rules.rule_updated_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "rules.rule_updated_error"
      );

      // update values
      nethserver.exec(
        ["nethserver-firewall-base/rules/update"],
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
    openDeleteSeparator(r) {
      this.currentRule = Object.assign({}, r);
      $("#deleteSeparatorModal").modal("show");
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
        Service: context.newRule.ServiceFull && context.newRule.ServiceFull.name
          ? context.newRule.ServiceFull
          : {
              "name": "any",
              "type": "fwservice"
            },
        order: context.newRule.order,
        Action: context.newRule.Action ? context.newRule.Action : null,
        Dst: context.newRule.DstFull
          ? context.newRule.DstFull
          : { name: context.newRule.Dst, type: "raw" },
        id: context.newRule.isEdit ? context.newRule.id : null,
        Src: context.newRule.SrcFull
          ? context.newRule.SrcFull
          : { name: context.newRule.Src, type: "raw" },
        type: "rule",
        State: context.newRule.State ? "all" : "new",
        Description: context.newRule.Description
      };
      context.newRule.isLoading = true;
      context.$forceUpdate();
      nethserver.exec(
        ["nethserver-firewall-base/rules/validate"],
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
              "nethserver-firewall-base/rules/" +
                (context.newRule.isEdit ? "update" : "create")
            ],
            ruleObj,
            function(stream) {
              console.info("firewall-base-update", stream);
            },
            function(success) {

              if ( context.newRule.order === 'top' ) {
                //We retrieve order of ruleS by status.order 
                //and we unshift with status.nextID
                var nextID = context.status.nextID;
                context.moveRuleTop(nextID, 'rule');
              } else {
                context.getRules();
              }
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
    saveSeparator() {
      var context = this;

      var ruleObj = {
        action: context.newSeparator.isEdit ? "update-separator" : "create-separator",
        Position: context.newSeparator.isEdit
          ? context.newSeparator.Position
          : context.status.next,
        Dst: "",
        id: context.newSeparator.isEdit ? context.newSeparator.id : null,
        color: context.newSeparator.color,
        type: "separator",
        Description: context.newSeparator.Description
      };
      context.newRule.isLoading = true;
      context.$forceUpdate();
      nethserver.exec(
        ["nethserver-firewall-base/rules/validate"],
        ruleObj,
        null,
        function(success) {
          context.newRule.isLoading = false;
          $("#createSeparatorModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "rules.separator_" +
              (context.newSeparator.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "rules.separator_" +
              (context.newSeparator.isEdit ? "updated" : "created") +
              "_error"
          );

          // update values
          nethserver.exec(
            [
              "nethserver-firewall-base/rules/update"
            ],
            ruleObj,
            function(stream) {
              console.info("firewall-base-update", stream);
            },
            function(success) {
                var nextID = context.status.nextID;
                context.moveRuleTop(nextID, 'separator');
            },
            function(error, data) {
              console.error(error, data);
            }
          );
        },
        function(error, data) {
          var errorData = {};
          context.newRule.isLoading = false;
          context.newSeparator.errors = context.initSeparatorErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              context.newSeparator.errors[attr.parameter].hasError = true;
              context.newSeparator.errors[attr.parameter].message = attr.error;
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
        ["nethserver-firewall-base/rules/delete"],
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
    deleteSeparator(r) {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "rules.separator_deleted_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "rules.separator_deleted_error"
      );

      $("#deleteSeparatorModal").modal("hide");
      nethserver.exec(
        ["nethserver-firewall-base/rules/update"],
        {
          id: r.id,
          action: "delete-separator"
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
    },
    getMacAddresses() {
      var context = this;

      context.view.macAddresses.isLoaded = false;
      nethserver.exec(
        ["nethserver-firewall-base/objects/read"],
        {
          action: "macs"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.view.macAddresses.isLoaded = true;
          var macAddresses = success["macs"];
          context.macAddresses = macAddresses.map(function(mac) {
            mac.type = context.$i18n.t("objects.mac_address");
            mac.typeId = "mac";
            var accessZoneName = mac.Zone;
            var accessZone = context.accessZones.find(function(elem) {
              return elem.name === accessZoneName;
            });
            mac.Zone = accessZone;
            return mac;
          });

          context.$forceUpdate();
          context.$parent.getFirewallStatus();
        },
        function(error) {
          console.error(error);
        }
      );
    },
  }
};
</script>

<style>
.blue-background {
  background-color: #00b9e4;
}
.green-background {
  background-color: #92d400;
}
.red-background {
  background-color: #cc0000c0;
}
.info-desc-local {
  min-width: 75px;
}

.small-icon {
  font-size: 12px !important;
  height: 25px !important;
  width: 25px !important;
}

.small-icon::before {
  line-height: 20px !important;
}

.flex-50 {
  flex: 1 0 calc(50% - 20px) !important;
}

.adjust-checkbox-nested {
  margin: 0px 0 0 !important;
}

.square-GREEN {
  background: #3f9c35;
  width: 15px;
  height: 15px;
  display: inline-block;
  margin-bottom: -2px;
  border-radius: 3px;
  margin-right: 3px;
}

.square-RED {
  background: #cc0000;
  width: 15px;
  height: 15px;
  display: inline-block;
  margin-bottom: -2px;
  border-radius: 3px;
  margin-right: 3px;
}

.square-ORANGE {
  background: #ec7a08;
  width: 15px;
  height: 15px;
  display: inline-block;
  margin-bottom: -2px;
  border-radius: 3px;
  margin-right: 3px;
}

.square-BLUE {
  background: #0088ce;
  width: 15px;
  height: 15px;
  display: inline-block;
  margin-bottom: -2px;
  border-radius: 3px;
  margin-right: 3px;
}

.square-VPN {
  background: black;
  width: 15px;
  height: 15px;
  display: inline-block;
  margin-bottom: -2px;
  border-radius: 3px;
  margin-right: 3px;
}

.square-IVPN {
  background: black;
  width: 15px;
  height: 15px;
  display: inline-block;
  margin-bottom: -2px;
  border-radius: 3px;
  margin-right: 3px;
}

.square-OTHER {
  background: #703fec;
  width: 15px;
  height: 15px;
  display: inline-block;
  margin-bottom: -2px;
  border-radius: 3px;
  margin-right: 3px;
}

.square-GRAY {
  background: #72767b;
  width: 15px;
  height: 15px;
  display: inline-block;
  margin-bottom: -2px;
  border-radius: 3px;
  margin-right: 3px;
}

.red {
  color: #cc0000;
}
.green {
  color: #3f9c35;
}
.gray {
  color: #72767b;
}
.orange {
  color: #ec7a08;
}
.blue {
  color: #0088ce;
}
.other {
  color: #703fec;
}
.vpn {
  color: #363636;
}
.ivpn {
  color: #363636;
}
.black {
  color: #363636;
}

.red-list {
  border-left: 3px solid #cc0000 !important;
}
.green-list {
  border-left: 3px solid #3f9c35 !important;
}
.orange-list {
  border-left: 3px solid #ec7a08 !important;
}
.blue-list {
  border-left: 3px solid #0088ce !important;
}
.other-list {
  border-left: 3px solid #703fec !important;
}
.gray-list {
  border-left: 3px solid #72767b !important;
}
.black-list {
  border-left: 3px solid #363636 !important;
}

.border-red {
  border: 2px solid #cc0000 !important;
}
.border-green {
  border: 2px solid #3f9c35 !important;
}
.border-orange {
  border: 2px solid #ec7a08 !important;
}
.border-blue {
  border: 2px solid #0088ce !important;
}
.border-other {
  border: 2px solid #703fec !important;
}
.border-gray {
  border: 2px solid #72767b !important;
}
.border-black {
  border: 2px solid #363636 !important;
}

.list-group-item-heading {
  flex: auto !important;
  width: calc(50% - 20px) !important;
  font-size: 16px !important;
}
.list-group-item-text {
  width: calc(50% - 20px) !important;
  font-size: 16px !important;
  font-weight: 600;
}

.sortable-chosen {
  border: 1px solid rgb(162, 212, 237) !important;
  z-index: 9;
}
.sortable-ghost {
  border: 2px dashed #b4b5b8 !important;
  z-index: 9;
}

.line-icon::before {
  vertical-align: sub;
}

.handle-overflow {
  text-overflow: ellipsis;
  overflow: hidden;
}

.drag-here {
  cursor: pointer;
}

.detail-icon {
  font-size: 12px !important;
}
.big-icon {
  font-size: 16px !important;
}

.log-icon {
  position: absolute;
  bottom: 9px;
  margin-left: -40px;
  font-size: 12px !important;
}

.state-icon {
  position: absolute;
  bottom: 50px;
  margin-left: -40px;
  font-size: 12px !important;
  color: #ec7a08;
}

.type-info {
  margin-top: 10px;
  display: inline-block;
}

.drag-size {
  min-width: 35px !important;
}

.drag-size-large {
  min-width: 65px !important;
}

.tooltip-inner {
  width: auto;
}

.rules-src-dst {
  width: 80% !important;
}
@media (min-width: 992px) and (max-width: 1300px) {
  .rules-src-dst {
    flex: 0 0 75% !important;
  }
}
.rules-info {
  width: 30% !important;
}
.expand-text {
  margin-right: 5px;
  vertical-align: top;
}
</style>
