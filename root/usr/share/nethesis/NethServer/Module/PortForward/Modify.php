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
    private $wanips = array();

    protected function calculateKeyFromRequest(\Nethgui\Controller\RequestInterface $request)
    {
         $rules = $this->getPlatform()->getDatabase('portforward')->getAll('pf');
         if (!$rules) {
             return 1;
         }
         return (int)max(array_keys($rules))+1;
    }

    private function readWanIPs()
    {
        $ret = array();
        $interfaces = $this->getPlatform()->getDatabase('networks')->getAll();
        foreach ($interfaces as $interface => $props) {
            if (preg_match('/ethernet|bridge|bond|alias|ipsec|vlan/',$props['type'])) {
                # if alias, search for parent ethernet
                if (isset($props['role']) && $props['role'] == 'alias') {
                    $tmp = explode(':',$interface);
                    $tmp = $interfaces[$tmp[0]];
                    $props['role'] = $tmp['role']?$tmp['role']:'';
                }   
                if(isset($props['role']) && $props['role'] == 'red' 
                   && isset($props['ipaddr']) && $props['ipaddr'] != '') {
                    $ret[] = $props['ipaddr'];
                }
            }
        }
        $ret[] = ''; # add any ip source
        return $ret;

    }

    public function initialize()
    {
        $portRangeValidator = $this->createValidator()
            ->orValidator(
                $this->createValidator(Validate::PORTNUMBER),
                $this->createValidator()->regexp('/^[0-9]+\:[0-9]+$/') #port range, no check on maximum value
            );
        $dstValidator = $this->createValidator()
            ->orValidator(
                $this->createValidator(Validate::PORTNUMBER),
                $this->createValidator()->isEmpty()
            );

        $protoValidator = $this->createValidator()->memberOf($this->protocols);
        if (!$this->wanips) {
            $this->wanips = $this->readWanIPs();
        }

        $parameterSchema = array(
            array('id', FALSE, \Nethgui\Controller\Table\Modify::KEY),
            array('Proto', $protoValidator, \Nethgui\Controller\Table\Modify::FIELD),
            array('Src',  $portRangeValidator, \Nethgui\Controller\Table\Modify::FIELD),
            array('Dst', $dstValidator, \Nethgui\Controller\Table\Modify::FIELD),
            array('DstRaw', $this->createValidator()->platform('firewall-object-exists'), \Nethgui\Controller\Table\Modify::FIELD, 'DstHost'),
            array('OriDst', Validate::IPv4_OR_EMPTY, \Nethgui\Controller\Table\Modify::FIELD),
            array('status', Validate::SERVICESTATUS, \Nethgui\Controller\Table\Modify::FIELD),
            array('Allow', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
            array('Description', $this->createValidator()->maxLength(35), \Nethgui\Controller\Table\Modify::FIELD),
        );


        $this->setSchema($parameterSchema);
        $this->setDefaultValue('Proto', 'tcp');
        $this->setDefaultValue('status', 'enabled');
        $this->setDefaultValue('OriDst', '');

        parent::initialize();
    }


    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        parent::bind($request);
        if ($this->getRequest()->isMutation()) {
            $this->parameters['status'] = 'enabled';
        }
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        if ($this->parameters['Allow']) {
            $validator = $this->createValidator()
                ->orValidator(
                    $this->createValidator(Validate::IPv4),
                    $this->createValidator(Validate::CIDR_BLOCK)
                );

            $values = explode(',', $this->parameters['Allow']);
            foreach ($values as $v) {
               if (!$validator->evaluate($v)) {
                   $report->addValidationError($this, 'Allow', $validator);
               }
            }

        }
        if ($this->getRequest()->isMutation()) {
            $duplicate = 0;
            foreach ($this->getPlatform()->getDatabase('portforward')->getAll('pf') as $key => $props) {
               # check duplicate id on create and update
               if (isset($this->parameters['id']) && $this->parameters['id'] == $key) {
                   continue;
               }
               if ($this->parameters['OriDst'] == $props['OriDst'] &&
                   $this->parameters['Src'] == $props['Src']) {
                   $duplicate = 1;
               } 
            }
            if ($duplicate) {
                $report->addValidationErrorMessage($this, 'Src', 'duplicate_pfw');
            }
        }
        parent::validate($report);
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

        if (!$this->wanips) {
            $this->wanips = $this->readWanIPs();
        }
        $view['OriDstDatasource'] = array_map(function($fmt) use ($view) {
                                return array($fmt, $fmt?$fmt:$view->translate('any_label'));
        }, $this->wanips);
        
        $view['Destination'] = $view['DstRaw'] ? \NethServer\Module\FirewallRules\RuleGenericController::translateFirewallObjectTitle($view, $view['DstRaw']) : '';
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
        $this->getPlatform()->signalEvent('firewall-adjust &');
    }

}
