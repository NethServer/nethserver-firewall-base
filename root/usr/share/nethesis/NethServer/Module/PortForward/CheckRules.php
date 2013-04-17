<?php
namespace NethServer\Module\PortForward;

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

/**
 * Check shorewall rules
 *
 * @author Giacomo Sanchietti <giacomo.sanchietti@nethesis.it>
 */
class CheckRules extends \Nethgui\Controller\Table\AbstractAction
{
    private $output = "";

    public function process()
    {
        $this->output = $this->getPlatform()->exec('/usr/bin/sudo /usr/libexec/nethserver/shorewall-check')->getOutput();
        parent::process();
    }
 
    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
         $view['check-rules'] = $this->output; 
    }
}
