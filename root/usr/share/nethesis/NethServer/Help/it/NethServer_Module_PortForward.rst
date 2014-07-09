============
Port forward
============

E' possibile usare questo pannello per modificare le regole del firewall
così da aprire una specifica porta (o un intervallo di porte) sul server
ed inoltrare una porta ad un altra. In questo modo è possibile
permettere l'accesso ad host privato nella rete locale.

Crea / Modifica
===============

Porta di origine
    Specifica la porta aperta sul IP pubblico.

Porta destinazione
    Specifica la porta sul host interno e destinazione del traffico.

Host destinazione
    Selezionare la macchina interna alla LAN a cui verrà rediretto il traffico.

Permetti solo da 
    Permette il forward del traffico solo da alcune sorgenti
    reti/host, secondo le `specifiche di Shorewall per SOURCE
    <http://shorewall.net/FAQ.htm#PortForwarding>`_.

Descrizione
    Descrizione opzionale della regola di port forwarding.


Abilita / Disabilita
====================

Le regole di Port Forwarding vengono abilitate di default al momento
della creazione, è possibile abilitare/disabilitare momentaneamente
attraverso questo pulsante

Si
    Abilita la regola.

No
    Disabilita la regola.

Controllo Firewall
==================

Esegue un controllo generale delle regole del firewall configurate. Utile per individuare inconsistenze.

