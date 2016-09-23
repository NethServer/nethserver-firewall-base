<?php

namespace NethServer\Module;

/*
 * Copyright (C) 2016 Nethesis S.r.l.
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

use Nethgui\System\PlatformInterface as Validate;

/**
 * Display nDPI protocols
 *
 * @author Giacomo Sanchietti
 *
 */
class Ndpi extends \Nethgui\Controller\TableController
{

    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $base)
    {
        if ( file_exists ('/proc/net/xt_ndpi') ) {
            return \Nethgui\Module\SimpleModuleAttributesProvider::extendModuleAttributes($base, 'Status');
        } else {
            return \Nethgui\Module\SimpleModuleAttributesProvider::extendModuleAttributes($base);
        }
    }


    public function initialize()
    {
        $columns = array(
            'Key',
            'count',
        );

        $this
            ->setTableAdapter($this->getPlatform()->getTableAdapter('NethServer::Database::Ndpi'))
            ->setColumns($columns)
        ;

        parent::initialize();
    }

    private function getNdpiIcon($v)
    {
         if (array_key_exists($v, \NethServer\Module\FirewallRules\Index::$ndpiProtocolIcons)) {
             return \NethServer\Module\FirewallRules\Index::$ndpiProtocolIcons[$v][0];
         }
         return 'fa-square-o';
    }


    public function prepareViewForColumnKey(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        return ' <i class="fa fa-lg '.$this->getNdpiIcon($key).'" aria-hidden="true" style="margin: 5px"></i>' . $key;
    }

}
