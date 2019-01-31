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
              <a @click="openEditHost(props.row)">
                <strong>{{ props.row.name}}</strong>
              </a>
            </td>
            <td class="fancy">
              <span class="fa fa-desktop"></span>
              {{props.row.IpAddress}}
            </td>
            <td class="fancy">{{ props.row.Description}}</td>
            <td>
              <button @click="openEditHost(props.row)" class="btn btn-default">
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
              {{$t('objects.start')}}: {{ props.row.start_ip}}
              <br>
              {{$t('objects.end')}}: {{ props.row.end_ip}}
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
              <span class="fa fa-desktop"></span>
              {{props.row.network}}
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
              <span class="fa fa-desktop"></span>
              {{props.row.interface}}
            </td>
            <td class="fancy">
              <span class="fa fa-desktop"></span>
              {{props.row.network}}
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
            <td class="fancy">{{ props.row.Protocol}}</td>
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

    <div class="modal" id="newHostModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{newHost.isEdit ? $t('objects.edit_host') + ' '+ newHost.name : $t('objects.add_host')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveHost()">
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
                  >
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
                >{{$t('objects.description')}}</label>
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

    <div class="modal" id="deleteHostModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('objects.delete_host')}} {{currentHost.name}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deleteHost(currentHost)">
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
    <div class="modal" id="deleteHostGroupModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('objects.delete_host')}} {{currentHostGroup.name}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deleteHostGroup(currentHostGroup)">
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
    <div class="modal" id="deleteIPRangeModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('objects.delete_host')}} {{currentIPRange.name}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deleteIPRange(currentIPRange)">
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
    <div class="modal" id="deleteCIDRSubModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('objects.delete_host')}} {{currentCIDRSub.name}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deleteCIDRSub(currentCIDRSub)">
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
    <div class="modal" id="deleteZoneModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('objects.delete_host')}} {{currentZone.name}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deleteZone(currentZone)">
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
            <h4 class="modal-title">{{$t('objects.delete_host')}} {{currentTimeCondition.name}}</h4>
          </div>
          <form
            class="form-horizontal"
            v-on:submit.prevent="deleteTimeCondition(currentTimeCondition)"
          >
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
    <div class="modal" id="deleteServiceModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('objects.delete_host')}} {{currentService.name}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deleteService(currentService)">
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
    $("#hosts-tab-parent").click();
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
          field: "Network",
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
      newService: this.initService()
    };
  },
  methods: {
    initHost() {
      return {
        isLoading: false,
        isEdit: false,
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
        name: "",
        Description: "",
        errors: this.initHostGroupErrors()
      };
    },
    initIPRange() {
      return {
        isLoading: false,
        isEdit: false,
        name: "",
        startIp: "",
        endIp: "",
        Description: "",
        errors: this.initIPRangeErrors()
      };
    },
    initCIDRSub() {
      return {
        isLoading: false,
        isEdit: false,
        name: "",
        network: "",
        Description: "",
        errors: this.initCIDRSubErrors()
      };
    },
    initZone() {
      return {
        isLoading: false,
        isEdit: false,
        name: "",
        interface: "",
        network: "",
        Description: "",
        errors: this.initZoneErrors()
      };
    },
    initTimeCondition() {
      return {
        isLoading: false,
        isEdit: false,
        name: "",
        Description: "",
        errors: this.initTimeConditionErrors()
      };
    },
    initService() {
      return {
        isLoading: false,
        isEdit: false,
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
        start_ip: {
          hasError: false,
          message: ""
        },
        end_ip: {
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
        network: {
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
        interface: {
          hasError: false,
          message: ""
        },
        network: {
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
        protocol: {
          hasError: false,
          message: ""
        },
        ports: {
          hasError: false,
          message: ""
        },
        Description: {
          hasError: false,
          message: ""
        }
      };
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
          context.hostsRows = success.hosts;

          context.$forceUpdate();
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
          context.hostGroupsRows = success.hostGroups;

          context.$forceUpdate();
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
          context.ipRangesRows = success.ipRanges;

          context.$forceUpdate();
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
          context.cidrSubsRows = success.cidrSubs;

          context.$forceUpdate();
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
          context.zonesRows = success.zones;

          context.$forceUpdate();
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
          context.timeConditionsRows = success.timeConditions;

          context.$forceUpdate();
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
          context.servicesRows = success.services;

          context.$forceUpdate();
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
    saveHost() {
      var context = this;

      var hostObj = Object.assign({}, host);

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
              (context.newPf.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "objects.host_" +
              (context.newPf.isEdit ? "updated" : "created") +
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
              context.newDns.errors[attr.parameter].hasError = true;
              context.newDns.errors[attr.parameter].message = attr.error;
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

      $("#deleteHostModal").modal("hide");
      nethserver.exec(
        ["nethserver-firewall-base/objects/delete"],
        {
          name: host.name
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
    saveHostGroup() {
      var context = this;

      var hostGroupObj = Object.assign({}, hostGroup);

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
              (context.newPf.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "objects.host_group_" +
              (context.newPf.isEdit ? "updated" : "created") +
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
              context.newDns.errors[attr.parameter].hasError = true;
              context.newDns.errors[attr.parameter].message = attr.error;
            }
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

      $("#deleteHostGroupModal").modal("hide");
      nethserver.exec(
        ["nethserver-firewall-base/objects/delete"],
        {
          name: hostGroup.name
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
    saveIPRange() {
      var context = this;

      var ipRangeObj = Object.assign({}, ipRange);

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
              (context.newPf.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "objects.ip_range_" +
              (context.newPf.isEdit ? "updated" : "created") +
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
              context.newDns.errors[attr.parameter].hasError = true;
              context.newDns.errors[attr.parameter].message = attr.error;
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

      $("#deleteIPRangeModal").modal("hide");
      nethserver.exec(
        ["nethserver-firewall-base/objects/delete"],
        {
          name: ipRange.name
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
    saveCIDRSub() {
      var context = this;

      var cidrSubObj = Object.assign({}, cidrSub);

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
              (context.newPf.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "objects.cidr_sub_" +
              (context.newPf.isEdit ? "updated" : "created") +
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
              context.newDns.errors[attr.parameter].hasError = true;
              context.newDns.errors[attr.parameter].message = attr.error;
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

      $("#deleteCIDRSubModal").modal("hide");
      nethserver.exec(
        ["nethserver-firewall-base/objects/delete"],
        {
          name: cidrSub.name
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
    saveZone() {
      var context = this;

      var zoneObj = Object.assign({}, zone);

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
              (context.newPf.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "objects.zone_" +
              (context.newPf.isEdit ? "updated" : "created") +
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
              context.newDns.errors[attr.parameter].hasError = true;
              context.newDns.errors[attr.parameter].message = attr.error;
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

      $("#deleteZoneModal").modal("hide");
      nethserver.exec(
        ["nethserver-firewall-base/objects/delete"],
        {
          name: zone.name
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
    saveTimeCondition() {
      var context = this;

      var timeConditionObj = Object.assign({}, timeCondition);

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
              (context.newPf.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "objects.time_condition_" +
              (context.newPf.isEdit ? "updated" : "created") +
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
              context.newDns.errors[attr.parameter].hasError = true;
              context.newDns.errors[attr.parameter].message = attr.error;
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

      $("#deleteTimeConditionModal").modal("hide");
      nethserver.exec(
        ["nethserver-firewall-base/objects/delete"],
        {
          name: timeCondition.name
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

    openCreateService() {
      this.newService = this.initService();
      $("#newServiceModal").modal("show");
    },
    openEditService(service) {
      this.newService = Object.assign({}, service);
      this.newService.errors = this.initServiceErrors();
      this.newService.isLoading = false;
      this.newService.isEdit = true;
      $("#newServiceModal").modal("show");
    },
    saveService() {
      var context = this;

      var serviceObj = Object.assign({}, service);

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
              (context.newPf.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "objects.service_" +
              (context.newPf.isEdit ? "updated" : "created") +
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
              context.newDns.errors[attr.parameter].hasError = true;
              context.newDns.errors[attr.parameter].message = attr.error;
            }
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

      $("#deleteServiceModal").modal("hide");
      nethserver.exec(
        ["nethserver-firewall-base/objects/delete"],
        {
          name: service.name
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
    }
  }
};
</script>

<style>
</style>
