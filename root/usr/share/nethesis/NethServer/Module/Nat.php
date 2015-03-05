<?php
namespace NethServer\Module;

/*
 * Copyright (C) 2015 Nethesis S.r.l.
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
 * Configure NAT 1:1
 * 
 * @author Edoardo Spadoni <edoardo.spadoni@nethesis.it>
 */
class NAT extends \Nethgui\Controller\TabsController
{
    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $base)
    {
        return \Nethgui\Module\SimpleModuleAttributesProvider::extendModuleAttributes($base, 'Gateway', 60);
    }

    public function initialize()
    {
        parent::initialize();
        $this->addChild(new \NethServer\Module\NAT\Configure());
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        // $isConfigured = 0 !== count(array_filter($this->getPlatform()->getDatabase('networks')->getAll('alias'), function ($record) {
        //             return $record['status'] === 'enabled';
        //         }));

        if ($isConfigured) {
            $this->sortChildren(function (\Nethgui\Module\ModuleInterface $a, \Nethgui\Module\ModuleInterface $b) {
                return 0;
            });
        }

        parent::prepareView($view);
    }

}

