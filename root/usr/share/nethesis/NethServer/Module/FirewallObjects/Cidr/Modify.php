<?php
namespace NethServer\Module\FirewallObjects\Cidr;

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
 * Modify Cidr object
 *
 * @author Giacomo Sanchietti <giacomo.sanchietti@nethesis.it>
 */
class Modify extends \Nethgui\Controller\Table\Modify
{
    private $interfaces;

    public function initialize()
    {
        $nameValidator = $this->getPlatform()->createValidator()->username();
        $interfaceValidator = $this->getPlatform()->createValidator()->memberOf($this->interfaces);
        $parameterSchema = array(
            array('name', $nameValidator, \Nethgui\Controller\Table\Modify::KEY),
            array('Address', Validate::CIDR_BLOCK, \Nethgui\Controller\Table\Modify::FIELD),
            array('Description', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
        );

        $this->setSchema($parameterSchema);

        parent::initialize();
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {        
        $keyExists = $this->getPlatform()->getDatabase('hosts')->getType($this->parameters['name']) != '';
        if ($this->getIdentifier() === 'create' && $keyExists) {
            $report->addValidationErrorMessage($this, 'name', 'Cidr_key_exists_message');
        }
        if ($this->getIdentifier() !== 'create' && ! $keyExists) {
            throw new \Nethgui\Exception\HttpException('Not found', 404, 1428583455);
        }
        if($this->getIdentifier() === 'delete') {
            $v = $this->createValidator()->platform('fwobject-cidr-delete', 'hosts');
            if( ! $v->evaluate($this->parameters['name'])) {
                $report->addValidationError($this, 'CidrKey', $v);
            }
        }
        parent::validate($report);
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $templates = array(
            'create' => 'NethServer\Template\FirewallObjects\Cidr\Modify',
            'update' => 'NethServer\Template\FirewallObjects\Cidr\Modify',
            'delete' => 'Nethgui\Template\Table\Delete',
        );
        $view->setTemplate($templates[$this->getIdentifier()]);
    }
}
