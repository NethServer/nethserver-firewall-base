<?php

namespace NethServer\Module;

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
 * Configure port forward
 */
class PortForward extends \Nethgui\Controller\TableController implements \Nethgui\Utility\SessionConsumerInterface
{
    /**
     *
     * @var \Nethgui\Utility\SessionInterface
     */
    private $session;
    private $myCurrentAction;

    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $attributes)
    {
        return new \NethServer\Tool\CustomModuleAttributesProvider($attributes, array(
            'languageCatalog' => array('NethServer_Module_PortForward', 'NethServer_Module_FirewallRules'),
            'category' => 'Gateway')
        );
    }

    public function initialize()
    {

        $columns = array(
            'Key',
            'Proto',
            'Src',
            'DstHost',
            'Dst',
            'OriDst',
            'Description',
            'Actions'
        );

        $this
            ->setTableAdapter($this->getPlatform()->getTableAdapter('portforward', 'pf'))
            ->setColumns($columns)
            ->addTableAction(new \NethServer\Module\PortForward\Modify('create'))
            ->addTableAction(new \Nethgui\Controller\Table\Help('Help'))
            ->addTableAction(new \NethServer\Module\PortForward\HairpinNat())
            ->addRowAction(new \NethServer\Module\PortForward\ToggleEnable('disable'))
            ->addRowAction(new \NethServer\Module\PortForward\ToggleEnable('enable'))
            ->addRowAction(new \NethServer\Module\PortForward\Modify('update'))
            ->addRowAction(new \NethServer\Module\PortForward\Modify('delete'))
            ->addChild(new \NethServer\Module\FirewallRules\CreateHost())
            ->addChild(new \NethServer\Tool\SaveState(NULL, 'pf'))
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
            if ($request->isMutation()) {
                $params = $request->getParameter('update');
            } else {
                $subRequest = $request->spawnRequest('update');
                $params = $subRequest->getParameter(\Nethgui\array_head($subRequest->getPath()));
            }
            $action = implode('/', $request->getPath());
        }
        if (isset($params['PickDestination']) && $request->isMutation()) {
            $this->getAction('SaveState')->setField('DstRaw')->setReturnPath($action)->setResumeState($params);
            return 'SaveState';
        }

        $this->myCurrentAction = parent::establishCurrentActionId();

        if (isset($params['f'], $params['h']) && ! $request->isMutation()) {
            $this->getAction('SaveState')->setResumeCallback(function (\Nethgui\View\ViewInterface $view, $state) {
                $view['Proto'] = $state['Proto'];
                $view['Description'] = $state['Description'];
                $view['DstRaw'] = $state['DstRaw'];
                $view['OriDst'] = $state['OriDst'];
                $view['Src'] = $state['Src'];
                $view['Dst'] = $state['Dst'];
                $view['Allow'] = $state['Allow'];
                $view['Destination'] = \NethServer\Module\FirewallRules\RuleGenericController::translateFirewallObjectTitle($view, $state['DstRaw']);
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

    public function prepareViewForColumnOriDst(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        return $values['OriDst']?$values['OriDst']:$view->translate('any_label');
    }

    public function prepareViewForColumnProto(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        if (!isset($values['status']) || ($values['status'] == "disabled")) {
            $rowMetadata['rowCssClass'] = trim($rowMetadata['rowCssClass'] . ' user-locked');
        }
        return strtoupper($values['Proto']);
    }

    public function prepareViewForColumnDstHost(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        if( ! isset($values['DstHost'])) {
            return '';
        }
        return \NethServer\Module\FirewallRules\RuleGenericController::translateFirewallObjectTitle($view, $values['DstHost']);
    }

    /**
     * Override prepareViewForColumnActions to hide/show enable/disable actions
     * @param \Nethgui\View\ViewInterface $view
     * @param string $key The data row key
     * @param array $values The data row values
     * @return \Nethgui\View\ViewInterface 
     */
    public function prepareViewForColumnActions(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        $cellView = $action->prepareViewForColumnActions($view, $key, $values, $rowMetadata);

        $killList = array();

        $state = isset($values['status']) ? $values['status'] : 'enabled';

        switch ($state) {
            case 'enabled':
                $remove = 'enable';
                break;
            case 'disabled';
                $remove = 'disable';
                break;
            default:
                break;
        }

        unset($cellView[$remove]);

        return $cellView;
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
