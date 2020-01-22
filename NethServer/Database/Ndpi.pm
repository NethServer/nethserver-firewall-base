#
# Copyright (C) 2016 Nethesis S.r.l.
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
package NethServer::Database::Ndpi;
require Tie::Hash;
our @ISA = 'Tie::StdHash';

my $db_file = '/proc/net/xt_ndpi/proto';

sub TIEHASH {
    my $storage = bless _read_db(), shift;
    return $storage;
}

sub _read_db
{
    my $db = {};
    open (my $fh, '<', $db_file) or return {};
    my $line = <$fh>; # skip 1st line
    while ($line = <$fh>) {
        chomp($line);
        my ($id, $mark, $mask, $name, $count) = split(m([\s#/]+), $line);
        next if ($mark eq 'disabled');
        $db->{$id} = "ndpi|name|$name|mark|$mark|mask|$mask|count|$count";
    }
    close($fh);
    return $db;
}

1;
