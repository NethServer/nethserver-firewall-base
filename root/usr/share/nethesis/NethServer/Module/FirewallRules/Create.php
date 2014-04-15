<?php

namespace NethServer\Module\FirewallRules;

/*
 * Copyright (C) 2014  Nethesis S.r.l.
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
 * Create a new firewall rule object
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class Create extends \Nethgui\Controller\Collection\AbstractAction
{
    private $ruleId = NULL;

    public function initialize()
    {
        parent::initialize();
        $this->setViewTemplate('NethServer\Template\FirewallRules\Edit');
        $this->declareParameter('Position', $this->createValidator()->memberOf('first', 'last'));
    }

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        $this->parameters['Position'] = \Nethgui\array_head($request->getPath());

        
        if ($this->parameters['Position'] === 'first') {
             $ruleId = 1;
        } else {
             $allKeys = array_keys(iterator_to_array($this->getAdapter()));
             $ruleId = 1 + (count($allKeys) > 0 ? max($allKeys) : 0);
        }

        $this->ruleId = $ruleId;

        $this->declareParameter('SrcRaw', Validate::ANYTHING);
        $this->declareParameter('DstRaw', Validate::ANYTHING);
        $this->declareParameter('ServiceRaw', Validate::ANYTHING);
        $this->declareParameter('status', Validate::SERVICESTATUS);
        $this->declareParameter('Description', Validate::ANYTHING);
        $this->declareParameter('LogType', $this->createValidator()->memberOf('none', 'info'), array('fwrules', $ruleId, 'Log'));
        $this->declareParameter('Action', $this->createValidator()->memberOf('ACCEPT', 'REJECT', 'DROP'), array('fwrules', $ruleId, 'Action'));

        $this->declareReadonlyParameters();
        parent::bind($request);
        $this->bindPickObjectParameters($request->hasParameter('picker'));
    }

    private function declareReadonlyParameters()
    {
        $P = $this->parameters;
        $this->declareParameter('Source', Validate::ANYTHING, function() use ($P) {
            return ucfirst(strtr($P['SrcRaw'], ';', ' '));
        });
        $this->declareParameter('Destination', Validate::ANYTHING, function() use ($P) {
            return ucfirst(strtr($P['DstRaw'], ';', ' '));
        });
        $this->declareParameter('Service', Validate::ANYTHING, function() use ($P) {
            return ucfirst(strtr($P['ServiceRaw'], ';', ' '));
        });
    }

    private function bindPickObjectParameters($hasPicker)
    {
        $sessionDb = $this->getPlatform()->getDatabase('SESSION');

        if ( ! $hasPicker) {
            $sessionDb->deleteKey('FirewallRules.PickObject.Out');
        } else {
            $outParams = $sessionDb->getKey('FirewallRules.PickObject.Out');
            foreach (array('SrcRaw' => 'Source', 'DstRaw' => 'Destination', 'ServiceRaw' => 'Service') as $p => $s) {
                if (isset($outParams[$s]) && $outParams[$s]) {
                    $this->parameters[$p] = $outParams[$s];
                }
            }
        }

        $sessionDb->setKey('FirewallRules.PickObject.In', 'Create', array('RuleId' => $this->ruleId, 'QueryNext' => '../Create/' . $this->parameters['Position'] . '?FirewallRules[Create][picker]=1'));
    }

    public function process()
    {
        if ($this->getRequest()->isMutation()) {
            if ($this->parameters['Position'] === 'first') {
                $this->stepKeysForward();
            }

            $props = array(
                'Src' => $this->parameters['SrcRaw'],
                'Dst' => $this->parameters['DstRaw'],
                'Description' => $this->parameters['Description'],
                'Log' => $this->parameters['LogType'],
                'Action' => $this->parameters['Action'],
                'Service' => $this->parameters['ServiceRaw'],
                'status' => $this->parameters['status']
            );

            $this->getPlatform()->getDatabase('fwrules')->setKey($this->ruleId, 'rule', $props);
        }
        parent::process();
        if ($this->getRequest()->isMutation()) {
            $this->getAdapter()->flush();
        }
    }

    private function stepKeysForward()
    {
        $changes = array();
        $A = $this->getAdapter();
        foreach ($A as $key => $values) {
            $changes[$key + 1] = $values;
        }

        foreach ($changes as $key => $values) {
            $A[$key] = $values;
        }
        $A->save();
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $view['FormAction'] = $view->getModuleUrl($this->parameters['Position']);
        $view['RuleId'] = $this->ruleId;
        
        if ( ! $this->getRequest()->isValidated()) {
            return;
        }

        if ( ! $this->getRequest()->isMutation()) {
            $view['status'] = 'enabled';
            $view->getCommandList()->show();
        }
    }

    public function nextPath()
    {
        if ($this->getRequest()->isMutation()) {
            return 'Index';
        }
        return parent::nextPath();
    }

}