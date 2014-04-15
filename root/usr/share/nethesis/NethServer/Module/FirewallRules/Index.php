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

    private function sortRules()
    {
        $changes = array();
        if ( ! is_array($this->parameters['Rules'])) {
            return;
        }
        $A = $this->getAdapter();
        foreach ($this->parameters['Rules'] as $key => $values) {
            if ($key == $values['position']) {
                continue;
            }
            $changes[$values['position']] = $A[$key];
        }
        foreach ($changes as $key => $values) {
            $A[$key] = $values;
        }

        if ($A->isModified()) {
            $A->save();
            $A->flush();
        }
    }

    public function process()
    {
        parent::process();
        if ($this->getRequest()->isMutation()) {
            $this->sortRules();
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
            'ACCEPT' => $view->translate('ActionAccept_label'),
            'REJECT' => $view->translate('ActionReject_label'),
            'DROP' => $view->translate('ActionDrop_label'),
            );

        $i = 1;
        foreach ($this->getAdapter() as $key => $values) {
            $values['id'] = (String) $key;
            $values['position'] = (String) $i;
            $values['Action'] = $actionLabels[$values['Action']];
            $values['Edit'] = $view->getModuleUrl('../Edit/' . $key);
            $values['RuleText'] = $view->translate('RuleText_label', array(
                'Src' => $this->resolveEndpoint($values['Src']),
                'Dst' => $this->resolveEndpoint($values['Dst']),
                'Service' => $this->resolveService($values['Service'])
            ));
            $values['Delete'] = $view->getModuleUrl('../Delete/' . $key);
            $r[] = $values;
            $i += 1;
        }
        usort($r, function ($a, $b) {
            return intval($a['id']) > intval($b['id']);
        });

        $view['hasChanges'] = $this->hasChanges();
        $view['Rules'] = $r;

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