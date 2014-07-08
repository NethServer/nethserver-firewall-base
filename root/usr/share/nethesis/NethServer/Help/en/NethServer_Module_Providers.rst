=========
Multi WAN
=========

If you have more than one connection to the Internet, 
you must configure individual WAN connection
(ISP) and establish usage policies (for example prefer a connection over another one).


Configure
=========

Select provider's management policy: 

* Balance to use all connections at the same time
* Active-Backup: to use backup connections in case of problems to the provider with the highest priority

Create or Edit
==============

Create or edit the configuration of providers. 

Name 
     A name to identify the connection (ISP). Max 5 characters. 

Enabled/disabled 
     Enable or disable the provider.

Weight 
     The "weight" of the connection. 
     Traffic will be routed proportionally to the weight: higher weight means more traffic.
     A provider with a weight of 100 will receive twice the traffic of one with weight 50. 
     Please, assign weights accordingly to connection bandwidth.
     When using active-backup mode, the weight determines the use of the line. 
     If the first provider has weight 100 and the second has weight 50,
     the traffic is always sent to the first provider. The second one will be used only if first provider goes down.

Description 
     An optional description to identify the provider. 

Check IP 
     A ping is sent to Check IP every 5 seconds. 
     In case of missing response, the system disables the provider until it begin to receive answers again. 
     Caution: the IP should be inside the provider's network: 
     the system determines it automatically, we recommend not to change the pre-set IP. 
     In case of connectivity problems, the Check IP host is not reachable. 

