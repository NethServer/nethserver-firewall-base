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
 * Create a new Time record
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class CreateTime extends \Nethgui\Controller\Collection\AbstractAction
{
    /**
     *
     * @var \NethServer\Module\FirewallRules\RuleWorkflow
     */
    private $state;
    
    private $interfaces;

    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $attributes)
    {
        return new \NethServer\Tool\CustomModuleAttributesProvider($attributes, array('languageCatalog' => 'NethServer_Module_FirewallObjects'));
    }

    public function initialize()
    {
        $weekDaysValidator = $this->createValidator()->notEmpty()->collectionValidator($this->createValidator()->memberOf(\NethServer\Module\FirewallObjects\Times\Modify::$weekDays));

        parent::initialize();
        $this->state = new \NethServer\Module\FirewallRules\RuleWorkflow();
        $this->declareParameter('name', Validate::USERNAME);
        $this->declareParameter('Description', Validate::ANYTHING);
        $this->declareParameter('TimeStart', Validate::TIME);
        $this->declareParameter('TimeStop', Validate::TIME);
        $this->declareParameter('WeekDays', $weekDaysValidator);
        $this->declareParameter('f', $this->createValidator()->memberOf('d', 's'));
        $this->declareParameter('i', Validate::POSITIVE_INTEGER);
        $this->declareParameter('q', Validate::ANYTHING);
    }

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        parent::bind($request);
        if ($request->isMutation()) {
            return;
        }
        $hint = $request->getParameter('q');
        if ($hint !== NULL) {
            foreach (array('name', 'Description') as $key) {
                if ($this->getValidator($key)->evaluate($hint)) {
                    $this->parameters[$key] = $hint;
                }
            }
        }
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        $key = $this->parameters['name'];
        if ($this->getPlatform()->getDatabase('fwtimes')->getType($key)) {
            $report->addValidationErrorMessage($this, 'name', 'Time_key_exists_message');
        }
        parent::validate($report);
    }

    public function process()
    {
        parent::process();
        if ($this->getRequest()->isMutation()) {
            $this->getPlatform()
                ->getDatabase('fwtimes')->setKey($this->parameters['name'], 'time', array(
                'Description' => $this->parameters['Description'],
                'TimeStart' => $this->parameters['TimeStart'],
                'TimeStop' => $this->parameters['TimeStop'],
                'WeekDays' => implode(',', $this->parameters['WeekDays']),
            ));
            $this->state->resume($this->getParent()->getSession())->assign(sprintf("time;%s", $this->parameters['name']));
        }
    }


    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $view->setTemplate('NethServer/Template/FirewallObjects/Times/Modify');
        $view['WeekDaysDatasource'] = \Nethgui\Renderer\AbstractRenderer::hashToDatasource(array(
            'Mon' => $view->translate('WeekDay_Mon_label'),
            'Tue' => $view->translate('WeekDay_Tue_label'),
            'Wed' => $view->translate('WeekDay_Wed_label'),
            'Thu' => $view->translate('WeekDay_Thu_label'),
            'Fri' => $view->translate('WeekDay_Fri_label'),
            'Sat' => $view->translate('WeekDay_Sat_label'),
            'Sun' => $view->translate('WeekDay_Sun_label'),
        ));
        if ( ! $this->getRequest()->isValidated()) {
            return;
        }
        if ($this->getRequest()->isMutation()) {
            $view->getCommandList()->sendQuery($view->getModuleUrl('../' . $this->state->getReturnPath()));
        } else {
            $view['WeekDays'] = \NethServer\Module\FirewallObjects\Times\Modify::$weekDays;
            $view->getCommandList()->show();
        }
    }

}
