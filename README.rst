
.. _firewall_gateway-section:

========================
nethserver-firewall-base
========================

NethServer can work into two basic modes:

* server mode: the system will be a standard host inside the network offering services like e-mail or file server.
* gateway mode: the system is the gateway and firewall of the local network

The system has an abstraction layer for firewall base functions, like opening ports for running services.
Actually two implementations are available:

* server mode: standard :command:`lokkit` (default on CentOS)
* gateway mode: advanced Shorewall configuration

The gateway functionality is built around three modules:

* nethserver-base: high-level abstraction of the firewall
* nethserver-firewall-base: Shorewall-based implementation
* nethserver-lsm: link status monitor for multi-wan configurations


.. _section-roles-and-zones:

Roles and zones
===============

Each network interface has a role which maps to a firewall zone.
The firewall has the following built-in zones, ordered from the most to the least privileged:

* *green*: local network, it's considered almost trusted. Hosts in this network can access any other zone. Hosts connected via VPN can be considered in green zone.
* *blue*: guest network.  Hosts in this network can access orange and red zones, can't access green zone
* *orange*: DMZ network. Hosts in this network can access red zone, can't access green and blue zones
* *red*: external/internet networks.  Hosts in this network can access only firewall zone

There is also a special *firewall* zone which represents the firewall itself. The firewall can access any other zone. 

Each network interface with a configured role is a firewall zone. Roles are mapped to Shorewall zones as:

* green -> loc
* red -> net
* blue -> blue
* orange -> orang (in Shorewall, a zone name can't be longer than 5 chars)
* firewall -> FW

Custom zone names are directly mapped to Shorewall respecting the limit of 5 chars.

Red interfaces can be configured with static IP address or using DHCP. All other interfaces can be configured only with static IP addresses.


General configuration
=====================

Properties of ``firewall`` key inside ``configuration`` db:

* ``event``: event to call when ``firewall-adjust`` event is fired
* ``ExternalPing``: if enabled, allow ping responses on external interface
* ``WanMode``: multi-wan mode. Default is ``balance``, can be:

  * ``balance``: traffic is balanced among red interfaces in weighted mode
  * ``backup``: traffic is routed via wan interface with maximum weight, all other interfaces are used as fallback
* ``nfq``: if enabled, traffic from external networks will be passed to NFQ and scanned with Snort. See :ref:`ips`.
* ``Policy``: can be ``permissive`` or ``strict``. See above.
* ``MACValidation``: if enabled, the firewall will check the traffic against a list of known MAC addresses (see: :ref:`ipbinding-section`)
* ``MACValidationPolicy``: can be ``accept`` or ``drop``. Default is ``drop``. See ``man shorewall.conf`` for all valid values
* ``InterfaceRoleList``: list of network interface roles configurable from web interface. Default is: ``green,red,blue,orange``
* ``CheckIP``: comma-separeted list of IP monitored by LSM, to check if a provider is up or down
* ``MaxNumberPacketLoss``: number of maximum consecutive packets lost, over this threshold the provider is considered down
* ``MaxPercentPacketLoss``: percentage of maximum packet lost, over this threshold the provider is considered down
* ``PingInterval``: seconds between ICMP packets sent by LSM, default is ``5``
* ``NotifyWan``: can be ``enabled`` or ``disabled``, if ``enabled`` a mail is sent every time a provider changes its own state
* ``NotifyWanFrom``: sender address for mails sent if NotifyWAN is set to enabled
* ``NotifyWanTo``: recipient address for mails sent if NotifyWAN is set to enabled
* ``VpnPolicy``: can be ``permissive`` or ``strict``. If set to ``permissive`` traffic between all VPNs (Rodwarrior OpenVPN, OpenVPN tunnels, IPSec tunnels)
  will be permitted
* ``SipAlg`` can be ``enabled`` (default) or ``disabled``. It enables/disables the application level gateway (ALG) for SIP|H323 protocol (the netfilter conntrack kernel modules for these protocols)

Example

::

 firewall=configuration
    CheckIP=8.8.8.8,208.67.222.222
    Docker=disabled
    ExternalPing=enabled
    HairpinNat=disabled
    MACValidation=disabled
    MACValidationPolicy=drop
    MaxNumberPacketLoss=10
    MaxPercentPacketLoss=50
    NotifyWan=disabled
    NotifyWanFrom=root@localhost
    NotifyWanTo=root@localhost
    PingInterval=5
    Policy=permissive
    SipAlg=enabled
    TCLinklayer=
    VpnPolicy=strict
    WanMode=balance


Events
======

The main event for firewall configuration is *firewall-adjust*. The event contains a single action which fires the event named in the property ``event`` inside the ``firewall`` key into the ``configuration`` database. 

Other events:

* lokkit-save: base firewall implementation using lokkit
* nethserver-firewall-base-save:  firewall implementation using Shorewall 
* wan-uplink-update:  fired when the status of an external interface changes
* conntrack-adjust: fired when we want to reset the conntrack and remove kernel modules used by shorewall

The ``wan-uplink-event`` event takes at least two parameters:

* provider name: name of the provider involved
* action: can be ``up`` or ``down``, describing the new provider status

Example: ::

  signal-event wan-uplink-update down myisp


Policy
======

For every network packet traveling between firewall zones, the system will evaluate a list of rules to allow/block the specific traffic.
Policies are default firewall rules which will be applied only if no other rule matches the ongoing traffic.

Firewall implements two standard policies:

* :dfn:`Permissive`: will enable all traffic from green (loc) zone to red (net) zone. 
* :dfn:`Strict`: will block all traffic from green (loc) zone to red (net) zone. Permitted traffic should be explicitly allowed.

The firewall configures 4 default zones with built-in policies (see above).
In the schema below, traffic is permitted from left to right and blocked from right to left:

GREEN -> BLUE -> ORANGE -> RED

To override a policy, you should create a firewall rule between zones.

.. _ipbinding-section:

IP/MAC binding
==============

When ``MACValidation`` option is enabled, the firewall analyzes all the traffic based on a well-known list of IPs associated to MAC addresses.
If the host generating the traffic is not inside the list, ``MACValidationPolicy`` will be applied.
The list of IP/MAC association is created from DHCP reservations.

Thus, enabling MACValidation and leaving MACValidationPolicy set to drop, will block all traffic from hosts without a DHCP reservation.


Rules
=====

Firewall rules can allow or deny traffic matching certain conditions.
Rules are saved inside the ``fwrules`` database as records of type ``rule``.

Each rule record has the following fields:

* ``key``: a unique key identifier
* ``Position``: integer sorting key
* ``Src``, ``Dst``: {*literal*|*reference*} where

  * *literal* is an IP, a CIDR, ``any`` (any source/destination) or ``fw`` (the firewall itself)
  * *reference* has the form ``prefix;value``, where prefix can be a DB type (``host``, ``host-group``,  ``zone``, ``iprange``, ``cidr``, ``mac``) or the string ``role``, 
    ``value`` is a DB key or an interface role name (``green``, ``red``...).  ``mac`` objects are *not* supported inside the ``Dst`` field.
* ``Action``: can be ``ACCEPT``, ``DROP`` or ``REJECT``

  * ``ACCEPT`` allows the traffic
  * ``REJECT`` denies the traffic, an ICMP port unreachable packet is sent to the source address
  * ``DROP`` discards the traffic without informing the source address (the connection will timeout)
* ``Service``: (optional) can be a service object, a port number or a ndpi object. If a port number is used, both TCP and UDP protocols are matched.
* ``Time``: (optional) can be a time object, the rule will be enabled only if the time conditions is matched
* ``Log``: can be ``none`` or ``info``. If value is ``info``, all matched packets will be logged in ``/var/log/firewall.log``. Defaults to ``none``
* ``status``: can be ``enabled`` or ``disabled``. Default is ``enabled``
* ``State``: (optional) select on which type of connection the rule will be applied to:

  * ``new`` or empty: default, the rule will be applied only to new connections
  * ``all``: the rule will be applied to new and established/related connections
* ``Description``: (optional)

Example of a rule accepting traffic: ::
  
  1=rule 
      Src=host;myhost 
      Dst=192.168.1.2 
      Service=service;ssh 
      Action=accept 
      Position=32

Accept all traffic from myhost to myserver for ssh service (port 22): ::

  db fwrules set 1 rule Src "host;myhost" Dst "host;myserver" Service ssh Action ACCEPT Log none status enabled Position 8765

Drop all traffic from 192.168.1.0/24 to 192.168.4.1 on TCP and UDP port 25: ::

  db fwrules set 2 rule Src  192.168.1.0/24 Dst 192.168.4.1 Service 22 Action DROP Log none status enabled Position 5469

Template Fragment
-----------------
Rules in the firewall can be added manually by a template fragment in the folder ``/etc/e-smith/templates/etc/shorewall/rules``

For example drop a file 40YourSpecificRule ::

  ## 40nethvoice
  
  {
      my $iax = $nethvoice{'AllowExternalIAX'} || 'disabled';

      my $webrtc = $nethvoice{'AllowExternalWebRTC'} || 'disabled';
      
      if ($iax eq 'enabled') {
      
          $OUT .= "# Enable IAX from red interfaces\n";
          
          $OUT .= "?COMMENT Enable IAX from red interfaces\n";
          
          $OUT .= "ACCEPT\tnet\t\$FW\tudp\t4569,5036\n";
      }
      
      if ($webrtc eq 'enabled') {
      
          $OUT .= "# Enable WebRTC from red interfaces\n";
          
          $OUT .= "?COMMENT Enable WebRTC from red interfaces\n";
          
          $OUT .= "ACCEPT\tnet\t\$FW\ttcp\t8089\n";
      }
  
      $OUT .= "?COMMENT\n";
  }
 
You can use all the settings below but you might be interested by the shorewall documentation also at http://shorewall.net/manpages/shorewall-rules.html)

* ``\t``       -> write a tab space (can be also written : ``$OUT .= "ACCEPT  net  $FW  tcp  8089\n";)``
* ``ACCEPT``   -> allows the traffic
* ``REJECT``   -> denies the traffic, an ICMP port unreachable packet is sent to the source address
* ``DROP``     -> discards the traffic without informing the source address (the connection will timeout)
* ``REDIRECT`` -> redirect the traffic to another firewall zone

The target may optionally be followed by ":" and a syslog log level (e.g, REJECT:info or Web(ACCEPT):debug).

* ``loc``      -> green (Local network)
* ``net``      -> red   (Internet network)
* ``blue``     -> blue  (Guest network)
* ``orang``    -> orange (DMZ network)
* ``$FW``      -> firewall
* ``tcp``      -> tcp port (comma separated list of ports)
* ``udp``      -> udp port (comma separated list of ports)

then you must expand your templates and restart your firewall by : ``signal-event firewall-adjust``

Firewall objects
=================

Firewall module uses objects to simplify rules creation. The use of objects is not mandatory but it's strongly encouraged.

Supported objects are:

* Host
* Group of host
* Service
* CIDR
* Ip range
* Zone
* Time
* MAC address

A host is an already defined entry inside the ``hosts`` db, or a new key of type ``host``: ::

   name=host
       IpAddress=IP
       Description=



A ``host-group`` is a group of hosts inside the ``hosts`` db. Hosts in a :index:`host-group` should always be reachable using the same interface.
For example: a group of host inside the LAN or on the Internet.
A ``host-group`` db entry can be something like: ::

    name=host-group
        Members=host1,host2

A ``CIDR`` is a group of hosts defined as a CIDR network. It's saved inside the ``hosts`` db: ::

    mycidr=cidr
        Address=192.168.100.0/24
        Description=


A ``IP range`` is a group of hosts defined as a range of IP. It's saved inside the ``hosts`` db: ::

    myrange=iprange
        Description=
        End=192.168.100.20
        Start=192.168.100.10


A zone represents a network zone which can be associated to an interface or a set of IP address. A ``zone`` entry in ``networks`` database can be something like: ::

    name=zone
       Network=CIDR
       Interface=eth0

A configured network interface is automatically a zone.

A service can have a protocol and one or more ports. A ``service`` entry in ``fwservices`` database can be something like: ::

    name=fwservice
       Protocol=tcp/udp/tcpudp/icmp
       Ports=port/port range

A service can also be a refence in the format ``ndpi;<protocol>`` where ``protocol`` is a supported protocol from nDPI kernel module.
To see a list of supported protocols: ::

    db NethServer::Database::Ndpi keys


A time condition is a ``time`` record entry in ``fwtimes`` database.
All times are saved in *local time* and converted to UTC on template expansion.

Database example: ::

    db fwtimes setprop officehours WeekDays 'Mon,Tue,Wed,Thu' TimeStart '09:00' TimeStop '18:00'

A MAC address is a ``mac`` record entry inside ``macs`` database.
The MAC must always have a ``Zone`` property which specifies the network segment where the device is connected.
It's something like: ::

 mac1=mac
    Address=52:54:00:05:2d:c3
    Description=My mac test
    Zone=green


Rules based on mac address
--------------------------

It's possible to create rules based on MAC address only using a template-custom.
For example to block internet access to a host on local network using its MAC address: ::

  mkdir -p /etc/e-smith/templates-custom/etc/shorewall/rules
  echo "DROP      loc:~xx-xx-xx-xx-xx-xx          net" > /etc/e-smith/templates-custom/etc/shorewall/rules/90mymac


Where ``xx-xx-xx-xx-xx-xx`` is the MAC address to block.

See :command:`man shorewall-rules` for more information.

Port forwarding
===============

All port-forwards are saved inside the ``portforward`` db.

Each record has:

* ``key``: auto-increment id 
* ``type``: pf
* ``protocol``: tcp/udp  
* ``src``: can be a port number or a range in the form xxxx:yyyy
* ``dst``: can be a port number, if empty the value of ``src`` is used
* ``dstHost``: destination host, can be an IP address or a hos firewall object
* ``allow``: allowed ip address or network, see SOURCE  at http://www.shorewall.net/4.2/manpages/shorewall-rules.html
* ``status``: enabled/disabled
* ``oriDst``: original destination ip, for example alias for a wan interface. If empty, the port forward is valid for all red interface
* ``description``: optional description

Source NAT (sNAT)
=================

All NAT one-to-one configurations are stored in ``networks`` db.

Each value is a new attribute for an existing alias key and the name of attribute is ``FwObjectNat`` that contains the reference of an associated host: ::

    eth1:0=alias
        FwObjectNat=host;host_name
        ipaddr=11.11.11.11
        netmask=255.255.255.0
        role=alias

During template-expanding phase, the associated host is mapping with referenced IP and added in shorewall nat configuration. The file is ``/etc/shorewall/nat``. 

More information are available here: http://shorewall.net/NAT.htm

.. _section-tc:

Traffic shaping
================

Traffic shaping is implemented using Shorewall mangle and FireQOS: each mangle rule sets a well-known marker,
markers are used to match traffic inside FireQOS tc classes. 

The firewall needs to know how much inbound and outbound bandwidth has a red interface.
The bandwidth value (expressed in kbit) is stored inside ``FwInBandwidth`` and ``FwOutBandwidth`` properties, wich are
parts of the network interface record inside the ``networks`` db.
Each red interface can have also the ``TCLinklayer`` property, see FireQoS documentation `supported values <https://firehol.org/fireqos-manual/fireqos-params-class/#linklayer-linklayer-name-ethernet-atm>`.

FireQOS tutorial suggests to use 90% of the declared bandwidth to shape the inbound traffic faster.

On red interfaces with ``FwInBandwidth`` and ``FwOutBandwidth`` set, ethernet offloading is automatically disabled.

Example: ::

 enp0s20f2=ethernet
    FwInBandwidth=30000
    FwOutBandwidth=24000
    TCLinklayer=ethernet
    bootproto=none
    gateway=1.2.3.4
    ipaddr=1.2.3.5
    netmask=255.255.255.0
    role=red


All traffic shaping rules are saved inside the ``fwrules`` database with the same format.
Valid actions for traffic shaping rules are:

- ``class;<name>``: set associated tc class.
- ``provider;<name>``: force the traffic to the provider specified by ``name``

tc classess
-----------

tc classes are saved inside the ``tc`` database with type ``class``.

Each tc class has the following properties:

- ``BindTo``: empty (default) or comma-separated list of red interfaces. If one ore more interface is listed,
  the class is applied only to given interface
- ``Description``: optional class description (used only in the UI)
- ``Mark``: integer value which identify the marker used for this class. Maximum is ``63``
- ``MaxInputRate``: maximum download rate, expressed in percentage of the total download bandwidth
- ``MaxOutputRate``: maximum upload rate, expressed in percentage of the total upload bandwidth
- ``MinInputRate``: reserved download rate, expressed in percentage of the total download bandwidth
- ``MinOutputRate``: reserved upload rate, expressed in percentage of the total upload bandwidth
- ``Unit``: bandwidth unit of measure for TC classes, default to ``%``, supported values are from FireQoS doc


Example: ::

 high=class
    BindTo=
    Description=
    Mark=2
    MaxInputRate=
    MaxOutputRate=
    MinInputRate=10
    MinOutputRate=10
    Unit=%

 low=class
    BindTo=ens1
    Description=
    Mark=2
    MaxInputRate=
    MaxOutputRate=
    MinInputRate=10
    MinOutputRate=10
    Unit=kbps

Assumptions and limitations
---------------------------

1. All nDPI traffic is marked in forward chain.
   When a nDPI protocol is found, the whole connection is marked.

2. Priority rules are in post chain and can use nDPI markers.
   If a priority rule uses a role (interface) as source, the rule can't be added 
   to postrouting chain since the packet is already natted: Shorewall will move the rule on top of forwarding chain.

3. nDPI rules can't block the http/https traffic if web proxy is enabled in transparent mode.

4. All nDPI markers are read from ``/proc/net/xt_ndpi/proto`` and shifted by 8 bits.

5. Divert rules can't be used with nDPI, because an established TCP connection can't be moved between providers.

6. Prerouting table is reserved by Shorewall for handlind the multi wan scenario.

See also: 

* https://github.com/firehol/firehol/wiki/FireQOS
* https://github.com/firehol/firehol/wiki/FireQOS-Tutorial
* http://shorewall.net/manpages/shorewall.conf.html

Divert rules
------------

A divert rule is used to force traffic to a specific provider.

For example, this rules will route all traffic to port 22 via the provider named myadsl: ::

 1=rule
     Src=192.168.1.0/24
     Dst=0.0.0.0/0
     Service=fwservice;ssh
     Action=provider;myadsl
     status=enabled
     Position=2
     Description=


Properties:

* ``key``: numeric id
* ``Src``: can be a 'any', role (execpt red), zone (not interface), host object, ip address, ip range or CIDR
* ``Dst``: can be a zone (not interface), host object, ip address, ip range  or CIDR
* ``Action``: provider object, in the form of "provider;<name>"
* ``Service``: (optional) can be a service object
* ``status``: can be enabled or disabled. Default is enabled
* ``Position``: integer sorting key
* ``Description``: (optional)


A rule is ignored during template expansion if:

* the source is red role
* the destination is a role which is not red
* source, destination and service are all set to any
* the provider doesn't exists
* destination is set to any



.. _section-multiwan:

Multi WAN
=========

NethServer firewall can handle 15 red (WAN) interfaces. Implementation uses Shorewall with LSM (Link Status Monitor).
The LSM daemon takes care of monitoring WAN connections (interface) using ICMP traffic and it informs Shorewall about interface up/down events.
Each interface can be checked using multiple IPs (see ``checkip`` property below). At least one IP must be reachable to mark the WAN connection as usable. 
If no IP is specified (recommended option), the system will uses well-known default IPs (Google DNS and OpenDNS).

For each configured provider, LSM will send ping to a configured IP (checkip). 
When a provider status changes, the system will signal a ``wan-uplink-update`` event.

Inside the event, the action ``nethserver-shorewall-wan-update`` invokes:

* shorewall enable <interface> when a red interface is usable
* shorewall disable <interface> then a red interface is not usable

When an interface is disabled, all associated routes will be deleted. 

When a new TCP connection is started, a route is selected and all successive packets will always be routed via same interface. If the used interfaces goes down, the connection is closed.


Actually two behaviors are implemented: balanced and active-backup.

Balanced
---------

All red interfaces are simultaneously used accordingly to the configured weight (see below).

**Example**: 

Given a connection A with weight 2, and connection B with weight 1, the firewall will route a double number of connections via A over B.

Active-backup
-------------

Red interfaces are ordered using the configured weight: higher the weight, higher the route priority.
The interface with maximum weight will be the active connection, all other interfaces will be used if the active one goes down.

**Example**

Given 3 wan connections:

* A with weight 3 
* B with weight 2
* C with weight 1

All traffic is routed via A. On failure of A, all traffic is routed via B. When B goes down, C is used.
Whenever A comes backup, all traffic is again routed through it.

Providers
---------

Providers are an abstraction over red interfaces (see :command:`man shorewall-providers`). 
All providers must have a weight which is used to select the route for packets.

A ``provider`` record inside the ``networks`` database has following properties:

* ``key``: name of provider
* ``interface``: associated red interface, it's mandatory
* ``weight``: weight of connection expressed with an integer number, it's mandatory
* ``Description``: (optional) custom description

Example: ::

  myisp=provider
    interface=eth1
    weight=5
    Description=my fast provider


Multi WAN example
-----------------

1. Configure two interfaces as red, for example eth1 and eth2 

::

  db networks setprop eth1 role red
  db networks setprop eth2 role red
  signal-event interface-update

2. Create two providers: 

::

  db networks set firstisp provider interface eth1 weight 2
  db networks set secondisp provider interface eth2 weight 1

3. Re-configure the firewall: 

::

  signal-event firewall-adjust


See :file:`/var/log/firewall.log` to check for up/down events.

Routes can be checked using: ::

 shorewall show routing


Static routes
=============

Static routes are saved inside the routes database with a record of type static. Example: ::

 8.8.4.4=static
     Description=My route
     Mask=255.255.255.255
     Router=89.97.220.225


Each record has the following properties:

* ``key``: network address
* ``Mask``: network mask
* ``Router``: gateway for the network
* ``Description``: a custom description (optional)

There is also a special type of static route called ``provider-static``.
These routes have the same properties as described above and are used to correctly route traffic for link monitor.
This type of rules should never be manually edited.
