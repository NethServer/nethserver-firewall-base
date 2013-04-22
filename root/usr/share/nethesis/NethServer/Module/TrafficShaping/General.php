<?php
namespace NethServer\Module\TrafficShaping;

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
 * Change the system time settings
 *
 * @author Giacomo Sanchietti <giacomo.sanchietti@nethesis.i>
 */
class General extends \Nethgui\Controller\AbstractController
{

    public function initialize()
    {
        parent::initialize();
        
        $this->declareParameter('tc', $this->createValidator()->memberOf('No','Simple'), array('configuration', 'firewall', 'tc'));        

    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);

        $view['tcDatasource'] = array(array('Simple',$view->translate('simple_label')),array('No',$view->translate('no_label')));

    }


    protected function onParametersSaved($changedParameters)
    {
        $this->getPlatform()->signalEvent('firewall-adjust@post-process');
    }

}
