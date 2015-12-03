Summary: NethServer firewall implementation based on Shorewall
Name: nethserver-firewall-base
Version: 2.10.1
Release: 1%{?dist}
License: GPL
Source0: %{name}-%{version}.tar.gz

BuildArch: noarch
Requires: nethserver-base > 2.5.2-1
Requires: nethserver-lsm
Requires: shorewall >= 4.6
Requires: ipset
Requires: rp-pppoe

Obsoletes: nethserver-shorewall
Provides: nethserver-firewall

BuildRequires: nethserver-devtools
AutoReq: no

%package ui
Summary: Web Interface for firewall configuration
Group: UI
Requires: %{name} = %{version}-%{release}
%description ui
%files ui -f %{name}-%{version}-%{release}-filelist-ui

%description
NethServer simple firewall

%prep
%setup -q

%build
%{makedocs}
perl createlinks
mkdir -p root%{perl_vendorlib}
mv -v NethServer root%{perl_vendorlib}

%install
rm -rf $RPM_BUILD_ROOT
(cd root ; find . -depth -print | cpio -dump $RPM_BUILD_ROOT)
%{genfilelist} $RPM_BUILD_ROOT > %{name}-%{version}-%{release}-filelist
grep -e php$ -e rst$ -e html$ %{name}-%{version}-%{release}-filelist > %{name}-%{version}-%{release}-filelist-ui
grep -v /usr/share/nethesis/NethServer %{name}-%{version}-%{release}-filelist > %{name}-%{version}-%{release}-filelist-core

%files -f %{name}-%{version}-%{release}-filelist-core
%defattr(-,root,root)
%doc COPYING

%changelog
* Thu Dec 03 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.10.1-1
- Deny port forwarding the same port - Bug #3327 [NethServer]

* Mon Nov 30 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.10.0-1
- Error deleting firewall service object - Bug #3308 [NethServer]
- Firewall: web interface for policy routing - Feature #2809 [NethServer]

* Wed Nov 11 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.9.0-1
- MultiWAN: remove static routes for checkip - Enhancement #3289 [NethServer]
- PPPoE default route overrides multi WAN configuration - Bug #3287 [NethServer]
- port forward not working with sNAT and multiWAN - Bug #3280 [NethServer]
- DB key name clash in networks db - Bug #3272 [NethServer]
- ip /mac binding blocks dhcp server requests - Bug #3257 [NethServer]

* Thu Sep 24 2015 Davide Principi <davide.principi@nethesis.it> - 2.8.0-1
- Drop lokkit support, always use shorewall - Enhancement #3258 [NethServer]

* Mon Sep 14 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.7.2-1
- Warning uninitialized value $fw_obj - Enhancement #3242 [NethServer]

* Wed Sep 02 2015 Davide Principi <davide.principi@nethesis.it> - 2.7.1-1
- hairpin nat intercepts outgoing traffic if port forward is on Any ip - Bug #3248 [NethServer]
- hairpin nat - shorewall syntax error with port-range port fwd and IPS - Bug #3232 [NethServer]

* Thu Aug 27 2015 Davide Principi <davide.principi@nethesis.it> - 2.7.0-1
- Firewall rules: support hosts within VPN zones - Enhancement #3233 [NethServer]
- hairpin nat - shorewall syntax error with port-range port fwd and IPS - Bug #3232 [NethServer]
- server-manager PPPoE support - Enhancement #3227 [NethServer]

* Fri Jul 17 2015 Davide Principi <davide.principi@nethesis.it> - 2.6.5-1
- PPPoE support - Feature #3218 [NethServer]

* Wed Jul 15 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.6.4-1
- Blocked firewall policy is too restrictive - Bug #3210 [NethServer]
- Event trusted-networks-modify - Enhancement #3195 [NethServer]
- Modify NAT 1:1 behavior  (only source NAT) - Enhancement #3192 [NethServer]

* Wed Jun 24 2015 Davide Principi <davide.principi@nethesis.it> - 2.6.3-1
- shorewall syntax error with port-range port fwd and IPS - Bug #3200 [NethServer]
- It's impossible to add a traffic shaping port in all protocol - Bug #3040 [NethServer]

* Wed May 20 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.6.2-1
- Invalid traffic shaping rules after deleting host object #3173

* Tue May 19 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.6.1-1
- Custom firewall zones precedence - Bug #3170 [NethServer]
- MultiWAN failover ineffective - Bug #3167 [NethServer]
- FirewallRules blank page error - Bug #3146 [NethServer]
- Invalid port forward after deleting firewall objects - Bug #3136 [NethServer]

* Thu Apr 23 2015 Davide Principi <davide.principi@nethesis.it> - 2.6.0-1
- IPS: shorewall configuration not applied if there is at least an orange interface - Bug #3129 [NethServer]
- Firewall rules (web UI): support ip range and CIDR object - Feature #3121 [NethServer]
- MAC validation (IP / MAC binding) - Feature #3120 [NethServer]
- Language packs support - Feature #3115 [NethServer]
- Firewall: support ip range and CIDR objects - Feature #3112 [NethServer]
- Copy action for firewall rules - Feature #3111 [NethServer]
- Add VPN zones to firewall rules - Enhancement #3055 [NethServer]

* Thu Apr 09 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.5.1-1
- Port forward: display WAN IP - Enhancement #3100 [NethServer]
- LSM: configuration tuning - Enhancement #3098 [NethServer]

* Thu Mar 26 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.5.0-1
- Hairpin nat - Feature #2989 [NethServer]
- Firewall-base: multi-wan dhcp failover not supported - Enhancement #2827 [NethServer]

* Wed Mar 11 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.4.0-1
- firewall: routeback on all interfaces - Enhancement #3083 [NethServer]
- NAT 1:1 - Feature #3035 [NethServer]

* Tue Mar 10 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.3.1-1
- Traffic from green to blue is not allowed - Bug #3081 [NethServer]

* Thu Mar 05 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.3.0-1
- Protect built-int zones in Firewall.pm library - Enhancement #3056 [NethServer]
- Port forward: limit validator for Allow field - Enhancement #3045 [NethServer]
- MultiWAN: restrict validator for weight field - Enhancement #3044 [NethServer]
- Firewall: avoid user lock out - Enhancement #3043 [NethServer]

* Wed Jan 28 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.2.3-1.ns6
- Shorewall: allow template-custom for ESTABLISHED and RELATED connection inside rules file - Enhancement #2999 [NethServer]

* Mon Dec 01 2014 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.2.2-1.ns6
- multi-wan: fix enable/disable failure - Bug #2966 [NethServer]

* Wed Nov 19 2014 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.2.1-1.ns6
- Notify user if event fails - Enhancement #2927 [NethServer]

* Tue Nov 04 2014 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.2.0-1.ns6
- Firewall fallback when IPS is not running - Enhancement #2935 [NethServer]

* Wed Oct 15 2014 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.1.1-1.ns6
- Support DHCP on multiple interfaces - Feature #2849

* Thu Oct 02 2014 Davide Principi <davide.principi@nethesis.it> - 2.1.0-1.ns6
- Handle nethserver-firewall-base uninstallation - Enhancement #2873 [NethServer]
- Shorewall: enable green-only mode - Feature #2861 [NethServer]
- Changing role to a red interface doesn't remove it from providers - Bug #2852 [NethServer]
- Firewall: add migration fragment for tc database - Bug #2846 [NethServer]

* Wed Aug 20 2014 Davide Principi <davide.principi@nethesis.it> - 2.0.0-2.ns6
- Migration fragment 000_capitalize_props for old portforward DB.

* Wed Aug 20 2014 Davide Principi <davide.principi@nethesis.it> - 2.0.0-1.ns6
- Masquerade all sources - Bug #2836 [NethServer]
- Firewall rules: preserve references to other DB records - Enhancement #2835 [NethServer]
- Embed Nethgui 1.6.0 into httpd-admin RPM - Enhancement #2820 [NethServer]
- Firewall: beautify rules page - Enhancement #2783 [NethServer]
- Firewall: disable unused roles - Enhancement #2776 [NethServer]
- Firewall: support DNS/DHCP objects in firewall rules - Enhancement #2775 [NethServer]
- Firewall: support objects on port forward and traffic shaping rules - Enhancement #2774 [NethServer]
- Merge nethserver-shorewall and nethserver-firewall-base - Enhancement #2771 [NethServer]
- Firewall: allow and deny access to local services - Enhancement #2752 [NethServer]
- Firewall: rules to divert traffic via specific provider - Feature #2740 [NethServer]
- Web UI: advanced network configuration  - Feature #2719 [NethServer]
- Custom firewall rules - Feature #2716 [NethServer]
- Firewall: select default policy - Feature #2714 [NethServer]
- Firewall: support custom objects - Feature #2705 [NethServer]
- Firewall-base: add support for multi-wan - Feature #2332 [NethServer]
- IDS/IPS (snort) - Feature #1771 [NethServer]

* Wed Feb 26 2014 Davide Principi <davide.principi@nethesis.it> - 1.1.0-1.ns6
- Allow to specify a port range in firewall rules - Feature #2644 [NethServer]

* Wed Feb 05 2014 Davide Principi <davide.principi@nethesis.it> - 1.0.9-1.ns6
- Update all inline help documentation - Task #1780 [NethServer]

* Wed Jan 15 2014 Davide Principi <davide.principi@nethesis.it> - 1.0.8-1.ns6
- Firewall ping response - Enhancement #2571 [NethServer]

* Mon Dec 23 2013 Davide Principi <davide.principi@nethesis.it> - 1.0.7-1.ns6
- Shorewall not configured, fails to start - Bug #2338 [NethServer]

* Tue Oct 22 2013 Davide Principi <davide.principi@nethesis.it> - 1.0.6-1.ns6
- VPN: support IPsec/L2TP - Feature #1957 [NethServer]

* Wed Aug 28 2013 Davide Principi <davide.principi@nethesis.it> - 1.0.5-1.ns6
- Traffic shaping: refactor templates to correctly handle traffic priorities - Enhancement #2112 [NethServer]
- Update NetworksDB on udev events - Enhancement #2075 [NethServer]

* Mon Jul 29 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.4-1.ns6
- Fix peerdns options usage #2057
- Use new shorewall syntax for COMMENT and FORMAT 

* Mon Jul 15 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.3-1.ns6
- Wnhance DHCP configuration on red interfaces  #2057

* Tue May 07 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.2-1.ns6
- NetworkAdapter UI module: validate values from request instead of values from DB

* Tue Apr 30 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.1-1.ns6
- Add sudoers fragment for shorewall-check script

* Tue Apr 30 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.0-1.ns6
- First release. #1887
