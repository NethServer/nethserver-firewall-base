<?php
namespace NethServer\Module\FirewallRules;
#
# Copyright (C) 2015 Nethesis S.r.l.
# http://www.nethesis.it - nethserver@nethesis.it
#
# This script is part of NethServer.
#
# NethServer is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License,
# or any later version.
#
# NethServer is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with NethServer.
#

class CheckRules extends \Nethgui\Controller\AbstractController
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
