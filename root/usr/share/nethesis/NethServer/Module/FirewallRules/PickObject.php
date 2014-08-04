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
 * Pick a firewall object for a rule field
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class PickObject extends \Nethgui\Controller\Collection\AbstractAction
{
    /**
     *
     * @var \NethServer\Module\FirewallRules\RuleWorkflow
     */
    private $state;

    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $attributes)
    {
        return new \NethServer\Tool\CustomModuleAttributesProvider($attributes, array('languageCatalog' => 'NethServer_Module_FirewallRules'));
    }

    public function initialize()
    {
        parent::initialize();
        $this->declareParameter('q', Validate::ANYTHING);  // search query
        $this->declareParameter('f', $this->createValidator()->memberOf('SrcRaw', 'DstRaw', 'ServiceRaw')); // field to modify
        $this->declareParameter('m', $this->createValidator()->memberOf('ts', 'pf', 'fr', ''));
        $this->declareParameter('Result', Validate::ANYTHING);
        $this->state = new \NethServer\Module\FirewallRules\RuleWorkflow();
    }

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        parent::bind($request);
        $this->state->resume($this->getParent()->getSession())->focus($this->parameters['f']);
    }

    public function process()
    {
        parent::process();
        if ($this->getRequest()->isMutation()) {
            $this->state->assign($this->parameters['Result']);
        }
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        if ($this->getRequest()->isMutation()) {
            $view->getCommandList()->sendQuery($view->getModuleUrl('../' . $this->state->getReturnPath()));
        } elseif ($this->getRequest()->isValidated()) {
            $view['CreateLinks'] = $this->getCreateLinks($view, $this->parameters['q']);
            $view['PickObjectHeader'] = $view->translate("PickObject_" . $this->parameters['f'] . "_header", array('RuleId' => $this->state->getStartIdentifier() === 'Edit' ? '#' . $this->state->getRuleId() : $view->translate('A_new_rule_label')));

            $results = array();
            $ANY = array('any', $view->translate('Any_label'));

            if ($this->parameters['f'] === 'ServiceRaw') {
                $results[] = $ANY;
                $where = array('fwservices' => array('fwservice'));
            } elseif ($this->parameters['m'] === 'ts') {
                $where = array('hosts' => array('host', 'remote', 'local'));
            } elseif ($this->parameters['m'] === 'pf') {
                $where = array('hosts' => array('host', 'remote', 'local'));
            } else {
                $results[] = $ANY;
                $where = array('hosts' => array('host', 'host-group', 'remote', 'local'), 'networks' => array('zone'), 'ROLES' => array());
            }

            $T = array($view, 'translate');
            $s = \NethServer\Tool\FirewallObjectsFinder::search($this->getPlatform(), $this->getRequest()->getParameter('q'), $where, $T);
            $i = 0;
            /* @var $result \NethServer\Tool\FirewallObject */
            $partial = 10;
            foreach ($s as $result) {
                $results[] = array($result->getValue(), $result->getTitle());
                if (++ $i >= $partial) {
                    $view['ResultsCount'] = $view->translate('Show_x_outof_y_label', array('partial' => $partial, 'total' => count($s)));
                    break;
                }
            }
            $view['ResultDatasource'] = $results;
            if ( ! isset($view['ResultsCount'])) {
                $view['ResultsCount'] = '';
            }

            if( ! $this->parameters['q']) {
               $view->getCommandList()->show();
            }
        }
    }

    public function nextPath()
    {
        return FALSE;
    }

    public function getCreateLinks(\Nethgui\View\ViewInterface $view, $hint)
    {
        if ( ! $hint) {
            return array();
        }

        if ($this->parameters['f'] === 'ServiceRaw') {
            return array(
                array('Create' => array($view->getModuleUrl('../CreateService?q=' . $hint), $view->translate('Services_create', array($hint)))),
            );
        } elseif($this->parameters['m'] === 'pf') {
            return array(
                array('Create' => array($view->getModuleUrl('../CreateHost?q=' . $hint), $view->translate('Hosts_create', array($hint)))),
            );
        } elseif($this->parameters['m'] === 'ts') {
            return array(
                array('Create' => array($view->getModuleUrl('../CreateHost?q=' . $hint), $view->translate('Hosts_create', array($hint)))),
            );
        } else {
            return array(
                array('Create' => array($view->getModuleUrl('../CreateHost?q=' . $hint), $view->translate('Hosts_create', array($hint)))),
                array('Create' => array($view->getModuleUrl('../CreateHostGroup?q=' . $hint), $view->translate('HostGroups_create', array($hint)))),
                array('Create' => array($view->getModuleUrl('../CreateZone?q=' . $hint), $view->translate('Zones_create', array($hint)))),
            );
        }
    }

}