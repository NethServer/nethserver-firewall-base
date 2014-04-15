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

use Nethgui\System\PlatformInterface as Validate;

/**
 * Delete firewall rules
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class Delete extends \Nethgui\Controller\Collection\AbstractAction
{
    private $ruleProps = array();

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
        $this->ruleProps = \iterator_to_array($this->getAdapter()->offsetGet($ruleId));
        $this->ruleProps['id'] = $ruleId;
    }

    public function process()
    {
        if ($this->getRequest()->isMutation()) {
            $A = $this->getAdapter();

            $found = FALSE;

            foreach ($A as $key => $values) {
                if ($key == $this->parameters['ruleId']) {
                    $found = TRUE;                   
                }

                if ($found === TRUE && isset($A[$key + 1])) {
                    $A[$key] = $A[$key + 1];
                }
            }
            unset($A[$key]);

            $A->save();
            $A->flush();
        }
        parent::process();
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        if ( ! $this->getRequest()->isValidated()) {
            return;
        }
        $view['FormAction'] = $view->getModuleUrl($this->parameters['ruleId']);
        $view['message'] = $view->translate('Delete_message', $this->ruleProps);
        if ( ! $this->getRequest()->isMutation()) {
            $view->getCommandList()->show();
        }
    }

    public function nextPath()
    {
        return $this->getRequest()->isMutation() ? 'Index' : parent::nextPath();
    }

}