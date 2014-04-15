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

/**
 * FirewallRules with plugin behaviour
 * All tabs can be plugins.
 */
class FirewallRules extends \Nethgui\Controller\CollectionController
{

    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $base)
    {
        return \Nethgui\Module\SimpleModuleAttributesProvider::extendModuleAttributes($base, 'Gateway', 10);
    }

    public function initialize()
    {
        $this
            ->setAdapter($this->getPlatform()->getTableAdapter('fwrules', 'rule'))
            ->setIndexAction(new \NethServer\Module\FirewallRules\Index())
        ;       
        $this
            ->addChild(new \NethServer\Module\FirewallRules\Create())
            ->addChild(new \NethServer\Module\FirewallRules\Edit())
            ->addChild(new \NethServer\Module\FirewallRules\PickObject())
            ->addChild(new \NethServer\Module\FirewallRules\Delete())
            ->addChild(new \NethServer\Module\FirewallRules\General())
            ->addChild(new \NethServer\Module\FirewallObjects\HostGroups())
            ->addChild(new \NethServer\Module\FirewallObjects\Zones())
            ->addChild(new \NethServer\Module\FirewallObjects\Hosts())
            ->addChild(new \NethServer\Module\FirewallObjects\Services())
        ;
        parent::initialize();
    }

}