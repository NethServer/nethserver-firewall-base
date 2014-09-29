<?php
namespace NethServer\Module;

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
 * Implement gui module for providers configuration
 */
class Providers extends \Nethgui\Controller\TableController
{
    private $interfaces;

    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $base)
    {
        return \Nethgui\Module\SimpleModuleAttributesProvider::extendModuleAttributes($base, 'Gateway', 80);
    }


    public function initialize()
    {
        $columns = array(
            'Key',
            'interface',
            'weight',
            'Description',
            'Actions',
        );

        $parameterSchema = array(
            array('name', Validate::USERNAME, \Nethgui\Controller\Table\Modify::KEY),
            array('checkip', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
            array('interface', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
            array('weight', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
            array('status', Validate::SERVICESTATUS, \Nethgui\Controller\Table\Modify::FIELD),
            array('Description', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
        );

        $this
            ->setTableAdapter($this->getPlatform()->getTableAdapter('networks', 'provider'))
            ->setColumns($columns)
            ->addRowAction(new \NethServer\Module\Providers\Modify('update'))
            ->addRowAction(new \Nethgui\Controller\Table\Modify('delete', $parameterSchema, 'Nethgui\Template\Table\Delete')) // Standard DELETE template
            ->addTableAction(new \NethServer\Module\Providers\Modify('create'))
            ->addTableAction(new \NethServer\Module\Providers\Configure())
            ->addTableAction(new \Nethgui\Controller\Table\Help('Help'))
        ;

        parent::initialize();
    }

    private function readInterfaces() {
        $ret = array();
        $types = array('bridge', 'bond', 'vlan', 'ethernet');
        $interfaces = $this->getPlatform()->getDatabase('networks')->getAll();
        foreach ($interfaces as $key => $props) {
           if (in_array($props['type'], $types) && isset($props['role']) && stripos($props['role'],'red') !== false) {
               $ret[] = $key;
           }
        }
        return $ret;
    }

    public function prepareViewForColumnKey(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        if (!$this->interfaces) {
            $this->interfaces = $this->readInterfaces();
        }
        if (!isset($values['status']) || ($values['status'] == 'disabled') || (!in_array($values['interface'],$this->interfaces)) ) {
            $rowMetadata['rowCssClass'] = trim($rowMetadata['rowCssClass'] . ' user-locked');
        }

        return strval($key);
    }


    public function onParametersSaved(\Nethgui\Module\ModuleInterface $currentAction, $changes, $parameters)
    {
        $this->getPlatform()->signalEvent('static-routes-save');
        $this->getPlatform()->signalEvent('firewall-adjust');
    }

}
