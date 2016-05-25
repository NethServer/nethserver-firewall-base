<?php
namespace NethServer\Module;

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
 * Change WanMode
 *
 * @author Giacomo Sanchietti<giacomo.sanchietti@nethesis.it>
 */
class Providers extends \Nethgui\Controller\AbstractController
{

    private $modes = array('balance','backup');

    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $base)
    {
          return \Nethgui\Module\SimpleModuleAttributesProvider::extendModuleAttributes($base, 'Gateway', 80);
    }


    public function initialize()
    {
        parent::initialize();
        $mv = $this->createValidator()->orValidator($this->createValidator(Validate::EMAIL), $this->createValidator(Validate::EMPTYSTRING));
        $mnplv = $this->createValidator()->integer()->greatThan(2)->lessThan(99);
        $mpplv = $this->createValidator()->integer()->greatThan(0)->lessThan(100);
        $piv = $this->createValidator()->integer()->greatThan(0)->lessThan(60);
        $this->declareParameter('WanMode', $this->createValidator()->memberOf($this->modes), array('configuration', 'firewall','WanMode'));
        $this->declareParameter('CheckIP', Validate::ANYTHING, array('configuration', 'firewall','CheckIP'));
        $this->declareParameter('NotifyWan', Validate::SERVICESTATUS, array('configuration', 'firewall','NotifyWan'));
        $this->declareParameter('NotifyWanFrom', $mv, array('configuration', 'firewall','NotifyWanFrom'));
        $this->declareParameter('NotifyWanTo', $mv, array('configuration', 'firewall','NotifyWanTo'));
        $this->declareParameter('MaxNumberPacketLoss', $mnplv, array('configuration', 'firewall','MaxNumberPacketLoss'));
        $this->declareParameter('MaxPercentPacketLoss', $mpplv, array('configuration', 'firewall','MaxPercentPacketLoss'));
        $this->declareParameter('PingInterval', $piv, array('configuration', 'firewall','PingInterval'));
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        parent::validate($report);
        if( $this->getRequest()->isMutation() ) {
            $ipv =  $this->createValidator(Validate::IPv4);
            foreach (explode(',',$this->parameters['CheckIP']) as $ip) {
                if (!$ipv->evaluate($ip)) {
                   $report->addValidationError($this, 'CheckIP', $ipv);
                }
            }
        }
    }


    protected function onParametersSaved($changes)
    {
        $this->getPlatform()->signalEvent('firewall-adjust');
    }

    public function prepareView(\Nethgui\View\ViewInterface $view) 
    {
        parent::prepareView($view);

        $view['WanMode'] = $this->getPlatform()->getDatabase('configuration')->getProp('firewall','WanMode');
        $view['WanModeDatasource'] = array_map(function($fmt) use ($view) {
                                return array($fmt, $view->translate($fmt . '_label'));
        }, $this->modes);
    }

}

