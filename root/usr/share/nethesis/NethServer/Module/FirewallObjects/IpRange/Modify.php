<?php
namespace NethServer\Module\FirewallObjects\IpRange;

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

/**
 * Modify IpRange object
 *
 * @author Giacomo Sanchietti <giacomo.sanchietti@nethesis.it>
 */
class Modify extends \Nethgui\Controller\Table\Modify
{

    public function initialize()
    {        
        $parameterSchema = array(
            array('name', Validate::USERNAME, \Nethgui\Controller\Table\Modify::KEY),
            array('Start', Validate::IPv4, \Nethgui\Controller\Table\Modify::FIELD),
            array('End', Validate::IPv4, \Nethgui\Controller\Table\Modify::FIELD),
            array('Description', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
        );

        $this->setSchema($parameterSchema);

        parent::initialize();
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {        
        $keyExists = $this->getPlatform()->getDatabase('hosts')->getType($this->parameters['name']) != '';
        if ($this->getIdentifier() === 'create' && $keyExists) {
            $report->addValidationErrorMessage($this, 'name', 'IpRange_key_exists_message');
        }
        if ($this->getIdentifier() !== 'create' && ! $keyExists) {
            throw new \Nethgui\Exception\HttpException('Not found', 404, 1428599495);
        }
        if($this->getIdentifier() === 'delete') {
            $v = $this->createValidator()->platform('fwobject-iprange-delete', 'hosts');
            if( ! $v->evaluate($this->parameters['name'])) {
                $report->addValidationError($this, 'IpRangeKey', $v);
            }
        }
        $ipValidator = $this->createValidator()->ipV4Address();

        if ( ($this->getIdentifier() === 'create' || $this->getIdentifier() == 'update' ) &&
             $ipValidator->evaluate($this->parameters['Start']) && $ipValidator->evaluate($this->parameters['End'])) {
            if (ip2long($this->parameters['Start']) >= ip2long($this->parameters['End'])) {
                $report->addValidationErrorMessage($this, 'Start', 'valid_iprange_outofbounds');
                $report->addValidationErrorMessage($this, 'End', 'valid_iprange_outofbounds');
            }
        }
        parent::validate($report);
    }



    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $templates = array(
            'create' => 'NethServer\Template\FirewallObjects\IpRange\Modify',
            'update' => 'NethServer\Template\FirewallObjects\IpRange\Modify',
            'delete' => 'Nethgui\Template\Table\Delete',
        );
        $view->setTemplate($templates[$this->getIdentifier()]);
    }
}
