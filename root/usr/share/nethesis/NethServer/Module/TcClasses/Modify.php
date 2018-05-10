<?php
namespace NethServer\Module\TcClasses;

/*
 * Copyright (C) 2018 Nethesis S.r.l.
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
 * Manage tc classes
 * 
 * @author Giacomo Sanchietti <giacomo.sanchietti@nethesis.it>
 */
class Modify extends \Nethgui\Controller\Table\Modify
{
    public function initialize()
    {
        $rateValidator = $this->createValidator()
            ->orValidator(
                $this->createValidator()->integer()->greatThan(0)->lessThan(100),
                $this->createValidator()->isEmpty()
            );


        $parameterSchema = array(
            array('Name', Validate::USERNAME, \Nethgui\Controller\Table\Modify::KEY),
            array('Mark', Validate::POSITIVE_INTEGER, \Nethgui\Controller\Table\Modify::FIELD),
            array('MaxInputRate',  $rateValidator, \Nethgui\Controller\Table\Modify::FIELD),
            array('MaxOutputRate',  $rateValidator, \Nethgui\Controller\Table\Modify::FIELD),
            array('MinInputRate',  $rateValidator, \Nethgui\Controller\Table\Modify::FIELD),
            array('MinOutputRate',  $rateValidator, \Nethgui\Controller\Table\Modify::FIELD),
            array('Description', $this->createValidator()->maxLength(35), \Nethgui\Controller\Table\Modify::FIELD),
        );

        $this->setSchema($parameterSchema);
        $this->setDefaultValue('Mark', $this->calculateNextMark());

        parent::initialize();
    }

    private function calculateNextMark()
    {
        $mark = 0;
        $classes = $this->getPlatform()->getDatabase('tc')->getAll('class');
        foreach ($classes as $class) {
            $mark = max($class['Mark'],$mark);
        }

        return $mark + 1;
    }


    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        if ($this->getRequest()->isMutation() && ($this->getIdentifier() !== 'delete')) {

            if ($this->getIdentifier() !== 'update') {
                $check = $this->getPlatform()->getDatabase('tc')->getKey($this->parameters['Name']);
                if ($check) {
                    $report->addValidationErrorMessage($this, 'Name', 'duplicate_class');
                }
            }

            // check if total reserver bandwitdth is higher than 100%
            $tot_in = 0;
            $tot_out = 0;
            foreach ($this->getPlatform()->getDatabase('tc')->getAll('class') as $key => $props) {
                $tot_in += $props['MinInputRate'];
                $tot_out += $props['MinOutputRate'];
            }
            $tot_out += $this->parameters['MinOutputRate'];
            $tot_in += $this->parameters['MinInputRate'];
            if ($tot_in > 100) {
                $report->addValidationErrorMessage($this, 'MinInputRate', 'input_overcommit');
            }
            if ($tot_out > 100) {
                $report->addValidationErrorMessage($this, 'MinOutputRate', 'output_overcommit');
            }
        }

        parent::validate($report);
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $templates = array(
            'create' => 'NethServer\Template\TcClasses\Modify',
            'update' => 'NethServer\Template\TcClasses\Modify',
            'delete' => 'Nethgui\Template\Table\Delete',
        );
        $view->setTemplate($templates[$this->getIdentifier()]);
        
    }


    /**
     * Delete the record after the event has been successfully completed
     * @param string $key
     */
    protected function processDelete($key)
    {
        parent::processDelete($key);
    }

    protected function onParametersSaved($changedParameters)
    {
        $this->getPlatform()->signalEvent('firewall-adjust &');
    }

}
