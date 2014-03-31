<?php
namespace NethServer\Module\FirewallObjects\Zones;

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
 * Mange firewall zones.
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
            'Interface',
            'Network',
        );

        if(!$this->interfaces) {
            $this->interfaces = $this->readInterfaces();
        }

        $p = $this->getPlatform();
        $nv = $p->createValidator()->maxLength(5)->minLength(1);
        $parameterSchema = array(
            array('name', $nv, \Nethgui\Controller\Table\Modify::KEY),
            array('Interface', $p->createValidator()->memberOf($this->interfaces), \Nethgui\Controller\Table\Modify::FIELD),
            array('Network', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD) //TODO: validator
        );

        $this->setSchema($parameterSchema);
        

        parent::initialize();
    }

    private function readInterfaces() {
        return array_keys($this->getPlatform()->getDatabase('networks')->getAll('ethernet'));
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $templates = array(
            'create' => 'NethServer\Template\FirewallObjects\Zones\Modify',
            'update' => 'NethServer\Template\FirewallObjects\Zones\Modify',
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
        $view['InterfaceDatasource'] = $tmp;
    }

}
