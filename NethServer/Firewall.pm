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
use esmith::util;
use NetAddr::IP;

use Exporter qw(import);
our @EXPORT_OK = qw(FIELDS_READ FIELDS_WRITE);

=head2 new

Create a NethServer::Firewall instance.

=cut
sub new
{
    my $class = shift;
    my $sdb_path = shift || '';
    my $ndb_path = shift || '';
    my $hdb_path = shift || '';

    my $self = {
        sdb_path => $sdb_path,
        ndb_path => $ndb_path,
        hdb_path => $hdb_path
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
        } elsif ( $db eq 'zone' ) {
            if ($expand_zone) {
                return $self->getZoneCIDR($key);
            } else { 
                if ($key eq 'red') {
                    return "net";
                } elsif ($key eq 'green') {
                    return "loc";
                } else { 
                    return substr($key, 0, 5); # truncate zone name to 5 chars
                }
            }
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
    
    if ( $id =~ m/^\d+$/  || $id =~ m/^\d+(\-\d+)*$/ ) { # single port or range
        ($ports{'tcp'} = $id) =~ s/-/:/; # convert port range syntax
        ($ports{'udp'} = $id) =~ s/-/:/; # convert port range syntax
        return %ports; 
    }
    
    if ( $id =~ m/;/ ) { # lookup is needed
        my ($db, $key) = split(';', $id);
        if ( $db eq 'service' ) {
            my $service = $self->{'sdb'}->get($key);
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
    }

    return %ports;
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
    my $str = $value;

    if ( $value =~ m/,/ ) { # host group, pick the first one
        my @tokens = split(/,/, $value);
        $str = $tokens[0];
    }
    my $needle = NetAddr::IP->new($str);
    return $value unless defined($needle); # skip garbage

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
        my $bootproto = $i->prop('bootproto') || 'none';
        next unless ($bootproto eq 'none' || $bootproto eq 'static');
        my $haystack = NetAddr::IP->new($i->prop('ipaddr'),$i->prop('netmask'));
        if ($needle->within($haystack)) {
            if ($i->prop('role') eq 'red') {
                return "net:$value";
            } elsif ($i->prop('role') eq 'green') {
                return "loc:$value";
            } else {
                return substr($i->prop('role'), 0, 5).":$value"; # truncate zone name to 5 chars
            }
        }
    }


    # best guess: we don't know anything, it should be in net zone
    return "net:$value";
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
    foreach my $provider ( @list ) {
        my $status = $provider->prop('status') || 'disabled';
        next if ($status eq 'disabled');
        my $name = $provider->key;
        my $interface_name = $provider->prop('interface') || next;
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
        push(@hosts, $self->_getHostAddress($key));  
    }
    return join (',',@hosts);
}

