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

sub read_provider_status
{
    my $interface = shift || return 0;
    my $file = "/var/lib/shorewall/$interface.status";
    if ( -f $file ) {
        open my $fh, '<', $file;
        my $content = do { local $/; <$fh> };
        chomp $content;
        return 1 if ($content == "0");
    }
    return 0;
}

sub read_addresses
{
    my $interface = shift;
    my $cidr = `/sbin/ip -o -4 address show $interface primary 2>/dev/null| head -1 | awk '{print \$4}'`;
    chomp $cidr;
    $cidr =~ /^(.*)\/(.*)$/;
    my $ipaddr = $1;
    my $gw = `ip -o route list dev $interface  | tail -n 1 | awk '{print \$1}'`;
    chomp $gw;

    my %ret = ('cidr' => $cidr, 'ipaddr' => $ipaddr, 'gateway' => $gw);
    return \%ret;
}

sub read_netdata
{
    my $api = shift;
    return `curl 'http://localhost:19999/$api' 2>/dev/null`;
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
    my %ret = ("name" => $key, "type" => $key);
    if ($key eq 'fw') {
        $ret{'type'} = 'fw';
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

sub get_service_info
{
    my $key = shift || return undef;
    my $db = shift;
    my $expand = shift;

    if ($key eq 'any') {
        return {'name' => 'any', 'type' => 'fwservice'};
    }

    my ($t, $v) = split(";",$key);
    my %ret = ('name' => $v, 'type' => $t);
    if ($expand) {
        my $r = $db->get($v);
        if ($r) {
            my %tprops = $r->props;
            my @ports = split(",",$tprops{'Ports'});
            $tprops{'Ports'} = \@ports;
            %ret = (%ret, %tprops);
        }
    }

    return \%ret;
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
    foreach ($fw->getTcRules()) {
        my %props = $_->props;
        my ($t, $v) = split(";",$props{'Action'});

        next if ($t ne $filter);

        $props{'id'} = $_->key;

        $props{'Position'} = int($props{'Position'});
        $props{'Src'} = get_target_info($props{'Src'}, $fw, $expand);
        $props{'Dst'} = get_target_info($props{'Dst'}, $fw, $expand);
        $props{'Time'} = get_time_info($props{'Time'}, $fw->{'ftdb'}, $expand);
        $props{'Service'} = get_service_info($props{'Service'}, $fw->{'sdb'}, $expand);

        push(@rules, \%props);
    }

    return \@rules;
}

sub list_fwrules
{
    my $expand = shift;
    my $skip_local = shift;
    my $fw = new NethServer::Firewall();
    my @rules;
    my $i = 1;
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
        $props{'Service'} = get_service_info($props{'Service'}, $fw->{'sdb'}, $expand);

        # normalize position
        $props{'Position'} = $i;

        push(@rules, \%props);
        $i++;
    }

    return \@rules;
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

1;
