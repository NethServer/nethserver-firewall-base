<?php

namespace NethServer\Module\FirewallRules;

/*
 * Copyright (C) 2015 Nethesis Srl
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Create a new rule, pre-filling the form fields with values from another rule
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 */
class Copy extends \NethServer\Module\FirewallRules\Create
{

    private $copyDefaults;
    
    protected function bindPosition(\Nethgui\Controller\RequestInterface $request)
    {
        $A = $this->getAdapter();
        $position = \Nethgui\array_head($request->getPath());
        $id = $request->spawnRequest($position)->getParameter('id');
        if(isset($id, $A[$id])) {
            $this->copyDefaults = \iterator_to_array($A[$id]);
        }
        return $position;
    }

    protected function getRuleDefaults()
    {
        $a = parent::getRuleDefaults();
        foreach (array(
            'Src' => 'SrcRaw',
            'Dst' => 'DstRaw',
            'Service' => 'ServiceRaw',
            'status' => 'status',
            'Description' => 'Description',
            'Log' => 'LogType',
            'Action' => 'Action'
            ) as $dbfield => $field) {
            if (isset($this->copyDefaults[$dbfield], $a[$field])) {
                $a[$field] = $this->copyDefaults[$dbfield];
            }
        }

        return $a;
    }

}
