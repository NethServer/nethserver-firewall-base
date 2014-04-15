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
class PickObject extends \Nethgui\Controller\Collection\AbstractAction
{
    private $target = NULL;

    public function initialize()
    {
        parent::initialize();
        $this->declareParameter('q', Validate::ANYTHING);
        $this->declareParameter('Result', Validate::ANYTHING);
        $this->declareParameter('RuleId', FALSE, array('SESSION', 'FirewallRules.PickObject.In', 'RuleId'));
        $this->declareParameter('QueryNext', FALSE, array('SESSION', 'FirewallRules.PickObject.In', 'QueryNext'));
    }

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        $this->target = \Nethgui\array_head($request->getPath());
        if ( ! in_array($this->target, array('Source', 'Destination', 'Service'))) {
            throw new \Nethgui\Exception\HttpException('Not found', 404, 1400233824);
        }
        parent::bind($request);
    }

    public function process()
    {
        parent::process();
        $db = $this->getPlatform()->getDatabase('SESSION');
        if ($this->getRequest()->isMutation()) {
            if ( ! $db->getKey('FirewallRules.PickObject.Out')) {
                $db->setKey('FirewallRules.PickObject.Out', 'PickObject', array());
            }
            $db->setProp('FirewallRules.PickObject.Out', array($this->target => $this->parameters['Result']));
        } else {
            $db->delProp('FirewallRules.PickObject.Out', array($this->target));
        }
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        if ($this->getRequest()->isMutation()) {
            $view->getCommandList()->sendQuery($view->getModuleUrl($this->parameters['QueryNext']));
        } elseif ($this->getRequest()->isValidated()) {
            $view['FormAction'] = $view->getModuleUrl($this->target);
            $view['CreateLinks'] = $this->getCreateLinks($view);
            $view['PickObjectHeader'] = $view->translate("PickObject_" . $this->target . "_header", array('RuleId' => $this->parameters['RuleId']));
            $r = array();

            if ($this->target === 'Service') {
                $where = array('fwservices' => array('fwservice'));
            } else {
                $where = array('hosts' => array('host', 'host-group'), 'networks' => array('zone'));
            }

            $s = \NethServer\Tool\FirewallObjectsFinder::search($this->getPlatform(), $this->getRequest()->getParameter('q'), $where);
            $i = 0;
            /* @var $result \NethServer\Tool\FirewallObject */
            foreach ($s as $result) {
                $r[] = array($result->getValue(), $result->getShortTitle());
                if (++ $i >= 10) {
                    $view['ResultsCount'] = 'Showing 10 results out of ' . count($s);
                    break;
                }
            }
            $view['ResultDatasource'] = $r;
            if ( ! isset($view['ResultsCount'])) {
                $view['ResultsCount'] = '';
            }

            $view->getCommandList()->show();
        }
    }

    public function nextPath()
    {
        return FALSE;
    }

    public function getCreateLinks(\Nethgui\View\ViewInterface $view)
    {
        if ( ! $this->parameters['q']) {
            return array();
        }

        return array(
            array('Create' => array($view->getModuleUrl('../HostGroups/create'), $view->translate('HostGroups_create', array($this->parameters['q'])))),
            array('Create' => array($view->getModuleUrl('../Zones/create'), $view->translate('Zones_create', array($this->parameters['q'])))),
            array('Create' => array($view->getModuleUrl('../Hosts/create'), $view->translate('Hosts_create', array($this->parameters['q'])))),
            array('Create' => array($view->getModuleUrl('../Services/create'), $view->translate('Services_create', array($this->parameters['q'])))),
        );
    }

}