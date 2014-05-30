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
class CreateHostGroup extends \Nethgui\Controller\Collection\AbstractAction
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
        $this->declareParameter('name', Validate::HOSTNAME);
        $this->declareParameter('Members', Validate::ANYTHING_COLLECTION);
        $this->declareParameter('Description', Validate::ANYTHING);
        $this->declareParameter('q', Validate::ANYTHING);
        $this->declareParameter('MembersDatasource', FALSE, array($this, 'provideMembersDatasource'));
    }

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        parent::bind($request);
        if ($request->isMutation()) {
            return;
        }
        $hint = $request->getParameter('q');
        if ($hint !== NULL) {
            foreach (array('name', 'Description') as $key) {
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
            $report->addValidationErrorMessage($this, 'name', 'Host_key_exists_message');
        }
        parent::validate($report);
    }

    public function process()
    {
        parent::process();
        if ($this->getRequest()->isMutation()) {
            $this->getPlatform()
                ->getDatabase('hosts')->setKey($this->parameters['name'], 'host', array(
                'Description' => $this->parameters['Description'],
                'Members' => implode(',', $this->parameters['Members']),
            ));
            $this->state->resume($this->getParent()->getSession())->assign(sprintf("host-group;%s", $this->parameters['name']));
        }
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $view->setTemplate('NethServer/Template/FirewallObjects/HostGroups/Modify');
        if ( ! $this->getRequest()->isValidated()) {
            return;
        }
        if ($this->getRequest()->isMutation()) {
            $view->getCommandList()->sendQuery($view->getModuleUrl('../' . $this->state->getReturnPath()));
        } else {
            $view->getCommandList()->show();
        }
    }

    public function provideMembersDatasource()
    {
        $platform = $this->getPlatform();
        if (is_null($platform)) {
            return array();
        }

        $hosts = $platform->getDatabase('hosts')->getAll();
        $values = array();

        // Build the datasource rows couples <key, label>
        foreach ($hosts as $key => $row) {
            if ( ! isset($row['IpAddress']) && ! isset($row['MacAddress'])) {
                continue;
            }
            $values[] = array($key, sprintf('%s (%s)', $key, $row['IpAddress'] ? $row['IpAddress'] : $row['MacAddress']));
        }
        return $values;
    }

}