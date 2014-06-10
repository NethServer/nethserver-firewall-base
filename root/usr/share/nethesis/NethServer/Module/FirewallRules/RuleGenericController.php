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
 * This generic worker controller reads the workflow session to override values
 * from DB, for fields wired with PickObject controller.
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class RuleGenericController extends \Nethgui\Controller\AbstractController
{
    public $ruleId;

    public function initializeFromAction(\Nethgui\Controller\Collection\AbstractAction $action)
    {
        $this
            ->setParent($action->getParent())
            ->setPlatform($action->getPlatform())
            ->setPolicyDecisionPoint($action->getPolicyDecisionPoint())
            ->setLog($action->getLog())
            ->initialize();
        return $this;
    }

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        $this->declareParameter('SrcRaw', Validate::ANYTHING, array('fwrules', $this->ruleId, 'Src'));
        $this->declareParameter('DstRaw', Validate::ANYTHING, array('fwrules', $this->ruleId, 'Dst'));
        $this->declareParameter('ServiceRaw', Validate::ANYTHING, array('fwrules', $this->ruleId, 'Service'));
        $this->declareParameter('status', Validate::SERVICESTATUS, array('fwrules', $this->ruleId, 'status'));
        $this->declareParameter('Description', Validate::ANYTHING, array('fwrules', $this->ruleId, 'Description'));
        $this->declareParameter('Position', Validate::POSITIVE_INTEGER, array('fwrules', $this->ruleId, 'Position'));
        $this->declareParameter('LogType', $this->createValidator()->memberOf('none', 'info'), array('fwrules', $this->ruleId, 'Log'));
        $this->declareParameter('Action', $this->createValidator()->memberOf('accept', 'reject', 'drop'), array('fwrules', $this->ruleId, 'Action'));
        $this->declareReadonlyParameters();
        parent::bind($request);
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

    public function process()
    {
        if ($this->getRequest()->isMutation() && $this->getRequest()->hasParameter('Submit')) {
            parent::process();
        }
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $view->setTemplate('NethServer\Template\FirewallRules\Rule');
        if ( ! $this->getRequest()->isValidated()) {
            return;
        }
        $view['RuleId'] = $this->ruleId;
        if ( ! $this->getRequest()->isMutation()) {
            $view->getCommandList()->show();
        } else {
            $view->getCommandList()->sendQuery($view->getModuleUrl($this->getNextRequest()));
        }
    }

    private function getNextRequest()
    {
        $R = $this->getRequest();
        if ($R->isMutation()) {
            if ($R->hasParameter('Submit')) {
                return '../Index';
            } elseif ($R->hasParameter('PickSource')) {
                return '../PickObject?f=SrcRaw';
            } elseif ($R->hasParameter('PickDestination')) {
                return '../PickObject?f=DstRaw';
            } elseif ($R->hasParameter('PickService')) {
                return '../PickObject?f=ServiceRaw';
            }
        }        
    }

}