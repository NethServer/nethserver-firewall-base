<?php

namespace NethServer\Tool;

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
 * TODO: add component description here
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.6
 */
class SaveState extends \Nethgui\Controller\AbstractController
{
    private $fieldName = '';
    private $returnPath = '';
    private $resumeCallback;
    private $moduleCode;

    /**
     *
     * @var \NethServer\Module\FirewallRules\RuleWorkflow
     */
    private $state;

    public function __construct($identifier = NULL, $moduleCode)
    {
        parent::__construct($identifier);
        $this->moduleCode = $moduleCode;
        $this->state = new \NethServer\Module\FirewallRules\RuleWorkflow();
    }

    public function setField($fieldName)
    {
        $this->fieldName = $fieldName;
        return $this;
    }

    public function setReturnPath($path)
    {
        $this->returnPath = $path;
        return $this;
    }

    public function setResumeCallback($f)
    {
        $this->resumeCallback = $f;
        return $this;
    }

    public function hasResumeCallback()
    {
        return isset($this->resumeCallback);
    }

    public function setResumeState($s)
    {
        $this->resumeState = $s;
        return $this;
    }

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        parent::bind($request);

        /* @var $session \Nethgui\Utility\SessionInterface */
        $session = $this->getParent()->getSession();

        if ($this->fieldName && $this->returnPath && $this->resumeState) {
            $this->state->start($session, NULL, $this->returnPath, NULL, $this->resumeState);
            $session->store(__CLASS__, new \ArrayObject(array_keys($this->resumeState)));
        }
    }

    public function resumeView(\Nethgui\View\ViewInterface $view)
    {
        $session = $this->getParent()->getSession();
        $vs = new \ArrayObject();
        $this->state->resume($session)->copyTo($vs, iterator_to_array($session->retrieve(__CLASS__)));
        call_user_func($this->resumeCallback, $view, $vs);
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);

        $view->setTemplate(function () {
            return '';
        });

        if ($this->fieldName && $this->returnPath && $this->resumeState) {
            $view->getCommandList()->sendQuery($view->getModuleUrl(sprintf('../PickObject?f=%s&m=%s', $this->fieldName, $this->moduleCode)));
        }
    }

}