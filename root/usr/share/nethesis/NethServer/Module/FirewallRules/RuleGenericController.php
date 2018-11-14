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
        $validActions = array_merge(array('accept', 'reject', 'drop'), array_map(
                        function ($x) {
                    return 'provider;' . $x;
                }, $this->getProviderKeys()), array_map( function ($x) {
                    return 'class;' . $x;
                }, $this->getClassKeys())
            );

        $this->declareParameter('SrcRaw', Validate::ANYTHING, array('fwrules', $this->ruleId, 'Src'));
        $this->declareParameter('DstRaw', Validate::ANYTHING, array('fwrules', $this->ruleId, 'Dst'));
        $this->declareParameter('ServiceRaw', Validate::ANYTHING, array('fwrules', $this->ruleId, 'Service'));
        $this->declareParameter('TimeRaw', Validate::ANYTHING, array('fwrules', $this->ruleId, 'Time'));
        $this->declareParameter('status', Validate::SERVICESTATUS, array('fwrules', $this->ruleId, 'status'));
        $this->declareParameter('Description', Validate::ANYTHING, array('fwrules', $this->ruleId, 'Description'));
        $this->declareParameter('Position', Validate::POSITIVE_INTEGER, array('fwrules', $this->ruleId, 'Position'));
        $this->declareParameter('LogType', $this->createValidator()->memberOf('none', 'info'), array('fwrules', $this->ruleId, 'Log'));
        $this->declareParameter('Action', $this->createValidator()->memberOf($validActions), array('fwrules', $this->ruleId, 'Action'));
        $this->declareParameter('Source', Validate::ANYTHING);
        $this->declareParameter('Destination', Validate::ANYTHING);
        $this->declareParameter('Service', Validate::ANYTHING);
        $this->declareParameter('Time', Validate::ANYTHING);
        parent::bind($request);
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        parent::validate($report);
        if ( ! $report->hasValidationErrors()
                && $this->getRequest()->isMutation()
                && $this->getRequest()->hasParameter('Submit')) {
            $v = $this->createValidator()->platform('fwrule-modify', $this->getIdentifier());
            if( ! $v->evaluate(json_encode($this->parameters->getArrayCopy()))) {
                $report->addValidationError($this, 'Rule', $v);
            }
        }
    }

    private function addReadonlyAdapter(\Nethgui\View\ViewInterface $view, $targetName, $sourceName)
    {
        if( ! isset($this->parameters[$targetName])) {
            return;
        }
        $this->parameters->addAdapter(new \Nethgui\Adapter\MultipleAdapter(function () use ($view, $sourceName) {
            if($sourceName === 'TimeRaw' && $view['TimeRaw'] === '') {
                return $view->translate('Time_always');
            }
            return \NethServer\Module\FirewallRules\RuleGenericController::translateFirewallObjectTitle($view, $view[$sourceName]);
        }), $targetName);
    }

    public static function translateFirewallObjectTitle(\Nethgui\View\ViewInterface $view, $raw)
    {
        $props = array();
        list($type, $key) = is_string($raw) ? array_merge(explode(';', $raw), array('', '')) : array('', '');
        # Resolve ndpi protocol name
        if ($type == 'ndpi') {
            $protocols = \NethServer\Module\FirewallRules\Index::listNdpiProtocols();
            if (isset($protocols[$key])) {
                $props = array("name" => $protocols[$key]);
            }
        }
        $o = new \NethServer\Tool\FirewallObject($key, $type, $props, array($view, 'translate'));
        return $o->getTitle();
    }

    public function process()
    {
        if ($this->getRequest()->isMutation() && $this->getRequest()->hasParameter('Submit')) {
            parent::process();
        }
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        $this->addReadonlyAdapter($view, 'Source', 'SrcRaw');
        $this->addReadonlyAdapter($view, 'Destination', 'DstRaw');
        $this->addReadonlyAdapter($view, 'Service', 'ServiceRaw');
        $this->addReadonlyAdapter($view, 'Time', 'TimeRaw');
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
        
        $actions =array(
            array('accept', $view->translate('ActionAccept_label')),
            array('reject', $view->translate('ActionReject_label')),
            array('drop', $view->translate('ActionDrop_label')),
        );
        foreach($this->getProviderKeys() as $provider) {
            $actions[] = array('provider;' . $provider, $view->translate('ActionRoute_label', array($provider)));
        }
        foreach($this->getClassKeys() as $class) {
            $actions[] = array('class;' . $class, $view->translate('ActionPriority_label', array($class)));
        }
        $view['ActionDatasource'] = $actions;
    }

    private function getProviderKeys()
    {
        return array_keys($this->getPlatform()->getDatabase('networks')->getAll('provider'));
    }

    private function getClassKeys()
    {
        return array_keys($this->getPlatform()->getDatabase('tc')->getAll('class'));
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
            } elseif ($R->hasParameter('PickTime')) {
                return '../PickObject?f=TimeRaw';
            }
        }        
    }

}
