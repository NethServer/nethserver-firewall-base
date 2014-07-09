================
Firewall objects
================

Firewall objects ease the creation of firewall rules.
An object can be used in any number of rule.

Hosts
=====

A host represent is a machine with an IP address.
It can be local or remote.
When rules are written to file,
the host object will be translated in its own IP address.

Name
    Name identifier for the host.

IP address
    IP address of the host. 

Description
    Optional description.

Host groups
===========

A host group is a group of machines with an IP address.
Hosts in a group should be homogeneous.
For example, a list of hosts with public addresses, or
a group of machines inside the LAN.

Name
    Name identifier for the host group.

Members
   List of host object. Host objects must be created
   inside the Hosts tab before use inside a group.

Description
    Optional description.

Services
========

A service is the representation of a network software responding
to a port with a specific protocol.
For example, SSH and DNS are services:

* SSH: protocol TCP, port 22
* HTTP: protocol UDP, port 53

Name
    Name identifier for the service.

Protocol
   Select one of the available protocols.

Ports
   An integer representing a port, or a list of integers separated by commas.

Description
    Optional description.

Zones
=====

A zone is a group of host identified with a network address in CIDR format (Classless Inter-Domain Routing).
For example, given the CIDR network 192.168.1.0/29, it represents all hosts
from 192.168.1.2 to 192.168.1.6, where 192.168.1.1 is the gateway and 192.168.1.7 is the broadcast.

Name
    Name identifier for the zone. Max 5 characters.

Network
    A network in CIDR format.

Interface
    The interface where the hosts are connected.

Description
    Optional description.
