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
 * Edit access to a service running on the local system
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class EditService extends \Nethgui\Controller\Collection\AbstractAction
{

    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $base)
    {
        return new \NethServer\Tool\CustomModuleAttributesProvider($base, array(
            'languageCatalog' => array('NethServer_Module_NetworkServices', 'NethServer_Module_FirewallRules'))
        );
    }

    public function initialize()
    {
        parent::initialize();
        $this->setViewTemplate('NethServer\Template\NetworkServices\Modify');
        $this->declareParameter('name', FALSE);
    }

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        $key = \Nethgui\array_end($request->getPath());
        $this->declareParameter('access', Validate::ANYTHING_COLLECTION, array('configuration', $key, 'access', ','));
        $this->declareParameter('status', FALSE, array('configuration', $key, 'status'));
        parent::bind($request);
        $this->parameters['name'] = $key;
    }

    private function listZones()
    {
        $invalid_roles = array('bridged', 'alias', 'slave', 'xdsl');
        $networks = $this->getPlatform()->getDatabase('networks')->getAll();
        $zones['red'] = ''; # always enable red
        foreach ($networks as $key => $values) {
            if ($values['type'] == 'zone') {
                $zones[$key] = '';
            }
            if (isset($values['role']) && ! preg_match("/(" . implode('|', $invalid_roles) . ")/", $values['role'])) {
                $zones[$values['role']] = '';
            }
        }

        return array_keys($zones);
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        if ( ! $this->getRequest()->isValidated()) {
            return;
        }

        $view['accessDatasource'] = array_map(function($fmt) use ($view) {
            $label = $view->translate($fmt . '_label');
            if ($label == $fmt . '_label') {
                $label = $fmt;
            }

            return array($fmt, $label);
        }, $this->listZones());

        $view['FormAction'] = $view->getModuleUrl($this->parameters['name']);

        if ($this->getRequest()->isMutation()) {
            $view->getCommandList()->sendQuery($view->getModuleUrl('../Index?a=services'));
        } else {
            $view->getCommandList()->show();
        }
    }

}
