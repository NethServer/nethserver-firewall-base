<?php
namespace NethServer\Module\FirewallObjects\Services;

/*
 * Copyright (C) 2014 Nethesis S.r.l.
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
 * Modify Service object
 *
 * @author Giacomo Sanchietti <giacomo.sanchietti@nethesis.it>
 */
class Modify extends \Nethgui\Controller\Table\Modify
{
    private $protocols = array('tcp', 'tcpudp', 'udp');

    public function initialize()
    {
        $parameterSchema = array(
            array('name', Validate::USERNAME, \Nethgui\Controller\Table\Modify::KEY),
            array('Protocol', $this->createValidator()->memberOf($this->protocols), \Nethgui\Controller\Table\Modify::FIELD),
            array('Description', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
            array('Ports', Validate::NOTEMPTY, \Nethgui\Controller\Table\Modify::FIELD)
        );

        $this->setSchema($parameterSchema);

        parent::initialize();
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $templates = array(
            'create' => 'NethServer\Template\FirewallObjects\Services\Modify',
            'update' => 'NethServer\Template\FirewallObjects\Services\Modify',
            'delete' => 'Nethgui\Template\Table\Delete',
        );
        $view->setTemplate($templates[$this->getIdentifier()]);
        $view['ProtocolDatasource'] = array_map(function($fmt) use ($view) {
                                return array($fmt, $view->translate($fmt . '_label'));
        }, $this->protocols);
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        if ($this->getRequest()->isMutation()) {
            if ( strpos($this->parameters['Ports'], ",") !== false ) { # list of ports
                $ports = explode(',',$this->parameters['Ports']);
                $v = $this->createValidator(Validate::PORTNUMBER);
                foreach($ports as $port) {
                    if(!$v->evaluate($port)) {
                        $report->addValidationErrorMessage($this, 'Ports', 'Ports_validator');
                    }
                }
            } else if ( strpos($this->parameters['Ports'], "-") !== false ) { # port range
                $tmp = explode("-",$this->parameters['Ports']);
                # two non-zero integers, right number must be greater then left one
                if ( !isset($tmp[0], $tmp[1]) || !(int)$tmp[0] || !(int)$tmp[1] || (int)$tmp[0] >= (int)$tmp[1] ) {
                    $report->addValidationErrorMessage($this, 'Ports', 'Port_range_validator');
                }
            }
        }
                
        $keyExists = $this->getPlatform()->getDatabase('fwservices')->getType($this->parameters['name']) != '';
        if($this->getIdentifier() === 'create' && $keyExists) {
             $report->addValidationErrorMessage($this, 'name', 'Service_key_exists_message');
        }
        if($this->getIdentifier() !== 'create' && ! $keyExists) {
            throw new \Nethgui\Exception\HttpException('Not found', 404, 1407169969);
        }
       if($this->getIdentifier() === 'delete') {
            $v = $this->createValidator()->platform('fwobject-fwservice-delete', 'fwservices');
            if( ! $v->evaluate($this->parameters['name'])) {
                $report->addValidationError($this, 'ServicesKey', $v);
            }
        }
        parent::validate($report);
    }


}
