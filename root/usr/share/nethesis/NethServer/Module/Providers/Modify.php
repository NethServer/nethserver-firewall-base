<?php
namespace NethServer\Module\Providers;

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
 * Manage providers..
 *
 * @author Giacomo Sanchietti
 *
 */
class Modify extends \Nethgui\Controller\Table\Modify
{

    private $interfaces = NULL;

    public function initialize()
    {
        $columns = array(
            'Key',
            'checkip',
            'interface',
            'weight',
            'Description',
        );

        if(!$this->interfaces) {
            $this->interfaces = $this->readInterfaces();
        }

        $p = $this->getPlatform();
        $nv = $p->createValidator()->maxLength(5)->minLength(1);
        $parameterSchema = array(
            array('name', $nv, \Nethgui\Controller\Table\Modify::KEY),
            array('interface', $p->createValidator()->memberOf($this->interfaces), \Nethgui\Controller\Table\Modify::FIELD),
            array('weight', $this->createValidator()->integer()->greatThan(0)->lessThan(256), \Nethgui\Controller\Table\Modify::FIELD),
            array('status', Validate::SERVICESTATUS, \Nethgui\Controller\Table\Modify::FIELD),
            array('Description', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD)
        );

        $this->setSchema($parameterSchema);
        $this->setDefaultValue('status', 'enabled'); 

        parent::initialize();
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        parent::validate($report);
        if( $this->getRequest()->isMutation() && ! $this->parameters['interface']) {
            $report->addValidationErrorMessage($this, 'interface', 'valid_no_red_interface');
        }
        if( $this->getRequest()->isMutation() && $this->getIdentifier() === 'create') {
            $v = $this->createValidator()->platform('network-create');
            if( ! $v->evaluate($this->getAdapter()->getKeyValue())) {
                $report->addValidationError($this, 'interface', $v);
            }
        }
        if($this->getIdentifier() === 'delete') {
            $v = $this->createValidator()->platform('fwobject-provider-delete', 'networks');
            if( ! $v->evaluate($this->getAdapter()->getKeyValue())) {
                $report->addValidationError($this, 'interface', $v);
            }
        }
    }

    private function readInterfaces() {
        $ret = array();
        $used = array();
        $types = array('bridge', 'bond', 'vlan', 'ethernet', 'xdsl');
        $providers = $this->getPlatform()->getDatabase('networks')->getAll('provider');
        foreach ($providers as $key => $props) {
            if (isset($props['interface'])) {
                $used[] = $props['interface'];
            }
        }
        $interfaces = $this->getPlatform()->getDatabase('networks')->getAll();
        foreach ($interfaces as $key => $props) {
           if (in_array($props['type'], $types) && isset($props['role']) && stripos($props['role'],'red') !== false && !in_array($key,$used)) {
               $ret[] = $key;
           }
        }
        return $ret;
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $templates = array(
            'create' => 'NethServer\Template\Providers\Modify',
            'update' => 'NethServer\Template\Providers\Modify',
            'delete' => 'Nethgui\Template\Table\Delete',
        );
        $view->setTemplate($templates[$this->getIdentifier()]);

        if(!$this->interfaces) {
            $this->interfaces = $this->readInterfaces();
        }

        $tmp = array();
        foreach($this->interfaces as $key) {
            $tmp[] = array($key,$key);
        }
        $view['interfaceDatasource'] = $tmp;
    }

}
