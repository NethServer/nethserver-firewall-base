<template>
  <div class="container-fluid container-cards-pf">
    <h3>{{ $t("troubleshooting.global") }}</h3>

    <div class="row row-cards-pf">
      <!-- INTERNET -->
      <div class="col-sm-4 col-md-3">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <h2 class="card-pf-title">
            <span class="fa fa-globe"></span
            >{{ $t("troubleshooting.internet") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.internet.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.internet.isLoaded"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span
                  v-if="view.internet.status == 'disabled'"
                  class="fa fa-ban gray"
                ></span>
                <span
                  v-if="view.internet.status == 'running'"
                  class="pficon pficon-ok"
                ></span>
                <span
                  v-if="view.internet.status == 'warning'"
                  class="pficon pficon-warning-triangle-o"
                ></span>
                <span
                  v-if="view.internet.status == 'failed'"
                  class="pficon pficon-error-circle-o"
                ></span>
              </span>
            </p>
          </div>
        </div>
      </div>
      <!-- END INTERNET -->

      <!-- MULTIWAN -->
      <div class="col-sm-4 col-md-3">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <h2 class="card-pf-title">
            <span class="pf-icon pficon-network"></span
            >{{ $t("troubleshooting.multiwan") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.multiwan.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.multiwan.isLoaded"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span
                  v-if="view.multiwan.status == 'disabled'"
                  class="fa fa-ban gray"
                ></span>
                <span
                  v-if="view.multiwan.status == 'running'"
                  class="pficon pficon-ok"
                ></span>
                <span
                  v-if="view.multiwan.status == 'warning'"
                  class="pficon pficon-warning-triangle-o"
                ></span>
                <span
                  v-if="view.multiwan.status == 'failed'"
                  class="pficon pficon-error-circle-o"
                ></span>
              </span>
            </p>
          </div>
        </div>
      </div>
      <!-- END MULTIWAN -->

      <!-- SHOREWALL -->
      <div class="col-sm-4 col-md-3">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <h2 class="card-pf-title">
            <span class="fa fa-shield"></span
            >{{ $t("troubleshooting.shorewall") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.shorewall.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.shorewall.isLoaded"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span
                  v-if="view.shorewall.status == 'disabled'"
                  class="fa fa-ban gray"
                ></span>
                <span
                  v-if="view.shorewall.status == 'running'"
                  class="pficon pficon-ok"
                ></span>
                <span
                  v-if="view.shorewall.status == 'warning'"
                  class="pficon pficon-warning-triangle-o"
                ></span>
                <span
                  v-if="view.shorewall.status == 'failed'"
                  class="pficon pficon-error-circle-o"
                ></span>
              </span>
            </p>
          </div>
        </div>
      </div>
      <!-- END SHOREWALL -->

      <!-- SYSTEMD -->
      <div class="col-sm-4 col-md-3">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <a
            target="_blank"
            href="/nethserver#/services"
            class="card-pf-link-with-icon card-action"
          >
            <span class="fa fa-external-link"></span>
          </a>
          <h2 class="card-pf-title">
            <span class="pf-icon pficon-services"></span
            >{{ $t("troubleshooting.systemd") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.systemd.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.systemd.isLoaded"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span
                  v-if="view.systemd.status == 'running'"
                  class="pficon pficon-ok"
                ></span>
                <span
                  v-if="view.systemd.status == 'warning'"
                  class="pficon pficon-warning-triangle-o"
                ></span>
              </span>
            </p>
          </div>
        </div>
      </div>
      <!-- END SYSTEMD -->
    </div>

    <h3>{{ $t("troubleshooting.services") }}</h3>

    <div class="row row-cards-pf">
      <!-- PROXY -->
      <div class="col-sm-4 col-md-3">
        <div
          class="card-pf card-pf-accented card-pf-aggregate-status card-with-footer"
        >
          <a
            target="_blank"
            href="/nethserver#/applications/nethserver-squid"
            class="card-pf-link-with-icon card-action"
          >
            <span class="fa fa-external-link"></span>
          </a>
          <h2 class="card-pf-title">
            <span class="pf-icon pficon-filter"></span
            >{{ $t("troubleshooting.proxy") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.squid.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.squid.isLoaded && view.squid.status == 'running'"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span class="pficon pficon-ok"></span>
              </span>
            </p>
            <p
              v-if="view.squid.isLoaded && view.squid.status == 'disabled'"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span class="fa fa-ban gray"></span>
              </span>
            </p>
            <p
              v-if="view.squid.isLoaded && view.squid.status == 'failed'"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span class="pficon pficon-error-circle-o"></span>
              </span>
            </p>
          </div>
          <div v-if="view.squid.status !== 'disabled'" class="footer">
            <span class="action" @click="showProxyModal">
              <span class="fa fa-search icon"></span
              >{{ $t("troubleshooting.details") }}
            </span>
          </div>
        </div>
      </div>
      <!-- END PROXY -->

      <!-- ufdbGuard FILTER -->
      <div class="col-sm-4 col-md-3">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <a
            target="_blank"
            href="/nethserver#/applications/nethserver-squid"
            class="card-pf-link-with-icon card-action"
          >
            <span class="fa fa-external-link"></span>
          </a>
          <h2 class="card-pf-title">
            <span class="pf-icon pficon-filter"></span
            >{{ $t("troubleshooting.ufdbGuard") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.ufdbGuard.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.ufdbGuard.isLoaded"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span
                  v-if="view.ufdbGuard.status == 'disabled'"
                  class="fa fa-ban gray"
                ></span>
                <span
                  v-if="view.ufdbGuard.status == 'running'"
                  class="pficon pficon-ok"
                ></span>
                <span
                  v-if="view.ufdbGuard.status == 'failed'"
                  class="pficon pficon-error-circle-o"
                ></span>
              </span>
            </p>
          </div>
        </div>
      </div>
      <!-- END FILTER -->

      <!-- ANTIVIRUS -->
      <div class="col-sm-4 col-md-3">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <a
            target="_blank"
            href="/nethserver#/applications/nethserver-squid"
            class="card-pf-link-with-icon card-action"
          >
            <span class="fa fa-external-link"></span>
          </a>
          <h2 class="card-pf-title">
            <span class="fa fa-bug"></span>{{ $t("troubleshooting.antivirus") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.antivirus.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.antivirus.isLoaded"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span
                  v-if="view.antivirus.status == 'disabled'"
                  class="fa fa-ban gray"
                ></span>
                <span
                  v-if="view.antivirus.status == 'running'"
                  class="pficon pficon-ok"
                ></span>
                <span
                  v-if="view.antivirus.status == 'failed'"
                  class="pficon pficon-error-circle-o"
                ></span>
              </span>
            </p>
          </div>
        </div>
      </div>
      <!-- END ANTIVIRUS -->

      <!-- NTOPNG -->
      <div class="col-sm-4 col-md-3">
        <div
          class="card-pf card-pf-accented card-pf-aggregate-status card-with-footer"
        >
          <a
            target="_blank"
            href="/nethserver#/applications/nethserver-ntopng"
            class="card-pf-link-with-icon card-action"
          >
            <span class="fa fa-external-link"></span>
          </a>
          <h2 class="card-pf-title">
            <span class="fa fa-tachometer"></span
            >{{ $t("troubleshooting.ntopng") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.ntopng.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <div
              v-if="view.ntopng.isLoaded && view.ntopng.status == 'running'"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="pficon pficon-ok"></span>
            </div>
            <p
              v-if="view.ntopng.isLoaded && view.ntopng.status == 'disabled'"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="fa fa-ban gray"></span>
            </p>
            <p
              v-if="view.ntopng.isLoaded && view.ntopng.status == 'failed'"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span class="pficon pficon-error-circle-o"></span>
              </span>
            </p>
          </div>
          <div v-if="view.ntopng.status !== 'disabled'" class="footer">
            <span class="action" @click="showNtopngModal">
              <span class="fa fa-search icon"></span
              >{{ $t("troubleshooting.details") }}
            </span>
          </div>
        </div>
      </div>
      <!-- END NTOPNG -->
    </div>

    <h3>{{ $t("troubleshooting.security") }}</h3>

    <div class="row row-cards-pf">
      <!-- IP BLACKLIST -->
      <div class="col-sm-4 col-md-3">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <a
            target="_blank"
            href="/nethserver#/applications/nethserver-blacklist"
            class="card-pf-link-with-icon card-action"
          >
            <span class="fa fa-external-link"></span>
          </a>
          <h2 class="card-pf-title">
            <span class="pf-icon pficon-cloud-security"></span
            >{{ $t("troubleshooting.ipblacklist") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.ipblacklist.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.ipblacklist.isLoaded"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span
                  v-if="view.ipblacklist.status == 'disabled'"
                  class="fa fa-ban gray"
                ></span>
                <span
                  v-if="view.ipblacklist.status == 'enabled'"
                  class="pficon pficon-ok"
                ></span>
              </span>
            </p>
          </div>
        </div>
      </div>
      <!-- END IP BLACKLIST -->

      <!-- FTL DNS BLACKLIST -->
      <div class="col-sm-4 col-md-3">
        <div class="card-pf card-pf-accented card-pf-aggregate-status  card-with-footer">
          <a
            target="_blank"
            href="/nethserver#/applications/nethserver-blacklist"
            class="card-pf-link-with-icon card-action"
          >
            <span class="fa fa-external-link"></span>
          </a>
          <h2 class="card-pf-title">
            <span class="pf-icon pficon-cloud-security"></span
            >{{ $t("troubleshooting.ftl") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.ftl.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.ftl.isLoaded"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span
                  v-if="view.ftl.status == 'disabled'"
                  class="fa fa-ban gray"
                ></span>
                <span
                  v-if="view.ftl.status == 'running'"
                  class="pficon pficon-ok"
                ></span>
              </span>
            </p>
          </div>
          <div v-if="view.ftl.status !== 'disabled'" class="footer">
            <span class="action" @click="showFtlModal">
              <span class="fa fa-search icon"></span
              >{{ $t("troubleshooting.details") }}
            </span>
          </div>
        </div>
      </div>
      <!-- END FTL -->

      <!-- FAIL2BAN -->
      <div class="col-sm-4 col-md-3">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <a
            target="_blank"
            href="/nethserver#/applications/nethserver-fail2ban"
            class="card-pf-link-with-icon card-action"
          >
            <span class="fa fa-external-link"></span>
          </a>
          <h2 class="card-pf-title">
            <span class="pf-icon pficon-locked"></span
            >{{ $t("troubleshooting.fail2ban") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.fail2ban.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.fail2ban.isLoaded"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span
                  v-if="view.fail2ban.status == 'disabled'"
                  class="fa fa-ban gray"
                ></span>
                <span
                  v-if="view.fail2ban.status == 'enabled'"
                  class="pficon pficon-ok"
                ></span>
              </span>
            </p>
          </div>
        </div>
      </div>
      <!-- END FAIL2BAN -->

      <!-- SURICATA -->
      <div class="col-sm-4 col-md-3">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <a
            target="_blank"
            href="/nethserver#/applications/nethserver-suricata"
            class="card-pf-link-with-icon card-action"
          >
            <span class="fa fa-external-link"></span>
          </a>
          <h2 class="card-pf-title">
            <span class="pf-icon pficon-security"></span
            >{{ $t("troubleshooting.suricata") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.suricata.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.suricata.isLoaded"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span
                  v-if="view.suricata.status == 'disabled'"
                  class="fa fa-ban gray"
                ></span>
                <span
                  v-if="view.suricata.status == 'running'"
                  class="pficon pficon-ok"
                ></span>
              </span>
            </p>
          </div>
        </div>
      </div>
      <!-- END SURICATA -->
    </div>

    <h3>{{ $t("troubleshooting.other") }}</h3>

    <div class="row row-cards-pf">
      <!-- TEMPLATES -->
      <div class="col-sm-4 col-md-3">
        <div
          class="card-pf card-pf-accented card-pf-aggregate-status card-with-footer"
        >
          <h2 class="card-pf-title">
            <span class="pf-icon pficon-settings"></span
            >{{ $t("troubleshooting.templates") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.templates.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.templates.isLoaded"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span
                  v-if="view.templates.status == 'disabled'"
                  class="fa fa-ban gray"
                ></span>
                <span
                  v-if="view.templates.status == 'warning'"
                  class="pficon pficon-warning-triangle-o"
                ></span>
              </span>
            </p>
          </div>
          <div v-if="view.templates.status !== 'disabled'" class="footer">
            <span class="action" @click="showCustomTemplatesModal">
              <span class="fa fa-search icon"></span
              >{{ $t("troubleshooting.details") }}
            </span>
          </div>
        </div>
      </div>
      <!-- END TEMPLATES -->

      <!-- YUM LAST UPDATE -->
      <div class="col-sm-4 col-md-3">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <h2 class="card-pf-title">
            <span class="pficon pficon-restart"></span
            >{{ $t("troubleshooting.yum_last_update") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.yum.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.yum.isLoaded"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span
                  v-if="view.yum.date"
                  class=""
                >{{view.yum.date | dateFormat}}</span>
              </span>
            </p>
          </div>
        </div>
      </div>
      <!-- END YUM LAST UPDATE -->

      <!-- UPS -->
      <div class="col-sm-4 col-md-3">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <a
            target="_blank"
            href="/nethserver#/applications/nethserver-nut"
            class="card-pf-link-with-icon card-action"
          >
            <span class="fa fa-external-link"></span>
          </a>
          <h2 class="card-pf-title">
            <span class="fa fa-battery"></span
            >{{ $t("troubleshooting.ups") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.ups.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <p
              v-if="view.ups.isLoaded"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span
                  v-if="view.ups.status == 'disabled'"
                  class="fa fa-ban gray"
                ></span>
                <span
                  v-if="view.ups.status == 'running'"
                  class="pficon pficon-ok"
                ></span>
                <span
                  v-if="view.ups.status == 'warning'"
                  class="pficon pficon-warning-triangle-o"
                ></span>
                <span
                  v-if="view.ups.status == 'failed'"
                  class="pficon pficon-error-circle-o"
                ></span>
              </span>
            </p>
          </div>
        </div>
      </div>
      <!-- END UPS -->

      <!-- FLASHSTART -->
      <div class="col-sm-4 col-md-3">
        <div
          class="card-pf card-pf-accented card-pf-aggregate-status card-with-footer"
        >
          <a
            target="_blank"
            href="/nethserver#/applications/nethserver-flashstart"
            class="card-pf-link-with-icon card-action"
          >
            <span class="fa fa-external-link"></span>
          </a>
          <h2 class="card-pf-title">
            <span class="fa fa-bolt"></span
            >{{ $t("troubleshooting.flashstart") }}
          </h2>
          <div class="card-pf-body">
            <div
              v-if="!view.flashstart.isLoaded"
              class="spinner spinner-lg view-spinner"
            ></div>
            <div
              v-if="view.flashstart.isLoaded && view.flashstart.status == 'running'"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="pficon pficon-ok"></span>
            </div>
            <p
              v-if="view.flashstart.isLoaded && view.flashstart.status == 'disabled'"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="fa fa-ban gray"></span>
            </p>
            <p
              v-if="view.flashstart.isLoaded && view.flashstart.status == 'failed'"
              class="card-pf-aggregate-status-notifications"
            >
              <span class="card-pf-aggregate-status-notification">
                <span class="pficon pficon-error-circle-o"></span>
              </span>
            </p>
          </div>
          <div v-if="view.flashstart.status !== 'disabled'" class="footer">
            <span class="action" @click="showFlashstartModal">
              <span class="fa fa-search icon"></span
              >{{ $t("troubleshooting.details") }}
            </span>
          </div>
        </div>
      </div>
      <!-- END FLASHSTART -->
    </div>

    <h3>{{ $t("troubleshooting.network") }}</h3>

    <div class="row row-cards-pf">
      <!-- PING CHART -->
      <div class="col-md-6">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <div
            v-if="!view.isChartLoaded"
            class="spinner spinner-lg view-spinner  mg-top-20"
          ></div>
          <div class="card-pf-body">
            <div
              v-if="view.invalidChartsPingData"
              class="alert alert-warning alert-dismissable col-sm-12"
            >
              <span class="pficon pficon-warning-triangle-o"></span>
              <strong>{{ $t("warning") }}!</strong>
              {{ $t("troubleshooting.ping_charts_not_updated") }}.
            </div>
            <div v-if="view.isChartLoaded">
              <div v-for="(data, index) in charts" :key="index">
                <h4 class="mg-top">
                  {{ $t("troubleshooting.ping") }}: {{ index }}
                </h4>
                <div
                  :id="'ping-legend-' + index"
                  class="troubleshooting-chart-legend"
                ></div>
                <div :id="'chart-ping-' + index" class="chart-ping"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- END PING CHART -->
      <!-- PING DROPRATE -->
      <div class="col-md-6">
        <div class="card-pf card-pf-accented card-pf-aggregate-status">
          <div
            v-if="!view.isChartLoaded"
            class="spinner spinner-lg view-spinner  mg-top-20"
          ></div>
          <div class="card-pf-body">
            <div
              v-if="view.invalidChartsPingDroprateData"
              class="alert alert-warning alert-dismissable col-sm-12"
            >
              <span class="pficon pficon-warning-triangle-o"></span>
              <strong>{{ $t("warning") }}!</strong>
              {{ $t("troubleshooting.ping_droprate_charts_not_updated") }}.
            </div>
            <div v-if="view.isChartLoaded">
              <div v-for="(data, index) in charts" :key="index">
                <h4 class="mg-top">
                  {{ $t("troubleshooting.ping_droprate") }}: {{ index }}
                </h4>
                <div
                  :id="'ping-droprate-legend-' + index"
                  class="troubleshooting-chart-legend"
                ></div>
                <div :id="'chart-ping-droprate-' + index" class="chart-ping"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- END PING DROPRATE -->
    </div>

    <!-- ntopng modal -->
    <div
      class="modal"
      id="ntopngModal"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">
              {{ $t("troubleshooting.ntopng") }}
            </h4>
          </div>
          <div class="modal-body">
            <div>
              {{ $t("troubleshooting.bandwidth_monitor_modal_description") }}
            </div>
            <ul v-if="view.ntopng.details" class="list-group mg-top-10">
              <li
                class="list-group-item"
                v-for="(role, name) in view.ntopng.details"
                :key="name"
              >
                <span class="pficon pficon-network icon"></span>
                <span :class="role">{{ name }}</span>
              </li>
            </ul>
          </div>
          <div class="modal-footer">
            <button
              class="btn btn-default"
              type="button"
              @click="hideNtopngModal"
            >
              {{ $t("close") }}
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- custom templates modal -->
    <div
      class="modal"
      id="customTemplatesModal"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">
              {{ $t("troubleshooting.templates") }}
            </h4>
          </div>
          <div class="modal-body">
            <div>
              {{ $t("troubleshooting.custom_templates_modal_description") }}
            </div>
            <ul v-if="view.templates.details" class="list-group mg-top-10">
              <li
                class="list-group-item"
                v-for="(template, index) in view.templates.details"
                :key="index"
              >
                <span class="fa fa-cogs icon"></span>
                <code>{{ template }}</code>
              </li>
            </ul>
          </div>
          <div class="modal-footer">
            <button
              class="btn btn-default"
              type="button"
              @click="hideCustomTemplatesModal"
            >
              {{ $t("close") }}
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- proxy modal -->
    <div
      class="modal"
      id="proxyModal"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">
              {{ $t("troubleshooting.proxy") }}
            </h4>
          </div>
          <div class="modal-body">
            <div>
              {{ $t("troubleshooting.proxy_modal_description") }}
            </div>
            <ul v-if="view.squid.details" class="list-group mg-top-10">
              <li
                class="list-group-item"
                v-for="(mode, role) in view.squid.details"
                :key="role"
              >
                <span class="pficon pficon-network icon"></span>
                <strong :class="role">{{ role.toUpperCase() }}</strong
                >:
                <code>{{ $t("troubleshooting." + mode) }}</code>
              </li>
            </ul>
          </div>
          <div class="modal-footer">
            <button
              class="btn btn-default"
              type="button"
              @click="hideProxyModal"
            >
              {{ $t("close") }}
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- flashstart modal -->
    <div
      class="modal"
      id="flashStartModal"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">
              {{ $t("troubleshooting.flashstart") }}
            </h4>
          </div>
          <div class="modal-body">
            <div>
              {{ $t("troubleshooting.flashstart_modal_description") }}
            </div>
            <ul v-if="view.flashstart.details" class="list-group mg-top-10">
              <li
                class="list-group-item"
                v-for="role in view.flashstart.details"
                :key="role"
              >
                <span class="pficon pficon-network icon"></span>
                <strong :class="role">{{ role.toUpperCase() }}</strong>
              </li>
            </ul>
          </div>
          <div class="modal-footer">
            <button
              class="btn btn-default"
              type="button"
              @click="hideFlashstartModal"
            >
              {{ $t("close") }}
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- ftl modal -->
    <div
      class="modal"
      id="ftlModal"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">
              {{ $t("troubleshooting.ftl") }}
            </h4>
          </div>
          <div class="modal-body">
            <div>
              {{ $t("troubleshooting.ftl_modal_description") }}
            </div>
            <ul v-if="view.ftl.details" class="list-group mg-top-10">
              <li
                class="list-group-item"
                v-for="role in view.ftl.details"
                :key="role"
              >
                <span class="pficon pficon-network icon"></span>
                <strong :class="role">{{ role.toUpperCase() }}</strong>
              </li>
            </ul>
          </div>
          <div class="modal-footer">
            <button
              class="btn btn-default"
              type="button"
              @click="hideFtlModal"
            >
              {{ $t("close") }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Dygraph from "dygraphs";

export default {
  name: "TroubleshootingServices",
  data() {
    return {
      view: {
        isChartLoaded: false,
        invalidChartsPingData: false,
        invalidChartsPingDroprateData: false,

        internet: { status: "disabled", isLoaded: false },
        shorewall: { status: "disabled", isLoaded: false },
        multiwan: { status: "disabled", isLoaded: false },
        systemd: { status: "disabled", isLoaded: false },

        squid: { status: "disabled", isLoaded: false, details: {} },
        antivirus: { status: "disabled", isLoaded: false },
        ufdbGuard: { status: "disabled", isLoaded: false },

        ipblacklist: { status: "disabled", isLoaded: false },
        ftl: { status: "disabled", isLoaded: false },

        ntopng: { status: "disabled", isLoaded: false },

        fail2ban: { status: "disabled", isLoaded: false },
        suricata: { status: "disabled", isLoaded: false },
        templates: { status: "disabled", isLoaded: false, details: [] },

        yum: { status: "disabled", isLoaded: false },
        ups: { status: "disabled", isLoaded: false },
        flashstart: { status: "disabled", isLoaded: false },
      },
      charts: {},
      pingChartInterval: null,
      pingDroprateChartInterval: null,
    };
  },
  created() {
    console.log("services created"); ////
  },
  mounted() {
    console.log("services mounted"); ////

    var context = this;

    context.updatePingChart();
    context.pingChartInterval = setInterval(function() {
      context.updatePingChart();
    }, 5000);

    context.updatePingDroprateChart();
    context.pingDroprateChartInterval = setInterval(function() {
      context.updatePingDroprateChart();
    }, 5000);

    const services = [
      "internet",
      "shorewall",
      "multiwan",
      "systemd",
      "ipblacklist",
      "ftl",
      "templates",
      "squid",
      "ufdbGuard",
      "antivirus",
      "fail2ban",
      "suricata",
      "ntopng",
      "yum",
      "ups",
      "flashstart"
    ];
    services.forEach(function(item, index) {
      context.getServiceStatus(item);
    });

    $("#ts-services-tab-parent").click();
  },
  beforeDestroy() {
    console.log("services beforeDestroy"); ////

    $(".modal").modal("hide");
    clearInterval(this.pingChartInterval);
    clearInterval(this.pingDroprateChartInterval);
  },
  methods: {
    getServiceStatus(service) {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/troubleshooting/read"],
        { action: "service", service: service },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context["view"][service]["isLoaded"] = true;
          context["view"][service]["status"] = success.status;
          if ("details" in success) {
            context["view"][service]["details"] = success.details;

            console.log(service, "details", success.details); ////
          }
          if ("date" in success) {
            context["view"][service]["date"] = parseInt(success.date) || null;

            console.log(service, "date", success.date); ////
          }
        },
        function(error) {
          console.error(error);
          context["view"][service]["isLoaded"] = false;
        }
      );
    },
    updatePingChart() {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/troubleshooting/read"],
        { action: "ping", time: 900 },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.charts = success;
          context.view.invalidChartsPingData = false;
          context.view.isChartLoaded = true;
          context.$nextTick(function() {
            for (const ip in context.charts) {
              var chart = context.charts[ip];

              for (var t in chart.data) {
                chart.data[t][0] = new Date(chart.data[t][0]);
              }

              const i18nLabels = chart.labels.map((label) =>
                context.$i18n.t("troubleshooting." + label)
              );

              var g = new Dygraph(
                document.getElementById("chart-ping-" + ip),
                chart.data,
                {
                  fillGraph: true,
                  stackedGraph: true,
                  labels: i18nLabels,
                  height: 150,
                  strokeWidth: 1,
                  strokeBorderWidth: 1,
                  ylabel: context.$i18n.t("troubleshooting.latency_ms"),
                  axisLineColor: "white",
                  labelsDiv: document.getElementById("ping-legend-" + ip),
                  labelsSeparateLines: true,
                  drawGrid: false,
                }
              );
              g.initialData = chart.data;
            }
          });
        },
        function(error) {
          console.error(error);
          context.view.isChartLoaded = true;
        }
      );
    },
    updatePingDroprateChart() {
      var context = this;
      nethserver.exec(
        ["nethserver-firewall-base/troubleshooting/read"],
        { action: "ping-droprate", time: 900 },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.charts = success;
          context.view.invalidChartsPingDroprateData = false;
          context.view.isChartLoaded = true;
          context.$nextTick(function() {
            for (const ip in context.charts) {
              var chart = context.charts[ip];

              for (var t in chart.data) {
                chart.data[t][0] = new Date(chart.data[t][0]);
              }

              const i18nLabels = chart.labels.map((label) =>
                context.$i18n.t("troubleshooting." + label)
              );

              var g = new Dygraph(
                document.getElementById("chart-ping-droprate-" + ip),
                chart.data,
                {
                  fillGraph: true,
                  stackedGraph: true,
                  labels: i18nLabels,
                  height: 150,
                  strokeWidth: 1,
                  strokeBorderWidth: 1,
                  ylabel: context.$i18n.t("troubleshooting.droprate_perc"),
                  axisLineColor: "white",
                  labelsDiv: document.getElementById("ping-droprate-legend-" + ip),
                  labelsSeparateLines: true,
                  drawGrid: false,
                  axes : {
                    y : {
                      axisLabelFormatter: function(y) { return (y * 100).toFixed(0) + '%' },
                      valueFormatter: function(y) { return (y * 100).toFixed(0) + '%' },
                    }
                  }
                }
              );
              g.initialData = chart.data;
            }
          });
        },
        function(error) {
          console.error(error);
          context.view.isChartLoaded = true;
        }
      );
    },
    showNtopngModal() {
      $("#ntopngModal").modal("show");
    },
    hideNtopngModal() {
      $("#ntopngModal").modal("hide");
    },
    showCustomTemplatesModal() {
      $("#customTemplatesModal").modal("show");
    },
    hideCustomTemplatesModal() {
      $("#customTemplatesModal").modal("hide");
    },
    showProxyModal() {
      $("#proxyModal").modal("show");
    },
    hideProxyModal() {
      $("#proxyModal").modal("hide");
    },
    showFlashstartModal() {
      $("#flashStartModal").modal("show");
    },
    hideFlashstartModal() {
      $("#flashStartModal").modal("hide");
    },
    showFtlModal() {
      $("#ftlModal").modal("show");
    },
    hideFtlModal() {
      $("#ftlModal").modal("hide");
    },
  },
};
</script>

<style scoped>
.card-pf-aggregate-status .card-pf-title {
  padding-left: 20px;
  padding-right: 20px;
}

.card-pf-title > .pf-icon {
  margin-right: 7px;
}

.pl-21 {
  padding-left: 21px;
}

.icon {
  margin-right: 7px;
}

.card-pf-aggregate-status .card-action {
  float: right;
  margin-top: 10px;
}

.card-pf-aggregate-status .card-action span {
  margin-right: 0;
}

.card-pf-aggregate-status .card-action .fa {
  font-size: 14px;
  color: #0088ce !important;
}

.card-pf-aggregate-status .card-action .fa:hover {
  color: #0088ce !important;
}

.container-fluid.container-cards-pf {
  margin-left: auto !important;
}

.chart-ping {
  padding-left: 5px;
  padding-right: 5px;
  margin-bottom: 20px;
}

.card-with-footer {
  padding-left: 0 !important;
  padding-right: 0 !important;
}

.card-with-footer .card-pf-title {
  padding-left: 10px;
  padding-right: 10px;
}

.card-with-footer .card-pf-body {
  padding-left: 10px;
  padding-right: 10px;
}

.card-with-footer .footer {
  border-top: 1px solid lightgray;
  background-color: #fafafa;
  padding: 10px;
  font-size: 14px;
}

.card-with-footer .footer .icon {
  margin-right: 7px;
}

.card-with-footer .footer .action {
  color: #0088ce;
  cursor: pointer;
}
</style>
