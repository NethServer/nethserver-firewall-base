<?php
namespace NethServer\Module\FirewallObjects;

/*
 * Copyright (C) 2011 Nethesis S.r.l.
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
 * Mange firewall services.
 *
 * @author Giacomo Sanchietti
 *
 */
class Services extends \Nethgui\Controller\TableController
{

    public function initialize()
    {
        $columns = array(
            'Key',
            'Protocol',
            'Ports',
            'Actions'
        );
        
        $p = $this->getPlatform();
        $parameterSchema = array(
            array('name', Validate::USERNAME, \Nethgui\Controller\Table\Modify::KEY),
            array('Protocol', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
            array('Description', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
            array('Ports', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD)
        );

        $this
            ->setTableAdapter($this->getPlatform()->getTableAdapter('configuration', 'fservice'))
            ->addRowAction(new \NethServer\Module\FirewallObjects\Services\Modify('update'))
            ->addRowAction(new \Nethgui\Controller\Table\Modify('delete', $parameterSchema, 'Nethgui\Template\Table\Delete')) // Standard DELETE template
            ->addTableAction(new \NethServer\Module\FirewallObjects\Services\Modify('create'))
            ->addTableAction(new \Nethgui\Controller\Table\Help('Help'))
            ->setColumns($columns)
        ;

        parent::initialize();
    }

}

