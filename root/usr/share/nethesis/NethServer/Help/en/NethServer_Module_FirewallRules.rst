==============
Firewall rules
==============

There are different kinds of rules to adjust the firewall behavior. When all
desired changes have been completed, click the :guilabel:`Apply Changes` button
to apply the rules.

Firewall rules
    The packets traversing the network zones can be accepted, rejected or
    ignored (dropped) by these rules.

Network services
    Packets directed to network services that run on the firewall itself obey
    these rules.

Policy routing
    Route matching packets towards a specific connection provider.

Traffic shaping
    Prioritize matching packets, by speeding-up or slowing-down them according
    to the available connection bandwidth.

Wherever possible, rules can be reordered by dragging them.

This page also allows:

* Create a rule at the bottom of listings
* Create a rule to the top of listings
* Configure



Configure
=========

Configure basic firewall policies.

Traffic to Internet red interface
  Possible choices are:

  * *Allowed*: all traffic from LAN (green) to Internet (red) is enabled by default.

  * *Blocked*: all traffic from LAN (green) to Internet (red) is disabled by default.

    In this case, you must explicitly create rules for all services
    which need to be allowed. For example, a rule that allows web
    traffic (ports 80 and 443) from green to red.

Ping from Internet
  If enabled, public interfaces (red) will respond to ping requests (ACCEPT).
  If disabled, public interfaces (red) will discard ping requests (DROP).

  To simplify troubleshooting, it is recommended to leave the ping enabled.

MAC validation (IP/MAC binding)
  If enabled, all traffic from hosts in green and blue interfaces is verified against a list of IP with associated MAC addresses.
  The IP/MAC association can be configured using the DHCP page.

Policy for hosts without IP/MAC binding (DHCP reservation)
  If MAC validation is enabled, select the policy for hosts without DHCP reservation.

Create / Edit
=============

When creating and editing rules, you can create the following types of firewall objects:

* Host
* Host group
* Zone
* Service
* CIDR network
* IP range
* Time condition

Each rule consists of the following fields.

Enabled
     Enable or disable the rule. A disabled rule is not effective and is ignored
     by the firewall configuration.

Action
     The action to take if the packet matches the criteria of the rule.
     The possible actions are:

     * *Accept*: accept the network traffic
     * *Reject*: block the traffic and notify the sender host
     * *Drop*: block the traffic, packets are dropped and not
       notification is sent to the sender host
     * *Route ...*: route the traffic to a specific WAN provider
     * *High priority*: speed-up the matching traffic
     * *Low priority*: slow-down the matching traffic

Source
    It's the source of the traffic, it can be: a host, a group of hosts or a zone.

Destination
    It's the destination of the traffic, it can be: a host, a group of hosts or a zone.

Service
    A service network consisting of protocol and port (optional).

Time condition
    Limit the effectiveness of the firewall rule to the selected time condition.

Write to log if this rule matches
    If enabled, all matched packets will be recorded in the log file
    :file:`/var/log/firewall.log`.

Description
    Optional description.

