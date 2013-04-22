<?php
namespace NethServer\Module\TrafficShaping\Interfaces;

/*
 * Copyright (C) 2012 Nethesis S.r.l.
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
use Nethgui\Controller\Table\Modify as Table;

/**
 * Modify domain
 *
 * Generic class to create/update/delete port forward records
 * 
 * @author Giacomo Sanchietti <giacomo.sanchietti@nethesis.it>
 */
class Modify extends \Nethgui\Controller\Table\Modify
{

    public function initialize()
    {
        $interfaces = $this->getPlatform()->getDatabase('networks')->getAll('ethernet');
        $parameterSchema = array(
            array('device', $this->createValidator()->memberOf(array_keys($interfaces)), \Nethgui\Controller\Table\Modify::KEY),
            array('in', Validate::POSITIVE_INTEGER, \Nethgui\Controller\Table\Modify::FIELD),
            array('out', Validate::POSITIVE_INTEGER, \Nethgui\Controller\Table\Modify::FIELD),
            array('priority', $this->createValidator()->memberOf(array("1","2","3")), \Nethgui\Controller\Table\Modify::FIELD),
            array('description', $this->createValidator()->maxLength(35), \Nethgui\Controller\Table\Modify::FIELD),
        );


        $this->setSchema($parameterSchema);

        parent::initialize();
    }


    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $templates = array(
            'create' => 'NethServer\Template\TrafficShaping\Interfaces\Modify',
            'update' => 'NethServer\Template\TrafficShaping\Interfaces\Modify',
            'delete' => 'Nethgui\Template\Table\Delete',
        );
        $view->setTemplate($templates[$this->getIdentifier()]);
        
        $view['priorityDatasource'] = array(array('1',$view->translate('1_label')),array('2',$view->translate('2_label')),array('3',$view->translate('3_label')));
        $interfaces = $this->getPlatform()->getDatabase('networks')->getAll('ethernet');
        $configured = $this->getPlatform()->getDatabase('tc')->getAll('device');
        $tmp = array();
        foreach($interfaces as $interface => $props) {
            if (!in_array($interface, array_keys($configured))) {
                # add only not configured interface with role red (for now)
                if (isset($props['role']) && strpos($props['role'],'red') !== false ) { 
                    $tmp[] = array($interface,$interface);
                }
            }
        }
        $view['deviceDatasource'] = $tmp;
    }


    /**
     * Delete the record after the event has been successfully completed
     * @param string $key
     */
    protected function processDelete($key)
    {
        parent::processDelete($key);
    }

    protected function onParametersSaved($changedParameters)
    {
        $this->getPlatform()->signalEvent('firewall-adjust@post-process');
    }

}
