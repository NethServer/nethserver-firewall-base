<?php
namespace NethServer\Module\FirewallObjects\HostGroups;

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
 * Group modify actions
 *
 * @author Giacomo Sanchietti <giacomo.sanchietti@nethesis.it>
 * @since 1.0
 */
class Modify extends \Nethgui\Controller\Table\Modify
{

    public function initialize()
    {
        // The group name must satisfy the USERNAME generic grammar:
        if ($this->getIdentifier() === 'create') {
            $groupNameValidator = $this->createValidator(Validate::USERNAME);
        } else {
            $groupNameValidator = FALSE;
        }

        $membersValidator = $this->createValidator()->notEmpty()->collectionValidator($this->createValidator(Validate::USERNAME));

        $parameterSchema = array(
            array('name', $groupNameValidator, Table::KEY),
            array('Description', Validate::ANYTHING, Table::FIELD, 'Description'),
            array('Members', $membersValidator, Table::FIELD, 'Members', ','),
            array('MembersDatasource', FALSE, array($this, 'provideMembersDatasource')), // this parameter will never be submitted: set an always-failing validator
        );
        
        $this->setSchema($parameterSchema);

        parent::initialize();
    }

    public function provideMembersDatasource()
    {
        $platform = $this->getPlatform();
        if (is_null($platform)) {
            return array();
        }

        $hosts = $platform->getDatabase('hosts')->getAll();
        $values = array();

        // Build the datasource rows couples <key, label>
        foreach ($hosts as $key => $row) {
            if (!isset($row['IpAddress'])) {
                continue;
            }
            $values[] = array($key, sprintf('%s (%s)', $key, $row['IpAddress']));
        }
        return $values;
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $templates = array(
            'create' => 'NethServer\Template\FirewallObjects\HostGroups\Modify',
            'update' => 'NethServer\Template\FirewallObjects\HostGroups\Modify',
            'delete' => 'Nethgui\Template\Table\Delete',
        );
        $view->setTemplate($templates[$this->getIdentifier()]);
    }

}
