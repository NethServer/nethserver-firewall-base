<?php
namespace NethServer\Module\Dashboard\SystemStatus;

/*
 * Copyright (C) 2013 Nethesis S.r.l.
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
 * Retrieve multi-wan status
 *
 * @author Giacomo Sanchietti
 */
class Providers extends \Nethgui\Controller\AbstractController
{

    public $sortId = 80;
 
    private $providers = array();

    private function readProviders()
    {
        return json_decode($this->getPlatform()->exec('/usr/bin/sudo /usr/libexec/nethserver/providers-status')->getOutput(), true);
    }

    public function process()
    {
        $this->providers = $this->readProviders();
    }
 
    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        if (!$this->providers) {
            $this->providers = $this->readProviders();
        }

        $view['providers'] = $this->providers;
    }
}
