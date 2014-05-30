<?php

namespace NethServer\Module\FirewallRules;

/*
 * Copyright (C) 2014  Nethesis S.r.l.
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

/**
 * Delete firewall rules
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class Delete extends \Nethgui\Controller\Collection\AbstractAction
{
    public function initialize()
    {
        parent::initialize();
        $this->declareParameter('ruleId', FALSE);
    }

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        $ruleId = \Nethgui\array_head($request->getPath());
        if ( ! $this->getAdapter()->offsetExists($ruleId)) {
            throw new \Nethgui\Exception\HttpException('Not found', 404, 1399992974);
        }
        parent::bind($request);
        $this->parameters['ruleId'] = $ruleId;
    }

    public function process()
    {
        parent::process();
        if ($this->getRequest()->isMutation()) {
            $this->getAdapter()->offsetUnset($this->parameters['ruleId']);
            $this->getAdapter()->save();
        }
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        if ( ! $this->getRequest()->isValidated() || $this->getRequest()->isMutation()) {
            return;
        }
        $view->getCommandList()->show();
        $view['FormAction'] = $view->getModuleUrl($view['ruleId']);

        $props = \iterator_to_array($this->getAdapter()->offsetGet($view['ruleId']));
        $props['id'] = $view['ruleId'];

        $view['message'] = $view->translate('Delete_message', $props);

    }

    public function nextPath()
    {
        return $this->getRequest()->isMutation() ? 'Index' : parent::nextPath();
    }

}