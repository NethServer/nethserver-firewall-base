<?php
namespace NethServer\Module\TrafficShaping;

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
 * Configure traffic shaping rules of type port
 */
class Ip extends \Nethgui\Controller\TableController implements \Nethgui\Utility\SessionConsumerInterface
{
    /**
     *
     * @var \Nethgui\Utility\SessionInterface
     */
    private $session;

    private $myCurrentAction;

    public function initialize()
    {

        $columns = array(
            'Key',
            'Priority',
            'Description',
            'Actions'
        );

        $this
            ->setTableAdapter($this->getPlatform()->getTableAdapter('tc','ip'))
            ->setColumns($columns)
            ->addTableAction(new \NethServer\Module\TrafficShaping\Ip\Modify('create'))            
            ->addTableAction(new \Nethgui\Controller\Table\Help('Help'))
            ->addRowAction(new \NethServer\Module\TrafficShaping\Ip\Modify('update'))
            ->addRowAction(new \NethServer\Module\TrafficShaping\Ip\Modify('delete'))
            ->addChild(new \NethServer\Module\FirewallRules\CreateHost())
            ->addChild(new \NethServer\Module\FirewallRules\CreateHostGroup())
            ->addChild(new \NethServer\Module\FirewallRules\CreateZone())
            ->addChild(new \NethServer\Tool\SaveState(NULL, 'ts'))
            ->addChild(new \NethServer\Tool\PickObject());
        ;

        parent::initialize();
    }

    protected function establishCurrentActionId()
    {
        $request = $this->getRequest();
        $params = array();
        if ($request->hasParameter('create')) {
            $params = $request->getParameter('create');
            $action = 'create';
        } elseif ($request->hasParameter('update')) {
            if($request->isMutation()) {
            $params = $request->getParameter('update');
            } else {
               $subRequest = $request->spawnRequest('update');
               $params = $subRequest->getParameter(\Nethgui\array_head($subRequest->getPath()));
            }
            $action = implode('/', $request->getPath());
        }
        if (isset($params['PickSource']) && $request->isMutation()) {
            $this->getAction('SaveState')->setField('SrcRaw')->setReturnPath($action)->setResumeState($params);
            return 'SaveState';                    
        }

        $this->myCurrentAction = parent::establishCurrentActionId();

        if(isset($params['f'], $params['h']) && ! $request->isMutation()) {
            $this->getAction('SaveState')->setResumeCallback(function (\Nethgui\View\ViewInterface $view, $state) {
                $view['Priority'] = $state['Priority'];
                $view['Description'] = $state['Description'];
                $view['SrcRaw'] = $state['SrcRaw'];
                $view['Source'] = ucfirst(str_replace(';', ' ', $state['SrcRaw']));
                $view->getCommandList()->show();
            });
        }

        return $this->myCurrentAction;
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        if($this->getAction('SaveState')->hasResumeCallback()) {
            $this->getAction('SaveState')->resumeView($view[$this->myCurrentAction]);
        }
    }

    public function prepareViewForColumnPriority(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        return $view->translate($values['Priority']."_label");
    }

    public function prepareViewForColumnKey(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        return ucfirst(str_replace(';', ' ', $key));
    }

    public function setSession(\Nethgui\Utility\SessionInterface $session)
    {
        $this->session = $session;
        return $this;
    }

    public function getSession()
    {
        return $this->session;
    }

}

