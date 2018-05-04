<?php
namespace NethServer\Module\TcClasses;

/*
 * Copyright (C) 2018 Nethesis S.r.l.
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
 * Change TC configuration
 *
 * @author Giacomo Sanchietti<giacomo.sanchietti@nethesis.it>
 */
class Configure extends \Nethgui\Controller\Table\AbstractAction
{

    public function initialize()
    {
        $rateValidator = $this->createValidator()
            ->orValidator(
                $this->createValidator()->integer()->greatThan(0)->lessThan(100),
                $this->createValidator()->isEmpty()
            );

        parent::initialize();
        $this->declareParameter('TCTosOptimization', Validate::SERVICESTATUS, array('configuration', 'firewall', 'TCTosOptimization'));
        $this->declareParameter('TCVoipReservation', $rateValidator, array('configuration', 'firewall', 'TCVoipReservation'));
        $this->declareParameter('TCPriority', $this->createValidator()->memberOf('priority', 'balanced'), array('configuration', 'firewall', 'TCPriority'));

    }

    protected function onParametersSaved($changes)
    {
        $this->getPlatform()->signalEvent('firewall-adjust &');
    }

}

