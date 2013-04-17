<?php
namespace NethServer\Module\PortForward;

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
 * Enable/disable port forward rule
 *
 * Fires events
 * - firewall-adjust
 */
class ToggleEnable extends \Nethgui\Controller\Table\RowAbstractAction
{
    private $exitCode = 0;

    public function __construct($identifier = NULL)
    {
        if ($identifier !== 'disable' && $identifier !== 'enable') {
            throw new \InvalidArgumentException(sprintf('%s: module identifier must be one of "disable" or "enable".', get_class($this)), 1325579325);
        }
        parent::__construct($identifier);
    }

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        $keyValue = \Nethgui\array_head($request->getPath());
        $this->getAdapter()->setKeyValue($keyValue);
        parent::bind($request);
        $this->parameters['status'] = $this->getIdentifier() === 'enable' ? 'enabled' : 'disabled';
    }

    public function initialize()
    {
        $parameterSchema = array(
            array('id', FALSE, \Nethgui\Controller\Table\Modify::KEY),
            array('status', Validate::SERVICESTATUS, \Nethgui\Controller\Table\Modify::FIELD),
        );

        $this->setSchema($parameterSchema);
    }


    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);

        if ($this->exitCode != 0) {
            $view->getCommandList('/Notification')->showMessage($view->translate('shorewall_check_error'), \Nethgui\Module\Notification\AbstractNotification::NOTIFY_ERROR);
        }
    }


    protected function onParametersSaved($changedParameters)
    {
        $this->exitCode = $this->getPlatform()->signalEvent('firewall-adjust')->getExitCode();
    }

}
