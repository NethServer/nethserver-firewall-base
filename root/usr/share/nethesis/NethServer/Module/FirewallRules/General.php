<?php
namespace NethServer\Module\FirewallRules;

/*
 * Copyright (C) 2011 Nethesis S.r.l.
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
 * Change general firewall options
 *
 * @author Giacomo Sanchietti<giacomo.sanchietti@nethesis.it>
 */
class General extends \Nethgui\Controller\AbstractController
{

    private $policies = array('permissive','strict');

    public function initialize()
    {
        parent::initialize();
        $this->declareParameter('Policy', $this->createValidator()->memberOf($this->policies), array('configuration', 'firewall', 'Policy'));
        $this->declareParameter('ExternalPing', Validate::SERVICESTATUS, array('configuration', 'firewall', 'ExternalPing'));
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $view['PolicyDatasource'] = array_map(function($fmt) use ($view) {
                                return array($fmt, $view->translate($fmt . '_label'));
        }, $this->policies);
        $view['ExternalPingDatasource'] = array_map(function($fmt) use ($view) {
                                return array($fmt, $view->translate($fmt . '_label'));
        }, array('enabled','disabled'));

    }


    protected function onParametersSaved($changes)
    {
        $this->getPlatform()->signalEvent('firewall-adjust');
    }

}

