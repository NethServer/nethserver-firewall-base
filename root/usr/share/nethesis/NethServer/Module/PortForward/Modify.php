<?php
namespace NethServer\Module\PortForward;

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

    private $exitCode = 0;
    private $protocols = array('tcp','udp','tcp,udp');

    protected function calculateKeyFromRequest(\Nethgui\Controller\RequestInterface $request)
    {
         $rules = $this->getPlatform()->getDatabase('portforward')->getAll('pf');
         if (!$rules) {
             return 1;
         }
         return (int)max(array_keys($rules))+1;
    }

    public function initialize()
    {
        $portRangeValidator = $this->createValidator()
            ->orValidator(
                $this->createValidator()->integer()->greatThan(0)->lessThan(65535),
                $this->createValidator()->regexp('/^[0-9]+\:[0-9]+$/') #port range, no check on maximum value
            );
        $dstValidator = $this->createValidator()
            ->orValidator(
                $this->createValidator()->integer()->greatThan(0)->lessThan(65535),
                $this->createValidator()->isEmpty()
            );

        $protoValidator = $this->createValidator()->memberOf($this->protocols);

        $parameterSchema = array(
            array('id', FALSE, \Nethgui\Controller\Table\Modify::KEY),
            array('Proto', $protoValidator, \Nethgui\Controller\Table\Modify::FIELD),
            array('Src',  $portRangeValidator, \Nethgui\Controller\Table\Modify::FIELD),
            array('Dst', $dstValidator, \Nethgui\Controller\Table\Modify::FIELD),
            array('DstHost', Validate::IPv4, \Nethgui\Controller\Table\Modify::FIELD),
            array('SrcHost', Validate::IPv4_OR_EMPTY, \Nethgui\Controller\Table\Modify::FIELD),
            array('status', Validate::SERVICESTATUS, \Nethgui\Controller\Table\Modify::FIELD),
            array('Allow', $this->createValidator(Validate::ANYTHING)->platform('shorewall-check'), \Nethgui\Controller\Table\Modify::FIELD),
            array('Description', $this->createValidator()->maxLength(35), \Nethgui\Controller\Table\Modify::FIELD),
        );


        $this->setSchema($parameterSchema);
        $this->setDefaultValue('Proto', 'tcp');
        $this->setDefaultValue('status', 'enabled');

        parent::initialize();
    }


    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        parent::bind($request);
        if ($this->getRequest()->isMutation()) {
            $this->parameters['status'] = 'enabled';
        }
    }

 
    public function process()
    {
        parent::process();
    }


    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $templates = array(
            'create' => 'NethServer\Template\PortForward\Modify',
            'update' => 'NethServer\Template\PortForward\Modify',
            'delete' => 'Nethgui\Template\Table\Delete',
        );
        $view->setTemplate($templates[$this->getIdentifier()]);
        
        $view['ProtoDatasource'] = array_map(function($fmt) use ($view) {
                                return array($fmt, $view->translate($fmt . '_label'));
        }, $this->protocols);

 
        if ($this->exitCode != 0) {
            $view->getCommandList('/Notification')->showMessage($view->translate('shorewall_check_error'), \Nethgui\Module\Notification\AbstractNotification::NOTIFY_ERROR);
        }
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
        $this->exitCode = $this->getPlatform()->signalEvent('firewall-adjust')->getExitCode();
    }

}
