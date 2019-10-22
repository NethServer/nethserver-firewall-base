<?php

/*
 * Copyright (C) 2019 Nethesis S.r.l.
 * http://www.nethesis.it - nethserver@nethesis.it
 *
 * This script is part of NethServer.
 *
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License,
 * or any later version.
 *
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see COPYING.
 */

require_once("/usr/libexec/nethserver/api/lib/Helpers.php");


/*
 * Validate a firewall rules. Takes 2 arguments:
 *
 * - data: the object describing the rule
 * - type: the rule type, it must contain the name of the controller
 *
 * Valid values for 'type' are: rules, local-rules, wan, traffic-shaping
 * */
function validate_rule($data, $type) {

    $v = new LegacyValidator($data);
    $rulesdb = new EsmithDatabase('fwrules');
    $ndb = new EsmithDatabase('networks');
    $tdb = new EsmithDatabase('tc');
    $timesdb = new EsmithDatabase('fwtimes');

    # set validActions for each type of rule
    if ($type == 'rules' || $type == 'local-rules') {
        $validActions = array('accept', 'reject', 'drop');
    } else if ($type == 'wan') {
        $validActions = array_map( function ($x) {
            return 'provider;' . $x;
        }, array_keys($ndb->getAll('provider')));
    } else if ($type == 'traffic-shaping') {
        $validActions = array_map( function ($x) {
            return 'class;' . $x;
        }, array_keys($tdb->getAll('class')));
    }
    $v->declareParameter('Action',  $v->createValidator()->memberOf($validActions));

    # local rules: src or dst must be equal to fw
    if ($type =='local-rules') {
        if ($data['Src']['name'] != 'fw' && $data['Dst']['name'] != 'fw') {
            $v->addValidationError('Src', 'src_or_dst_must_be_fw', $data['Src']);
        }

        if ($data['Service'] &&  $data['Dst']['name'] == 'fw') {
            $sdb = new EsmithDatabase('configuration');
            $fsdb = new EsmithDatabase('fwservices');
            if (($data['Service']['type'] != 'service' && $data['Service']['type'] != 'fwservice') || (!$sdb->getKey($data['Service']['name']) && !$fsdb->getKey($data['Service']['name']))) {
                $v->addValidationError('Service', 'must_be_local_service', $data['Service']);
            }
        }
    }

    # checks for divert and tc rules
    if (strpos($data['Action'],'provider;') !== FALSE || strpos($data['Action'],'class;') !== FALSE) {
        if ($data['Src']['name'] == 'red' && $data['Src']['type'] == 'role') {
            $v->addValidationError('Src', 'role_no_red', $data['Src']);
        }

        if ($data['Dst']['type'] == 'role') {
            if ($data['Dst']['name'] != 'red') {
                $v->addValidationError('Dst', 'role_only_red', $data['Dst']);
            }
        } else {
            if (($data['Dst']['type'] != 'raw') && (! in_array($data['Dst']['type'], array('host', 'iprange', 'zone', 'cidr')))) {
                $v->addValidationError('Dst', 'valid_types_zone_host_iprange_cidr', $data['Dst']);
            }
        }
    }


    # Checks for ndpi service
    if ($data['Service'] && @strpos($data['Service']['name'],'ndpi;') !== FALSE) {
        if ($data['Src'] == 'any' || $data['Dst'] == 'any') {
            $v->addValidationError('Service', 'any_not_allowed', $data['Service']);
        }
        if ($data['Src']['name'] == 'fw' || $data['Dst']['name'] == 'fw') {
            $v->addValidationError('Service', 'fw_not_allowed', $data['Service']);
        }
        if ($data['Action'] == 'reject') {
            $v->addValidationError('Service', 'reject_not_allowed', $data['Service']);
        }
        if ($type == 'wan') {
            $v->addValidationError('Service', 'divert_not_allowed', $data['Service']);
        }
    }

    # check for id on creation/update
    if ($data['action'] == 'create-rule') {
        if ($rulesdb->getKey($data['id'])) {
            $v->addValidationError('id', 'rule_exists', $data['id']);
        }
    } else if ($data['action'] == 'update-rule') {
        if (! $rulesdb->getKey($data['id'])) {
            $v->addValidationError('id', 'rule_not_exists', $data['id']);
        }
    }

    # Check raw value. Eg: {"name": "192.168.1.1", "type": "raw"}
    $ip_v = $v->createValidator(Validate::IPv4);
    $cidr_v = $v->createValidator(Validate::CIDR_BLOCK);
    $ip_cidr_v = $v->createValidator()->orValidator($ip_v, $cidr_v);
    if ($data['Src']['type'] == 'raw') {
        if (!$ip_cidr_v->evaluate($data['Src']['name'])) {
            $v->addValidationError('Src', 'valid_ip_or_cidr', $data['Src']);
        }
        $src = $data['Src']['name'];
    } else {
        $src = $data['Src']['type'].";". $data['Src']['name'];
    }
    if ($data['Dst']['type'] == 'raw') {
        if (!$ip_cidr_v->evaluate($data['Dst']['name'])) {
            $v->addValidationError('Dst', 'valid_ip_or_cidr', $data['Dst']);
        }
        $dst = $data['Dst']['name'];
    } else {
        $dst = $data['Dst']['type'].";". $data['Dst']['name'];
    }

    # Check if src and dst are on the same zone
    exec("/usr/libexec/nethserver/api/nethserver-firewall-base/lib/same-zone '$src' '$dst'", $output, $ret);
    if ($ret == 0) {
        $v->addValidationError('Dst', 'src_dst_are_in_same_zone', $data['Dst']);
    }

    $v->declareParameter('Position', Validate::POSITIVE_INTEGER);
    $v->declareParameter('status', Validate::SERVICESTATUS);


    if ($data['Log']) {
        $v->declareParameter('Log', $v->createValidator()->memberOf('none', 'info'));
    }

    if ($data['Time']) {
        if(!in_array($data['Time']['name'],array_keys($timesdb->getAll('time')))) {
            $v->addValidationError('Time', 'time_not_found', $data['Time']['name']);
        }
    }

    # Validate the input
    if ($v->validate()) {
        success();
    } else {
        error($v);
    }

}
