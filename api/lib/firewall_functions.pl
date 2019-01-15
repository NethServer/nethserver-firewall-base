#!/usr/bin/perl

#
# Copyright (C) 2018 Nethesis S.r.l.
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

1;
