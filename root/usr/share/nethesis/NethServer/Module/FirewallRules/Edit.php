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
 * Delete firewall rules
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class Edit extends \Nethgui\Controller\Collection\AbstractAction
{
    private $ruleId = NULL;

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        $ruleId = \Nethgui\array_head($request->getPath());
        if ( ! $this->getAdapter()->offsetExists($ruleId)) {
            throw new \Nethgui\Exception\HttpException('Not found', 404, 1399992975);
        }
        $this->ruleId = $ruleId;

        $this->declareParameter('SrcRaw', Validate::ANYTHING, array('fwrules', $ruleId, 'Src'));
        $this->declareParameter('DstRaw', Validate::ANYTHING, array('fwrules', $ruleId, 'Dst'));
        $this->declareParameter('ServiceRaw', Validate::ANYTHING, array('fwrules', $ruleId, 'Service'));
        $this->declareParameter('status', Validate::SERVICESTATUS, array('fwrules', $ruleId, 'status'));
        $this->declareParameter('Description', Validate::ANYTHING, array('fwrules', $ruleId, 'Description'));
        $this->declareParameter('LogType', $this->createValidator()->memberOf('none', 'info'), array('fwrules', $ruleId, 'Log'));
        $this->declareParameter('Action', $this->createValidator()->memberOf('ACCEPT', 'REJECT', 'DROP'), array('fwrules', $ruleId, 'Action'));

        $this->declareReadonlyParameters();
        parent::bind($request);
        $this->bindPickObjectParameters();
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

    private function bindPickObjectParameters()
    {
        $sessionParameters = $this->getPlatform()->getDatabase('SESSION')->getKey('FirewallRules.PickObject.Out');
        foreach (array('SrcRaw' => 'Source', 'DstRaw' => 'Destination', 'ServiceRaw' => 'Service') as $p => $s) {
            if (isset($sessionParameters[$s]) && $sessionParameters[$s]) {
                $this->parameters[$p] = $sessionParameters[$s];
            }
        }
        $this->getPlatform()->getDatabase('SESSION')->setKey('FirewallRules.PickObject.In', 'Edit', array('RuleId' => $this->ruleId, 'QueryNext' => '../Edit/' . $this->ruleId));
    }

    public function process()
    {
        parent::process();
        if ($this->getRequest()->isMutation()) {
            $this->getAdapter()->flush();
        }
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        if ( ! $this->getRequest()->isValidated()) {
            return;
        }
        $view['FormAction'] = $view->getModuleUrl($this->ruleId);
        $view['RuleId'] = $this->ruleId;
        if ( ! $this->getRequest()->isMutation()) {
            $view->getCommandList()->show();
        }
    }

    public function nextPath()
    {
        return $this->getRequest()->isMutation() ? 'Index' : parent::nextPath();
    }

}