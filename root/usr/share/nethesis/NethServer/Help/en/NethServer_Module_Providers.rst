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

Check IP 
     A ping is sent to Check IP every X (default is 5) seconds. 
     In case of missing response, the system disables the provider until it begin to receive answers again.
     It can be an IP or a list of IPs.
     If using a list of IPs, the provider will be considered down if all specified hots are unreachable.

Link status monitor
     This group of options control the responsiveness of the Link Status Monitor, which monitors
     the availability of each provider.
     Available options:

     * Ping interval: seconds between each ping to Check IP
     * # lost pings: number of lost pings after a provider is set to down state
     * % lost pings: percentage of lost pings after a provider is set to down state

Send mail notification on provider status change 
     If enabled, the system will send an email when a provider changes its state.
     Available options:

     * From: mail sender address
     * To: mail recipient address


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


