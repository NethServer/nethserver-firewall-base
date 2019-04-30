Summary: NethServer firewall implementation based on Shorewall
Name: nethserver-firewall-base
Version: 3.6.0
Release: 1%{?dist}
License: GPL
Source0: %{name}-%{version}.tar.gz
Source1: %{name}.tar.gz
URL: %{url_prefix}/%{name}
BuildArch: noarch

Requires: nethserver-base
Requires: nethserver-lsm
Requires: shorewall >= 4.6
Requires: ipset
Requires: rp-pppoe
Requires: firehol

Obsoletes: nethserver-shorewall
Provides: nethserver-firewall

BuildRequires: nethserver-devtools

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

for _nsdb in fwservices portforward ; do
   mkdir -p root/%{_nsdbconfdir}/${_nsdb}/{migrate,force,defaults}
done


%install
rm -rf %{buildroot}
(cd root ; find . -depth -print | cpio -dump %{buildroot})

mkdir -p %{buildroot}/usr/share/cockpit/%{name}/
mkdir -p %{buildroot}/usr/share/cockpit/nethserver/applications/
mkdir -p %{buildroot}/usr/libexec/nethserver/api/%{name}/
tar xvf %{SOURCE1} -C %{buildroot}/usr/share/cockpit/%{name}/
cp -a %{name}.json %{buildroot}/usr/share/cockpit/nethserver/applications/
cp -a api/* %{buildroot}/usr/libexec/nethserver/api/%{name}/

%{genfilelist} %{buildroot} > %{name}-%{version}-%{release}-filelist
grep -e php$ -e rst$ -e html$ -e cockpit %{name}-%{version}-%{release}-filelist > %{name}-%{version}-%{release}-filelist-ui
grep -v -e /usr/share/nethesis/NethServer -e cockpit %{name}-%{version}-%{release}-filelist > %{name}-%{version}-%{release}-filelist-core

%files -f %{name}-%{version}-%{release}-filelist-core
%defattr(-,root,root)
%doc COPYING
%doc README.rst
%dir %{_nseventsdir}/%{name}-update
%dir %{_nsdbconfdir}/fwservices
%dir %{_nsdbconfdir}/portforward


%changelog
* Tue Apr 09 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.6.0-1
- Cockpit: basic firewall configuration - NethServer/dev#5695

* Wed Dec 05 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.5.0-1
- Firewall: update to nDPI-netfilter-2.2 and nDPI-2.4  - NethServer/dev#5645
- Server unreachable after creating a logical interface - Bug NethServer/dev#5637
- PPPoE: use high speed plugin - NethServer/dev#5630

* Tue Nov 20 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.4.3-1
- Ingress QoS not working for squid (http) traffic - Bug NethServer/dev#5642
- Firewall: bad zone resolution - Bug NethServer/dev#5625
- Server unreachable after creating a logical interface - Bug NethServer/dev#5637

* Wed Aug 29 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.4.2-1
- Refactor provider template: allow override of provider options

* Fri Jun 15 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.4.1-1
- Firewall objects: services with port range - NethServer/dev#5531
- Firewall: log port forwarding - NethServer/dev#5529

* Wed May 16 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.4.0-1
- Change of defaults for NS 7.5 - NethServer/dev#5490
- nDPI: support CentOS 7.5 - NethServer/dev#5482
- Advanced traffic shaping (QoS) - NethServer/dev#5484

* Thu Apr 26 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.3.2-1
- Shorewall: remove obsolete masq file - NethServer/dev#5467

* Mon Mar 12 2018 Davide Principi <davide.principi@nethesis.it> - 3.3.1-1
- Revert "shorewall.conf: set AUTOMAKE to Yes" - NethServer/nethserver-firewall-base#62

* Fri Mar 09 2018 Davide Principi <davide.principi@nethesis.it> - 3.3.0-1
- Update to shorewall 5.1 - NethServer/dev#5429
- Multi WAN shorewall warning DEFAULT_ROUTE - NethServer/dev#5431

* Mon Feb 05 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.2.11-1
- Firewall: invalid source NAT to host with DHCP reservation - Bug NethServer/dev#5417

* Thu Dec 07 2017 Filippo Carletti <filippo.carletti@gmail.com> - 3.2.10-1
- shorewall: some netfilter helpers not loaded - Bug NethServer/dev#5385

* Fri Nov 24 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.2.9-1
- shorewall: some netfilter helpers not loaded - Bug NethServer/dev#5385

* Mon Nov 13 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.2.8-1
- Shorewall config error with multiple sshd instances - Bug NethServer/dev#5380

* Mon Oct 16 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.2.7-1
- PPPoE connect timeout - NethServer/dev#5358

* Mon Oct 09 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.2.6-1
- Relax lsm ping packets defaults
- Fix mangle warnings

* Fri Sep 08 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.2.5-1
- Shorewall config error with X11 forwarding - Bug NethServer/dev#5336
- NS 6 upgrade: avoid restore-data when possible - NethServer/dev#5343

* Mon Jul 17 2017 Davide Principi <davide.principi@nethesis.it> - 3.2.4-1
- Traffic shaping not applied on new installations - Bug NethServer/dev#5333

* Thu Jul 06 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.2.3-1
- Set Monday as first day of week in Firewall - NethServer/dev#5326

* Fri Jun 30 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.2.2-1
- Port forward: display Allow field in table - NethServer/nethserver-firewall-base#50

* Tue May 16 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.2.1-1
- shorewall: load Connection Tracking helpers - Bug NethServer/dev#5279

* Wed May 10 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.2.0-1
- Upgrade from NS 6 via backup and restore - NethServer/dev#5234

* Wed Apr 26 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.1.8-1
- Bad port forward to host inside a green alias network - Bug NethServer/dev#5274

* Mon Apr 10 2017 Davide Principi <davide.principi@nethesis.it> - 3.1.7-1
- Upgrade from NS 6 via backup and restore - NethServer/dev#5234
- PPPoE backup connection restart failure - Bug NethServer/dev#5260

* Thu Jan 19 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.1.6-1
- Firewall web interface: wrong selected time condition - Bug NethServer/dev#5200

* Tue Jan 17 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.1.5-1
- Console message: failed to register logger - NethServer/dev#5202

* Mon Jan 16 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.1.4-1
- DC: restore configuration fails - Bug NethServer/dev#5188
- Exhibit bad network configuration - NethServer/dev#5193

* Wed Jan 11 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.1.3-1
- Firewall services not initialized on install - Bug NethServer/dev#5185

* Mon Oct 17 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.1.2-1
- Unable to make FTP connection with virtualhost - Bug NethServer/dev#5127

* Tue Oct 04 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.1.1-1
- Firewall: can't create a rule with "vpn" role - Bug NethServer/dev#5119

* Wed Sep 28 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.1.0-1
- Enchance traffic shaping - NethServer/dev#5113
- nDPI support: deep packet inspection - NethServer/dev#5102
- Firewall: time rules - NethServer/dev#5107
- Replace Snort with Suricata - NethServer/dev#5104

* Thu Sep 01 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.0.4-1
- Missing i18n labels - Bug NethServer/dev#5094
- Fix shorewall error on divert rules from firewall - Bug NethServer/dev#5091

* Thu Aug 25 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.0.3-1
- Port forward from VLAN red - Bug NethServer/dev#5087

* Fri Aug 05 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.0.2-1
- Blank checkbox on FirewallRules/EditService page - Bug NethServer/dev#5070

* Thu Jul 21 2016 Stefano Fancello <stefano.fancello@nethesis.it> - 3.0.1-1
- Network services does not enforce "localhost" access - Bug NethServer/dev#5066
- VPS: can't access public services  - Bug NethServer/dev#5068

* Thu Jul 07 2016 Stefano Fancello <stefano.fancello@nethesis.it> - 3.0.0-1
- First NS7 release

* Thu May 26 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.10.5-1
- PPPoE backup connection restart failure - Bug #3394 [NethServer]
- Can't create host groups with certain hosts - Bug #3392 [NethServer]
- Multiwan: provider with space in name breaks firewall configuration - Bug #3388 [NethServer]

* Wed Apr 27 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.10.4-1
- Hairpin NAT from blue and orange - Enhancement #3380 [NethServer]

* Fri Mar 04 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.10.3-1
- Capitalized dhcp hostname can't be added to host groups - Bug #3342 [NethServer]
- Invalid TCP port range - Bug #3333 [NethServer]

* Thu Feb 18 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.10.2-1
- Cannot use main as provider name - Bug #3346 [NethServer]

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
