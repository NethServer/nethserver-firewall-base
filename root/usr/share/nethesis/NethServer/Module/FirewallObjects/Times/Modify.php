<?php
namespace NethServer\Module\FirewallObjects\Times;

/*
 * Copyright (C) 2012 Nethesis S.r.l.
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
 * Modify Service object
 *
 * @author Giacomo Sanchietti <giacomo.sanchietti@nethesis.it>
 */
class Modify extends \Nethgui\Controller\Table\Modify
{
    private $interfaces;

    public static $weekDays = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');

    public function initialize()
    {
        $weekDaysValidator = $this->createValidator()->notEmpty()->collectionValidator($this->createValidator()->memberOf(self::$weekDays));

        $parameterSchema = array(
            array('name', Validate::USERNAME, \Nethgui\Controller\Table\Modify::KEY),
            array('Description', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
            array('TimeStart', Validate::TIME, \Nethgui\Controller\Table\Modify::FIELD),
            array('TimeStop', Validate::TIME, \Nethgui\Controller\Table\Modify::FIELD),
            array('WeekDays', $weekDaysValidator, \Nethgui\Controller\Table\Modify::FIELD, 'WeekDays', ','),
        );

        $this->setSchema($parameterSchema);
        $this->setDefaultValue('WeekDays', self::$weekDays);
        parent::initialize();
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        $keyExists = $this->getPlatform()->getDatabase('fwtimes')->getType($this->parameters['name']) != '';
        if ($this->getIdentifier() === 'create' && $keyExists) {
            $report->addValidationErrorMessage($this, 'name', 'Time_key_exists_message');
        }
        if ($this->getIdentifier() !== 'create' && ! $keyExists) {
            throw new \Nethgui\Exception\HttpException('Not found', 404, 1407169970);
        }
        if($this->getIdentifier() === 'delete') {
            $v = $this->createValidator()->platform('fwobject-time-delete', 'fwtimes');
            if( ! $v->evaluate($this->parameters['name'])) {
                $report->addValidationError($this, 'TimesKey', $v);
            }
        }
        parent::validate($report);
        if( ! $report->hasValidationErrors()) {
            if(strcmp($this->parameters['TimeStart'], $this->parameters['TimeStop']) > 0) {
                $report->addValidationErrorMessage($this, 'TimeStop', 'TimeStop_compare_TimeStart_message', array($this->parameters['TimeStart']));
            }
        }
    }


    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $templates = array(
            'create' => 'NethServer\Template\FirewallObjects\Times\Modify',
            'update' => 'NethServer\Template\FirewallObjects\Times\Modify',
            'delete' => 'Nethgui\Template\Table\Delete',
        );
        $view->setTemplate($templates[$this->getIdentifier()]);
        $view['WeekDaysDatasource'] = \Nethgui\Renderer\AbstractRenderer::hashToDatasource(array(
            'Mon' => $view->translate('WeekDay_Mon_label'),
            'Tue' => $view->translate('WeekDay_Tue_label'),
            'Wed' => $view->translate('WeekDay_Wed_label'),
            'Thu' => $view->translate('WeekDay_Thu_label'),
            'Fri' => $view->translate('WeekDay_Fri_label'),
            'Sat' => $view->translate('WeekDay_Sat_label'),
            'Sun' => $view->translate('WeekDay_Sun_label'),
        ));
    }

}
