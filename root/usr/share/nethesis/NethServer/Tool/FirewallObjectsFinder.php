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
     * @var \IteratorAggregate
     */
    private $results;

    private function __construct(\IteratorAggregate $results)
    {
        $this->results = $results;
    }

    /**
     * @param \Nethgui\System\PlatformInterface $platform
     * @param string $text
     * @param string $where
     * @return \self
     */
    public static function search(\Nethgui\System\PlatformInterface $platform, $text = '', $where = array())
    {
        $results = array();
        $filter = NULL;

        if ($text) {
            $filter = function($key, $props) use ($text) {
                if ($text === '') {
                    return TRUE;
                }

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
                $results[] = new \NethServer\Tool\FirewallObject($key, $type, $props);
            }
        }

        return new self(new \ArrayObject($results));
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