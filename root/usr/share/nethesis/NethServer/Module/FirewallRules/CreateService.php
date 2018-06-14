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
 * Create a new Service record
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class CreateService extends \Nethgui\Controller\Collection\AbstractAction
{
    private $protocols = array('tcp', 'tcpudp', 'udp');

    /**
     *
     * @var \NethServer\Module\FirewallRules\RuleWorkflow
     */
    private $state;

    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $attributes)
    {
        return new \NethServer\Tool\CustomModuleAttributesProvider($attributes, array('languageCatalog' => 'NethServer_Module_FirewallObjects'));
    }

    public function initialize()
    {
        parent::initialize();
        $this->state = new \NethServer\Module\FirewallRules\RuleWorkflow();
        $this->declareParameter('name', Validate::USERNAME);
        $this->declareParameter('Protocol', $this->createValidator()->memberOf($this->protocols));
        $this->declareParameter('Description', Validate::ANYTHING);
        $this->declareParameter('Ports', Validate::ANYTHING);
        $this->declareParameter('q', Validate::ANYTHING);
    }

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        parent::bind($request);
        if ($request->isMutation()) {
            return;
        }
        $hint = $request->getParameter('q');
        if ($hint !== NULL) {
            foreach (array('name', 'Protocol', 'Description') as $key) {
                if ($this->getValidator($key)->evaluate($hint)) {
                    $this->parameters[$key] = $hint;
                }
            }
            if (intval($hint) > 0) {
                 $this->parameters['Ports'] = $hint;
            }
        }
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        $key = $this->parameters['name'];
        if ($this->getPlatform()->getDatabase('fwservices')->getType($key)) {
            $report->addValidationErrorMessage($this, 'name', 'Service_key_exists_message');
        }

        if ($this->getRequest()->isMutation()) {
            if ( strpos($this->parameters['Ports'], ",") !== false ) { # list of ports
                $ports = explode(',',$this->parameters['Ports']);
                $v = $this->createValidator(Validate::PORTNUMBER);
                foreach($ports as $port) {
                    if(!$v->evaluate($port)) {
                        $report->addValidationErrorMessage($this, 'Ports', 'Ports_validator');
                    }
                }
            } else if ( strpos($this->parameters['Ports'], "-") !== false ) {
                $tmp = explode("-",$this->parameters['Ports']);
                if ( !isset($tmp[0]) || !isset($tmp[1]) || !(int)$tmp[0] || !(int)$tmp[1] || (int)$tmp[0] >= (int)$tmp[1] ) {
                    $report->addValidationErrorMessage($this, 'Ports', 'Port_range_validator');
                }
            }
        }
        parent::validate($report);
    }

    public function process()
    {
        parent::process();
        if ($this->getRequest()->isMutation()) {
            $this->getPlatform()
                ->getDatabase('fwservices')->setKey($this->parameters['name'], 'fwservice', array(
                'Description' => $this->parameters['Description'],
                'Protocol' => $this->parameters['Protocol'],
                'Ports' => $this->parameters['Ports'],
            ));
            $this->state->resume($this->getParent()->getSession())->assign(sprintf("fwservice;%s", $this->parameters['name']));
        }
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $view->setTemplate('NethServer/Template/FirewallObjects/Services/Modify');
        $view['ProtocolDatasource'] = array_map(function($fmt) use ($view) {
            return array($fmt, $view->translate($fmt . '_label'));
        }, $this->protocols);
        if ( ! $this->getRequest()->isValidated()) {
            return;
        }
        if ($this->getRequest()->isMutation()) {
            $view->getCommandList()->sendQuery($view->getModuleUrl('../' . $this->state->getReturnPath()));
        } else {
            $view->getCommandList()->show();
        }
    }

}
