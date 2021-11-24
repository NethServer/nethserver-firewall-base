#!/usr/bin/perl

#
# Copyright (C) 2019 Nethesis S.r.l.
# http://www.nethesis.it - nethserver@nethesis.it
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
# along with NethServer.  If not, see COPYING.
#

use strict;
use warnings;
use esmith::NetworksDB;
use esmith::ConfigDB;
use NethServer::Firewall;
use List::Util qw[max];

sub read_provider_status
{
    my $interface = shift || return 0;
    my $file = "/var/lib/shorewall/$interface.status";
    if ( -f $file ) {
        open my $fh, '<', $file;
        my $content = do { local $/; <$fh> };
        chomp $content;
        if ($content eq "0") {
            return 1;
        } else {
            return 0;
        }
    }
    # assume provider is up if shorewall doesn't have a status file
    return 1;
}

sub read_addresses
{
    my $interface = shift;
    my $cidr = `/sbin/ip -o -4 address show $interface primary 2>/dev/null| head -1 | awk '{print \$4}'`;
    chomp $cidr;
    $cidr =~ /^(.*)\/(.*)$/;
    my $ipaddr = $1;
    my $ndb = esmith::NetworksDB->open_ro();
    my %props = $ndb->get($interface)->props;
    my $gw;

    if (exists($props{'bootproto'}) && $props{'bootproto'} eq 'dhcp') {
        # DHCP
        $gw = `/usr/bin/grep routers /var/lib/dhclient/dhclient--$interface.lease | /usr/bin/tail -n 1 | /usr/bin/awk '{print \$3}' | /usr/bin/cut -d ';' -f 1`;
    } elsif ($interface eq 'ppp0') {
        # PPPoE
        $gw = `/sbin/ip -4 route show dev ppp0 | /usr/bin/grep src | /usr/bin/awk '{print \$1}'`;
    } else {
        $gw = $ndb->get($interface)->prop('gateway');
    }
    chomp $gw;

    my %ret = ('cidr' => $cidr, 'ipaddr' => $ipaddr, 'gateway' => $gw);
    return \%ret;
}

sub read_netdata
{
    my $api = shift;
    my $output = `curl 'http://localhost:19999/$api' 2>/dev/null`;
    $output =~ s/null/0/g;
    return $output;
}

sub get_zone_name
{
    my $zone= shift || return '';
    my %roles = ('loc' => 'green', 'net' => 'red', 'blue' => 'blue', 'orang' => 'orange', 'all' => 'any');
    return $roles{$zone} ? $roles{$zone} : $zone;
}

sub get_target_info
{
    my $key = shift;
    my $fw = shift;
    my $expand = shift;
    my %ret = ("name" => $key, "type" => 'raw');
    if ($key eq 'fw' || $key eq 'any') {
        $ret{'type'} = $key;
    } elsif (index($key,';') >= 0) {
        my ($type, $name) = split(';', $key);
        $ret{'name'} = $name;
        $ret{'type'} = $type;

        if ($expand) {
            my $addr = $fw->getAddress($key, 1);
            my ($zone, $value) = split(":",$fw->getZone($addr));
            $ret{'zone'} = get_zone_name($zone);
            if ($type eq 'role') {
                my @interfaces = $fw->getZoneInterface($name);
                $ret{'Interfaces'} = \@interfaces;
            } elsif ($type eq 'zone') {
                $ret{'Network'} = $addr;
                $ret{'Interface'} = $fw->{'ndb'}->get_prop($name, 'Interface');
            } else {
                $ret{'IpAddress'} = $addr;
            }
        }
    }

    # figure out possible object type for raw keys
    if ($ret{'type'} eq 'raw') {
        if (index($ret{'name'}, '/') > 0) {
            $ret{'object'} = 'cidr';
        } else {
            $ret{'object'} = 'host';
        }
    }

    return \%ret;
}

sub get_time_info
{
    my $key = shift || return undef;
    my $db = shift;
    my $expand = shift;

    my ($t, $v) = split(";",$key);
    my %ret = ('name' => $v, 'type' => $t);
    if ($expand) {
        my $r = $db->get($v);
        if ($r) {
            my %tprops = $r->props;
            my @days = split(",",$tprops{'WeekDays'});
            $tprops{'WeekDays'} = \@days;
            %ret = (%ret, %tprops);
        }
    }

    return \%ret;
}

sub get_local_service_info
{
    my $key = shift || return undef;
    my $db = shift;
    my $expand = shift;

    my $service = $db->get($key);
    my %props = $service->props;
    my $props_names = join("",keys %props);
    return undef if ($props_names !~ /Port/);

    if ($expand) {
        my $proto = '';
        if ($props_names =~ m/UDP/ and $props_names =~ m/TCP/) {
            $proto = 'tcpudp';
        } elsif ($props_names =~ m/UDP/) {
            $proto = 'udp';
        } elsif ($props_names =~ m/TCP/) {
            $proto = 'tcp';
        }

        my @ports;
        foreach my $k (keys %props) {
            if ($k =~ m/Port/) {
                push(@ports, split(",",$props{$k}));
            }
        }

        return { 'name' => $key, 'type' => 'service', 'Protocol' => $proto, 'Ports' => \@ports, 'Description' => '' };
    } else {
        return { 'name' => $key, 'type' => 'service' };
    }
}

sub get_fwservice_service_info
{
    my $key = shift || return undef;
    my $db = shift;
    my $expand = shift;

    my %ret = ('name' => $key, 'type' => 'fwservice');
    if ($expand) {
        my $r = $db->get($key);
        if ($r) {
            my %tprops = $r->props;
            my @ports = split(",",$tprops{'Ports'});
            $tprops{'Ports'} = \@ports;
            %ret = (%ret, %tprops);
        }
    }

    return \%ret;
}

sub get_application_info
{
    my $key = shift;
    my $apps = list_applications();

    foreach (@$apps) {
        if ($_->{'id'} eq $key) {
            $_->{'type'} = 'application';
            return $_;
        }
    }

    return {"name" => $key, "id" => '-', "icon" => 'fa-circle', 'type' => 'application'};
}

sub get_service_info
{
    my $id = shift || return undef;
    my $fw = shift;
    my $expand = shift;

    if ($id eq 'any') {
        return {'name' => 'any', 'type' => 'fwservice'};
    }

    my ($db, $key) = split(';', $id);

    if ($db eq 'fwservice') {
        return get_fwservice_service_info($key, $fw->{'sdb'},$expand);
    } elsif ($db eq 'ndpi') {
        return get_application_info($key);
    } else {
        return get_local_service_info($key, $fw->{'cdb'},$expand);
    }
}


sub get_policy_type
{
    my $key = shift || return '';
    my %roles = ('loc' => 1, 'net' => 1, 'blue' => 1, 'orang' => 1, 'all' => 1, 'vpn' => 1, 'ivpn' => 1, 'ovpn' => 1, 'fw' => 1);
    return $roles{$key} ? 'role' : 'zone';
}

sub list_roles_for_fwrules
{
    my %roles;
    my $ndb = esmith::NetworksDB->open_ro();
    my $cdb = esmith::ConfigDB->open_ro();
    foreach ($ndb->get_all()) {
        my $role = $_->prop('role') || next;
        next if ($role =~ m/bridged|alias|slave|hotsp/);
        $roles{$role} = 1;
    }
    if ($cdb->get_prop('openvpn@host-to-net','status')) {
        $roles{'ovpn'} = 1;
        $roles{'vpn'} = 1;
    }
    if ($cdb->get_prop('ipsec','status')) {
        $roles{'ivpn'} = 1;
        $roles{'vpn'} = 1;
    }

    my @tmp = keys %roles;
    return \@tmp;
}

sub list_tc_rules
{
    my $expand = shift;
    my $filter = shift || 'provider';
    my $fw = new NethServer::Firewall();
    my @rules;
    my $max_pos = 0;
    foreach ($fw->getTcRules()) {
        my %props = $_->props;
        my ($t, $v) = split(";",$props{'Action'});

        next if ($t ne $filter);

        $props{'id'} = $_->key;

        $props{'Position'} = int($props{'Position'});
        $props{'Src'} = get_target_info($props{'Src'}, $fw, $expand);
        $props{'Dst'} = get_target_info($props{'Dst'}, $fw, $expand);
        $props{'Time'} = get_time_info($props{'Time'}, $fw->{'ftdb'}, $expand);
        $props{'Service'} = get_service_info($props{'Service'}, $fw, $expand);
        $max_pos = max($max_pos, $props{'Position'});

        push(@rules, \%props);
    }

    return {"status" => {"count" => scalar(@rules), "next" => $max_pos+1 }, "rules" => \@rules};
}

sub list_fw_rules
{
    my $expand = shift;
    my $skip_local = shift;
    my $fw = new NethServer::Firewall();
    my @rules;
    my $max_posRule = 0;
    my $max_posSeparator = 0;
    my $nextID = 0;
    my $next = 0;
    foreach ($fw->getRules()) {
        my %props = $_->props;

        if ($skip_local) {
            # skip rule to/from the firewall
            next if ($props{'Src'} eq 'fw' or $props{'Dst'} eq 'fw');
        } else {
            # skip rule not for the firewall itself
            next if ($props{'Src'} ne 'fw' and $props{'Dst'} ne 'fw');
        }

        $props{'id'} = $_->key;

        $props{'Src'} = get_target_info($props{'Src'}, $fw, $expand);
        $props{'Dst'} = get_target_info($props{'Dst'}, $fw, $expand);
        $props{'Time'} = get_time_info($props{'Time'}, $fw->{'ftdb'}, $expand);
        $props{'Service'} = get_service_info($props{'Service'}, $fw, $expand);
        $props{'Position'} = int($props{'Position'});
        $max_posRule = max($max_posRule, $props{'Position'});
        push(@rules, \%props);
    }
    foreach ($fw->getSeparators()) {
        my %props = $_->props;
        $props{'id'} = $_->key;
        $props{'Position'} = int($props{'Position'});
        $max_posSeparator = max($max_posSeparator, $props{'Position'});
        push(@rules, \%props);
    }
    $next = int(`/usr/libexec/nethserver/api/nethserver-firewall-base/lib/rules-next-id`);

    my @sorted = sort { $a->{'Position'} <=> $b->{'Position'} } @rules;

    return {"status" => {"count" => scalar(@rules), "next" => max($max_posRule, $max_posSeparator)+1, "nextID" => $next }, "rules" => \@sorted};
}

sub list_policies
{
    my $skip_local = shift;
    my @policies;
    open my $fh, '<', "/etc/shorewall/policy";
    if ($fh) {
        my $i = 1;
        while (my $row = <$fh>) {
            next if ($row =~ /^#/); # skip comments
            next if ($row =~ /^\s*$/); # skip empty lines
            chomp $row;
            my ($src, $dst, $action, $log) = split(/\s+/, $row, 4);
            if ($skip_local) {
                # skip policy to/from the firewall
                next if ($dst eq '$FW' or $src eq '$FW');
            } else {
                # skip policy not for the firewall itself
                next if ($dst ne '$FW' and $src ne '$FW');
            }
            push(@policies, {
                    'Log' => defined($log) ? $log : 'none',
                    'Time' => undef,
                    'Service' => undef,
                    'type' => 'policy',
                    'status' => 'enabled',
                    'Position' => $i,
                    'id' => 10000 + $i,
                    'Action' => lc($action),
                    'Dst' => {'name' => get_zone_name($dst), 'type' => get_policy_type($dst)},
                    'Src' => {'name' => get_zone_name($src), 'type' => get_policy_type($src)}
                });
            $i++;
        }
        close($fh);
    }

    return \@policies;
}


sub list_applications
{
    my @applications;
    # return if ndpi is not loaded
    return \@applications if (! -e "/proc/net/xt_ndpi/proto");
    my $json;
    {
        local $/; #Enable 'slurp' mode
        open my $fh, "<", "/usr/libexec/nethserver/api/nethserver-firewall-base/lib/ndpi-icons.json",  or return undef;
        $json = <$fh>;
        close $fh;
    }

    my $icons = decode_json($json);
    open my $fh, '<', "/proc/net/xt_ndpi/proto";
    if ($fh) {
        while (my $row = <$fh>) {
            next if ($row =~ /^#/);
            chomp $row;
            my ($id, $mark, $name, $hashtag, $counter) = split(/\s+/, $row, 6);
            my $icon = defined($icons->{lc($name)}) ? $icons->{lc($name)} : "fa-circle";
            push(@applications, {"name" => $name, "id" => $id, "icon" => $icon, "counter" => int($counter)});
        }
    }

    return \@applications;
}
1;
