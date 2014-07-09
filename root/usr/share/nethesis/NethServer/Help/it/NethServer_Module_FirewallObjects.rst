================
Oggetti firewall
================

Gli oggetti firewall semplificano la creazione di regole.
Un oggetto può essere usato in un numero illimitato di regole.

Host
====

Un host rappresenta una macchina con un indirizzo IP.
Può essere remoto o locale.
Quando le regole sono scritte su file, l'oggetto host è
tradotto nel suo indirizzo IP

Nome
    Nome identificativo per l'host.

Indirizzo IP
    Indirizzo IP dell'host. 

Descrizione
    Descrizione opzionale

Gruppi di host
==============

Un gruppo di host è un gruppo di macchine con un indirizzo IP.
Gli host in un gruppo devono essere omogenei.
Per esempio, una lista di host con indirizzo pubblico, oppure
un gruppo di macchine interne alla LAN.

Nome
    Nome identificativo per il gruppo.

Membri
   Lista di oggetti di tipo host. Gli oggetti host devono essere
   create all'interno del tab Host prima di essere usati in un gruppo.

Descrizione
    Descrizione opzionale

Servizi
=======

Un servizio è la rappresentazione di un software di rete che risponde
su una porta con uno specifico protocollo.
Per esempio, SSH e DNS sono servizi:

* SSH: protocollo TCP, porta 22
* HTTP: protocollo UDP, porta 53

Nome
    Nome identificativo del servizio.

Protocollo
   Scegliere uno dei protocollo dalla lista.

Porte
   Un numero intero che rappresenta una porta, oppure una lista di interi separati da virgole.

Descrizione
    Descrizione opzionale

Zone
====

Una zona è un gruppi di host identificato da un indirizzo di rete in formato CIDR (Classless Inter-Domain Routing).
Per esempio, la rete CIDR 192.168.1.0/29, rappresenta tutti gli host
da 192.168.1.2 a 192.168.1.6, dove 192.168.1.1 è il gateway e 192.168.1.7 l'indirizzo di broadcast.

Nome
    Nome identificativo per la zona. Massimo 5 caratteri.

Indirizzo di rete
    Una rete in formato CIDR.

Interfaccia
    L'interfaccia di rete a cui sono collegati gli host.

Descrizione
    Descrizione opzionale
