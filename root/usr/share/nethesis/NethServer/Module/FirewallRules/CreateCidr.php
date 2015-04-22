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
 * Create a new Zone record
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class CreateCidr extends \Nethgui\Controller\Collection\AbstractAction
{
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
        $this->declareParameter('Address', Validate::CIDR_BLOCK);
        $this->declareParameter('Description', Validate::ANYTHING);
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
            foreach (array('name', 'Address', 'Description') as $key) {
                if ($this->getValidator($key)->evaluate($hint)) {
                    $this->parameters[$key] = $hint;
                }
            }
        }
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        $key = $this->parameters['name'];
        if ($this->getPlatform()->getDatabase('hosts')->getType($key)) {
            $report->addValidationErrorMessage($this, 'name', 'Cidr_key_exists_message');
        }
        parent::validate($report);
    }

    public function process()
    {
        parent::process();
        if ($this->getRequest()->isMutation()) {
            $this->getPlatform()
                ->getDatabase('hosts')->setKey($this->parameters['name'], 'cidr', array(
                'Description' => $this->parameters['Description'],
                'Address' => $this->parameters['Address'],
            ));
            $this->state->resume($this->getParent()->getSession())->assign(sprintf("cidr;%s", $this->parameters['name']));
        }
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $view->setTemplate('NethServer/Template/FirewallObjects/Cidr/Modify');
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
