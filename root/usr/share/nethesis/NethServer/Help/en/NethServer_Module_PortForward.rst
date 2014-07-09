============
Port forward
============

Use this panel to change firewall rules, i.e. to open a specific port
(or a range of ports) on the server and forward the traffic from a
port to another.  Port forwarding rules allow access to hosts on the
local network from the Internet.

Create / Modify
===============

Source port
    Insert the port open on the public IP.

Destination port
    Insert the port on the internal host which will be destination of the traffic.

Destination host
    Select the internal machine where traffic will be redirected.

Allow only
    Allow traffic forward only from some networks/hosts, as specified
    into `Shorewall Port Forwarding FAQ
    <http://shorewall.net/FAQ.htm#PortForwarding>`_.

Description
    Optional description of port forwarding rule.

Enable / Disable
====================

Port forwarding rules are enabled by default on
creation. You can temporarily enable/disable them
using this button

Yes
    Enable the rule.

No
    Disable the rule.

Firewall check
==================

Performs a general control over configured firewall rules. Useful for inconsistencies detection.
