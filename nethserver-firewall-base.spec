Summary: NethServer simple firewall
Name: nethserver-firewall-base
Version: 2.4.0
Release: 1%{?dist}
License: GPL
Group: Networking/Daemons
Source0: %{name}-%{version}.tar.gz
Packager: Giacomo Sanchietti <giacomo@nethesis.it>

BuildArch: noarch
Requires: nethserver-base > 2.5.2-1
Requires: nethserver-lsm
Requires: shorewall >= 4.6
Requires: ipset

Obsoletes: nethserver-shorewall
Provides: nethserver-firewall

BuildRequires: nethserver-devtools, gettext
AutoReq: no

%description
NethServer simple firewall


%prep
%setup -q

%build
%{makedocs}
for D in locale/*/LC_MESSAGES; do
  [ -d "$D" ] && msgfmt -v $D/%{name}.po -o $D/%{name}.mo
done
perl createlinks
mkdir -p root%{perl_vendorlib}
mv -v NethServer root%{perl_vendorlib}

%install
rm -rf $RPM_BUILD_ROOT
(cd root ; find . -depth -print | cpio -dump $RPM_BUILD_ROOT)
rm -f %{name}-%{version}-%{release}-filelist
/sbin/e-smith/genfilelist $RPM_BUILD_ROOT > %{name}-%{version}-%{release}-filelist
echo "%doc COPYING" >> %{name}-%{version}-%{release}-filelist
for F in locale/*/LC_MESSAGES/%{name}.mo; do
   install -D $F $RPM_BUILD_ROOT/%{_datadir}/$F
done
%{find_lang} %{name}

%post

%preun

%clean
rm -rf $RPM_BUILD_ROOT

%files -f %{name}-%{version}-%{release}-filelist -f %{name}.lang
%defattr(-,root,root)

%changelog
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
