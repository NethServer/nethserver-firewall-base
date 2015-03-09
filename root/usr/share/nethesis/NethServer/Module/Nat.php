<?php

namespace NethServer\Module;

/*
 * Copyright (C) 2015 Nethesis S.r.l.
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
 * Configure NAT 1:1
 * 
 * @author Edoardo Spadoni <edoardo.spadoni@nethesis.it>
 */
class Nat extends \Nethgui\Controller\AbstractController
{

    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $base)
    {
        return \Nethgui\Module\SimpleModuleAttributesProvider::extendModuleAttributes($base, 'Gateway', 60);
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        parent::validate($report);
        if ( ! $this->getRequest()->isMutation()) {
            return;
        }

        $validKeys = array_keys($this->getNetworkInterfaces());

        foreach (array_keys($this->getRequest()->getParameter('interfaces')) as $key) {
            if ( ! in_array($key, $validKeys)) {
                $report->addValidationErrorMessage($this, 'interfaces', 'invalid alias interface');
            }
        }
    }

    private function getNetworkInterfaces()
    {
        static $interfaces;

        if (isset($interfaces)) {
            return $interfaces;
        }

        // get all networks params
        $all = $this->getPlatform()->getDatabase('networks')->getAll();

        // declare empty array for red interfaces
        $red_inter = array();

        // get only red interfaces
        foreach ($all as $key => $value) {
            if (isset($value['role']) && $value['role'] === 'red') {
                array_push($red_inter, $key);
            }
        }

        // declare alias array
        $aliases = array();

        // get only aliases
        foreach ($red_inter as $key_red) {
            foreach ($all as $key => $value) {
                if (isset($value['role']) && preg_match("/^$key_red/", $key) && $value['role'] === 'alias') {
                    $aliases[$key] = $all[$key];
                }
            }
        }

        return $aliases;
    }

    public function process()
    {
        parent::process();
        if ( ! $this->getRequest()->isMutation()) {
            return;
        }
        $changes = 0;

        $db = $this->getPlatform()->getDatabase('networks');
        foreach ($this->getRequest()->getParameter('interfaces') as $key => $vals) {
            $cur = $db->getProp($key, 'FwObjectNat');
            if($cur === $vals['FwObjectNat']) {
                continue;
            }
            $db->setProp($key, array('FwObjectNat' => $vals['FwObjectNat']));
            $changes ++;
        }

        if ($changes) {
            $this->getPlatform()->signalEvent('nethserver-firewall-base-save');
        }
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $interfaces = $this->getNetworkInterfaces();

        $dstHosts = array();
        $descriptions = array();
        foreach (\NethServer\Tool\FirewallObjectsFinder::search($this->getPlatform(), '', array('hosts' => array('host')), $view->getTranslator()) as $o) {
            $label = sprintf('%s (%s)', $o->getDetails(), $o->key);
            $descriptions[$o->getValue()] = $label;
            $dstHosts[] = array('label' => $label, 'value' => $o->getValue());
        }

        $ds = array();
        foreach ($interfaces as $key => $props) {
            $ds[] = array(
                'id' => $key,
                'InterIp' => $props['ipaddr'],
                'FwObjectDesc' => isset($descriptions[$props['FwObjectNat']]) ? $descriptions[$props['FwObjectNat']] : '',
                'FwObjectNat' => isset($props['FwObjectNat']) ? $props['FwObjectNat'] : ''
            );
        }


        $view['DstHosts'] = $dstHosts;
        $view['interfaces'] = $ds;
    }

}
