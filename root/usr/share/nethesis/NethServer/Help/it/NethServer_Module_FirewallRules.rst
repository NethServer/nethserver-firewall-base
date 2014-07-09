===============
Regole firewall
===============

Le regole del firewall descrivono quale traffico di rete è permesso o bloccato.
I pacchetti che attraversano le zone del firewall vengono analizzati e confrontanti
con le regole create.
La prima regola che corrisponde ai criteri, viene applicata.

Dalla pagina principale è possibile riordinare le regole semplicemente trascinandole.

In questa pagina sono inoltre presenti tre pulsanti:

* Crea una regola in fondo
* Crea una regola in cima
* Configura

Quando tutte le modifiche desiderate sono state portate a termine,
cliccare il pulsante :guilabel:`Applica modifiche` per applicare le regole.

Configura
=========

Configura le policy di base.

Traffico verso Internet (interfaccia red)
    Le possibili scelte sono:

    * *Permesso*: tutto il traffico dalla LAN (green) a Internet (red) è abilitato di default.
    * *Bloccato*: tutto il traffico dalla LAN (green) a Internet (red) è disabilitato di default.
      In questo caso è necessario creare esplicitamente regole per tutti i servizi che si vogliono permettere.
      Ad esempio, una regola che permette il traffico web (porte 80 e 443) dalla green alla red.


Ping da Internet
    Se abilitato, le interfacce pubblice (red) risponderanno alle richieste di ping (ACCEPT).
    Se disabilitato, le interfacce pubblice (red) scarteranno le richieste di ping (DROP)

    Per semplificare la risoluzione di problemi, si consiglia di lasciare il ping abilitato.


Crea / Modifica
===============

Durante la creazione e modifica di regole, è possibile creare i seguenti tipi di oggetti firewall:

* Host
* Gruppi di host
* Zone
* Servizi

Ogni regola è composta dai seguenti campi.

Abilitato
    Abilita o disabilita la regola.
    Una regola disabilitata non viene salvata nel file di configurazione

Azione
    L'azione da intraprendere se il pacchetto corrisponde ai criteri della regola.
    Le azioni possibili sono:

    * *Accept*: accetta il traffico 
    * *Reject*: blocca il traffico ed informa il mittente che la richiesta effettuata non è permessa
    * *Drop*: blocca il traffico, i pacchetti vengono scartati e il mittente non viene notificato

Origine
    Indica l'origine del traffico, può essere: un host, un gruppo di host oppure una zona.

Destinazione
    Indica la destinazione del traffico, può essere: un host, un gruppo di host oppure una zona.

Servizio
    Un servizio di rete composto da protocollo e porta (opzionale).

Registra nel log se questa regola viene applicata
    Se abilitato, tutti i pacchetti che corrispondono alla regola descritta verranno registrati
    nel file :file:`/var/log/firewall.log`.

Descrizione
    Descrizione opzionale.



