<?php

namespace NethServer\Module\NAT;

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

use \Nethgui\System\PlatformInterface as Validate;

class Configure extends \Nethgui\Controller\AbstractController
{

    public function initialize()
    {
        parent::initialize();
        $this->tableAdapter = $this->getPlatform()->getTableAdapter('networks', 'alias');
    }

    private function getDataSource()
    {
        static $dataSource;
        if (isset($dataSource)) {
            return $dataSource;
        }
        $dataSource = new \ArrayObject();

        foreach ($this->tableAdapter as $key => $record) {
            $dataSource[$key] = new \Nethgui\Adapter\RecordAdapter($this->tableAdapter);
            $dataSource[$key]->setKeyValue($key);
        }

        return $dataSource;
    }

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        parent::bind($request);
        if ($request->isMutation()) {
            $dataSource = $this->getDataSource();
            foreach ($request->getParameter('interfaces') as $key => $props) {
                if (isset($dataSource[$key])) {
                    $dataSource[$key]->set($props);
                } else {
                    $dataSource[$key] = new \Nethgui\Adapter\RecordAdapter($this->tableAdapter);
                    $dataSource[$key]->setKeyValue($key)->set($props);
                }
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
            if($value['role'] === 'red')
                array_push($red_inter, $key);
        }

        // declare alias array
        $aliases = array();

        // get only aliases
        foreach ($red_inter as $key_red) {
            foreach ($all as $key => $value) {
                if(preg_match("/^$key_red/", $key) && $value['role'] === 'alias')
                    $aliases[$key] = $all[$key];
            }
        }

        return $aliases;
    }

    public function process()
    {
        parent::process();
        if ($this->getRequest()->isMutation()) {
            foreach ($this->getDataSource() as $record) {
                $record->save();
            }
            $changes = $this->tableAdapter->save();
            if ($changes) {
                $this->getPlatform()->signalEvent('nethserver-firewall-base-save');
            }
        }
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $interfaces = $this->getNetworkInterfaces();
        $dataSource = $this->getDataSource();
        $ds = array();
        foreach ($interfaces as $key => $props) {
            $record = isset($dataSource[$key]) ? \iterator_to_array($dataSource[$key]) : array();
            $ds[] = array(
                'id' => $key,
                'InterName' => $key,
                'InterIp' => $props['ipaddr'],
                'FwObjectNat' => isset($record['FwObjectNat']) ? $record['FwObjectNat'] : ''
            );
        }

        $view['interfaces'] = $ds;
    }

}