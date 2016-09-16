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

/**
 * Show the index of defined firewall rules
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class Index extends \Nethgui\Controller\Collection\AbstractAction
{
    public static $ndpiProtocolIcons = array(
        'amazon' => array('fa-amazon', 'F270'),
        'facebook' => array('fa-facebook-square', 'F082'),
        'ftp_control' => array('fa-cloud-download', 'F0ED'),
        'mail_pop' => array('fa-envelope', 'F0E0'),
        'mail_smtp' => array('fa-envelope', 'F0E0'),
        'mail_imap' => array('fa-envelope', 'F0E0'),
        'dns' => array('fa-at', 'F1FA'),
        'ipp' => array('fa-exchange', 'F0EC'),
        'http' => array('fa-at', 'F1FA'),
        'mdns' => array('fa-at', 'F1FA'),
        'ntp' => array('fa-clock-o', 'F017'),
        'netbios' => array('fa-at', 'F1FA'),
        'nfs' => array('fa-folder-open', 'F07C'),
        'ssdp' => array('fa-cogs', 'F085'),
        'bgp' => array('fa-at', 'F1FA'),
        'snmp' => array('fa-at', 'F1FA'),
        'xdmcp' => array('fa-at', 'F1FA'),
        'smb' => array('fa-folder-open', 'F07C'),
        'syslog' => array('fa-cogs', 'F085'),
        'dhcp' => array('fa-cogs', 'F085'),
        'postgres' => array('fa-database', 'F1C0'),
        'mysql' => array('fa-database', 'F1C0'),
        'tds' => array('fa-database', 'F1C0'),
        'direct_download_link' => array('fa-cloud-download', 'F0ED'),
        'mail_pops' => array('fa-envelope', 'F0E0'),
        'directconnect' => array('fa-exchange', 'F0EC'),
        'winmx' => array('fa-exchange', 'F0EC'),
        'vmware' => array('fa-cogs', 'F085'),
        'mail_smtps' => array('fa-envelope', 'F0E0'),
        'filetopia' => array('fa-cloud-download', 'F0ED'),
        'imesh' => array('fa-exchange', 'F0EC'),
        'kontiki' => array('fa-exchange', 'F0EC'),
        'openft' => array('fa-folder-open', 'F07C'),
        'fasttrack' => array('fa-exchange', 'F0EC'),
        'gnutella' => array('fa-exchange', 'F0EC'),
        'edonkey' => array('fa-exchange', 'F0EC'),
        'bittorrent' => array('fa-exchange', 'F0EC'),
        'epp' => array('fa-file-text', 'F15C'),
        'avi' => array('fa-video-camera', 'F03D'),
        'flash' => array('fa-video-camera', 'F03D'),
        'ogg' => array('fa-video-camera', 'F03D'),
        'mpeg' => array('fa-video-camera', 'F03D'),
        'quicktime' => array('fa-video-camera', 'F03D'),
        'realmedia' => array('fa-video-camera', 'F03D'),
        'windowsmedia' => array('fa-video-camera', 'F03D'),
        'mms' => array('fa-video-camera', 'F03D'),
        'xbox' => array('fa-gamepad', 'F11B'),
        'qq' => array('fa-comments', 'F086'),
        'rtsp' => array('fa-video-camera', 'F03D'),
        'mail_imaps' => array('fa-envelope', 'F0E0'),
        'icecast' => array('fa-headphones', 'F025'),
        'pplive' => array('fa-video-camera', 'F03D'),
        'ppstream' => array('fa-video-camera', 'F03D'),
        'zattoo' => array('fa-exchange', 'F0EC'),
        'shoutcast' => array('fa-headphones', 'F025'),
        'sopcast' => array('fa-headphones', 'F025'),
        'tvants' => array('fa-video-camera', 'F03D'),
        'tvuplayer' => array('fa-video-camera', 'F03D'),
        'http_app_veohtv' => array('fa-video-camera', 'F03D'),
        'qqlive' => array('fa-video-camera', 'F03D'),
        'soulseek' => array('fa-exchange', 'F0EC'),
        'ssl_no_cert' => array('fa-file-text', 'F15C'),
        'irc' => array('fa-comments', 'F086'),
        'ayiya' => array('fa-file-text', 'F15C'),
        'unencryped_jabber' => array('fa-comments', 'F086'),
        'msn' => array('fa-comments', 'F086'),
        'yahoo' => array('fa-yahoo', 'F19E'),
        'battlefield' => array('fa-gamepad', 'F11B'),
        'quake' => array('fa-gamepad', 'F11B'),
        'ip_vrrp' => array('fa-video-camera', 'F03D'),
        'steam' => array('fa-gamepad', 'F11B'),
        'halflife2' => array('fa-gamepad', 'F11B'),
        'worldofwarcraft' => array('fa-gamepad', 'F11B'),
        'telnet' => array('fa-file-text', 'F15C'),
        'stun' => array('fa-volume-control-phone', 'F2A0'),
        'ip_ipsec' => array('fa-file-text', 'F15C'),
        'ip_gre' => array('fa-file-text', 'F15C'),
        'ip_icmp' => array('fa-file-text', 'F15C'),
        'ip_igmp' => array('fa-file-text', 'F15C'),
        'ip_egp' => array('fa-file-text', 'F15C'),
        'ip_sctp' => array('fa-file-text', 'F15C'),
        'ip_ospf' => array('fa-file-text', 'F15C'),
        'ip_ip_in_ip' => array('fa-file-text', 'F15C'),
        'rtp' => array('fa-volume-control-phone', 'F2A0'),
        'rdp' => array('fa-desktop', 'F108'),
        'vnc' => array('fa-desktop', 'F108'),
        'pcanywhere' => array('fa-desktop', 'F108'),
        'ssl' => array('fa-lock', 'F023'),
        'ssh' => array('fa-file-text', 'F15C'),
        'usenet' => array('fa-at', 'F1FA'),
        'mgcp' => array('fa-file-text', 'F15C'),
        'iax' => array('fa-volume-control-phone', 'F2A0'),
        'tftp' => array('fa-cloud-download', 'F0ED'),
        'afp' => array('fa-folder-open', 'F07C'),
        'stealthnet' => array('fa-exchange', 'F0EC'),
        'sip' => array('fa-volume-control-phone', 'F2A0'),
        'truphone' => array('fa-volume-control-phone', 'F2A0'),
        'ip_icmpv6' => array('fa-at', 'F1FA'),
        'dhcpv6' => array('fa-at', 'F1FA'),
        'armagetron' => array('fa-gamepad', 'F11B'),
        'crossfire' => array('fa-gamepad', 'F11B'),
        'dofus' => array('fa-gamepad', 'F11B'),
        'florensia' => array('fa-gamepad', 'F11B'),
        'guildwars' => array('fa-gamepad', 'F11B'),
        'http_app_activesync' => array('fa-at', 'F1FA'),
        'kerberos' => array('fa-cogs', 'F085'),
        'ldap' => array('fa-cogs', 'F085'),
        'maplestory' => array('fa-gamepad', 'F11B'),
        'mssql' => array('fa-database', 'F1C0'),
        'pptp' => array('fa-lock', 'F023'),
        'warcraft3' => array('fa-gamepad', 'F11B'),
        'world_of_kung_fu' => array('fa-gamepad', 'F11B'),
        'meebo' => array('fa-comments', 'F086'),
        'twitter' => array('fa-twitter', 'F099'),
        'dropbox' => array('fa-dropbox', 'F16B'),
        'gmail' => array('fa-envelope', 'F0E0'),
        'google_maps' => array('fa-map-marker', 'F041'),
        'youtube' => array('fa-youtube-play', 'F16A'),
        'skype' => array('fa-skype', 'F17E'),
        'google' => array('fa-google', 'F1A0'),
        'dcerpc' => array('fa-cogs', 'F085'),
        'netflow' => array('fa-cogs', 'F085'),
        'sflow' => array('fa-cogs', 'F085'),
        'http_connect' => array('fa-at', 'F1FA'),
        'http_proxy' => array('fa-at', 'F1FA'),
        'citrix' => array('fa-desktop', 'F108'),
        'netflix' => array('fa-video-camera', 'F03D'),
        'lastfm' => array('fa-lastfm-square', 'F203'),
        'waze' => array('fa-map-marker', 'F041'),
        'skyfile_prepaid' => array('fa-exchange', 'F0EC'),
        'skyfile_rudics' => array('fa-exchange', 'F0EC'),
        'skyfile_postpaid' => array('fa-exchange', 'F0EC'),
        'citrix_online' => array('fa-desktop', 'F108'),
        'apple' => array('fa-apple', 'F179'),
        'webex' => array('fa-volume-control-phone', 'F2A0'),
        'whatsapp' => array('fa-whatsapp', 'F232'),
        'apple_icloud' => array('fa-apple', 'F179'),
        'viber' => array('fa-volume-control-phone', 'F2A0'),
        'apple_itunes' => array('fa-apple', 'F179'),
        'radius' => array('fa-cogs', 'F085'),
        'windows_update' => array('fa-windows', 'F17A'),
        'teamviewer' => array('fa-desktop', 'F108'),
        'tuenti' => array('fa-volume-control-phone', 'F2A0'),
        'lotus_notes' => array('fa-folder-open', 'F07C'),
        'sap' => array('fa-cogs', 'F085'),
        'gtp' => array('fa-file-text', 'F15C'),
        'upnp' => array('fa-file-text', 'F15C'),
        'llmnr' => array('fa-file-text', 'F15C'),
        'remote_scan' => array('fa-at', 'F1FA'),
        'spotify' => array('fa-spotify', 'F1BC'),
        'webm' => array('fa-video-camera', 'F03D'),
        'h323' => array('fa-video-camera', 'F03D'),
        'openvpn' => array('fa-lock', 'F023'),
        'ciscovpn' => array('fa-lock', 'F023'),
        'teamspeak' => array('fa-volume-control-phone', 'F2A0'),
        'tor' => array('fa-at', 'F1FA'),
        'rtcp' => array('fa-file-text', 'F15C'),
        'rsync' => array('fa-exchange', 'F0EC'),
        'oracle' => array('fa-database', 'F1C0'),
        'corba' => array('fa-cogs', 'F085'),
        'ubuntuone' => array('fa-cloud', 'F0C2'),
        'whois_das' => array('fa-at', 'F1FA'),
        'collectd' => array('fa-cogs', 'F085'),
        'socks5' => array('fa-cogs', 'F085'),
        'socks4' => array('fa-cogs', 'F085'),
        'rtmp' => array('fa-file-text', 'F15C'),
        'ftp_data' => array('fa-cloud-download', 'F0ED'),
        'wikipedia' => array('fa-wikipedia-w', 'F266'),
        'zmq' => array('fa-cogs', 'F085'),
        'ebay' => array('fa-at', 'F1FA'),
        'cnn' => array('fa-at', 'F1FA'),
        'megaco' => array('fa-volume-control-phone', 'F2A0'),
        'redis' => array('fa-cogs', 'F085'),
        'telegram' => array('fa-comments', 'F086'),
        'vevo' => array('fa-video-camera', 'F03D'),
        'pandora' => array('fa-headphones', 'F025'),
        'quic' => array('fa-file-text', 'F15C'),
        'whatsapp_voice' => array('fa-whatsapp', 'F232'),
        'kakaotalk' => array('fa-volume-control-phone', 'F2A0'),
        'kakaotalk_voice' => array('fa-volume-control-phone', 'F2A0'),
        'twitch' => array('fa-gamepad', 'F11B'),
        'quickplay' => array('fa-video-camera', 'F03D'),
        'tim' => array('fa-volume-control-phone', 'F2A0'),
        'mpegts' => array('fa-headphones', 'F025'),
        'snapchat' => array('fa-snapchat-square', 'F2AD'),
        'opensignal' => array('fa-comments', 'F086'),
        'easytaxi' => array('fa-at', 'F1FA'),
        'globotv' => array('fa-video-camera', 'F03D'),
        'timsomdechamada' => array('fa-volume-control-phone', 'F2A0'),
        'timmenu' => array('fa-volume-control-phone', 'F2A0'),
        'timportasabertas' => array('fa-volume-control-phone', 'F2A0'),
        'timrecarga' => array('fa-volume-control-phone', 'F2A0'),
        'timbeta' => array('fa-volume-control-phone', 'F2A0'),
        'deezer' => array('fa-headphones', 'F025'),
        'instagram' => array('fa-instagram', 'F16D'),
        'microsoft' => array('fa-windows', 'F17A'),
        'starcraft' => array('fa-gamepad', 'F11B'),
        'teredo' => array('fa-file-text', 'F15C'),
        'hotspot_shield' => array('fa-lock', 'F023'),
    );

    public function initialize()
    {
        parent::initialize();
        $this->declareParameter('Rules', \Nethgui\System\PlatformInterface::ANYTHING_COLLECTION);
        $this->declareParameter('a', $this->createValidator()->memberOf('', 'rules', 'routes', 'services'));
    }

    public function process()
    {
        parent::process();
        if ( ! $this->getRequest()->isMutation()) {
            return;
        }

        $rules = is_array($this->parameters['Rules']) ? $this->parameters['Rules'] : array();
        $A = $this->getAdapter();
        foreach ($rules as $key => $values) {
            if($key[0] === 's') {
                continue;
            } elseif ( ! isset($A[$key])) {
                throw new \RuntimeException("Unexistent fwrule key: $key", 1402062247);
            }
            $hasPosition = isset($rules[$key]['Position']) && $rules[$key]['Position'] > 0;
            if ( ! $hasPosition) {
                continue;
            }

            if ( ! isset($A[$key]['Position']) || $A[$key]['Position'] != $rules[$key]['Position']) {
                // array assignment merges with existing props:
                $A[$key] = array('Position' => $rules[$key]['Position']);
            }
        }
        if ($A->isModified()) {
            $A->save();
            $this->getParent()->fixOrderedSetPositions();
        }

        if ( ! $this->getRequest()->hasParameter('sortonly')) {
            $this->getPlatform()->signalEvent('firewall-adjust &');
        }
    }

    private function resolveEndpoint($ep, $view)
    {
        if ($ep == 'any') {
            return $view->translate("any_src_dst_label");
        }
        $tmp = explode(';', $ep);
        if (isset($tmp[1])) {
            return $view->translate($tmp[0].'_label')." ".$tmp[1];
        } else {
            return $ep;
        }
    }

    private function resolveService($ep, $view = null)
    {
        if ($ep == 'any') {
            if ($view == null) {
                return '';
            }  else {
                return $view->translate("any_service_label");
            }
        }
        return preg_replace('/^(fwservice|service|ndpi);/', "", $ep);
    }

    private function resolveName($ep,$view)
    {
        $tmp = explode(';', $ep);
        if ($ep == 'any') {
            return $view->translate("all_label");
        } elseif ($ep === 'fw') {
            return $view->translate("fw_label");
        }
        return isset($tmp[1])?$tmp[1]:$ep;
    }

    private function getObjectIcon($v)
    {
        $objectIcons = array(
            'any' => 'fa-globe',
            'role' => 'fa-square',
            'host' => 'fa-cube',
            'zone' => 'fa-square-o',
            'host-group' => 'fa-cubes',
            'iprange' => 'fa-cubes',
            'cidr' => 'fa-cubes',
            'fwservice' => 'fa-gear',
            'service' => 'fa-circle-thin',
            'fw' => 'fa-fire',
            'ndpi' => 'fa-file-o',
        );
        $tmp = explode(';', $v);
        if($tmp[0] === 'ndpi' && array_key_exists($tmp[1], self::$ndpiProtocolIcons)) {
            return self::$ndpiProtocolIcons[$tmp[1]][0];
        } elseif (isset($objectIcons[$tmp[0]])) {
            return $objectIcons[$tmp[0]];
        } else {
            return $v;
        }
    }


    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $r = array();

        $actionLabels = array(
            'accept' => $view->translate('ActionAccept_label'),
            'reject' => $view->translate('ActionReject_label'),
            'drop' => $view->translate('ActionDrop_label'),
        );

        $actionIcons = array(
            'accept' => 'fa-check-circle',
            'drop' => 'fa-minus-circle',
            'reject' => 'fa-shield',
        );

        foreach(array_keys($this->getPlatform()->getDatabase('networks')->getAll('provider')) as $provider) {
            $actionLabels['provider;' . $provider] = $view->translate('ActionRouteIndex_label', array($provider));
            $actionIcons['provider;' . $provider] = 'fa-share';
        }

        foreach ($this->getAdapter() as $key => $values) {

            if($this->parameters['a'] === 'rules' && substr($values['Action'], 0, 9) === 'provider;') {
                continue;
            } elseif($this->parameters['a'] === 'routes' && in_array($values['Action'], array('accept', 'drop', 'reject'))) {
                continue;
            } elseif($this->parameters['a'] === 'services') {
                continue;
            }

            $values['id'] = (String) $key;
            $values['Position'] = isset($values['Position']) ? intval($values['Position']) : 0;
            $values['cssAction'] = 'sortable '  . str_replace(';', ' ', $values['Action']);
            $values['ActionIcon'] = $actionIcons[$values['Action']];
            $values['SrcIcon'] = $this->getObjectIcon($values['Src']);
            $values['DstIcon'] = $this->getObjectIcon($values['Dst']);
            if ($values['Service'] == 'any') {
                $values['ServiceIcon'] = '';
            } else {
                $values['ServiceIcon'] = $this->getObjectIcon($values['Service']);
            }

            $values['Action'] = $actionLabels[$values['Action']];
            $values['Edit'] = array($view->getModuleUrl('../Edit/' . $key), $view->translate('EditRule_label'));
            $values['Copy'] = $view->getModuleUrl('../Copy/' . ($values['Position'] + 1) . '?id=' . $key);
            $values['Src'] = $this->resolveName($values['Src'],$view);
            $values['SrcCss'] = $values['Src'];
            $values['Dst'] = $this->resolveName($values['Dst'],$view);
            $values['Service'] = $this->resolveService($values['Service']);
            $values['Delete'] = $view->getModuleUrl('../Delete/' . $key);
            $values['LogIcon'] = ($values['Log']!='none')?'fa-book':'';
            $values['LogLabel'] = ($values['Log']!='none')? $view->translate('ActionLog_label') : '';
            $r[] = $values;
        }
        usort($r, function ($a, $b) {
            return $a['Position'] > $b['Position'];
        });

        $positions = array_map(function ($v) {
            return $v['Position'];
        }, $r);
        $first = (isset($positions[0]) ? $positions[0] / 2 : \NethServer\Module\FirewallRules::RULESTEP);
        $last = (end($positions) ? end($positions) : 0) + \NethServer\Module\FirewallRules::RULESTEP;

        if($this->parameters['a'] === 'services' || ! $this->parameters['a']) {
            $serviceCount = 0;
            foreach($this->getNetworkServices() as $key => $values) {

                $values['id'] = 's' . (++$serviceCount);
                $values['Position'] = '';
                $values['SrcIcon'] = $this->getAccessIconSrc($values['access']);
                $values['DstIcon'] = $this->getObjectIcon('fw');
                $values['ServiceIcon'] = 'fa-circle-thin';

                $values['Action'] = 'accept';
                $values['ActionIcon'] = $actionIcons[$values['Action']];
                $values['cssAction'] = 'unsortable ' . $values['Action'];
                $values['Action'] = $actionLabels[$values['Action']];

                $values['Edit'] = array($view->getModuleUrl('../EditService/' . $key), $view->translate('EditService_label'));
                $values['Copy'] = $view->getModuleUrl('..');
                $values['Delete'] = $view->getModuleUrl('..');

                $values['Src'] = $this->resolveAccessSrc($values['access'], $view);
                $values['SrcCss'] = strstr($values['access'], ',') ? '' : $values['access'];
                $values['Dst'] = $this->resolveName('fw', $view);
                $values['Service'] = $key;

                $values['LogIcon'] = '';
                $values['LogLabel'] = '';

                $r[] = $values;
            }
        }

        $view['hasChanges'] = $this->hasChanges();
        $view['Rules'] = $r;
        $view['Create_last'] = $view->getModuleUrl('../Create/' . intval($last));
        $view['Create_first'] = $view->getModuleUrl('../Create/' . intval($first));

        if ($this->getRequest()->isValidated()) {
            $view->getCommandList()->show();
        }
    }

    private function getAccessIconSrc($access) {
        if( ! $access) {
            return 'fa-times';
        } elseif(strstr($access, ',')) {
            return 'fa-th-large';
        }
        return 'fa-square';
    }

    private function resolveAccessSrc($access, \Nethgui\View\ViewInterface $view) {
        if( ! $access) {
            return 'localhost';
        }

        return strtr($access, ',', ', ');
    }

    private function getNetworkServices()
    {
        $searchProps = array('TCPPort', 'UDPPort', 'TCPPorts', 'UDPPorts');
        $services = $this->getPlatform()->getDatabase('configuration')->getAll('service');

        // Unset records without a defined "port" prop:
        foreach ($services as $key => $props) {
            $hasPorts = FALSE;
            foreach(\array_intersect($searchProps, \array_keys($props)) as $p) {
                if($props[$p] !== '') {
                    $hasPorts = TRUE;
                    break;
                }
            }
            if( ! $hasPorts ) {
                unset($services[$key]);
            }
        }

        return $services;
    }

    private function hasChanges()
    {
        $fwStat = $this->getPhpWrapper()->stat('/etc/shorewall/rules');
        $dbStat = $this->getPhpWrapper()->stat('/var/lib/nethserver/db/fwrules');

        return $dbStat['ctime'] > $fwStat['ctime'] ? '1' : '0';
    }

}
