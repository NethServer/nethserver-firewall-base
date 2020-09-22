<template>
  <div>
    <form v-if="view.isLoaded" class="mg-top-20">
      <div class="form-group">
        <label class="col-sm-3 control-label show-network-services" for="show-all-services">
          {{$t('rules.show_network_services')}}
          <doc-info :placement="'bottom'" :chapter="'network_services_system_services'" :inline="true"></doc-info>
        </label>
        <div class="col-sm-1 mg-bottom-10">
          <input
            id="show-all-services"
            type="checkbox"
            :checked="showNetworkServices"
            class="form-control mg-top-minus-2"
            @click="toggleShowNetworkServices()"
          />
        </div>
      </div>
    </form>

    <h2 class="clear">{{$t('rules.title_local')}}</h2>
    <div v-if="!view.isLoaded" class="spinner spinner-lg view-spinner"></div>
    <div v-if="rules.length == 0 && view.isLoaded" class="blank-slate-pf white">
      <div class="blank-slate-pf-icon">
        <span class="fa fa-fire"></span>
      </div>
      <h1>{{$t('rules.no_rules_found')}}</h1>
      <p>{{$t('rules.no_rules_found_text')}}.</p>
      <div class="blank-slate-pf-main-action">
        <button class="btn btn-primary btn-lg" @click="openCreateRule()">{{$t('rules.create_rule')}}</button>
      </div>
    </div>

    <div v-if="rules.length > 0 && view.isLoaded">
      <h3>{{$t('actions')}}</h3>
      <button @click="openCreateRule()" class="btn btn-primary btn-lg">{{$t('rules.create_rule')}}</button>
    </div>

    <div class="pf-container" v-if="rules.length > 0 && view.isLoaded">
      <h3>{{$t('rules.list')}}</h3>
      <form v-if="rules.length > 0" role="form" class="search-pf has-button search clear">
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
            />
          </div>
        </div>
      </form>

      <ul
        v-if="rules.length > 0 && view.isLoaded"
        v-sortable="{onEnd: reorder, handle: '.drag-here'}"
        class="list-group list-view-pf list-view-pf-view no-mg-top mg-top-10"
      >
        <li
          :class="[r.status == 'disabled' ? 'gray-list' : mapList(r.Action), 'list-group-item', r.status == 'disabled' ? 'gray' : '']"
          v-for="(r,k) in filteredRules"
          v-bind:key="k"
        >
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

    <!-- network services -->
    <div v-show="showNetworkServices">
      <h2>{{$t('rules.network_services')}}</h2>
      <div v-show="!networkServicesLoaded" class="spinner spinner-lg view-spinner"></div>

      <ul
        v-show="networkServicesLoaded && networkServices.length > 0"
        class="list-group list-view-pf list-view-pf-view no-mg-top mg-top-20"
      >
        <li
          class="green-list list-group-item"
          v-for="(service, k) in networkServices"
          v-bind:key="k"
        >
          <div class="list-view-pf-actions">
            <button @click="openEditService(service)" class="btn btn-default">
              <span class="fa fa-edit span-right-margin"></span>
              {{$t('edit')}}
            </button>
          </div>
          <div class="list-view-pf-main-info small-list">
            <div class="list-view-pf-left">
              <span
                data-toggle="tooltip"
                data-placement="top"
                data-html="true"
                title="<b>ACCEPT</b>"
                class="fa fa-check green border-green list-view-pf-icon-sm"
              ></span>
            </div>
            <div class="list-view-pf-body">
              <div class="list-view-pf-description rules-src-dst">
                <div class="list-group-item-heading zone-network-service">
                  <span
                    v-for="(zone, k) in service.ports.access.split(',')"
                    class="handle-overflow mg-right-10"
                    v-bind:key="k"
                  >
                    <span v-if="zone">
                      <span :class="mapZoneIcon(zone)"></span>
                      <span
                        :class="[defaultZones.includes(zone) ? zone : 'other', 'mg-left-5']"
                      >{{ zone.toUpperCase() }}</span>
                    </span>
                    <span v-else>
                      <!-- empty access: localhost -->
                      <span class="square-GRAY"></span>
                      <span class="gray mg-left-5">LOCALHOST</span>
                    </span>
                  </span>
                </div>
                <div class="list-group-item-text fw-network-service">
                  <span class="gray fa fa-arrow-right mg-right-10 big-icon]"></span>
                  <span
                    data-toggle="tooltip"
                    data-placement="top"
                    data-html="true"
                    title="<b>FW</b><br><span class='type-info'><b>Firewall</b></span>"
                    class="handle-overflow"
                  >
                    <span class="fa fa-fire"></span>
                    <span class="fw mg-left-5">FW</span>
                  </span>
                </div>
              </div>
              <div class="list-view-pf-additional-info rules-info">
                <div
                  data-toggle="tooltip"
                  data-placement="top"
                  data-html="true"
                  :title="mapTitleNetworkService(service)"
                  class="list-view-pf-additional-info-item"
                >
                  <span class="fa fa-cogs"></span>
                  <strong>{{ service.name }}</strong>
                  <span v-if="service.custom" class="gray">(custom)</span>
                </div>
                <div class="list-view-pf-additional-info-item"></div>
              </div>
            </div>
          </div>
        </li>
      </ul>
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
              <div class="form-group">
                <label class="col-sm-3 control-label"></label>
                <input
                  id="to-fw-radio"
                  class="col-sm-2"
                  type="radio"
                  v-model="newRule.fwTarget"
                  value="to-fw"
                />
                <label
                  class="col-sm-6 control-label text-align-left"
                  for="to-fw-radio"
                >{{$t("rules.to_firewall")}}</label>

                <label class="col-sm-3 control-label"></label>
                <input
                  id="from-fw-radio"
                  class="col-sm-2"
                  type="radio"
                  v-model="newRule.fwTarget"
                  value="from-fw"
                />
                <label
                  class="col-sm-6 control-label text-align-left"
                  for="from-fw-radio"
                >{{$t("rules.from_firewall")}}</label>
              </div>
              <div
                v-show="newRule.fwTarget == 'to-fw'"
                :class="['form-group', newRule.errors.Src.hasError ? 'has-error' : '']"
              >
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
                    :required="newRule.fwTarget == 'to-fw'"
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

              <div
                v-show="newRule.fwTarget == 'from-fw'"
                :class="['form-group', newRule.errors.Dst.hasError ? 'has-error' : '']"
              >
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
                    :required="newRule.fwTarget == 'from-fw'"
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
                <label class="col-sm-3 control-label">
                  {{$t('rules.service')}}
                  <doc-info
                    :placement="'top'"
                    :title="$t('rules.service')"
                    :chapter="'rules_service'"
                    :inline="true"
                  ></doc-info>
                </label>
                <div class="col-sm-9">
                  <suggestions
                    v-model="newRule.Service"
                    :options="serviceOptions"
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

              <div :class="['form-group', newRule.errors.Action.hasError ? 'has-error' : '']">
                <label class="col-sm-3 control-label">{{$t('rules.action')}}</label>
                <div class="col-sm-9">
                  <select v-model="newRule.Action" class="form-control">
                    <option value="accept">{{$t('rules.accept')}}</option>
                    <option value="reject">{{$t('rules.reject')}}</option>
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
                  <input class="form-control" type="text" v-model="newRule.Description" />
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
                <label class="col-sm-3 control-label">{{$t('rules.log')}}</label>
                <div class="col-sm-9">
                  <input class="form-control" type="checkbox" v-model="newRule.Log" />
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
                  <input required type="text" v-model="newObject.name" class="form-control" />
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
                  <input required type="text" v-model="newObject.IpAddress" class="form-control" />
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
                  <input required type="text" v-model="newObject.Address" class="form-control" />
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
                  <input type="text" v-model="newObject.Description" class="form-control" />
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

    <!-- edit service modal -->
    <div class="modal" id="edit-service-modal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">
              <span>{{$t('rules.edit_service')}}</span>
            </h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="editService()">
            <div class="modal-body">
              <!-- name -->
              <div class="form-group">
                <label class="col-sm-3 control-label">{{$t('rules.name')}}</label>
                <div class="col-sm-9">
                  <input type="text" v-model="currentService.name" class="form-control" disabled />
                </div>
              </div>
              <!-- tcp ports -->
              <div
                :class="['form-group', currentService.errorProps['tcpPorts'] || currentService.errorProps['no_tcp_udp_ports'] ? 'has-error' : '']"
              >
                <label class="col-sm-3 control-label">
                  {{$t('rules.tcpPorts')}}
                  <doc-info :placement="'top'" :chapter="'list_of_ports'" :inline="true"></doc-info>
                </label>
                <div class="col-sm-9">
                  <input
                    type="text"
                    v-model="currentService.tcpPorts"
                    class="form-control"
                    :disabled="!currentService.custom"
                  />
                  <span
                    v-if="currentService.errorProps['tcpPorts']"
                    class="help-block"
                  >{{$t('validation.' + currentService.errorProps['tcpPorts'])}}</span>
                </div>
              </div>
              <!-- udp ports -->
              <div
                :class="['form-group', currentService.errorProps['udpPorts'] || currentService.errorProps['no_tcp_udp_ports'] ? 'has-error' : '']"
              >
                <label class="col-sm-3 control-label">
                  {{$t('rules.udpPorts')}}
                  <doc-info :placement="'top'" :chapter="'list_of_ports'" :inline="true"></doc-info>
                </label>
                <div class="col-sm-9">
                  <input
                    type="text"
                    v-model="currentService.udpPorts"
                    class="form-control"
                    :disabled="!currentService.custom"
                  />
                  <span
                    v-if="currentService.errorProps['udpPorts']"
                    class="help-block"
                  >{{$t('validation.' + currentService.errorProps['udpPorts'])}}</span>
                </div>
              </div>
              <!-- access -->
              <div :class="['form-group', currentService.errorProps['access'] ? 'has-error' : '']">
                <label class="col-sm-3 control-label">{{$t('rules.access')}}</label>
                <div class="col-sm-9">
                  <select
                    @change="addZoneToCurrentService(currentService.selectedZone)"
                    v-model="currentService.selectedZone"
                    class="combobox form-control"
                  >
                    <option v-for="(zone, i) in accessZones" v-bind:key="i">{{ zone }}</option>
                  </select>
                  <span
                    v-if="currentService.errorProps['access']"
                    class="help-block"
                  >{{$t('validation.' + currentService.errorProps['access'])}}</span>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label"></label>
                <div class="col-sm-9">
                  <ul class="list-inline compact">
                    <li v-for="(zone, i) in currentService.access" v-bind:key="i">
                      <span
                        :class="['label', 'label-info', defaultZones.includes(zone) ? 'bg-' + zone : 'bg-missing']"
                      >
                        {{ zone }}
                        <a
                          @click="removeZoneFromcurrentService(i)"
                          class="remove-item-inline"
                        >
                          <span class="fa fa-times"></span>
                        </a>
                      </span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <div class="modal-footer submit">
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button
                class="btn btn-primary"
                type="submit"
              >{{ currentService.isEdit ? $t('edit') : $t('add') }}</button>
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
    this.getHosts();
    this.getHostGroups();
    this.getIPRanges();
    this.getCIDRSubs();
    this.getZones();
    this.getTimeConditions();
    this.getServices();
    this.getLocalServices();
    this.getRoles();
    this.getNetworkServices();
    this.getAccessZones();

    var context = this;
    context.$parent.$on("changes-applied", function() {
      context.getRules();
      context.getHosts();
      context.getHostGroups();
      context.getIPRanges();
      context.getCIDRSubs();
      context.getZones();
      context.getTimeConditions();
      context.getServices();
      context.getLocalServices();
      context.getRoles();
      context.getNetworkServices();
      context.getAccessZones();
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
      rules: [],
      hosts: [],
      hostGroups: [],
      ipRanges: [],
      cidrSubs: [],
      zones: [],
      timeConditions: [],
      services: [],
      localServices: [],
      roles: [],
      serviceOptions: {
        placeholder: 'any',
        inputClass: "form-control"
      },
      autoOptions: {
        inputClass: "form-control"
      },
      newRule: this.initRule(),
      currentRule: {},
      searchString: "",
      highlightInstance: null,
      expandInfo: true,
      status: {},
      newObject: this.initObject(),
      showNetworkServices:
        localStorage.getItem("showNetworkServicesInLocalRules") === "true" ||
        false,
      networkServices: [],
      defaultZones: ["green", "red", "blue", "orange"],
      currentService: this.initService(),
      accessZones: [],
      networkServicesLoaded: false
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
        ["nethserver-firewall-base/local-rules/update"],
        {
          action: "reorder",
          rules: ids
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
    mapTitleNetworkService(service) {
      var html = "<b>" + service.name + "</b><br>";

      if (service.ports.TCP.length > 0) {
        html +=
          '<div><span class="fa fa-arrows-h mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>TCP</span></div>";

        html +=
          '<div><span class="pficon pficon-template mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          service.ports.TCP.join(", ") +
          "</span></div>";
      }

      if (service.ports.UDP.length > 0) {
        html +=
          '<div><span class="fa fa-arrows-h mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>UDP</span></div>";

        html +=
          '<div><span class="pficon pficon-template mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          service.ports.UDP.join(", ") +
          "</span></div>";
      }
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
      this.newRule.Src = null;
      this.newRule.SrcFull = null;
      this.newRule.SrcType = "";

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
      this.newRule.SrcFull.name =
        this.newRule.SrcFull.typeId == "role"
          ? this.newRule.SrcFull.name.toLowerCase()
          : this.newRule.SrcFull.name;
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
            this.ipRanges.concat(this.cidrSubs.concat(this.zones))
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
      this.newRule.DstFull.name =
        this.newRule.DstFull.typeId == "role"
          ? this.newRule.DstFull.name.toLowerCase()
          : this.newRule.DstFull.name;
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

      var objects = this.services.concat(this.localServices);

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
    getLocalServices() {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/objects/read"],
        {
          action: "local-services"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.localServices = success["local-services"];
          context.localServices = context.localServices.map(function(i) {
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
        ["nethserver-firewall-base/local-rules/read"],
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
              return r;
            });
            context.rules = rules;
            context.status = success.status;

            context.view.isLoaded = true;

            setTimeout(function() {
              $('[data-toggle="tooltip"]').tooltip();
            }, 750);

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
    openCreateRule() {
      this.newRule = this.initRule();
      $("#createRuleModal").modal("show");
    },
    openEditRule(r, duplicate) {
      r.fwTarget = r.Dst.type == "fw" ? "to-fw" : "from-fw";

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
    toggleEnableRule(r) {
      var context = this;

      var ruleObj = {
        action: "update-rule",
        Log: r.Log ? "info" : " none",
        Time: r.Time ? r.Time : null,
        Position: r.Position,
        status: r.status == "enabled" ? "disabled" : "enabled",
        Service: r.Service
          ? r.Service
          : {
              name: "any",
              type: "fwservice"
            },
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
        ["nethserver-firewall-base/local-rules/update"],
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
          : {
              name: "any",
              type: "fwservice"
            },
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

      if (context.newRule.fwTarget == "to-fw") {
        ruleObj.Dst = {
          name: "fw",
          type: "fw"
        };
      }
      if (context.newRule.fwTarget == "from-fw") {
        ruleObj.Src = {
          name: "fw",
          type: "fw"
        };
      }

      context.newRule.isLoading = true;
      context.$forceUpdate();
      nethserver.exec(
        ["nethserver-firewall-base/local-rules/validate"],
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
              "nethserver-firewall-base/local-rules/" +
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
        ["nethserver-firewall-base/local-rules/delete"],
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
    },

    toggleShowNetworkServices() {
      this.showNetworkServices = !this.showNetworkServices;
      localStorage.setItem(
        "showNetworkServicesInLocalRules",
        this.showNetworkServices
      );
    },

    getNetworkServices() {
      var context = this;
      context.networkServicesLoaded = false;

      nethserver.exec(
        ["system-services/read"],
        {
          action: "list"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
            context.networkServicesLoaded = true;
          }
          for (var c in success.configuration) {
            var config = success.configuration[c];
            for (var s in success.status) {
              var status = success.status[s];
              if (status.name == config.name) {
                config.running = status.running;
              }
            }
          }
          context.networkServices = success.configuration.filter(function(
            service
          ) {
            return (
              service.ports.access ||
              service.ports.TCP.length > 0 ||
              service.ports.UDP.length > 0
            );
          });
          context.networkServicesLoaded = true;

          setTimeout(function() {
            $('[data-toggle="tooltip"]').tooltip("fixTitle");
          }, 750);
        },
        function(error) {
          console.error(error);
          context.networkServicesLoaded = true;
        }
      );
    },

    openEditService(service) {
      this.currentService = this.mapService(service);
      this.currentService.isEdit = true;
      $("#edit-service-modal").modal("show");
    },

    initService() {
      return {
        name: "",
        access: [],
        tcpPorts: "",
        udpPorts: "",
        custom: 1,
        selectedZone: null,
        errorProps: [],
        isEdit: false
      };
    },

    mapService(service) {
      return {
        name: service.name,
        access:
          service.ports.access === "" ? [] : service.ports.access.split(","),
        tcpPorts: service.ports.TCP.join(", "),
        udpPorts: service.ports.UDP.join(", "),
        custom: service.custom,
        selectedZone: null,
        errorProps: [],
        isEdit: false
      };
    },

    editService() {
      var context = this;
      var tcpPorts = [];
      var udpPorts = [];
      context.currentService.errorProps = [];

      if (context.currentService.tcpPorts) {
        // remove spaces and convert to array
        tcpPorts = context.currentService.tcpPorts
          .replace(/\s/g, "")
          .split(",");
      }

      if (context.currentService.udpPorts) {
        // remove spaces and convert to array
        udpPorts = context.currentService.udpPorts
          .replace(/\s/g, "")
          .split(",");
      }

      var editServiceObj = {
        action: "edit",
        serviceName: context.currentService.name.trim(),
        access: context.currentService.access,
        tcpPorts: tcpPorts,
        udpPorts: udpPorts,
        custom: context.currentService.custom
      };

      nethserver.exec(
        ["system-services/validate"],
        editServiceObj,
        null,
        function(success) {
          $("#edit-service-modal").modal("hide");

          nethserver.notifications.success = context.$i18n.t(
            "rules.service_edited_successfully"
          );
          nethserver.notifications.error = context.$i18n.t(
            "rules.service_edited_error"
          );

          nethserver.exec(
            ["system-services/update"],
            editServiceObj,
            function(stream) {
              console.info("serviceEdit", stream);
            },
            function(success) {
              context.currentService = context.initService();
              context.getNetworkServices();
            },
            function(error, data) {
              console.error(error);
            }
          );
        },
        function(error, data) {
          var errorData = {};

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              context.currentService.errorProps[attr.parameter] = attr.error;
            }
            context.$forceUpdate();
          } catch (e) {
            console.error(e);
          }
        }
      );
    },

    zoneAlreadyAdded(zone) {
      return this.currentService.access.indexOf(zone) > -1;
    },

    addZoneToCurrentService(zone) {
      if (zone.length > 0 && zone != "-") {
        if (!this.zoneAlreadyAdded(zone)) {
          this.currentService.access.push(zone);
        }
      }
    },

    getAccessZones() {
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
          var roles = success.roles;

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
              var zones = success.zones.map(zone => zone.name);
              context.accessZones = roles.concat(zones);
            },
            function(error) {
              console.error(error);
            }
          );
        },
        function(error) {
          console.error(error);
        }
      );
    },

    removeZoneFromcurrentService(index) {
      this.currentService.access.splice(index, 1);
    }
  }
};
</script>

<style>
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
  color: black;
}
.ivpn {
  color: black;
}

.bg-green {
  background-color: #3f9c35;
}

.bg-red {
  background-color: #cc0000;
}

.bg-orange {
  background-color: #ec7a08;
}

.bg-blue {
  background-color: #0088ce;
}

.bg-black {
  background-color: black;
}

.bg-missing {
  background-color: #703fec;
}

.red-list {
  border-left: 3px solid #cc0000 !important;
}
.green-list {
  border-left: 3px solid #3f9c35 !important;
}
.gray-list {
  border-left: 3px solid #72767b !important;
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

.border-red {
  border: 2px solid #cc0000 !important;
}
.border-green {
  border: 2px solid #3f9c35 !important;
}
.border-gray {
  border: 2px solid #72767b !important;
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

.type-info {
  margin-top: 10px;
  display: inline-block;
}

.drag-size {
  min-width: 35px !important;
}

.tooltip-inner {
  width: auto;
}

.rules-src-dst {
  width: calc(70% - 20px) !important;
}

.rules-info {
  width: calc(30% - 20px) !important;
}

.expand-text {
  margin-right: 5px;
  vertical-align: top;
}

.mg-top-minus-2 {
  margin-top: -2px !important;
}

.clear {
  clear: both;
}

.show-network-services {
  padding-left: 0;
  width: auto;
}

.mg-bottom-10 {
  margin-bottom: 10px;
}

.mg-top-20 {
  margin-top: 20px;
}

.zone-network-service {
  width: calc(60% - 20px) !important;
}

.fw-network-service {
  width: calc(40% - 20px) !important;
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

.remove-item-inline {
  color: white !important;
}
</style>
