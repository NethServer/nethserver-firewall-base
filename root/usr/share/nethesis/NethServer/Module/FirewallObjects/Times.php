<?php

namespace NethServer\Module\FirewallObjects;

/*
 * Copyright (C) 2014 Nethesis S.r.l.
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
 * Mange firewall Times.
 *
 * @author Giacomo Sanchietti
 *
 */
class Times extends \Nethgui\Controller\TableController
{

    public function initialize()
    {
        $columns = array(
            'Key',
            'Description',
            'Actions'
        );
        
        $this
            ->setTableAdapter($this->getPlatform()->getTableAdapter('fwtimes', 'time'))
            ->addRowAction(new \NethServer\Module\FirewallObjects\Times\Modify('update'))
            ->addRowAction(new \NethServer\Module\FirewallObjects\Times\Modify('delete'))
            ->addTableAction(new \NethServer\Module\FirewallObjects\Times\Modify('create'))
            ->addTableAction(new \Nethgui\Controller\Table\Help('Help'))
            ->setColumns($columns)
        ;

        parent::initialize();
    }

    function onParametersSaved(\Nethgui\Module\ModuleInterface $currentAction, $changes, $parameters)
    {
        if($currentAction->getIdentifier() === 'create') {
            return;
        }
        $this->getPlatform()->signalEvent('firewall-objects-modify &');
    }
}
