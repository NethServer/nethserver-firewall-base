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
 * Create a new firewall rule object
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class Create extends \Nethgui\Controller\Collection\AbstractAction
{
    /**
     *
     * @var \NethServer\Module\FirewallRules\RuleGenericController
     */
    private $worker;

    /**
     *
     * @var \NethServer\Module\FirewallRules\RuleWorkflow
     */
    private $workflow;

    public function initialize()
    {
        parent::initialize();
        /* @var $worker \Nethgui\Controller\AbstractController */
        $this->worker = new RuleGenericController($this->getIdentifier());
        $this->worker->initializeFromAction($this);
        $this->workflow = new \NethServer\Module\FirewallRules\RuleWorkflow();
    }

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        parent::bind($request);
        $this->position = \Nethgui\array_head($request->getPath());
        if (intval($this->position) <= 0) {
            throw new \Nethgui\Exception\HttpException('Not found', 404, 1399992980);
        }

        $subrequest = $request->spawnRequest($this->position);
        if ($subrequest->hasParameter('f') || $subrequest->hasParameter('Submit')) {
            $this->workflow->resume($this->getParent()->getSession());
        } else {
            // start a new workflow generating a random rule key
            $defaults = array('SrcRaw' => 'any',
                'DstRaw' => 'any',
                'ServiceRaw' => 'any',
                'status' => 'enabled'
            );
            foreach (array('SrcRaw', 'DstRaw', 'ServiceRaw', 'status', 'Description', 'LogType', 'Action') as $f) {
                if ($request->hasParameter($f)) {
                    $defaults[$f] = $request->getParameter($f);
                }
            }
            $this->workflow->start($this->getParent()->getSession(), $this->getIdentifier(), 'Create/' . $this->position, $this->generateNextRuleId(), $defaults);
        }
        $this->worker->ruleId = $this->workflow->getRuleId();
        $this->worker->bind($request);
        $this->workflow->copyTo($this->worker->parameters, array('SrcRaw', 'DstRaw', 'ServiceRaw', 'status', 'Description', 'LogType', 'Action'));

        if ($request->isMutation()) {
            $this->worker->parameters['Position'] = $this->position;
        }
    }

    public function generateNextRuleId()
    {
        $a = iterator_to_array($this->getAdapter());
        if (count($a) === 0) {
            return 1;
        }
        return intval(max(array_filter(array_keys($a), 'intval'))) + 1;
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        $this->worker->validate($report);
    }

    public function process()
    {
        if ($this->getRequest()->isMutation() && $this->getRequest()->hasParameter('Submit')) {
            // create the DB key:
            $this->getPlatform()->getDatabase('fwrules')->setKey($this->workflow->getRuleId(), 'rule', array());
            $this->worker->process();
            $this->getParent()->fixOrderedSetPositions();
        } else {
            $this->worker->process();
        }
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        $this->worker->prepareView($view);
        if (isset($this->position)) {
            $view['FormAction'] = $view->getModuleUrl($this->position);
        }
    }

    public function nextPath()
    {
        return $this->worker->nextPath();
    }

}