<?php
namespace NethServer\Module\FirewallObjects\Zones;

/*
 * Copyright (C) 2012 Nethesis S.r.l.
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
 * Modify Service object
 *
 * @author Giacomo Sanchietti <giacomo.sanchietti@nethesis.it>
 */
class Modify extends \Nethgui\Controller\Table\Modify
{
    private $interfaces;

    public function initialize()
    {
        if (!$this->interfaces) {
            $this->interfaces = $this->readInterfaces();
        }

        $nameValidator = $this->getPlatform()->createValidator()->maxLength(5)->username();
        $interfaceValidator = $this->getPlatform()->createValidator()->memberOf($this->interfaces);
        $parameterSchema = array(
            array('name', $nameValidator, \Nethgui\Controller\Table\Modify::KEY),
            array('Network', Validate::CIDR_BLOCK, \Nethgui\Controller\Table\Modify::FIELD),
            array('Interface', $interfaceValidator, \Nethgui\Controller\Table\Modify::FIELD),
            array('Description', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
        );

        $this->setSchema($parameterSchema);

        parent::initialize();
    }

    private function readInterfaces() {
        $ret = array();
        $types = array('bridge', 'bond', 'vlan', 'ethernet');
        $interfaces = $this->getPlatform()->getDatabase('networks')->getAll();
        foreach ($interfaces as $key => $props) {
           if (in_array($props['type'], $types)) {
               $ret[] = $key;
           }
        }
        return $ret;
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
        if (!$this->interfaces) {
            $this->interfaces = $this->readInterfaces();
        }
        $view['InterfaceDatasource'] = array_map(function($fmt) use ($view) {
                                return array($fmt, $fmt);
        }, $this->interfaces);
    }

    public function nextPath()
    {
        // Workaround for LazyLoaderAdapter to reload table contents after mutation request
        if($this->getRequest()->isMutation()) {
            return '/FirewallObjects/Zones/read';
        }
        return parent::nextPath();
    }

}
