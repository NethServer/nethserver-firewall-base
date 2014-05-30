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
 * Edit existing firewall rules
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class Edit extends \Nethgui\Controller\Collection\AbstractAction
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
        $this->worker
            ->setParent($this->getParent())
            ->setPlatform($this->getPlatform())
            ->setPolicyDecisionPoint($this->getPolicyDecisionPoint())
            ->setLog($this->getLog())
            ->initialize();
    }

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        $ruleId = \Nethgui\array_head($request->getPath());
        if ( ! $this->getAdapter()->offsetExists($ruleId)) {
            throw new \Nethgui\Exception\HttpException('Not found', 404, 1399992975);
        }
        $this->worker->ruleId = $ruleId;
        $this->worker->bind($request);

        $this->workflow = new \NethServer\Module\FirewallRules\RuleWorkflow();
        if ($request->spawnRequest($ruleId)->hasParameter('f') || $request->isMutation()) {
            $this->workflow->resume($this->getParent()->getSession())->copyTo($this->worker->parameters, array('SrcRaw', 'DstRaw', 'ServiceRaw'));
        } else {
            $this->workflow->start($this->getParent()->getSession(), $this->getIdentifier(), 'Edit/' . $ruleId, $ruleId);
        }
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        $this->worker->validate($report);
    }

    public function process()
    {
        $this->worker->process();
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        $this->worker->prepareView($view);
        $view['FormAction'] = $view->getModuleUrl($this->worker->ruleId);
    }

    public function nextPath()
    {
        return $this->worker->nextPath();
    }

}