<?php

namespace NethServer\Module;

/*
 * Copyright (C) 2018 Nethesis S.r.l.
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
 * Configure tc classess
 */
class TcClasses extends \Nethgui\Controller\TableController
{
    /**
     *
     * @var \Nethgui\Utility\SessionInterface
     */
    private $session;
    private $myCurrentAction;

    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $attributes)
    {
        return new \NethServer\Tool\CustomModuleAttributesProvider($attributes, array(
            'languageCatalog' => array('NethServer_Module_TcClasses', 'NethServer_Module_FirewallRules'),
            'category' => 'Gateway')
        );
    }

    public function initialize()
    {

        $columns = array(
            'Key',
            'Download',
            'Upload',
            'Description',
            'Actions'
        );

        $this
            ->setTableAdapter($this->getPlatform()->getTableAdapter('tc', 'class'))
            ->setColumns($columns)
            ->addTableAction(new \NethServer\Module\TcClasses\Modify('create'))
            ->addTableAction(new \Nethgui\Controller\Table\Help('Help'))
            ->addTableAction(new \NethServer\Module\TcClasses\Configure())
            ->addRowAction(new \NethServer\Module\TcClasses\Modify('update'))
            ->addRowAction(new \NethServer\Module\TcClasses\Modify('delete'))
        ;

        parent::initialize();
    }

    private function formatRate($value)
    {
        return $value?$value."%":'-';
    }
    
    public function prepareViewForColumnDownload(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        $str = '';
        if ($values['MinInputRate']) {
            $str .= $view->translate("Min_label").": ".$this->formatRate($values['MinInputRate']);
        }
        if ($values['MaxInputRate']) {
            $str .= " ".$view->translate("Max_label").": ". $this->formatRate($values['MaxInputRate']);
        }
        return $str;
    }

    public function prepareViewForColumnUpload(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        $str = '';
        if ($values['MinOutputRate']) {
            $str .= $view->translate("Min_label").": ".$this->formatRate($values['MinOutputRate']);
        }
        if ($values['MaxOutputRate']) {
            $str .= " ".$view->translate("Max_label").": ". $this->formatRate($values['MaxOutputRate']);
        }
        return $str;
    }

}
