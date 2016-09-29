<?php

namespace NethServer\Tool;

/*
 * Copyright (C) 2014  Nethesis S.r.l.
 *
 * This script is part of NethServer.
 *
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Find existing firewall objects, by given search criteria
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class FirewallObjectsFinder implements \IteratorAggregate, \Countable
{
    /**
     *
     * @var \ArrayObject
     */
    private $results;

    /**
     *
     * @var callable
     */
    private $translator;

    private function __construct()
    {
        $this->results = new \ArrayObject();
    }

    /**
     * @param \Nethgui\System\PlatformInterface $platform
     * @param string $text
     * @param string $where
     * @return \self
     */
    public static function search(\Nethgui\System\PlatformInterface $platform, $text = '', $where = array(), $translator = NULL)
    {
        $o = new self();
        $o->translator = $translator;
        
        if (isset($where['ROLES'])) {
            $o->addSearchRoles($platform, $text);
            unset($where['ROLES']);
        }
        if(isset($where['SERVICES'])) {
            $o->addSearchServices($platform, $text);
            unset($where['SERVICES']);
        }
        $o->addSearchInDb($platform, $text, $where);
        
        return $o;
    }

    private function addSearchInDb(\Nethgui\System\PlatformInterface $platform, $text, $where)
    {
        $filter = NULL;

        if ($text) {
            $filter = function($key, $props) use ($text) {
                if (strstr(strtolower($key), strtolower($text)) !== FALSE) {
                    return TRUE;
                }

                foreach ($props as $p => $v) {
                    if (strstr(strtolower($v), strtolower($text)) !== FALSE) {
                        return TRUE;
                    }
                }

                return FALSE;
            };
        }

        foreach ($where as $dbName => $types) {
            foreach ($platform->getTableAdapter($dbName, $types, $filter) as $key => $props) {
                if (isset($props['type'])) {
                    $type = $props['type'];
                    unset($props['type']);
                } else {
                    $type = $types[0];
                }
                $this->results->append(new \NethServer\Tool\FirewallObject($key, $type, $props, $this->translator));
            }
        }
    }

    private function addSearchRoles(\Nethgui\System\PlatformInterface $platform, $text)
    {
        $tmp = array();
        foreach ($platform->getDatabase('networks')->getAll() as $key => $props) {
            if (isset($props['role'])) {
                $tmp[$props['role']] = '';
            }
        }
        $ovpn = $platform->getDatabase('configuration')->getProp('openvpn@host-to-net','status');
        $ivpn = $platform->getDatabase('configuration')->getProp('ipsec','status');
        if ($ovpn || $ivpn) {
            $tmp['vpn'] = '';
        }
        $roles = array_filter(array_diff(array_keys($tmp),array('bridged','alias','slave')));
        foreach ($roles as $role) {
            if ( ! $text || strstr($role, strtolower($text)) !== FALSE) {
                $this->results->append(new \NethServer\Tool\FirewallObject($role, 'role', array(), $this->translator));
            }
        }
    }

    private function addSearchServices(\Nethgui\System\PlatformInterface $platform, $text)
    {
        $searchProps = array('TCPPort', 'UDPPort', 'TCPPorts', 'UDPPorts');
        foreach ($platform->getDatabase('configuration')->getAll('service') as $key => $props) {
            $found = FALSE;
            foreach(\array_intersect($searchProps, \array_keys($props)) as $p) {
                if($props[$p] !== '') {
                    $found = TRUE;
                }
            }

            if ( $found && (! $text || strstr($key, strtolower($text)))) {
                $this->results->append(new \NethServer\Tool\FirewallObject($key, 'service', $props, $this->translator));
            }
        }
    }

    public function getIterator()
    {
        return $this->results->getIterator();
    }

    public function count()
    {
        return count($this->results);
    }

}
