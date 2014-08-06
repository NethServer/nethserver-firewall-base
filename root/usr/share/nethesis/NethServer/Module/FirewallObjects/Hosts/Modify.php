<?php

namespace NethServer\Module\FirewallObjects\Hosts;

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
use Nethgui\Controller\Table\Modify as Table;

/**
 * Host modify actions
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class Modify extends \Nethgui\Controller\Table\Modify
{

    public function initialize()
    {
        $this->setSchema(array(
            array('name', Validate::USERNAME, Table::KEY),
            array('IpAddress', Validate::IPv4, Table::FIELD),
            array('Description', Validate::ANYTHING, Table::FIELD)
        ));
        parent::initialize();
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        $keyExists = $this->getPlatform()->getDatabase('hosts')->getType($this->parameters['name']) != '';
        if ($this->getIdentifier() === 'create' && $keyExists) {
            $report->addValidationErrorMessage($this, 'name', 'Host_key_exists_message');
        }
        if ($this->getIdentifier() !== 'create' && ! $keyExists) {
            throw new \Nethgui\Exception\HttpException('Not found', 404, 1407169971);
        }
        if($this->getIdentifier() === 'delete') {
            $v = $this->createValidator()->platform('fwobject-host-delete', 'hosts');
            if( ! $v->evaluate($this->parameters['name'])) {
                $report->addValidationError($this, 'HostsKey', $v);
            }
        }
        parent::validate($report);
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $templates = array(
            'create' => 'NethServer\Template\FirewallObjects\Hosts',
            'update' => 'NethServer\Template\FirewallObjects\Hosts',
            'delete' => 'Nethgui\Template\Table\Delete',
        );
        $view->setTemplate($templates[$this->getIdentifier()]);
    }

}