=========
Multi WAN
=========

If you have more than one connection to the Internet, 
you must configure individual WAN connection
(ISP) and establish usage policies (for example prefer a connection over another one).


Select provider's management policy: 

* Balance to use all connections at the same time
* Active-Backup: to use backup connections in case of problems to the provider with the highest priority

Check IP 
     A ping is sent to Check IP every Ping interval (default is 5) seconds. 
     In case of missing response, the system disables the provider until it begin to receive answers again.
     It can be an IP or a comma-separated list of IPs.
     If using a list of IPs, the provider will be considered down if all specified hosts are unreachable.

Link status monitor
     This group of options control the responsiveness of the Link Status Monitor, which monitors
     the availability of each provider.
     Available options:

     * Ping interval: seconds between each ping to Check IP
     * # lost pings: number of consecutive lost pings after a provider is set to down state
     * % lost pings: percentage of lost pings after a provider is set to down state

Send mail notification on provider status change 
     If enabled, the system will send an email when a provider changes its state.
     Available options:

     * From: mail sender address
     * To: mail recipient address

