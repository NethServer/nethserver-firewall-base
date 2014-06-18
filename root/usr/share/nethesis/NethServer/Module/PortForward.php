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
 * Configure port forward
 */
class PortForward extends \Nethgui\Controller\TableController
{
    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $base)
    {
        return \Nethgui\Module\SimpleModuleAttributesProvider::extendModuleAttributes($base, 'Gateway', 20);
    }

    public function initialize()
    {

        $columns = array(
            'Key',
            'Proto',
            'Src',
            'DstHost',
            'Dst',
            'Description',
            'Actions'
        );

        $this
            ->setTableAdapter($this->getPlatform()->getTableAdapter('portforward','pf'))
            ->setColumns($columns)
            ->addTableAction(new \NethServer\Module\PortForward\Modify('create'))            
            ->addTableAction(new \NethServer\Module\PortForward\CheckRules())            
            ->addTableAction(new \Nethgui\Controller\Table\Help('Help'))
            ->addRowAction(new \NethServer\Module\PortForward\ToggleEnable('disable'))
            ->addRowAction(new \NethServer\Module\PortForward\ToggleEnable('enable'))
            ->addRowAction(new \NethServer\Module\PortForward\Modify('update'))
            ->addRowAction(new \NethServer\Module\PortForward\Modify('delete'))
        ;

        parent::initialize();
    }

    public function prepareViewForColumnProto(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        if (!isset($values['status']) || ($values['status'] == "disabled")) {
            $rowMetadata['rowCssClass'] = trim($rowMetadata['rowCssClass'] . ' user-locked');
        }
        return strtoupper($values['Proto']);
    }

    public function prepareViewForColumnDstHost(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        $tmp = explode(';',$values['DstHost']);
        if (isset($tmp[1])) {
            return $tmp[1];
        } else {
            return $values['DstHost'];
        }
    }


    /**
     * Override prepareViewForColumnActions to hide/show enable/disable actions
     * @param \Nethgui\View\ViewInterface $view
     * @param string $key The data row key
     * @param array $values The data row values
     * @return \Nethgui\View\ViewInterface 
     */
    public function prepareViewForColumnActions(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        $cellView = $action->prepareViewForColumnActions($view, $key, $values, $rowMetadata);

        $killList = array();

        $state = isset($values['status']) ? $values['status'] : 'enabled';

        switch ($state) {
            case 'enabled':
                $remove = 'enable';
                break;
            case 'disabled';
                $remove = 'disable';
                break;
            default:
                break;
        }

        unset($cellView[$remove]);

        return $cellView;
    }
        
}

