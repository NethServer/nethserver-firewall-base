<template>
  <div>
    <h2>{{$t('rules.title_local')}}</h2>

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
      <div class="right">
        <span class="expand-text">{{$t('rules.expand')}}</span>
        <toggle-button
          class="min-toggle"
          :width="40"
          :height="20"
          :color="{checked: '#0088ce', unchecked: '#bbbbbb'}"
          :value="expandInfo"
          :sync="true"
          @change="toggleExpand()"
        />
      </div>
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
          :class="[mapList(r.Action), 'list-group-item']"
          v-for="r in filteredRules"
          v-bind:key="r"
        >
          <div class="drag-size">
            <span class="gray mg-right-5">{{r.id}}</span>
          </div>
          <div v-show="searchString.length == 0" class="list-view-pf-checkbox drag-here">
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
                <label class="col-sm-4 control-label"></label>
                <input
                  id="to-fw-radio"
                  class="col-sm-2"
                  type="radio"
                  v-model="newRule.fwTarget"
                  value="to-fw"
                >
                <label
                  class="col-sm-6 control-label text-align-left"
                  for="to-fw-radio"
                >{{$t("rules.to_firewall")}}</label>

                <label class="col-sm-4 control-label"></label>
                <input
                  id="from-fw-radio"
                  class="col-sm-2"
                  type="radio"
                  v-model="newRule.fwTarget"
                  value="from-fw"
                >
                <label
                  class="col-sm-6 control-label text-align-left"
                  for="from-fw-radio"
                >{{$t("rules.from_firewall")}}</label>
              </div>
              <div
                v-show="newRule.fwTarget == 'to-fw'"
                :class="['form-group', newRule.errors.Src.hasError ? 'has-error' : '']"
              >
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

              <div
                v-show="newRule.fwTarget == 'from-fw'"
                :class="['form-group', newRule.errors.Dst.hasError ? 'has-error' : '']"
              >
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

              <div :class="['form-group', newRule.errors.Action.hasError ? 'has-error' : '']">
                <label class="col-sm-4 control-label">{{$t('rules.action')}}</label>
                <div class="col-sm-8">
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
                    @change="toggleAdvancedMode()"
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
    this.getApplications();
    this.getRoles();
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
      newRule: this.initRule(),
      searchString: "",
      highlightInstance: null,
      expandInfo:
        (localStorage.getItem("expandInfo") &&
          localStorage.getItem("expandInfo") == "true") ||
        false
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
    toggleExpand() {
      this.expandInfo = !this.expandInfo;
      localStorage.setItem("expandInfo", this.expandInfo) || false;
      this.getRules();
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
    mapIcon(action) {
      switch (action) {
        case "accept":
          return "fa fa-check green border-green";
          break;
        case "reject":
          return "fa fa-shield orange border-orange";
          break;
        case "drop":
          return "fa fa-ban red border-red";
          break;
      }
    },
    toggleAdvancedMode() {
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
  width: 70% !important;
}
.rules-info {
  width: 30% !important;
}
.expand-text {
  margin-right: 5px;
  vertical-align: top;
}
</style>