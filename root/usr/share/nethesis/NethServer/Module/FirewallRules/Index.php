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

/**
 * Show the index of defined firewall rules
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class Index extends \Nethgui\Controller\Collection\AbstractAction
{

    public function initialize()
    {
        parent::initialize();
        $this->declareParameter('Rules', \Nethgui\System\PlatformInterface::ANYTHING_COLLECTION);
    }

    public function process()
    {
        parent::process();
        if ( ! $this->getRequest()->isMutation()) {
            return;
        }

        $rules = is_array($this->parameters['Rules']) ? $this->parameters['Rules'] : array();
        $A = $this->getAdapter();
        foreach ($rules as $key => $values) {
            if ( ! isset($A[$key])) {
                throw new \RuntimeException("Unexistent fwrule key: $key", 1402062247);
            }
            $hasPosition = isset($rules[$key]['Position']) && $rules[$key]['Position'] > 0;
            if ( ! $hasPosition) {
                continue;
            }

            if( ! isset($A[$key]['Position']) || $A[$key]['Position'] != $rules[$key]['Position']) {
                // array assignment merges with existing props:
                $A[$key] = array('Position' => $rules[$key]['Position']);
            }
        }
        if($A->isModified()) {
            $A->save();
        }

        if( ! $this->getRequest()->hasParameter('sortonly')) {
            $this->getPlatform()->signalEvent('firewall-adjust');
        }
    }

    private function resolveEndpoint($ep)
    {
        return strtr($ep, ";", " ");
    }

    private function resolveService($svc)
    {
        return strtr($svc, ";", " ");
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $r = array();

        $actionLabels = array(
            'accept' => $view->translate('ActionAccept_label'),
            'reject' => $view->translate('ActionReject_label'),
            'drop' => $view->translate('ActionDrop_label'),
        );

        foreach ($this->getAdapter() as $key => $values) {
            $values['id'] = (String) $key;
            $values['Position'] = isset($values['Position']) ? intval($values['Position']) : 0;
            $values['Action'] = $actionLabels[$values['Action']];
            $values['Edit'] = $view->getModuleUrl('../Edit/' . $key);
            $values['RuleText'] = $view->translate('RuleText_label', array(
                'Src' => $this->resolveEndpoint($values['Src']),
                'Dst' => $this->resolveEndpoint($values['Dst']),
                'Service' => $this->resolveService($values['Service'])
            ));
            $values['Delete'] = $view->getModuleUrl('../Delete/' . $key);
            $r[] = $values;
        }
        usort($r, function ($a, $b) {
            return $a['Position'] > $b['Position'];
        });

        $positions = array_map(function ($v) { return $v['Position']; }, $r);        
        $first = (isset($positions[0]) ? $positions[0]/2 : \NethServer\Module\FirewallRules::RULESTEP);
        $last = (end($positions) ? end($positions) : 0)  + \NethServer\Module\FirewallRules::RULESTEP;

        $view['hasChanges'] = $this->hasChanges();
        $view['Rules'] = $r;
        $view['Create_last'] = $view->getModuleUrl('../Create/' . intval($last));
        $view['Create_first'] = $view->getModuleUrl('../Create/' . intval($first));

        if ($this->getRequest()->isValidated()) {
            $view->getCommandList()->show();
        }
    }

    private function hasChanges()
    {
        $fwStat = $this->getPhpWrapper()->stat('/etc/shorewall/rules');
        $dbStat = $this->getPhpWrapper()->stat('/var/lib/nethserver/db/fwrules');

        return $dbStat['ctime'] > $fwStat['ctime'] ? '1' : '0';
    }

}