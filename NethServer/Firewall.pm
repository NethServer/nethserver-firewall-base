#
# Copyright (C) 2012 Nethesis S.r.l.
# http://www.nethesis.it - support@nethesis.it
# 
# This script is part of NethServer.
# 
# NethServer is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License,
# or any later version.
# 
# NethServer is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with NethServer.  If not, see <http://www.gnu.org/licenses/>.
#

package NethServer::Firewall;

use strict;
use esmith::ConfigDB;
use esmith::NetworksDB;
use esmith::HostsDB;
use NethServer::Database::Ndpi;
use esmith::util;
use NetAddr::IP;
use Carp;
use File::Basename qw(dirname);
use Time::Local;

use Exporter qw(import);
our @EXPORT_OK = qw(FIELDS_READ FIELDS_WRITE register_callback);

=head1 NAME

NethServer::Firewall -- extensible module for firewall rules generation

=over

=back

=head1 DESCRIPTION

This modules implements many utilities and can determinate the zone of a given ip address
using the getZone function.

The module also defines an API to extend getZone function behavior.
For example, VPNs bring new zones. Each VPN package, using the API system,
can find if the given address belongs to their own zones.

Each package will be a "provider", implementing a special callback function.

To define a provider function, add a Perl module under
Firewall/ directory, with namespace prefix
NethServer::Firewall.

The callback function must:

    * return the name of the zone if the ip address belongs to a zone defined by the package
    * othwerwise, an empty string

=head1 USAGE

This is an example provider "Provider1" definition.

 package NethServer::Firewall::Provider1;
 use NethServer::Firewall qw(register_callback);

 register_callback(&provider1);

 sub provider1
 {
    my $value = shift;

    # return the name of the zone if $value is in my zone
    # return '' otherwise
 }

=over

=cut

my @callbacks = ();

sub _init {
    my $dir = dirname($INC{'NethServer/Firewall.pm'});
    foreach (glob qq($dir/Firewall/*.pm)) {
        require "$_";
    }
}

sub register_callback
{
    my $func = shift;
    my $order = shift;
    push @callbacks, [$func, $order || 50];
}

sub _run_callbacks
{
    my $value = shift;

    if( ! @callbacks) {
        _init();
    }
    foreach (sort { $a->[1] <=> $b->[1] } @callbacks) {
        my $ret = &{$_->[0]}($value);
        if ($ret ne '') {
            return $ret;
        }
    }

    return '';
}


=back

=head1 FUNCTIONS

=head2 new

Create a NethServer::Firewall instance.

=cut
sub new
{
    my $class = shift;
    my $sdb_path = shift || 'fwservices';
    my $ndb_path = shift || '';
    my $hdb_path = shift || '';
    my $fdb_path = shift || 'fwrules';
    my $cdb_path = shift || '';
    my $pfdb_path = shift || 'portforward';
    my $ftdb_path = shift || 'fwtimes';
    my $mdb_path = shift || 'macs';
    my $sepdb_path = shift || 'separators';

    my $self = {
        sdb_path => $sdb_path,
        ndb_path => $ndb_path,
        hdb_path => $hdb_path,
        fdb_path => $fdb_path,
        pfdb_path => $pfdb_path,
        ftdb_path => $ftdb_path,
        cdb_path => $cdb_path,
        mdb_path => $mdb_path,
        sepdb_path => $sepdb_path
    };
    bless $self, $class;
    $self->_initialize();
    
    return $self;
}


sub _initialize()
{
    my $self = shift;
    $self->{'sdb'} = esmith::ConfigDB->open_ro($self->{'sdb_path'});
    $self->{'ndb'} = esmith::NetworksDB->open_ro($self->{'ndb_path'});
    $self->{'hdb'} = esmith::HostsDB->open_ro($self->{'hdb_path'});
    $self->{'fdb'} = esmith::ConfigDB->open_ro($self->{'fdb_path'});
    $self->{'cdb'} = esmith::ConfigDB->open_ro($self->{'cdb_path'});
    $self->{'pfdb'} = esmith::ConfigDB->open_ro($self->{'pfdb_path'});
    $self->{'ftdb'} = esmith::ConfigDB->open_ro($self->{'ftdb_path'});
    $self->{'mdb'} = esmith::ConfigDB->open_ro($self->{'mdb_path'});
    $self->{'sepdb'} = esmith::ConfigDB->open_ro($self->{'sepdb_path'});
}

=head2 getAddress(id, expand_zone = 0)

Return the address value corresponding to given id.
If id matches a valid IP or CIDR syntax, simply return it.
Otherwise lookup for the id inside other databases and return the 
value of the key.

If expand_zone flag is set to 1, zone name will be replaced
with CIDR notation or interface name.

=cut
sub getAddress($)
{
    my $self = shift;
    my $id = shift;
    my $expand_zone = shift || 0;

    if ( lc($id) eq 'any') {
        return 'any+';
    }

    if ( lc($id) eq 'fw') {
        return '$FW';
    }

    if ( $id =~ m/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/ ) {
        return $id; # IP address
    }
    if ( $id =~ m/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])(\/(\d|[1-2]\d|3[0-2]))$/ ) {
        return $id; # CIDR address
    }
   
    if ( $id =~ m/;/ ) { # lookup is needed
        my ($db, $key) = split(';', $id);
        if ( $db eq 'host') {
            return $self->_getHostAddress($key);
        } elsif ( $db eq 'host-group' ) {
            return $self->_getHostGroupAddresses($key);
        } elsif ( $db eq 'role' ) {
            if ($key eq 'red') {
                return "net";
            } elsif ($key eq 'green') {
                return "loc";
            } elsif ($key eq 'vpn') {
                my @vpn;
                if ($self->{'cdb'}->get('openvpn@host-to-net')) {
                    push(@vpn,'ovpn');
                }
                if ($self->{'cdb'}->get('ipsec')) {
                    push(@vpn,'ivpn');
                }
                return join(',',@vpn);
            } else { 
                return substr($key, 0, 5); # truncate zone name to 5 chars
            }

        } elsif ( $db eq 'zone' ) {
            if ($expand_zone) {
                return $self->getZoneCIDR($key);
            } else { 
                return substr($key, 0, 5); # truncate zone name to 5 chars
            }
        } elsif ( $db eq 'cidr' ) {
            return $self->_getCidrAddress($key);
        } elsif ( $db eq 'iprange' ) {
            return $self->_getIpRangeAddress($key);
        } elsif ($db eq 'mac' ) {
            return $self->_getMacAddress($key);
        }
    } 

    return '';
}



=head2 getZoneInterface($zone)

Return a list of interfaces associated with given zone.

=cut

sub getZoneInterface($)
{
    my $self = shift;
    my $zone = shift || '';
    my @ret;

    if ($zone eq 'net') {
        $zone = 'red';
    }
    if ($zone eq 'loc') {
        $zone = 'green';
    }
    foreach my $i ($self->{'ndb'}->interfaces) {
        my $role = $i->prop('role') || next;
        if ($role eq $zone) {
            push(@ret, $i->key);
        }
    }

    return @ret;
}

=head2 getZoneCIDR($zone)

Return the CIDR rappresentation of given zone.

=cut

sub getZoneCIDR($)
{
    my $self = shift;
    my $zone = shift || '';

    return '' if ($zone eq '');

    my $z = $self->{'ndb'}->get($zone);
    return '' if (!defined($z));

    return $z->prop('Network');
}

=head2 isValidNdpiProtocol(protocol)

Return 1 if given protocol is listed in xt_ndpi kernel module,
return 0 otherwise.

=cut
sub isValidNdpiProtocol($)
{
    my $self = shift;
    my $protocol = shift;

    my %ndpi;
    tie %ndpi, 'NethServer::Database::Ndpi';

    return exists $ndpi{$protocol} ? 1 : 0;
}

sub __localToUtc($)
{
    my $self = shift;
    my $value = shift;

    my ($hh,$mm,) = split(':', $value);
    my ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
    my $time = timelocal( 0, $mm, $hh, $mday, $mon, $year );
    ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = gmtime($time);

    return sprintf("%02d:%02d", $hour, $min);
}

=head2 getTime(id)

Return the time string in UTC.
Return an '-' if the key doesn't exists;

=cut
sub getTime($)
{
    my $self = shift;
    my $key = shift;

    return '-' if ($key eq '');
    $key = (split(';',$key))[1];
    my $record = $self->{'ftdb'}->get($key) || return '-';
    my $week_days = $record->prop('WeekDays') || '';
    my $time_start = $record->prop('TimeStart') || '';
    my $time_stop = $record->prop('TimeStop') || '';
    my $str = 'utc';

    if  ($week_days) {
        $str .= "&weekdays=$week_days";
    }
    if ($time_start ne '') {
        $str .= "&timestart=".$self->__localToUtc($time_start);
    }
    if ($time_stop ne '') {
        $str .= "&timestop=".$self->__localToUtc($time_stop);
    }

    return $str;
}


=head2 getPorts(id)

Return the port value corresponding to given service id.
If id matches a valid port or port list syntax, simply return it.
Otherwise lookup for the id inside other databases and return an
hash containg port grouped by protocol.

Example:
{
   tcp => 1234
   udp => 1234,456:500
}

=cut
sub getPorts($)
{
    my $self = shift;
    my $id = shift;
    my %ports;
    
    if ( lc($id) eq 'any') {
        $ports{'-'} = '';
        return %ports; 
    }
    
    if ( $id =~ m/^\d+$/  || $id =~ m/^\d+(\-\d+)*$/ ) { # single port or range
        ($ports{'tcp'} = $id) =~ s/-/:/; # convert port range syntax
        ($ports{'udp'} = $id) =~ s/-/:/; # convert port range syntax
        return %ports; 
    }
    
    if ( $id =~ m/;/ ) { # lookup is needed
        my ($db, $key) = split(';', $id);
        my $service = undef;
        if ( $db eq 'fwservice' ) {
            $service = $self->{'sdb'}->get($key);
            return %ports unless defined($service);
            if ($service->prop('Protocol') eq 'tcpudp') {
                ($ports{'tcp'} = $service->prop('Ports')) =~ s/-/:/; # convert port range syntax
                ($ports{'udp'} = $service->prop('Ports')) =~ s/-/:/; # convert port range syntax
            } elsif ($service->prop('Protocol') eq 'tcp' || $service->prop('Protocol') eq 'udp') {
                ($ports{$service->prop('Protocol')} = $service->prop('Ports')) =~ s/-/:/; # convert port range syntax
            } else { # icmp
                $ports{$service->prop('Protocol')} = $service->prop('Ports');
            }
        }
        if ( $db eq 'service' ) {
            $service = $self->{'cdb'}->get($key);
            return %ports unless defined($service);
            my @tcp =  $service->prop('TCPPort');
            $ports{'tcp'} =  $service->prop('TCPPorts') || $service->prop('TCPPort') || '';
            $ports{'udp'} =  $service->prop('UDPPorts') || $service->prop('UDPPort') || '';
            delete $ports{'tcp'} if ($ports{'tcp'} eq '');
            delete $ports{'udp'} if ($ports{'udp'} eq '');
        }
    }

    return %ports;
}

=head2 getAliasZone(alias)

Return the zone of an alias by searching its parent.

=cut
sub getAliasZone($)
{
    my $self = shift;
    my $alias = shift;

    my $parent = (split(/:/,$alias))[0];
    $parent = $self->{'ndb'}->get($parent) || return 'net'; # parent not found
    my $role = $parent->prop('role');

    if ($role eq 'red') {
        return "net";
    } elsif ($role eq 'green') {
        return "loc";
    } else {
        return substr($role, 0, 5); # truncate zone name to 5 chars
    }
}

=head2 getZone(value)

Return the given value prefixed with its own zone.
Value can be an ip address, an host group or a CIDR subnet.
This function is used to create Shorewall rules file.

Example:
   $v = $fw->getZone('192.168.1.2');
   $v will be "loc:192.168.1.2"

=cut
sub getZone($)
{
    my $self = shift;
    my $value = shift;
    my $original_value = undef;
    
    if ( lc($value) eq 'any') {
        return 'any';
    }

    # sanitize the list:
    $value = join(",", grep { $_ ne '' } split(/,/, $value));

    # protect built-in zones from name resolution Refs #3056
    if ($value =~ /loc|net|blue|orang|ivpn|ovpn/) {
        return $value;
    }
    # protect extra zones from name resolution NethServer/dev#5625
    my $z = $self->{'ndb'}->get($value);
    if (defined($z) && $z->prop('type') eq 'zone') {
         return $z->key;
    }

    # ip range
    if ($value =~ /\d+\.\d+\.\d+\.\d+\-\d+\.\d+\.\d+\.\d+/) {
        $original_value = $value;
        my @tmp = split('-',$value);
        $value = $tmp[0];
    }

    # check mac address in Shorewall format
    if ($value =~ m/^\~([0-9a-fA-F][0-9a-fA-F]\-){5}([0-9a-fA-F][0-9a-fA-F])$/) {
        # reverse zone search inside macs db
        my $mk = substr($value, 1);
        $mk =~ s/\-/:/g;
        foreach my $m ($self->{'mdb'}->get_all_by_prop('type'=>'mac')) {
            if ($m->prop('Address') eq $mk && $m->prop('Zone') ne '') {
                # translate zone name for Shorewall syntax
                my %zones = $self->listZones();
                my $z = $zones{$m->prop('Zone')};
                return $z.':'.$value;
            }
        }
    }

    # host group or not: always pick the first element:
    my $needle = NetAddr::IP->new((split(/,/, $value))[0]);
    return $value unless defined($needle); # skip garbage

    # restore original value if needed
    if ($original_value) {
        $value = $original_value;
    }

    # check zones
    my @zones =  $self->{'ndb'}->zones;
    foreach my $z (@zones) { 
        next unless ($z->prop('Network') ne '');
        my $haystack = NetAddr::IP->new($z->prop('Network'));
        if ($needle->within($haystack)) {
            return $z->key.":$value";
        }
    }

    # check interfaces
    my @interfaces = $self->{'ndb'}->interfaces;
    foreach my $i (@interfaces) {
        my $role = $i->prop('role') || next;
        my $ipaddr = $i->prop('ipaddr') || next;
        my $netmask = $i->prop('netmask') || next;
        my $bootproto = $i->prop('bootproto') || 'none';
        next unless ($bootproto eq 'none' || $bootproto eq 'static');
        my $haystack = NetAddr::IP->new($ipaddr,$netmask);
        if ($needle->within($haystack)) {
            if ($i->prop('role') eq 'red') {
                return "net:$value";
            } elsif ($i->prop('role') eq 'green') {
                return "loc:$value";
            } elsif ($i->prop('role') eq 'alias') {
                return $self->getAliasZone($i->key).":$value";
            } else {
                return substr($i->prop('role'), 0, 5).":$value"; # truncate zone name to 5 chars
            }
        }
    }

    my $extra_zone = _run_callbacks($value);
    if ($extra_zone ne '') {
        return "$extra_zone:$value";
    }

    # best guess: we don't know anything, it should be in net zone
    return "net:$value";
}


#
#  Output mangle rule in compact format
#  { key1:value1, key2:value2, ... }
#
sub _compactRuleFormat($)
{
    my $self = shift;
    my $params = shift;

    my $str = "{";
    foreach my $key ( keys $params ) {
        $str .= $key.':'.$params->{$key}.', ';
    }
    $str = substr($str, 0, -2);
    $str .= "}";

    return $str."\n";
}

=head2 outMangleRule

Return the mangle rule(s) in Shorewall format.

=cut

sub outMangleRule($)
{
    my $self = shift;
    my $params = shift;

    my $service = $params->{'service'};
    my $str = "?COMMENT ".$params->{'comment'}."\n";
    delete($params->{'comment'});
    delete($params->{'service'});

    if ($self->isNdpiService($service)) {
        $params->{'test'} = $self->getNdpiMark($service).":C";
        $str .= $self->_compactRuleFormat($params);
    } else {
        my %ports = $self->getPorts($service);
        foreach my $protocol (keys %ports) {
            $params->{'proto'} =  $protocol || '-';
            $params->{'dport'} =  $ports{$protocol} || '-';
            $str .= $self->_compactRuleFormat($params);
         }
    }

    $str .= "\n?COMMENT\n\n"; # clear comment

    return $str;
}



=head2 outRule

Return the rule(s) in Shorewall format.

Fields:

1. ACTION
2. SOURCE
3. DEST
4. PROTO
5. DPORT
6. SPORT (unused)
7. ORIGDEST (unused)
8. RATE (unused)
9. USER (unused)
10. MARK (unused)
11. CONNLIMIT (unused)
12. TIME

=cut
sub outRule($)
{
    my $self = shift;
    my $params = shift;

    my $str = "?COMMENT ".$params->{'comment'}."\n";
    my $service = $params->{'service'};
    delete($params->{'service'});
    delete($params->{'comment'});
    if ($service ne '-') { # handle special services
        if ($self->isNdpiService($service)) {
            $params->{'mark'} = $self->getNdpiMark($service);
            $str .= $self->_compactRuleFormat($params);
        } else {
            my %ports = $self->getPorts($service);
            foreach my $protocol (keys %ports) {
                $params->{'proto'} =  $protocol || '-';
                $params->{'dport'} =  $ports{$protocol} || '-';
                $str .= $self->_compactRuleFormat($params);
            }

        }
    } else { # no service
        $str .= $self->_compactRuleFormat($params);
    }


    $str .= "\n?COMMENT\n\n"; # clear comment

    return $str;
}

=head2 listZones

Return an hash of zones with the following format:
 zone_name => shorewall_name

=cut

sub listZones($)
{
    my $self = shift;
    my %zones;
    foreach my $z ($self->{'ndb'}->get_all_by_prop('type' => 'zone')) {
        $zones{$z->key} = substr($z->key, 0, 5);
    }
    foreach my $i ($self->{'ndb'}->interfaces) {
        my $role = $i->prop('role') || next;
        my $m = substr($role, 0, 5);
        if ($role eq 'red') {
            $m = 'net';
        }
        if ($role eq 'green') {
            $m = 'loc';
        }
        $zones{$role} = $m;
    }
    
    return %zones;
}


=head2 isNdpiEnabled

Return 1 if the current xt_ndpi module is loaded
and ndpi kernel module has not failed the update.
Return 0 otherwise.

=cut

sub isNdpiEnabled
{
    return ( -d '/proc/net/xt_ndpi/') && ( ! -f '/var/run/ndpi-lock');
}


=head2 isNdpiService

Return 1 if the current service is a nDPI target, 0 otherwise

=cut

sub isNdpiService($)
{
    my $self = shift;
    my $key = shift;
    return ($key =~ /^ndpi;/);
}

=head2 getNdpiMark

Return ndpi mark

=cut

sub getNdpiMark($)
{
    my $self = shift;
    my $key = shift;
    tie my %udb, 'NethServer::Database::Ndpi';

    my $proto = $key;
    if ($key =~ /^ndpi;(.*)/) {
        $proto = $1;
    }
    my $mark  = esmith::db::db_get_prop(\%udb, $proto, 'mark') || return '';
    my $dec = hex($mark);
    $mark = sprintf("0x%X", $dec);
    return "$mark/0xff00";
}

=head2 isZone

Return 1 if the given key is a zone, 0 otherwise

=cut 

sub isZone($)
{
    my $self = shift;
    my $key = shift;
    my %zones = $self->listZones();
    my %reverse = reverse %zones;
    
    return 1 if (exists $zones{$key} || exists $reverse{$key});
    return 0;
}

=head2 getProviders

Return the provider list ordered by weight (descending order).
Each record has all database properties plus mask, number and name fields.
The mask field is ready to be used inside Shorewall configuration. 

Each entry is a reference to hash of properties.

=cut
sub getProviders
{
    my $self = shift;
    my @providers;
    my $number = 1;
    my @list = sort _sort_by_weight $self->{'ndb'}->get_all_by_prop('type' => 'provider'); # descending sort
    my %reds;
    foreach ($self->{'ndb'}->red()) {
        $reds{$_->key} = '';
    }
    foreach my $provider ( @list ) {
        my $name = $provider->key;
        my $interface_name = $provider->prop('interface') || next;
        # skip providers associated to non-existing red interfaces
        next if (!exists($reds{$interface_name}));
        my $weight = $provider->prop('weight') || "1";
        my $mask = "0x" . $number . "0000";
        my @mask_array = ('mask', $mask);
        my %props = $provider->props;
        $props{'mask'} = $mask;
        $props{'number'} = $number;
        $props{'name'} = $name;
        push(@providers, \%props);
        $number++;
    }
    return @providers;
}

sub _sort_by_weight 
{
     ($b->prop('weight') || '1') <=> ($a->prop('weight') || '1');
}


=head2 getRules

Return the rule list ordered by Position property (ascending order).
Each record has all database properties.

=cut
sub getRules
{
    my $self = shift;
    my @list;
    foreach ($self->{'fdb'}->get_all_by_prop('type' => 'rule')) {
        my $action = $_->prop('Action');
        next if ($action =~ /^provider;/ || $action =~ /^class;/); # skip tc rules
        push(@list,$_);
    }
    return sort _sort_by_position @list; # ascending sort
}

=head2 getSeparators

Return the separator list ordered by Position property (ascending order).
Each record has all database properties.

=cut
sub getSeparators
{
    my $self = shift;
    my @list;
    foreach ($self->{'sepdb'}->get_all_by_prop('type' => 'separator')) {
        push(@list,$_);
    }
    return sort _sort_by_position @list; # ascending sort
}

=head2 getTcRules

Return the tc rule list ordered by Position property (ascending order).
Each record has all database properties.

=cut
sub getTcRules
{
    my $self = shift;
    my @list;
    foreach ($self->{'fdb'}->get_all_by_prop('type' => 'rule')) {
        my $action = $_->prop('Action');
        if ($action =~ /^provider;/ || $action =~ /^class;/) { # skip traffic rules
            push(@list,$_);
        }
    }
    @list = sort _sort_by_position @list; # ascending sort
    return @list;
}


=head2 getBypassRules

Return the list of proxy bypasses by source or destination.
Each record has all database properties.

=cut
sub getBypassRules
{
    my $self = shift;
    my @list = $self->{'fdb'}->get_all_by_prop('type' => 'bypass-src');
    my @dst = $self->{'fdb'}->get_all_by_prop('type' => 'bypass-dst');

    push(@list,@dst);
    return @list;
}

=head2 getPortForwards

Return the list of port forward.
Each record has all database properties.

=cut
sub getPortForwards
{
    my $self = shift;
    my @list = $self->{'pfdb'}->get_all_by_prop('type' => 'pf');
    return @list;
}

=head2 getInterfaceFromIP

Return the name of the interfa connected to the given ip,
or undef if no interface can be found.

=cut
sub getInterfaceFromIP($)
{
    my $self = shift;
    my $value = shift;

    my $needle = NetAddr::IP->new($value);
    my @interfaces = $self->{'ndb'}->interfaces;
    foreach my $i (@interfaces) {
        my $ipaddr = $i->prop('ipaddr') || next;
        my $netmask = $i->prop('netmask') || next;
        my $bootproto = $i->prop('bootproto') || 'none';
        next unless ($bootproto eq 'none' || $bootproto eq 'static');
        my $haystack = NetAddr::IP->new($ipaddr,$netmask);
        if ($needle->within($haystack)) {
            return $i->key;
        }
    }
    return undef;
}

sub _sort_by_position 
{
     $a->prop('Position') <=> $b->prop('Position') ;
}


sub _getHostAddress($)
{
    my $self = shift;
    my $key = shift;
    
    my $record = $self->{'hdb'}->get($key);
    return '' unless defined($record);
    my $ip = $record->prop('IpAddress') || '';
    return $ip unless ($ip eq ''); # IP has precedence over MAC address
    return $record->prop('MacAddress') || '';
}
 

sub _getHostGroupAddresses($)
{
    my $self = shift;
    my $key = shift;

    my $record = $self->{'hdb'}->get($key);
    return '' unless defined($record);
    my $members = $record->prop('Members') || '';
    my @keys = split(',', $members);
    return '' unless (@keys) ;
    my @hosts = ();
    foreach my $key (@keys) {
        my $address = $self->_getHostAddress($key) || next;
        push(@hosts, $address);
    }
    return join (',',@hosts);
}


sub _getCidrAddress($)
{
    my $self = shift;
    my $key = shift;

    my $record = $self->{'hdb'}->get($key) || return '';
    my $address = $record->prop('Address') || '';
    return $address;
}

sub _getIpRangeAddress($)
{
    my $self = shift;
    my $key = shift;

    my $record = $self->{'hdb'}->get($key) || return '';
    my $start = $record->prop('Start') || return '';
    my $end = $record->prop('End') || return '';
    return $start.'-'.$end;
}

sub _getMacAddress($)
{
    my $self = shift;
    my $key = shift;

    my $record = $self->{'mdb'}->get($key) || return '';
    my $address = $record->prop('Address') || return '';
    $address = lc($address);
    $address =~ s/:/-/g;
    return '~'.$address;
}

=head2 countReferences(db, key)

Returns the number of references of the given <DB, key>. 
The object is searched inside one of following lists:
* firewall rules
* proxy bypasses
* traffic shaping rules
* port forwards
* source nat rules

=cut
sub countReferences($$)
{
    my $self = shift;
    my $dbName = shift;
    my $key = shift;

    my $typeMap = {
        'provider' => 'provider',
        'host-group' => 'host-group',
        'host' => 'host',
        'remote' => 'host',
        'local' => 'host',
        'fwservice' => 'fwservice',
        'zone' => 'zone',
        'ethernet' => 'role',
        'bridge' => 'role',
        'vlan' => 'role',
        'alias' => 'role',
        'bond' => 'role',
        'cidr', => 'cidr',
        'time', => 'time',
        'iprange' => 'iprange',
        'mac' => 'mac'
	};

    my $db = esmith::DB::db->open_ro($dbName);
    
    if( ! $db) {
	return ();
    }

    my $record = $db->get($key);

    if( ! $record) {
	return ();
    }

    my $type = $typeMap->{$record->prop('type')};

    if( ! $type) {
	carp "Unknown object type for $dbName:$key";
	return ();
    }

    my $target = $type . ';' . $key;
    my $found = 0;
    my @fwrules = $self->getRules();
    my @tcrules = $self->getTcRules(); 
    my @pfrules = $self->getPortForwards();
    my @bypass = $self->getBypassRules();
    push(@fwrules, @tcrules);
    push(@fwrules, @pfrules);
    push(@fwrules, @bypass);

    foreach my $rule (@fwrules) {
	my @props = qw(Src Dst DstHost Host Service Action Time);

	if ($type eq 'role') {
	    $target = 'role;' . $record->prop('role');
	}
        if ($type eq 'host') { # also check in tc rules using key
            if($rule->key eq $target) {
                $found++;
            }
        }

	foreach(@props) {
            my $prop = $rule->prop($_) || next;
	    if($prop eq $target) {
		$found++;
	    }
	}
    }

    # source nat rules
    my @aliases = $self->{'ndb'}->aliases;

    foreach my $alias (@aliases) {
        my $prop = $alias->prop('FwObjectNat') || next;
        my @snatObjects = split(',', $prop);

        foreach(@snatObjects) {
            if($_ eq $target) {
                $found++;
            }
        }
    }
    
    return $found;
}

1;
