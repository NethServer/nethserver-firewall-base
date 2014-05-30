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
class FirewallRules extends \Nethgui\Controller\CollectionController implements \Nethgui\Utility\SessionConsumerInterface
{

    const RULESTEP = 64;
    
    /**
     *
     * @var \Nethgui\Utility\SessionInterface
     */
    private $session;

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
            ->addChild(new \NethServer\Module\FirewallRules\CreateHostGroup())
            ->addChild(new \NethServer\Module\FirewallRules\CreateZone())
            ->addChild(new \NethServer\Module\FirewallRules\CreateHost())
            ->addChild(new \NethServer\Module\FirewallRules\CreateService())
        ;

        parent::initialize();
    }

    public function process()
    {
        parent::process();
        if ($this->getRequest()->isMutation()) {
            $A = $this->getAdapter();

            $H = \iterator_to_array($A);
           
            uasort($H, function($a, $b) {
                $ap = isset($a['Position']) ? $a['Position'] : 0;
                $bp = isset($b['Position']) ? $b['Position'] : 0;
                return $ap > $bp;
            });

            // FIXME: Stupid routine to fix Position on every record.
            // Could be optimized.
            // Here we assume every rule must fit exactly a slot of RULESTEP
            // units width.
            $adjustPositions = function () use ($H, $A) {
                $i = 0;
                foreach(array_keys($H) as $key) {
                    $A[$key] = array('Position' => ($i+1) * \NethServer\Module\FirewallRules::RULESTEP);
                    $i++;
                }
                $A->save();
            };

            // Check distances are large enough:
            $prev = array('Position' => 0);
            foreach($H as $key => $curr) {
                if($curr['Position'] - $prev['Position'] < 4) {
                    $adjustPositions();
                    break;
                }
                $prev = $curr;
            }

            $A->flush();
        }
    }

    public function setSession(\Nethgui\Utility\SessionInterface $session)
    {
        $this->session = $session;
        return $this;
    }

    public function getSession()
    {
        return $this->session;
    }

}