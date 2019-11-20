<template>
  <div>
    <h2>{{$t('objects.title')}}</h2>

    <ul class="nav nav-tabs nav-tabs-pf">
      <li>
        <a
          class="nav-link"
          data-toggle="tab"
          href="#hosts-tab"
          id="hosts-tab-parent"
        >{{$t('objects.hosts')}}</a>
      </li>
      <li>
        <a
          class="nav-link"
          data-toggle="tab"
          href="#host-groups-tab"
          id="host-groups-tab-parent"
        >{{$t('objects.host_groups')}}</a>
      </li>
      <li>
        <a
          class="nav-link"
          data-toggle="tab"
          href="#ip-ranges-tab"
          id="ip-ranges-tab-parent"
        >{{$t('objects.ip_ranges')}}</a>
      </li>
      <li>
        <a
          class="nav-link"
          data-toggle="tab"
          href="#cidr-subs-tab"
          id="cidr-subs-tab-parent"
        >{{$t('objects.cidr_subs')}}</a>
      </li>
      <li>
        <a
          class="nav-link"
          data-toggle="tab"
          href="#zones-tab"
          id="zones-tab-parent"
        >{{$t('objects.zones')}}</a>
      </li>
      <li>
        <a
          class="nav-link"
          data-toggle="tab"
          href="#time-conditions-tab"
          id="time-conditions-tab-parent"
        >{{$t('objects.time_conditions')}}</a>
      </li>
      <li>
        <a
          class="nav-link"
          data-toggle="tab"
          href="#services-tab"
          id="services-tab-parent"
        >{{$t('objects.services')}}</a>
      </li>
    </ul>

    <div class="tab-content" id="objectsTabContent">
      <!-- HOSTS -->
      <div class="tab-pane fade active" id="hosts-tab" role="tabpanel" aria-labelledby="hosts-tab">
        <h3>{{$t('actions')}}</h3>
        <button @click="openCreateHost()" class="btn btn-primary btn-lg">{{$t('objects.add_host')}}</button>

        <h3>{{$t('list')}}</h3>
        <div v-if="!view.hosts.isLoaded" class="spinner spinner-lg"></div>
        <vue-good-table
          v-show="view.hosts.isLoaded"
          :customRowsPerPageDropdown="[25,50,100]"
          :perPage="25"
          :columns="hostsColumns"
          :rows="hostsRows"
          :lineNumbers="false"
          :defaultSortBy="{field: 'name', type: 'asc'}"
          :globalSearch="true"
          :paginate="true"
          styleClass="table"
          :nextText="tableLangsTexts.nextText"
          :prevText="tableLangsTexts.prevText"
          :rowsPerPageText="tableLangsTexts.rowsPerPageText"
          :globalSearchPlaceholder="tableLangsTexts.globalSearchPlaceholder"
          :ofText="tableLangsTexts.ofText"
        >
          <template slot="table-row" slot-scope="props">
            <td class="fancy">
              <a
                :class="[props.row.type != 'host' ? 'disabled-black' : '']"
                @click="props.row.type != 'host' ? undefined : openEditHost(props.row)"
              >
                <strong>{{ props.row.name}}</strong>
                <span class="mg-left-5" v-if="props.row.type != 'host'">({{$t('objects.'+props.row.type)}})</span>
              </a>
            </td>
            <td class="fancy">
              <span class="pficon pficon-screen"></span>
              {{props.row.IpAddress}}
            </td>
            <td class="fancy">{{ props.row.Description}}</td>
            <td>
              <button
                v-if="props.row.type == 'host'"
                @click="openEditHost(props.row)"
                class="btn btn-default"
              >
                <span class="fa fa-pencil span-right-margin"></span>
                {{$t('edit')}}
              </button>
              <div v-if="props.row.type == 'host'" class="dropup pull-right dropdown-kebab-pf">
                <button
                  class="btn btn-link dropdown-toggle"
                  type="button"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="true"
                >
                  <span class="fa fa-ellipsis-v"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                  <li>
                    <a @click="openDeleteHost(props.row)">
                      <span class="fa fa-times span-right-margin"></span>
                      {{$t('delete')}}
                    </a>
                  </li>
                </ul>
              </div>
            </td>
          </template>
        </vue-good-table>
      </div>
      <!-- END HOSTS -->
      <!-- HOST GROUPS -->
      <div
        class="tab-pane fade active"
        id="host-groups-tab"
        role="tabpanel"
        aria-labelledby="host-groups-tab"
      >
        <h3>{{$t('actions')}}</h3>
        <button
          @click="openCreateHostGroup()"
          class="btn btn-primary btn-lg"
        >{{$t('objects.add_host_group')}}</button>

        <h3>{{$t('list')}}</h3>
        <div v-if="!view.hostGroups.isLoaded" class="spinner spinner-lg"></div>
        <vue-good-table
          v-show="view.hostGroups.isLoaded"
          :customRowsPerPageDropdown="[25,50,100]"
          :perPage="25"
          :columns="hostGroupsColumns"
          :rows="hostGroupsRows"
          :lineNumbers="false"
          :defaultSortBy="{field: 'name', type: 'asc'}"
          :globalSearch="true"
          :paginate="true"
          styleClass="table"
          :nextText="tableLangsTexts.nextText"
          :prevText="tableLangsTexts.prevText"
          :rowsPerPageText="tableLangsTexts.rowsPerPageText"
          :globalSearchPlaceholder="tableLangsTexts.globalSearchPlaceholder"
          :ofText="tableLangsTexts.ofText"
        >
          <template slot="table-row" slot-scope="props">
            <td class="fancy">
              <a @click="openEditHostGroup(props.row)">
                <strong>{{ props.row.name}}</strong>
              </a>
            </td>
            <td class="fancy">{{ props.row.Description}}</td>
            <td>
              <button @click="openEditHostGroup(props.row)" class="btn btn-default">
                <span class="fa fa-pencil span-right-margin"></span>
                {{$t('edit')}}
              </button>
              <div class="dropup pull-right dropdown-kebab-pf">
                <button
                  class="btn btn-link dropdown-toggle"
                  type="button"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="true"
                >
                  <span class="fa fa-ellipsis-v"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                  <li>
                    <a @click="openDeleteHostGroup(props.row)">
                      <span class="fa fa-times span-right-margin"></span>
                      {{$t('delete')}}
                    </a>
                  </li>
                </ul>
              </div>
            </td>
          </template>
        </vue-good-table>
      </div>
      <!-- END HOST GROUPS -->
      <!-- IP RANGES -->
      <div
        class="tab-pane fade active"
        id="ip-ranges-tab"
        role="tabpanel"
        aria-labelledby="ip-ranges-tab"
      >
        <h3>{{$t('actions')}}</h3>
        <button
          @click="openCreateIPRange()"
          class="btn btn-primary btn-lg"
        >{{$t('objects.add_ip_range')}}</button>

        <h3>{{$t('list')}}</h3>
        <div v-if="!view.ipRanges.isLoaded" class="spinner spinner-lg"></div>
        <vue-good-table
          v-show="view.ipRanges.isLoaded"
          :customRowsPerPageDropdown="[25,50,100]"
          :perPage="25"
          :columns="ipRangesColumns"
          :rows="ipRangesRows"
          :lineNumbers="false"
          :defaultSortBy="{field: 'name', type: 'asc'}"
          :globalSearch="true"
          :paginate="true"
          styleClass="table"
          :nextText="tableLangsTexts.nextText"
          :prevText="tableLangsTexts.prevText"
          :rowsPerPageText="tableLangsTexts.rowsPerPageText"
          :globalSearchPlaceholder="tableLangsTexts.globalSearchPlaceholder"
          :ofText="tableLangsTexts.ofText"
        >
          <template slot="table-row" slot-scope="props">
            <td class="fancy">
              <a @click="openEditIPRange(props.row)">
                <strong>{{ props.row.name}}</strong>
              </a>
            </td>
            <td class="fancy">
              <span class="col-sm-1">{{$t('objects.start')}}:</span>
              <b class="col-sm-2">{{ props.row.Start}}</b>
              <br />
              <span class="col-sm-1">{{$t('objects.end')}}:</span>
              <b class="col-sm-2">{{ props.row.End}}</b>
            </td>
            <td class="fancy">{{ props.row.Description}}</td>
            <td>
              <button @click="openEditIPRange(props.row)" class="btn btn-default">
                <span class="fa fa-pencil span-right-margin"></span>
                {{$t('edit')}}
              </button>
              <div class="dropup pull-right dropdown-kebab-pf">
                <button
                  class="btn btn-link dropdown-toggle"
                  type="button"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="true"
                >
                  <span class="fa fa-ellipsis-v"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                  <li>
                    <a @click="openDeleteIPRange(props.row)">
                      <span class="fa fa-times span-right-margin"></span>
                      {{$t('delete')}}
                    </a>
                  </li>
                </ul>
              </div>
            </td>
          </template>
        </vue-good-table>
      </div>
      <!-- END IP RANGE -->
      <!-- CIDR SUBS -->
      <div
        class="tab-pane fade active"
        id="cidr-subs-tab"
        role="tabpanel"
        aria-labelledby="cidr-subs-tab"
      >
        <h3>{{$t('actions')}}</h3>
        <button
          @click="openCreateCIDRSub()"
          class="btn btn-primary btn-lg"
        >{{$t('objects.add_cidr_sub')}}</button>

        <h3>{{$t('list')}}</h3>
        <div v-if="!view.cidrSubs.isLoaded" class="spinner spinner-lg"></div>
        <vue-good-table
          v-show="view.cidrSubs.isLoaded"
          :customRowsPerPageDropdown="[25,50,100]"
          :perPage="25"
          :columns="cidrSubsColumns"
          :rows="cidrSubsRows"
          :lineNumbers="false"
          :defaultSortBy="{field: 'name', type: 'asc'}"
          :globalSearch="true"
          :paginate="true"
          styleClass="table"
          :nextText="tableLangsTexts.nextText"
          :prevText="tableLangsTexts.prevText"
          :rowsPerPageText="tableLangsTexts.rowsPerPageText"
          :globalSearchPlaceholder="tableLangsTexts.globalSearchPlaceholder"
          :ofText="tableLangsTexts.ofText"
        >
          <template slot="table-row" slot-scope="props">
            <td class="fancy">
              <a @click="openEditCIDRSub(props.row)">
                <strong>{{ props.row.name}}</strong>
              </a>
            </td>
            <td class="fancy">
              <span class="pficon pficon-screen"></span>
              {{props.row.Address}}
            </td>
            <td class="fancy">{{ props.row.Description}}</td>
            <td>
              <button @click="openEditCIDRSub(props.row)" class="btn btn-default">
                <span class="fa fa-pencil span-right-margin"></span>
                {{$t('edit')}}
              </button>
              <div class="dropup pull-right dropdown-kebab-pf">
                <button
                  class="btn btn-link dropdown-toggle"
                  type="button"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="true"
                >
                  <span class="fa fa-ellipsis-v"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                  <li>
                    <a @click="openDeleteCIDRSub(props.row)">
                      <span class="fa fa-times span-right-margin"></span>
                      {{$t('delete')}}
                    </a>
                  </li>
                </ul>
              </div>
            </td>
          </template>
        </vue-good-table>
      </div>
      <!-- END CIDR SUBS -->
      <!-- ZONES -->
      <div class="tab-pane fade active" id="zones-tab" role="tabpanel" aria-labelledby="zones-tab">
        <h3>{{$t('actions')}}</h3>
        <button @click="openCreateZone()" class="btn btn-primary btn-lg">{{$t('objects.add_zone')}}</button>

        <h3>{{$t('list')}}</h3>
        <div v-if="!view.zones.isLoaded" class="spinner spinner-lg"></div>
        <vue-good-table
          v-show="view.zones.isLoaded"
          :customRowsPerPageDropdown="[25,50,100]"
          :perPage="25"
          :columns="zonesColumns"
          :rows="zonesRows"
          :lineNumbers="false"
          :defaultSortBy="{field: 'name', type: 'asc'}"
          :globalSearch="true"
          :paginate="true"
          styleClass="table"
          :nextText="tableLangsTexts.nextText"
          :prevText="tableLangsTexts.prevText"
          :rowsPerPageText="tableLangsTexts.rowsPerPageText"
          :globalSearchPlaceholder="tableLangsTexts.globalSearchPlaceholder"
          :ofText="tableLangsTexts.ofText"
        >
          <template slot="table-row" slot-scope="props">
            <td class="fancy">
              <a @click="openEditZone(props.row)">
                <strong>{{ props.row.name}}</strong>
              </a>
            </td>
            <td class="fancy">
              <span class="pficon pficon-plugged"></span>
              {{props.row.Interface}}
            </td>
            <td class="fancy">
              <span class="pficon pficon-screen"></span>
              {{props.row.Network}}
            </td>
            <td class="fancy">{{ props.row.Description}}</td>
            <td>
              <button @click="openEditZone(props.row)" class="btn btn-default">
                <span class="fa fa-pencil span-right-margin"></span>
                {{$t('edit')}}
              </button>
              <div class="dropup pull-right dropdown-kebab-pf">
                <button
                  class="btn btn-link dropdown-toggle"
                  type="button"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="true"
                >
                  <span class="fa fa-ellipsis-v"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                  <li>
                    <a @click="openDeleteZone(props.row)">
                      <span class="fa fa-times span-right-margin"></span>
                      {{$t('delete')}}
                    </a>
                  </li>
                </ul>
              </div>
            </td>
          </template>
        </vue-good-table>
      </div>
      <!-- END ZONES -->
      <!-- TIME CONDITIONS -->
      <div
        class="tab-pane fade active"
        id="time-conditions-tab"
        role="tabpanel"
        aria-labelledby="time-conditions-tab"
      >
        <h3>{{$t('actions')}}</h3>
        <button
          @click="openCreateTimeCondition()"
          class="btn btn-primary btn-lg"
        >{{$t('objects.add_time_condition')}}</button>

        <h3>{{$t('list')}}</h3>
        <div v-if="!view.timeConditions.isLoaded" class="spinner spinner-lg"></div>
        <vue-good-table
          v-show="view.timeConditions.isLoaded"
          :customRowsPerPageDropdown="[25,50,100]"
          :perPage="25"
          :columns="timeConditionsColumns"
          :rows="timeConditionsRows"
          :lineNumbers="false"
          :defaultSortBy="{field: 'name', type: 'asc'}"
          :globalSearch="true"
          :paginate="true"
          styleClass="table"
          :nextText="tableLangsTexts.nextText"
          :prevText="tableLangsTexts.prevText"
          :rowsPerPageText="tableLangsTexts.rowsPerPageText"
          :globalSearchPlaceholder="tableLangsTexts.globalSearchPlaceholder"
          :ofText="tableLangsTexts.ofText"
        >
          <template slot="table-row" slot-scope="props">
            <td class="fancy">
              <a @click="openEditTimeCondition(props.row)">
                <strong>{{ props.row.name}}</strong>
              </a>
            </td>
            <td class="fancy">{{ props.row.Description}}</td>
            <td>
              <button @click="openEditTimeCondition(props.row)" class="btn btn-default">
                <span class="fa fa-pencil span-right-margin"></span>
                {{$t('edit')}}
              </button>
              <div class="dropup pull-right dropdown-kebab-pf">
                <button
                  class="btn btn-link dropdown-toggle"
                  type="button"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="true"
                >
                  <span class="fa fa-ellipsis-v"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                  <li>
                    <a @click="openDeleteTimeCondition(props.row)">
                      <span class="fa fa-times span-right-margin"></span>
                      {{$t('delete')}}
                    </a>
                  </li>
                </ul>
              </div>
            </td>
          </template>
        </vue-good-table>
      </div>
      <!-- END TIME CONDITIONS -->
      <!-- SERVICES -->
      <div
        class="tab-pane fade active"
        id="services-tab"
        role="tabpanel"
        aria-labelledby="services-tab"
      >
        <h3>{{$t('actions')}}</h3>
        <button
          @click="openCreateService()"
          class="btn btn-primary btn-lg"
        >{{$t('objects.add_service')}}</button>

        <h3>{{$t('list')}}</h3>
        <div v-if="!view.services.isLoaded" class="spinner spinner-lg"></div>
        <vue-good-table
          v-show="view.services.isLoaded"
          :customRowsPerPageDropdown="[25,50,100]"
          :perPage="25"
          :columns="servicesColumns"
          :rows="servicesRows"
          :lineNumbers="false"
          :defaultSortBy="{field: 'name', type: 'asc'}"
          :globalSearch="true"
          :paginate="true"
          styleClass="table"
          :nextText="tableLangsTexts.nextText"
          :prevText="tableLangsTexts.prevText"
          :rowsPerPageText="tableLangsTexts.rowsPerPageText"
          :globalSearchPlaceholder="tableLangsTexts.globalSearchPlaceholder"
          :ofText="tableLangsTexts.ofText"
        >
          <template slot="table-row" slot-scope="props">
            <td class="fancy">
              <a @click="openEditService(props.row)">
                <strong>{{ props.row.name}}</strong>
              </a>
            </td>
            <td class="fancy">{{ props.row.Protocol | uppercase}}</td>
            <td class="fancy">{{ props.row.Ports.join(', ')}}</td>
            <td class="fancy">{{ props.row.Description}}</td>
            <td>
              <button @click="openEditService(props.row)" class="btn btn-default">
                <span class="fa fa-pencil span-right-margin"></span>
                {{$t('edit')}}
              </button>
              <div class="dropup pull-right dropdown-kebab-pf">
                <button
                  class="btn btn-link dropdown-toggle"
                  type="button"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="true"
                >
                  <span class="fa fa-ellipsis-v"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                  <li>
                    <a @click="openDeleteService(props.row)">
                      <span class="fa fa-times span-right-margin"></span>
                      {{$t('delete')}}
                    </a>
                  </li>
                </ul>
              </div>
            </td>
          </template>
        </vue-good-table>
      </div>
      <!-- END SERVICES -->
    </div>

    <!-- CREATE MODALS -->
    <div class="modal" id="newHostModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{newHost.isEdit ? $t('objects.edit_host') + ' '+ newHost.name : $t('objects.add_host')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveHost(newHost)">
            <div class="modal-body">
              <div :class="['form-group', newHost.errors.name.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.name')}}</label>
                <div class="col-sm-9">
                  <input
                    :disabled="newHost.isEdit"
                    required
                    type="text"
                    v-model="newHost.name"
                    class="form-control"
                  />
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
                >{{$t('objects.ip_address')}}</label>
                <div class="col-sm-9">
                  <input required type="text" v-model="newHost.IpAddress" class="form-control" />
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
                >{{$t('objects.description')}}</label>
                <div class="col-sm-9">
                  <input type="text" v-model="newHost.Description" class="form-control" />
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
    <div class="modal" id="newHostGroupModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{newHostGroup.isEdit ? $t('objects.edit_host_group') + ' '+ newHostGroup.name : $t('objects.add_host_group')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveHostGroup(newHostGroup)">
            <div class="modal-body">
              <div :class="['form-group', newHostGroup.errors.name.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.name')}}</label>
                <div class="col-sm-9">
                  <input
                    :disabled="newHostGroup.isEdit"
                    required
                    type="text"
                    v-model="newHostGroup.name"
                    class="form-control"
                  />
                  <span
                    v-if="newHostGroup.errors.name.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newHostGroup.errors.name.message)}}</span>
                </div>
              </div>
              <div :class="['form-group', newHostGroup.errors.Members.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.members')}}</label>
                <div class="col-sm-9">
                  <select
                    @change="addHostToGroup(newHostGroup.hostToAdd)"
                    v-model="newHostGroup.hostToAdd"
                    class="combobox form-control"
                    :required="newHostGroup.Members.length == 0"
                  >
                    <option>-</option>
                    <option
                      :value="i.name"
                      v-for="(i, ki) in hostsRows"
                      v-bind:key="ki"
                    >{{i.name}} | {{i.IpAddress}}</option>
                  </select>
                  <span
                    v-if="newHostGroup.errors.Members.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newHostGroup.errors.Members.message)}}</span>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label" for="textInput-modal-markup"></label>
                <div class="col-sm-9">
                  <ul class="list-inline compact">
                    <li v-for="(i, ki) in newHostGroup.Members" v-bind:key="i" class="mg-bottom-5">
                      <span class="label label-info">
                        {{i}}
                        <a @click="removeHostToGroup(ki)" class="remove-item-inline">
                          <span class="fa fa-times"></span>
                        </a>
                      </span>
                    </li>
                  </ul>
                </div>
              </div>
              <div
                :class="['form-group', newHostGroup.errors.Description.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.description')}}</label>
                <div class="col-sm-9">
                  <input type="text" v-model="newHostGroup.Description" class="form-control" />
                  <span
                    v-if="newHostGroup.errors.Description.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newHostGroup.errors.Description.message)}}</span>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <div v-if="newHostGroup.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-primary" type="submit">{{$t('save')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal" id="newIPRangeModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{newIPRange.isEdit ? $t('objects.edit_ip_range') + ' '+ newIPRange.name : $t('objects.add_ip_range')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveIPRange(newIPRange)">
            <div class="modal-body">
              <div :class="['form-group', newIPRange.errors.name.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.name')}}</label>
                <div class="col-sm-9">
                  <input
                    :disabled="newIPRange.isEdit"
                    required
                    type="text"
                    v-model="newIPRange.name"
                    class="form-control"
                  />
                  <span
                    v-if="newIPRange.errors.name.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newIPRange.errors.name.message)}}</span>
                </div>
              </div>
              <div :class="['form-group', newIPRange.errors.Start.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.start_ip')}}</label>
                <div class="col-sm-9">
                  <input required type="text" v-model="newIPRange.Start" class="form-control" />
                  <span
                    v-if="newIPRange.errors.Start.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newIPRange.errors.Start.message)}}</span>
                </div>
              </div>
              <div :class="['form-group', newIPRange.errors.End.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.end_ip')}}</label>
                <div class="col-sm-9">
                  <input required type="text" v-model="newIPRange.End" class="form-control" />
                  <span
                    v-if="newIPRange.errors.End.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newIPRange.errors.End.message)}}</span>
                </div>
              </div>
              <div
                :class="['form-group', newIPRange.errors.Description.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.description')}}</label>
                <div class="col-sm-9">
                  <input type="text" v-model="newIPRange.Description" class="form-control" />
                  <span
                    v-if="newIPRange.errors.Description.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newIPRange.errors.Description.message)}}</span>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <div v-if="newIPRange.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-primary" type="submit">{{$t('save')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal" id="newCIDRSubModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{newCIDRSub.isEdit ? $t('objects.edit_cidr_sub') + ' '+ newCIDRSub.name : $t('objects.add_cidr_sub')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveCIDRSub(newCIDRSub)">
            <div class="modal-body">
              <div :class="['form-group', newCIDRSub.errors.name.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.name')}}</label>
                <div class="col-sm-9">
                  <input
                    :disabled="newCIDRSub.isEdit"
                    required
                    type="text"
                    v-model="newCIDRSub.name"
                    class="form-control"
                  />
                  <span
                    v-if="newCIDRSub.errors.name.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newCIDRSub.errors.name.message)}}</span>
                </div>
              </div>
              <div :class="['form-group', newCIDRSub.errors.Address.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.network')}}</label>
                <div class="col-sm-9">
                  <input required type="text" v-model="newCIDRSub.Address" class="form-control" />
                  <span
                    v-if="newCIDRSub.errors.Address.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newCIDRSub.errors.Address.message)}}</span>
                </div>
              </div>
              <div
                :class="['form-group', newCIDRSub.errors.Description.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.description')}}</label>
                <div class="col-sm-9">
                  <input type="text" v-model="newCIDRSub.Description" class="form-control" />
                  <span
                    v-if="newCIDRSub.errors.Description.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newCIDRSub.errors.Description.message)}}</span>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <div v-if="newCIDRSub.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-primary" type="submit">{{$t('save')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal" id="newZoneModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{newZone.isEdit ? $t('objects.edit_zone') + ' '+ newZone.name : $t('objects.add_zone')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveZone(newZone)">
            <div class="modal-body">
              <div :class="['form-group', newZone.errors.name.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.name')}}</label>
                <div class="col-sm-9">
                  <input
                    :disabled="newZone.isEdit"
                    required
                    type="text"
                    v-model="newZone.name"
                    class="form-control"
                  />
                  <span
                    v-if="newZone.errors.name.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newZone.errors.name.message)}}</span>
                </div>
              </div>
              <div :class="['form-group', newZone.errors.Network.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.network')}}</label>
                <div class="col-sm-9">
                  <input required type="text" v-model="newZone.Network" class="form-control" />
                  <span
                    v-if="newZone.errors.Network.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newZone.errors.Network.message)}}</span>
                </div>
              </div>
              <div :class="['form-group', newZone.errors.Interface.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.interface')}}</label>
                <div class="col-sm-9">
                  <select required type="text" v-model="newZone.Interface" class="form-control">
                    <option v-for="(i,k) in interfaces" v-bind:key="k" :value="i">{{i}}</option>
                  </select>
                  <span
                    v-if="newZone.errors.Interface.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newZone.errors.Interface.message)}}</span>
                </div>
              </div>
              <div :class="['form-group', newZone.errors.Description.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.description')}}</label>
                <div class="col-sm-9">
                  <input type="text" v-model="newZone.Description" class="form-control" />
                  <span
                    v-if="newZone.errors.Description.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newZone.errors.Description.message)}}</span>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <div v-if="newZone.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-primary" type="submit">{{$t('save')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div
      class="modal"
      id="newTimeConditionModal"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{newTimeCondition.isEdit ? $t('objects.edit_time_condition') + ' '+ newTimeCondition.name : $t('objects.add_time_condition')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveTimeCondition(newTimeCondition)">
            <div class="modal-body">
              <div
                :class="['form-group', newTimeCondition.errors.name.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.name')}}</label>
                <div class="col-sm-9">
                  <input
                    :disabled="newTimeCondition.isEdit"
                    required
                    type="text"
                    v-model="newTimeCondition.name"
                    class="form-control"
                  />
                  <span
                    v-if="newTimeCondition.errors.name.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newTimeCondition.errors.name.message)}}</span>
                </div>
              </div>
              <div
                :class="['form-group', newTimeCondition.errors.TimeStart.hasError || newTimeCondition.errors.TimeStop.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.range')}}</label>
                <div class="col-sm-4">
                  <label>{{$t('objects.time_start')}}</label>
                  <input
                    required
                    class="col-sm-3 form-control"
                    type="text"
                    placeholder="00:15"
                    v-model="newTimeCondition.TimeStart"
                  />
                  <span v-if="newTimeCondition.errors.TimeStart.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newTimeCondition.errors.TimeStart.message)}}
                  </span>
                </div>
                <div class="col-sm-4">
                  <label>{{$t('objects.time_stop')}}</label>
                  <input
                    required
                    class="col-sm-3 form-control"
                    type="text"
                    placeholder="23:30"
                    v-model="newTimeCondition.TimeStop"
                  />
                  <span v-if="newTimeCondition.errors.TimeStop.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+newTimeCondition.errors.TimeStop.message)}}
                  </span>
                </div>
              </div>
              <div
                :class="['form-group', newTimeCondition.errors.WeekDays.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.weekdays')}}</label>
                <div class="col-sm-9">
                  <select
                    @change="addDayToWeekdays(newTimeCondition.dayToAdd)"
                    v-model="newTimeCondition.dayToAdd"
                    class="combobox form-control"
                    :required="newTimeCondition.WeekDays.length == 0"
                  >
                    <option>-</option>
                    <option :value="d" v-for="(d,k) in weekdays" v-bind:key="k">{{$t(d)}}</option>
                  </select>
                  <span
                    v-if="newTimeCondition.errors.WeekDays.hasError"
                    class="help-block"
                  >{{newTimeCondition.errors.WeekDays.message}}</span>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label" for="textInput-modal-markup"></label>
                <div class="col-sm-9">
                  <ul class="list-inline compact">
                    <li
                      v-for="(i, ki) in newTimeCondition.WeekDays"
                      v-bind:key="i"
                      class="mg-bottom-5"
                    >
                      <span class="label label-info">
                        {{$t(i)}}
                        <a @click="removeDayToWeekdays(ki)" class="remove-item-inline">
                          <span class="fa fa-times"></span>
                        </a>
                      </span>
                    </li>
                  </ul>
                </div>
              </div>
              <div
                :class="['form-group', newTimeCondition.errors.Description.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.description')}}</label>
                <div class="col-sm-9">
                  <input type="text" v-model="newTimeCondition.Description" class="form-control" />
                  <span
                    v-if="newTimeCondition.errors.Description.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newTimeCondition.errors.Description.message)}}</span>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <div v-if="newTimeCondition.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-primary" type="submit">{{$t('save')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal" id="newServiceModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{newService.isEdit ? $t('objects.edit_service') + ' '+ newService.name : $t('objects.add_service')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveService(newService)">
            <div class="modal-body">
              <div :class="['form-group', newService.errors.name.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.name')}}</label>
                <div class="col-sm-9">
                  <input
                    :disabled="newService.isEdit"
                    required
                    type="text"
                    v-model="newService.name"
                    class="form-control"
                  />
                  <span
                    v-if="newService.errors.name.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newService.errors.name.message)}}</span>
                </div>
              </div>
              <div :class="['form-group', newService.errors.Protocol.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.protocol')}}</label>
                <div class="col-sm-9">
                  <select required type="text" v-model="newService.Protocol" class="form-control">
                    <option
                      v-for="(p,k) in protocols"
                      v-bind:key="k"
                      :value="p"
                    >{{$t('protocols.'+p)}}</option>
                  </select>
                  <span
                    v-if="newService.errors.Protocol.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newService.errors.Protocol.message)}}</span>
                </div>
              </div>
              <div :class="['form-group', newService.errors.Ports.hasError ? 'has-error' : '']">
                <label class="col-sm-3 control-label" for="textInput-modal-markup">
                  {{$t('objects.ports')}}
                  <doc-info
                    :placement="'top'"
                    :title="$t('objects.ports')"
                    :chapter="'objects_ports'"
                    :inline="true"
                  ></doc-info>
                </label>
                <div class="col-sm-9">
                  <input required type="text" v-model="newService.Ports" class="form-control" />
                  <span
                    v-if="newService.errors.Ports.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newService.errors.Ports.message)}}</span>
                </div>
              </div>
              <div
                :class="['form-group', newService.errors.Description.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('objects.description')}}</label>
                <div class="col-sm-9">
                  <input type="text" v-model="newService.Description" class="form-control" />
                  <span
                    v-if="newService.errors.Description.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newService.errors.Description.message)}}</span>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <div v-if="newService.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-primary" type="submit">{{$t('save')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- END CREATE MODALS -->
    <!-- DELETE MODALS -->
    <div class="modal" id="deleteHostModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('objects.delete_host')}} {{currentHost.name}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deleteHost(currentHost)">
            <div class="modal-body">
              <div v-show="currentHost.isError" class="alert alert-warning alert-dismissable">
                <span class="pficon pficon-warning-triangle-o"></span>
                <strong>{{$t('warning')}}.</strong>
                {{$t('validation.'+currentHost.isError)}}.
              </div>
              <div class="form-group">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('are_you_sure')}}?</label>
              </div>
            </div>
            <div class="modal-footer">
              <div v-if="currentHost.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-danger" type="submit">{{$t('delete')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal" id="deleteHostGroupModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('objects.delete_host_group')}} {{currentHostGroup.name}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deleteHostGroup(currentHostGroup)">
            <div class="modal-body">
              <div v-show="currentHostGroup.isError" class="alert alert-warning alert-dismissable">
                <span class="pficon pficon-warning-triangle-o"></span>
                <strong>{{$t('warning')}}.</strong>
                {{$t('validation.'+currentHostGroup.isError)}}.
              </div>
              <div class="form-group">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('are_you_sure')}}?</label>
              </div>
            </div>
            <div class="modal-footer">
              <div v-if="currentHostGroup.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-danger" type="submit">{{$t('delete')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal" id="deleteIPRangeModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('objects.delete_ip_range')}} {{currentIPRange.name}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deleteIPRange(currentIPRange)">
            <div class="modal-body">
              <div v-show="currentIPRange.isError" class="alert alert-warning alert-dismissable">
                <span class="pficon pficon-warning-triangle-o"></span>
                <strong>{{$t('warning')}}.</strong>
                {{$t('validation.'+currentIPRange.isError)}}.
              </div>
              <div class="form-group">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('are_you_sure')}}?</label>
              </div>
            </div>
            <div class="modal-footer">
              <div v-if="currentIPRange.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-danger" type="submit">{{$t('delete')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal" id="deleteCIDRSubModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('objects.delete_cidr_sub')}} {{currentCIDRSub.name}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deleteCIDRSub(currentCIDRSub)">
            <div class="modal-body">
              <div v-show="currentCIDRSub.isError" class="alert alert-warning alert-dismissable">
                <span class="pficon pficon-warning-triangle-o"></span>
                <strong>{{$t('warning')}}.</strong>
                {{$t('validation.'+currentCIDRSub.isError)}}.
              </div>
              <div class="form-group">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('are_you_sure')}}?</label>
              </div>
            </div>
            <div class="modal-footer">
              <div v-if="currentCIDRSub.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-danger" type="submit">{{$t('delete')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal" id="deleteZoneModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('objects.delete_zone')}} {{currentZone.name}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deleteZone(currentZone)">
            <div class="modal-body">
              <div v-show="currentZone.isError" class="alert alert-warning alert-dismissable">
                <span class="pficon pficon-warning-triangle-o"></span>
                <strong>{{$t('warning')}}.</strong>
                {{$t('validation.'+currentZone.isError)}}.
              </div>
              <div class="form-group">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('are_you_sure')}}?</label>
              </div>
            </div>
            <div class="modal-footer">
              <div v-if="currentZone.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-danger" type="submit">{{$t('delete')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div
      class="modal"
      id="deleteTimeConditionModal"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{$t('objects.delete_time_condition')}} {{currentTimeCondition.name}}</h4>
          </div>
          <form
            class="form-horizontal"
            v-on:submit.prevent="deleteTimeCondition(currentTimeCondition)"
          >
            <div class="modal-body">
              <div
                v-show="currentTimeCondition.isError"
                class="alert alert-warning alert-dismissable"
              >
                <span class="pficon pficon-warning-triangle-o"></span>
                <strong>{{$t('warning')}}.</strong>
                {{$t(currentTimeCondition.isError)}}.
              </div>
              <div class="form-group">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('are_you_sure')}}?</label>
              </div>
            </div>
            <div class="modal-footer">
              <div
                v-if="currentTimeCondition.isLoading"
                class="spinner spinner-sm form-spinner-loader"
              ></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-danger" type="submit">{{$t('delete')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal" id="deleteServiceModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('objects.delete_service')}} {{currentService.name}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deleteService(currentService)">
            <div class="modal-body">
              <div v-show="currentService.isError" class="alert alert-warning alert-dismissable">
                <span class="pficon pficon-warning-triangle-o"></span>
                <strong>{{$t('warning')}}.</strong>
                {{$t('validation.'+currentService.isError)}}.
              </div>
              <div class="form-group">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('are_you_sure')}}?</label>
              </div>
            </div>
            <div class="modal-footer">
              <div v-if="currentService.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-danger" type="submit">{{$t('delete')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- DELETE END MODALS -->
  </div>
</template>

<script>
export default {
  name: "DNS",
  beforeRouteLeave(to, from, next) {
    $(".modal").modal("hide");
    next();
  },
  mounted() {
    this.getHosts();
    this.getHostGroups();
    this.getIPRanges();
    this.getCIDRSubs();
    this.getZones();
    this.getTimeConditions();
    this.getServices();
    this.getProtocols();
    this.getInterfaces();
    $("#hosts-tab-parent").click();

    var context = this;
    context.$parent.$on("changes-applied", function() {
      context.getHosts();
      context.getHostGroups();
      context.getIPRanges();
      context.getCIDRSubs();
      context.getZones();
      context.getTimeConditions();
      context.getServices();
      context.getProtocols();
      context.getInterfaces();
    });
  },
  data() {
    return {
      view: {
        hosts: {
          isLoaded: false
        },
        hostGroups: {
          isLoaded: false
        },
        ipRanges: {
          isLoaded: false
        },
        cidrSubs: {
          isLoaded: false
        },
        zones: {
          isLoaded: false
        },
        timeConditions: {
          isLoaded: false
        },
        services: {
          isLoaded: false
        }
      },
      tableLangsTexts: this.tableLangs(),
      hostsColumns: [
        {
          label: this.$i18n.t("objects.name"),
          field: "name",
          filterable: true
        },
        {
          label: this.$i18n.t("objects.ip_address"),
          field: "IpAddress",
          filterable: true,
          sortFn: function(a, b, col, rowX, rowY) {
            a = a.split(".");
            b = b.split(".");
            for (var i = 0; i < a.length; i++) {
              if ((a[i] = parseInt(a[i])) < (b[i] = parseInt(b[i]))) return -1;
              else if (a[i] > b[i]) return 1;
            }
          }
        },
        {
          label: this.$i18n.t("objects.description"),
          field: "Description",
          filterable: true
        },
        {
          label: this.$i18n.t("action"),
          field: "",
          filterable: true,
          sortable: false
        }
      ],
      hostGroupsColumns: [
        {
          label: this.$i18n.t("objects.name"),
          field: "name",
          filterable: true
        },
        {
          label: this.$i18n.t("objects.description"),
          field: "Description",
          filterable: true
        },
        {
          label: this.$i18n.t("action"),
          field: "",
          filterable: true,
          sortable: false
        }
      ],
      ipRangesColumns: [
        {
          label: this.$i18n.t("objects.name"),
          field: "name",
          filterable: true
        },
        {
          label: this.$i18n.t("objects.range"),
          field: "range",
          filterable: true
        },
        {
          label: this.$i18n.t("objects.description"),
          field: "Description",
          filterable: true
        },
        {
          label: this.$i18n.t("action"),
          field: "",
          filterable: true,
          sortable: false
        }
      ],
      cidrSubsColumns: [
        {
          label: this.$i18n.t("objects.name"),
          field: "name",
          filterable: true
        },
        {
          label: this.$i18n.t("objects.network"),
          field: "Address",
          filterable: true
        },
        {
          label: this.$i18n.t("objects.description"),
          field: "Description",
          filterable: true
        },
        {
          label: this.$i18n.t("action"),
          field: "",
          filterable: true,
          sortable: false
        }
      ],
      zonesColumns: [
        {
          label: this.$i18n.t("objects.name"),
          field: "name",
          filterable: true
        },
        {
          label: this.$i18n.t("objects.interface"),
          field: "Network",
          filterable: true
        },
        {
          label: this.$i18n.t("objects.network"),
          field: "Description",
          filterable: true
        },
        {
          label: this.$i18n.t("objects.description"),
          field: "Description",
          filterable: true
        },
        {
          label: this.$i18n.t("action"),
          field: "",
          filterable: true,
          sortable: false
        }
      ],
      timeConditionsColumns: [
        {
          label: this.$i18n.t("objects.name"),
          field: "name",
          filterable: true
        },
        {
          label: this.$i18n.t("objects.description"),
          field: "Description",
          filterable: true
        },
        {
          label: this.$i18n.t("action"),
          field: "",
          filterable: true,
          sortable: false
        }
      ],
      servicesColumns: [
        {
          label: this.$i18n.t("objects.name"),
          field: "name",
          filterable: true
        },
        {
          label: this.$i18n.t("objects.protocol"),
          field: "Description",
          filterable: true
        },
        {
          label: this.$i18n.t("objects.ports"),
          field: "Description",
          filterable: true
        },
        {
          label: this.$i18n.t("objects.description"),
          field: "Description",
          filterable: true
        },
        {
          label: this.$i18n.t("action"),
          field: "",
          filterable: true,
          sortable: false
        }
      ],
      hostsRows: [],
      hostGroupsRows: [],
      ipRangesRows: [],
      cidrSubsRows: [],
      zonesRows: [],
      timeConditionsRows: [],
      servicesRows: [],
      currentHost: {},
      newHost: this.initHost(),
      currentHostGroup: {},
      newHostGroup: this.initHostGroup(),
      currentIPRange: {},
      newIPRange: this.initIPRange(),
      currentCIDRSub: {},
      newCIDRSub: this.initCIDRSub(),
      currentZone: {},
      newZone: this.initZone(),
      currentTimeCondition: {},
      newTimeCondition: this.initTimeCondition(),
      currentService: {},
      newService: this.initService(),
      protocols: [],
      interfaces: [],
      weekdays: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"]
    };
  },
  methods: {
    hostAlreadyAdded(bind) {
      return this.newHostGroup.Members.indexOf(bind) > -1;
    },
    addHostToGroup(host) {
      if (host.length > 0 && host != "-") {
        if (!this.hostAlreadyAdded(host)) {
          this.newHostGroup.Members.push(host);
        }
      }
    },
    removeHostToGroup(index) {
      this.newHostGroup.Members.splice(index, 1);
    },
    dayAlreadyAdded(bind) {
      return this.newTimeCondition.WeekDays.indexOf(bind) > -1;
    },
    addDayToWeekdays(day) {
      if (day.length > 0 && day != "-") {
        if (!this.dayAlreadyAdded(day)) {
          this.newTimeCondition.WeekDays.push(day);
        }
      }
    },
    removeDayToWeekdays(index) {
      this.newTimeCondition.WeekDays.splice(index, 1);
    },
    initHost() {
      return {
        isLoading: false,
        isEdit: false,
        isError: false,
        name: "",
        IpAddress: "",
        Description: "",
        errors: this.initHostErrors()
      };
    },
    initHostGroup() {
      return {
        isLoading: false,
        isEdit: false,
        isError: false,
        name: "",
        Description: "",
        Members: [],
        hostToAdd: {},
        errors: this.initHostGroupErrors()
      };
    },
    initIPRange() {
      return {
        isLoading: false,
        isEdit: false,
        isError: false,
        name: "",
        Start: "",
        End: "",
        Description: "",
        errors: this.initIPRangeErrors()
      };
    },
    initCIDRSub() {
      return {
        isLoading: false,
        isEdit: false,
        isError: false,
        name: "",
        Address: "",
        Description: "",
        errors: this.initCIDRSubErrors()
      };
    },
    initZone() {
      return {
        isLoading: false,
        isEdit: false,
        isError: false,
        name: "",
        Interface: "",
        Network: "",
        Description: "",
        errors: this.initZoneErrors()
      };
    },
    initTimeCondition() {
      return {
        isLoading: false,
        isEdit: false,
        isError: false,
        name: "",
        TimeStart: "",
        TimeStop: "",
        WeekDays: [],
        Description: "",
        dayToAdd: {},
        errors: this.initTimeConditionErrors()
      };
    },
    initService() {
      return {
        isLoading: false,
        isEdit: false,
        isError: false,
        name: "",
        Protocol: "",
        Ports: "",
        Description: "",
        errors: this.initServiceErrors()
      };
    },
    initHostErrors() {
      return {
        name: {
          hasError: false,
          message: ""
        },
        IpAddress: {
          hasError: false,
          message: ""
        },
        Description: {
          hasError: false,
          message: ""
        }
      };
    },
    initHostGroupErrors() {
      return {
        name: {
          hasError: false,
          message: ""
        },
        Members: {
          hasError: false,
          message: ""
        },
        Description: {
          hasError: false,
          message: ""
        }
      };
    },
    initIPRangeErrors() {
      return {
        name: {
          hasError: false,
          message: ""
        },
        Start: {
          hasError: false,
          message: ""
        },
        End: {
          hasError: false,
          message: ""
        },
        Description: {
          hasError: false,
          message: ""
        }
      };
    },
    initCIDRSubErrors() {
      return {
        name: {
          hasError: false,
          message: ""
        },
        Address: {
          hasError: false,
          message: ""
        },
        Description: {
          hasError: false,
          message: ""
        }
      };
    },
    initZoneErrors() {
      return {
        name: {
          hasError: false,
          message: ""
        },
        Interface: {
          hasError: false,
          message: ""
        },
        Network: {
          hasError: false,
          message: ""
        },
        Description: {
          hasError: false,
          message: ""
        }
      };
    },
    initTimeConditionErrors() {
      return {
        name: {
          hasError: false,
          message: ""
        },
        TimeStart: {
          hasError: false,
          message: ""
        },
        TimeStop: {
          hasError: false,
          message: ""
        },
        WeekDays: {
          hasError: false,
          message: ""
        },
        Description: {
          hasError: false,
          message: ""
        }
      };
    },
    initServiceErrors() {
      return {
        name: {
          hasError: false,
          message: ""
        },
        Protocol: {
          hasError: false,
          message: ""
        },
        Ports: {
          hasError: false,
          message: ""
        },
        Description: {
          hasError: false,
          message: ""
        }
      };
    },
    getInterfaces() {
      var context = this;

      nethserver.exec(
        ["nethserver-firewall-base/objects/read"],
        {
          action: "interfaces"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.interfaces = success["interfaces"].sort();

          context.$forceUpdate();
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
          } catch (e) {
            console.error(e);
          }
          context.protocols = success["protocols"].sort();

          context.$forceUpdate();
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getHosts() {
      var context = this;

      context.view.hosts.isLoaded = false;
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
          context.view.hosts.isLoaded = true;
          context.hostsRows = success["hosts"];

          context.$forceUpdate();
          context.$parent.getFirewallStatus();
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getHostGroups() {
      var context = this;

      context.view.hostGroups.isLoaded = false;
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
          context.view.hostGroups.isLoaded = true;
          context.hostGroupsRows = success["host-groups"];

          context.$forceUpdate();
          context.$parent.getFirewallStatus();
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getIPRanges() {
      var context = this;

      context.view.ipRanges.isLoaded = false;
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
          context.view.ipRanges.isLoaded = true;
          context.ipRangesRows = success["ip-ranges"];

          context.$forceUpdate();
          context.$parent.getFirewallStatus();
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getCIDRSubs() {
      var context = this;

      context.view.cidrSubs.isLoaded = false;
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
          context.view.cidrSubs.isLoaded = true;
          context.cidrSubsRows = success["cidr-subs"];

          context.$forceUpdate();
          context.$parent.getFirewallStatus();
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getZones() {
      var context = this;

      context.view.zones.isLoaded = false;
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
          context.view.zones.isLoaded = true;
          context.zonesRows = success["zones"];

          context.$forceUpdate();
          context.$parent.getFirewallStatus();
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getTimeConditions() {
      var context = this;

      context.view.timeConditions.isLoaded = false;
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
          context.view.timeConditions.isLoaded = true;
          context.timeConditionsRows = success["time-conditions"];

          context.$forceUpdate();
          context.$parent.getFirewallStatus();
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getServices() {
      var context = this;

      context.view.services.isLoaded = false;
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
          context.view.services.isLoaded = true;
          context.servicesRows = success["services"];

          context.$forceUpdate();
          context.$parent.getFirewallStatus();
        },
        function(error) {
          console.error(error);
        }
      );
    },
    openCreateHost() {
      this.newHost = this.initHost();
      $("#newHostModal").modal("show");
    },
    openEditHost(host) {
      this.newHost = Object.assign({}, host);
      this.newHost.errors = this.initHostErrors();
      this.newHost.isLoading = false;
      this.newHost.isEdit = true;
      $("#newHostModal").modal("show");
    },
    saveHost(host) {
      var context = this;

      var hostObj = Object.assign({}, host);
      delete hostObj.isLoading;
      delete hostObj.isEdit;
      delete hostObj.isError;
      delete hostObj.errors;
      hostObj.action = host.isEdit ? "update-host" : "create-host";

      context.newHost.isLoading = true;
      nethserver.exec(
        ["nethserver-firewall-base/objects/validate"],
        hostObj,
        null,
        function(success) {
          context.newHost.isLoading = false;
          $("#newHostModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "objects.host_" +
              (context.newHost.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "objects.host_" +
              (context.newHost.isEdit ? "updated" : "created") +
              "_error"
          );

          // update values
          if (host.isEdit) {
            nethserver.exec(
              ["nethserver-firewall-base/objects/update"],
              hostObj,
              function(stream) {
                console.info("hosts", stream);
              },
              function(success) {
                // get hosts
                context.getHosts();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          } else {
            nethserver.exec(
              ["nethserver-firewall-base/objects/create"],
              hostObj,
              function(stream) {
                console.info("hosts", stream);
              },
              function(success) {
                // get hosts
                context.getHosts();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          }
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
        }
      );
    },
    openDeleteHost(host) {
      this.currentHost = Object.assign({}, host);
      $("#deleteHostModal").modal("show");
    },
    deleteHost(host) {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "objects.host_deleted_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "objects.host_deleted_error"
      );

      nethserver.exec(
        ["nethserver-firewall-base/objects/validate"],
        {
          name: host.name,
          action: "delete-host"
        },
        null,
        function(success) {
          $("#deleteHostModal").modal("hide");
          nethserver.exec(
            ["nethserver-firewall-base/objects/delete"],
            {
              name: host.name,
              action: "delete-host"
            },
            function(stream) {
              console.info("hosts", stream);
            },
            function(success) {
              // get hosts
              context.getHosts();
            },
            function(error, data) {
              console.error(error, data);
            }
          );
        },
        function(error, data) {
          console.error(error, data);
          try {
            var errorData = JSON.parse(data);
            host.isError = errorData.attributes[0].error;
            context.$forceUpdate();
          } catch (e) {
            console.error(e);
          }
        }
      );
    },

    openCreateHostGroup() {
      this.newHostGroup = this.initHostGroup();
      $("#newHostGroupModal").modal("show");
    },
    openEditHostGroup(hostGroup) {
      this.newHostGroup = Object.assign({}, hostGroup);
      this.newHostGroup.errors = this.initHostGroupErrors();
      this.newHostGroup.isLoading = false;
      this.newHostGroup.isEdit = true;
      $("#newHostGroupModal").modal("show");
    },
    saveHostGroup(hostGroup) {
      var context = this;

      var hostGroupObj = Object.assign({}, hostGroup);
      delete hostGroupObj.isLoading;
      delete hostGroupObj.isEdit;
      delete hostGroupObj.isError;
      delete hostGroupObj.errors;
      hostGroupObj.action = hostGroup.isEdit
        ? "update-host-group"
        : "create-host-group";

      context.newHostGroup.isLoading = true;
      nethserver.exec(
        ["nethserver-firewall-base/objects/validate"],
        hostGroupObj,
        null,
        function(success) {
          context.newHostGroup.isLoading = false;
          $("#newHostGroupModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "objects.host_group_" +
              (context.newHostGroup.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "objects.host_group_" +
              (context.newHostGroup.isEdit ? "updated" : "created") +
              "_error"
          );

          // update values
          if (hostGroup.isEdit) {
            nethserver.exec(
              ["nethserver-firewall-base/objects/update"],
              hostGroupObj,
              function(stream) {
                console.info("hostGroups", stream);
              },
              function(success) {
                // get hostGroups
                context.getHostGroups();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          } else {
            nethserver.exec(
              ["nethserver-firewall-base/objects/create"],
              hostGroupObj,
              function(stream) {
                console.info("hostGroups", stream);
              },
              function(success) {
                // get hostGroups
                context.getHostGroups();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          }
        },
        function(error, data) {
          var errorData = {};
          context.newHostGroup.isLoading = false;
          context.newHostGroup.errors = context.initHostGroupErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              context.newHostGroup.errors[attr.parameter].hasError = true;
              context.newHostGroup.errors[attr.parameter].message = attr.error;
            }

            context.$forceUpdate();
          } catch (e) {
            console.error(e);
          }
        }
      );
    },
    openDeleteHostGroup(hostGroup) {
      this.currentHostGroup = Object.assign({}, hostGroup);
      $("#deleteHostGroupModal").modal("show");
    },
    deleteHostGroup(hostGroup) {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "objects.host_group_deleted_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "objects.host_group_deleted_error"
      );

      nethserver.exec(
        ["nethserver-firewall-base/objects/validate"],
        {
          name: hostGroup.name,
          action: "delete-host-group"
        },
        null,
        function(success) {
          $("#deleteHostGroupModal").modal("hide");
          nethserver.exec(
            ["nethserver-firewall-base/objects/delete"],
            {
              name: hostGroup.name,
              action: "delete-host-group"
            },
            function(stream) {
              console.info("hostGroups", stream);
            },
            function(success) {
              // get hostGroups
              context.getHostGroups();
            },
            function(error, data) {
              console.error(error, data);
            }
          );
        },
        function(error, data) {
          console.error(error, data);
          try {
            var errorData = JSON.parse(data);
            hostGroup.isError = errorData.attributes[0].error;
            context.$forceUpdate();
          } catch (e) {
            console.error(e);
          }
        }
      );
    },

    openCreateIPRange() {
      this.newIPRange = this.initIPRange();
      $("#newIPRangeModal").modal("show");
    },
    openEditIPRange(ipRange) {
      this.newIPRange = Object.assign({}, ipRange);
      this.newIPRange.errors = this.initIPRangeErrors();
      this.newIPRange.isLoading = false;
      this.newIPRange.isEdit = true;
      $("#newIPRangeModal").modal("show");
    },
    saveIPRange(ipRange) {
      var context = this;

      var ipRangeObj = Object.assign({}, ipRange);
      delete ipRangeObj.isLoading;
      delete ipRangeObj.isEdit;
      delete ipRangeObj.isError;
      delete ipRangeObj.errors;
      ipRangeObj.action = ipRange.isEdit
        ? "update-ip-range"
        : "create-ip-range";

      context.newIPRange.isLoading = true;
      nethserver.exec(
        ["nethserver-firewall-base/objects/validate"],
        ipRangeObj,
        null,
        function(success) {
          context.newIPRange.isLoading = false;
          $("#newIPRangeModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "objects.ip_range_" +
              (context.newIPRange.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "objects.ip_range_" +
              (context.newIPRange.isEdit ? "updated" : "created") +
              "_error"
          );

          // update values
          if (ipRange.isEdit) {
            nethserver.exec(
              ["nethserver-firewall-base/objects/update"],
              ipRangeObj,
              function(stream) {
                console.info("ipRanges", stream);
              },
              function(success) {
                // get ipRanges
                context.getIPRanges();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          } else {
            nethserver.exec(
              ["nethserver-firewall-base/objects/create"],
              ipRangeObj,
              function(stream) {
                console.info("ipRanges", stream);
              },
              function(success) {
                // get ipRanges
                context.getIPRanges();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          }
        },
        function(error, data) {
          var errorData = {};
          context.newIPRange.isLoading = false;
          context.newIPRange.errors = context.initIPRangeErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              context.newIPRange.errors[attr.parameter].hasError = true;
              context.newIPRange.errors[attr.parameter].message = attr.error;
            }
          } catch (e) {
            console.error(e);
          }
        }
      );
    },
    openDeleteIPRange(ipRange) {
      this.currentIPRange = Object.assign({}, ipRange);
      $("#deleteIPRangeModal").modal("show");
    },
    deleteIPRange(ipRange) {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "objects.ip_range_deleted_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "objects.ip_range_deleted_error"
      );

      nethserver.exec(
        ["nethserver-firewall-base/objects/validate"],
        {
          name: ipRange.name,
          action: "delete-ip-range"
        },
        null,
        function(success) {
          $("#deleteIPRangeModal").modal("hide");
          nethserver.exec(
            ["nethserver-firewall-base/objects/delete"],
            {
              name: ipRange.name,
              action: "delete-ip-range"
            },
            function(stream) {
              console.info("ipRanges", stream);
            },
            function(success) {
              // get ipRanges
              context.getIPRanges();
            },
            function(error, data) {
              console.error(error, data);
            }
          );
        },
        function(error, data) {
          console.error(error, data);
          try {
            var errorData = JSON.parse(data);
            ipRange.isError = errorData.attributes[0].error;
            context.$forceUpdate();
          } catch (e) {
            console.error(e);
          }
        }
      );
    },

    openCreateCIDRSub() {
      this.newCIDRSub = this.initCIDRSub();
      $("#newCIDRSubModal").modal("show");
    },
    openEditCIDRSub(cidrSub) {
      this.newCIDRSub = Object.assign({}, cidrSub);
      this.newCIDRSub.errors = this.initCIDRSubErrors();
      this.newCIDRSub.isLoading = false;
      this.newCIDRSub.isEdit = true;
      $("#newCIDRSubModal").modal("show");
    },
    saveCIDRSub(cidrSub) {
      var context = this;

      var cidrSubObj = Object.assign({}, cidrSub);
      delete cidrSubObj.isLoading;
      delete cidrSubObj.isEdit;
      delete cidrSubObj.isError;
      delete cidrSubObj.errors;
      cidrSubObj.action = cidrSub.isEdit
        ? "update-cidr-sub"
        : "create-cidr-sub";

      context.newCIDRSub.isLoading = true;
      nethserver.exec(
        ["nethserver-firewall-base/objects/validate"],
        cidrSubObj,
        null,
        function(success) {
          context.newCIDRSub.isLoading = false;
          $("#newCIDRSubModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "objects.cidr_sub_" +
              (context.newCIDRSub.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "objects.cidr_sub_" +
              (context.newCIDRSub.isEdit ? "updated" : "created") +
              "_error"
          );

          // update values
          if (cidrSub.isEdit) {
            nethserver.exec(
              ["nethserver-firewall-base/objects/update"],
              cidrSubObj,
              function(stream) {
                console.info("cidrSubs", stream);
              },
              function(success) {
                // get cidrSubs
                context.getCIDRSubs();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          } else {
            nethserver.exec(
              ["nethserver-firewall-base/objects/create"],
              cidrSubObj,
              function(stream) {
                console.info("cidrSubs", stream);
              },
              function(success) {
                // get cidrSubs
                context.getCIDRSubs();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          }
        },
        function(error, data) {
          var errorData = {};
          context.newCIDRSub.isLoading = false;
          context.newCIDRSub.errors = context.initCIDRSubErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              context.newCIDRSub.errors[attr.parameter].hasError = true;
              context.newCIDRSub.errors[attr.parameter].message = attr.error;
            }
          } catch (e) {
            console.error(e);
          }
        }
      );
    },
    openDeleteCIDRSub(cidrSub) {
      this.currentCIDRSub = Object.assign({}, cidrSub);
      $("#deleteCIDRSubModal").modal("show");
    },
    deleteCIDRSub(cidrSub) {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "objects.cidr_sub_deleted_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "objects.cidr_sub_deleted_error"
      );

      nethserver.exec(
        ["nethserver-firewall-base/objects/validate"],
        {
          name: cidrSub.name,
          action: "delete-cidr-sub"
        },
        null,
        function(success) {
          $("#deleteCIDRSubModal").modal("hide");
          nethserver.exec(
            ["nethserver-firewall-base/objects/delete"],
            {
              name: cidrSub.name,
              action: "delete-cidr-sub"
            },
            function(stream) {
              console.info("cidrSubs", stream);
            },
            function(success) {
              // get cidrSubs
              context.getCIDRSubs();
            },
            function(error, data) {
              console.error(error, data);
            }
          );
        },
        function(error, data) {
          console.error(error, data);
          try {
            var errorData = JSON.parse(data);
            cidrSub.isError = errorData.attributes[0].error;
            context.$forceUpdate();
          } catch (e) {
            console.error(e);
          }
        }
      );
    },

    openCreateZone() {
      this.newZone = this.initZone();
      $("#newZoneModal").modal("show");
    },
    openEditZone(zone) {
      this.newZone = Object.assign({}, zone);
      this.newZone.errors = this.initZoneErrors();
      this.newZone.isLoading = false;
      this.newZone.isEdit = true;
      $("#newZoneModal").modal("show");
    },
    saveZone(zone) {
      var context = this;

      var zoneObj = Object.assign({}, zone);
      delete zoneObj.isLoading;
      delete zoneObj.isEdit;
      delete zoneObj.isError;
      delete zoneObj.errors;
      zoneObj.action = zone.isEdit ? "update-zone" : "create-zone";

      context.newZone.isLoading = true;
      nethserver.exec(
        ["nethserver-firewall-base/objects/validate"],
        zoneObj,
        null,
        function(success) {
          context.newZone.isLoading = false;
          $("#newZoneModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "objects.zone_" +
              (context.newZone.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "objects.zone_" +
              (context.newZone.isEdit ? "updated" : "created") +
              "_error"
          );

          // update values
          if (zone.isEdit) {
            nethserver.exec(
              ["nethserver-firewall-base/objects/update"],
              zoneObj,
              function(stream) {
                console.info("zones", stream);
              },
              function(success) {
                // get zones
                context.getZones();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          } else {
            nethserver.exec(
              ["nethserver-firewall-base/objects/create"],
              zoneObj,
              function(stream) {
                console.info("zones", stream);
              },
              function(success) {
                // get zones
                context.getZones();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          }
        },
        function(error, data) {
          var errorData = {};
          context.newZone.isLoading = false;
          context.newZone.errors = context.initZoneErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              context.newZone.errors[attr.parameter].hasError = true;
              context.newZone.errors[attr.parameter].message = attr.error;
            }
          } catch (e) {
            console.error(e);
          }
        }
      );
    },
    openDeleteZone(zone) {
      this.currentZone = Object.assign({}, zone);
      $("#deleteZoneModal").modal("show");
    },
    deleteZone(zone) {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "objects.zone_deleted_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "objects.zone_deleted_error"
      );

      nethserver.exec(
        ["nethserver-firewall-base/objects/validate"],
        {
          name: zone.name,
          action: "delete-zone"
        },
        null,
        function(success) {
          $("#deleteZoneModal").modal("hide");
          nethserver.exec(
            ["nethserver-firewall-base/objects/delete"],
            {
              name: zone.name,
              action: "delete-zone"
            },
            function(stream) {
              console.info("zones", stream);
            },
            function(success) {
              // get zones
              context.getZones();
            },
            function(error, data) {
              console.error(error, data);
            }
          );
        },
        function(error, data) {
          console.error(error, data);
          try {
            var errorData = JSON.parse(data);
            zone.isError = errorData.attributes[0].error;
            context.$forceUpdate();
          } catch (e) {
            console.error(e);
          }
        }
      );
    },

    openCreateTimeCondition() {
      this.newTimeCondition = this.initTimeCondition();
      $("#newTimeConditionModal").modal("show");
    },
    openEditTimeCondition(timeCondition) {
      this.newTimeCondition = Object.assign({}, timeCondition);
      this.newTimeCondition.errors = this.initTimeConditionErrors();
      this.newTimeCondition.isLoading = false;
      this.newTimeCondition.isEdit = true;
      $("#newTimeConditionModal").modal("show");
    },
    saveTimeCondition(timeCondition) {
      var context = this;

      var timeConditionObj = Object.assign({}, timeCondition);
      delete timeConditionObj.isLoading;
      delete timeConditionObj.isEdit;
      delete timeConditionObj.isError;
      delete timeConditionObj.errors;
      timeConditionObj.action = timeCondition.isEdit
        ? "update-time-condition"
        : "create-time-condition";

      context.newTimeCondition.isLoading = true;
      nethserver.exec(
        ["nethserver-firewall-base/objects/validate"],
        timeConditionObj,
        null,
        function(success) {
          context.newTimeCondition.isLoading = false;
          $("#newTimeConditionModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "objects.time_condition_" +
              (context.newTimeCondition.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "objects.time_condition_" +
              (context.newTimeCondition.isEdit ? "updated" : "created") +
              "_error"
          );

          // update values
          if (timeCondition.isEdit) {
            nethserver.exec(
              ["nethserver-firewall-base/objects/update"],
              timeConditionObj,
              function(stream) {
                console.info("timeConditions", stream);
              },
              function(success) {
                // get timeConditions
                context.getTimeConditions();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          } else {
            nethserver.exec(
              ["nethserver-firewall-base/objects/create"],
              timeConditionObj,
              function(stream) {
                console.info("timeConditions", stream);
              },
              function(success) {
                // get timeConditions
                context.getTimeConditions();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          }
        },
        function(error, data) {
          var errorData = {};
          context.newTimeCondition.isLoading = false;
          context.newTimeCondition.errors = context.initTimeConditionErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              context.newTimeCondition.errors[attr.parameter].hasError = true;
              context.newTimeCondition.errors[attr.parameter].message =
                attr.error;
            }
          } catch (e) {
            console.error(e);
          }
        }
      );
    },
    openDeleteTimeCondition(timeCondition) {
      this.currentTimeCondition = Object.assign({}, timeCondition);
      $("#deleteTimeConditionModal").modal("show");
    },
    deleteTimeCondition(timeCondition) {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "objects.time_condition_deleted_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "objects.time_condition_deleted_error"
      );

      nethserver.exec(
        ["nethserver-firewall-base/objects/validate"],
        {
          name: timeCondition.name,
          action: "delete-time-condition"
        },
        null,
        function(success) {
          $("#deleteTimeConditionModal").modal("hide");
          nethserver.exec(
            ["nethserver-firewall-base/objects/delete"],
            {
              name: timeCondition.name,
              action: "delete-time-condition"
            },
            function(stream) {
              console.info("timeConditions", stream);
            },
            function(success) {
              // get timeConditions
              context.getTimeConditions();
            },
            function(error, data) {
              console.error(error, data);
            }
          );
        },
        function(error, data) {
          console.error(error, data);
          try {
            var errorData = JSON.parse(data);
            timeCondition.isError = errorData.attributes[0].error;
            context.$forceUpdate();
          } catch (e) {
            console.error(e);
          }
        }
      );
    },

    openCreateService() {
      this.newService = this.initService();
      $("#newServiceModal").modal("show");
    },
    openEditService(service) {
      this.newService = Object.assign({}, service);
      this.newService.Ports = this.newService.Ports.join(", ");
      this.newService.errors = this.initServiceErrors();
      this.newService.isLoading = false;
      this.newService.isEdit = true;
      $("#newServiceModal").modal("show");
    },
    saveService(service) {
      var context = this;

      var serviceObj = Object.assign({}, service);
      serviceObj.Ports =
        serviceObj.Ports.length > 0
          ? serviceObj.Ports.split(",").map(function(item) {
              return item.trim();
            })
          : [];
      delete serviceObj.isLoading;
      delete serviceObj.isEdit;
      delete serviceObj.isError;
      delete serviceObj.errors;
      serviceObj.action = service.isEdit ? "update-service" : "create-service";

      context.newService.isLoading = true;
      nethserver.exec(
        ["nethserver-firewall-base/objects/validate"],
        serviceObj,
        null,
        function(success) {
          context.newService.isLoading = false;
          $("#newServiceModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "objects.service_" +
              (context.newService.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "objects.service_" +
              (context.newService.isEdit ? "updated" : "created") +
              "_error"
          );

          // update values
          if (service.isEdit) {
            nethserver.exec(
              ["nethserver-firewall-base/objects/update"],
              serviceObj,
              function(stream) {
                console.info("services", stream);
              },
              function(success) {
                // get services
                context.getServices();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          } else {
            nethserver.exec(
              ["nethserver-firewall-base/objects/create"],
              serviceObj,
              function(stream) {
                console.info("services", stream);
              },
              function(success) {
                // get services
                context.getServices();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          }
        },
        function(error, data) {
          var errorData = {};
          context.newService.isLoading = false;
          context.newService.errors = context.initServiceErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              context.newService.errors[attr.parameter].hasError = true;
              context.newService.errors[attr.parameter].message = attr.error;
            }
            context.$forceUpdate();
          } catch (e) {
            console.error(e);
          }
        }
      );
    },
    openDeleteService(service) {
      this.currentService = Object.assign({}, service);
      $("#deleteServiceModal").modal("show");
    },
    deleteService(service) {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "objects.service_deleted_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "objects.service_deleted_error"
      );

      nethserver.exec(
        ["nethserver-firewall-base/objects/validate"],
        {
          name: service.name,
          action: "delete-service"
        },
        null,
        function(success) {
          $("#deleteServiceModal").modal("hide");
          nethserver.exec(
            ["nethserver-firewall-base/objects/delete"],
            {
              name: service.name,
              action: "delete-service"
            },
            function(stream) {
              console.info("services", stream);
            },
            function(success) {
              // get services
              context.getServices();
            },
            function(error, data) {
              console.error(error, data);
            }
          );
        },
        function(error, data) {
          console.error(error, data);
          try {
            var errorData = JSON.parse(data);
            service.isError = errorData.attributes[0].error;
            context.$forceUpdate();
          } catch (e) {
            console.error(e);
          }
        }
      );
    }
  }
};
</script>

<style>
.disabled-black {
  cursor: initial;
  color: #363636;
}
.disabled-black:hover {
  color: #363636;
}
</style>
