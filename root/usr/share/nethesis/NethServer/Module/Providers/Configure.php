<?php
namespace NethServer\Module\Providers;

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
class Configure extends \Nethgui\Controller\Table\AbstractAction
{

    private $modes = array('balance','backup');

    public function initialize()
    {
        parent::initialize();
        $this->declareParameter('WanMode', $this->createValidator()->memberOf($this->modes), array('configuration', 'firewall','WanMode'));
        $this->declareParameter('CheckIP', Validate::IPv4, array('configuration', 'firewall','CheckIP'));
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

