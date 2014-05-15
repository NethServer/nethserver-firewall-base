#!/usr/bin/perl -w

use esmith::ConfigDB;
use esmith::NetworksDB;
use NethServer::Firewall;

use autodie;
use File::Temp();
use Test::More 'no_plan';


my $ndb = <<"END";
eth0=ethernet|hwaddr|00:xx:xx:27:d7:03|onboot|yes|role|green|device|eth0|gateway|192.168.q.254|ipaddr|192.168.1.1|netmask|255.255.255.0|bootproto|static
eth1=ethernet|bootproto|static|device|eth1|gateway|10.10.10.225|hwaddr|xx:xx:0B:2B:F7:A3|ipaddr|10.10.10.226|netmask|255.255.255.248|nm_controlled|yes|onboot|yes|peer_dns|n|peerdns|yes|persistent_dhclient|n|role|red
eth2=ethernet|DEFROUTE|no|bootproto|dhcp|device|eth2|gateway||hwaddr|yy:yy:0B:2B:F7:A4|ipaddr||netmask||nm_controlled|yes|onboot|yes|peerdns|no|persistent_dhclient|y|role|red
eth3=ethernet|hwaddr|00:xx:xx:zz:zz:03|onboot|yes|role|blue|device|eth3|gateway||ipaddr|192.168.10.1|netmask|255.255.0.0|bootproto|static
provider1=provider|Description|Provider1|checkip|1.2.3.4|interface|eth1|status|enabled|weight|100
provider2=provider|Description|Provider1|checkip|1.2.3.5|interface|eth2|status|enabled|weight|10
zone1=zone|Network|192.168.2.0/24
END

my $cdb = <<"END";
dns=configuration|NameServers|8.8.8.8|role|resolver
firewall=configuration|ExternalPing|enabled|Policy|permissive|WanMode|balance|event|nethserver-firewall-base-save|level|high|nfqueue|enabled|tc|Simple
https=fservice|Description||Ports|443,446,980|Protocol|tcp
tcp1=fservice|Description||Ports|25|Protocol|tcp
udp1=fservice|Description||Ports|1,2,3|Protocol|udp
udp2=fservice|Description||Ports|10-20|Protocol|udp
both1=fservice|Description|Both|Ports|10-20|Protocol|tcpudp
both2=fservice|Description|Both|Ports|10|Protocol|tcpudp
squid=service|TCPPorts|3128,3129,3130|access|private|status|enabled
sshd=service|TCPPort|222|access|public|status|enabled
ping=fservice|Ports||Protocol|icmp
END

my $hdb = <<"END";
hg1=host-group|Description|HG1|Members|local1,local2
hg2=host-group|Description|HG1|Members|local1
local1=local|Description|Host 1|IpAddress|192.168.1.1|MacAddress|xx:yy:3E:2A:9A:A0
local2=local|Description|Host 1|IpAddress|192.168.1.2
remote1.mydomain.tld=remote|Description|Remote1|IpAddress|7.7.4.4|MacAddress|
alias1.mydomain.tld=self|Description|Alias 1
END



sub write_db
{
    my $str = shift;
    my $tmp = File::Temp->new( UNLINK => 0, SUFFIX => 'db' );
    print $tmp $str;
    return $tmp->filename;
}


my $cdb_file = write_db($cdb);
my $ndb_file = write_db($ndb);
my $hdb_file = write_db($hdb);

my $fw = NethServer::Firewall->new($cdb_file, $ndb_file, $hdb_file);
ok( defined $fw, 'fw instance' );
is( $fw->getAddress('192.168.1.2'),  '192.168.1.2', 'IP' );
is( $fw->getAddress('192.168.'),  '', 'BAD IP' );
is( $fw->getAddress('192.168.1.0/24'),  '192.168.1.0/24', 'CIDR' );
is( $fw->getAddress('192.168.1.0/255.255.255'),  '', 'BAD CIDR' );
is( $fw->getAddress('not-existing'),  '', 'not-existing' );
is( $fw->getAddress('host;local1'),  '192.168.1.1', 'host;local1' );
is( $fw->getAddress('host;remote1.mydomain.tld'),  '7.7.4.4', 'host;remote1.mydomain.tld' );
is( $fw->getAddress('host;alias1.mydomain.tld'),  '', 'host;alias1.mydomain.tld' );
is( $fw->getAddress('zone;green'),  'loc', 'zone;green' );
is( $fw->getAddress('zone;red'),  'net', 'zone;net' );
is( $fw->getAddress('zone;orange'),  'orang', 'zone;orange' );
is( $fw->getAddress('zone;myfirstzone'),  'myfir', 'zone;myfirstzone' );
is( $fw->getAddress('host-group;hg1'),  '192.168.1.1,192.168.1.2', 'host-group;hg1' );
is( $fw->getAddress('host-group;hg2'),  '192.168.1.1', 'host-group;hg2' );
is( $fw->getAddress('host-group;not-existing'),  '', 'host-group;not-existing' );
is( $fw->getAddress('zone;zone1',1),  '192.168.2.0/24', 'zone;zone1 to network' ); # address

my %h = ('tcp' => '222');
is( $fw->getPorts('service;sshd'),  %h, 'service;sshd' );
%h = ('tcp', '443,446,980');
is( $fw->getPorts('service;https'),  %h, 'service;https' );
%h = ('tcp', '25');
is( $fw->getPorts('service;tcp1'),  %h, 'service;tcp1' );
%h = ('udp', '1,2,3');
is( $fw->getPorts('service;udp1'),  %h, 'service;udp1' );
%h = ('udp', '10-20');
is( $fw->getPorts('service;udp2'),  %h, 'service;udp2' );
%h = ('udp', '10:20', 'tcp', '10:20');
is( $fw->getPorts('service;both1'),  %h, 'service;both1' );
is( $fw->getPorts('10-20'),  %h, 'service;implicit-range' );
%h = ('udp', '10', 'tcp', '10');
is( $fw->getPorts('service;both2'),  %h, 'service;both2' );
is( $fw->getPorts('10'),  %h, 'service;implicit-simple' );
%h = ('tcp', '3128,3129,3130');
is( $fw->getPorts('service;squid'),  %h, 'service;squid' );
%h = ('icmp', '');
is( $fw->getPorts('service;ping'),  %h, 'service;ping' );

is( $fw->getZoneCIDR('zone1'), '192.168.2.0/24', 'zone1 cidr' );
is( $fw->getZoneCIDR('zone2'), '', 'zone non existing' );
my @z = ('eth1','eth2');
is( $fw->getZoneInterface('red'), @z, 'zone red' );
is( $fw->getZoneInterface('net'), @z, 'zone net' );
@z = ('eth0');
is( $fw->getZoneInterface('green'), @z, 'zone green' );
is( $fw->getZoneInterface('loc'), @z, 'zone loc' );

is( $fw->getZone('192.168.10.2'), 'blue:192.168.10.2', 'getZone blue' );
is( $fw->getZone('10.10.10.226'), 'net:10.10.10.226', 'getZone net' );
is( $fw->getZone('192.168.1.123'), 'loc:192.168.1.123', 'getZone green' );
is( $fw->getZone('6.6.6.6'), 'net:6.6.6.6', 'getZone net unknown' );

%h = ('red', 'net', 'green', 'loc', 'blue', 'blue');
is( $fw->listZones(), %h, 'listZones' );
is( $fw->isZone('red'), 1, 'isZone(red)' );
is( $fw->isZone('yellow'), 0, 'isZone(yellow)' );

unlink($cdb_file);
unlink($ndb_file);
unlink($hdb_file);
