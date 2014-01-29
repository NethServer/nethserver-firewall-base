==============
Gestione banda
==============

La gestione banda permette di cambiare priorità al traffico che
attraversa il firewall (che dovrà avere almeno due interfacce di rete).

Generale
========

Attiva o disattiva la gestione banda.


Regole interfacce
=================

Per ogni interfaccia su cui si desidera gestire la priorità di banda è
necessario indicare la quantità massima di banda disponibile sia in
uscita che in entrata. Non verranno trasmessi dati ad una velocità
superiore a quella configurata. E' fondamentale utilizzare valori reali,
preferibilmente misurati con dei test, in particolare per la banda in
upload (in uscita). La tabella mostra i valori configurati su ogni
interfaccia, permettendo di modificare i limiti di banda.

Crea / Modifica
---------------

Crea una configurazione di limiti di banda per interfaccia.

Interfaccia
    Selezionare l'interfaccia a cui si riferisce la quantità di banda
    sottostante. In genere si limita la banda solo nelle interfacce WAN.
Banda entrante (kbps)
    Impostare la quantità di banda in ingresso (download).
Banda uscente (kbps)
    Impostare la quantità di banda in uscita (upload).
Descrizione
    E' possibile indicare una nota (per esempio: ADSL 1280/256).


Regole indirizzi 
================

La tabella mostra l'elenco degli indirizzi di rete (IP o MAC) che hanno
regole di priorità personalizzate. Per esempio, è possibile decidere
che il traffico proveniente da uno specifico computer della rete locale
abbia una priorità bassa oppure alta rispetto agli altri.


Crea / Modifica 
---------------

Indirizzo IP o MAC
    Indicare l'indirizzo IP o MAC che identifica il computer.
Descrizione
     E' possibile aggiungere una descrizione opzionale per descrivere
     chiaramente la scopo della regola. Per esempio: priorità alta per il pc del
     direttore.

Regole porte 
============

La tabella mostra l'elenco delle porte TCP/UDP che hanno regole di
priorità personalizzate. Per esempio, è possibile specificare che il
traffico relativo ad un determinato servizio di rete (proveniente o
destinato a una determinata porta) abbia una priorità bassa oppure alta
rispetto al normale traffico di rete.


Crea 
----

Porta
    Indicare la porta utilizzata dal servizio di rete
Protocollo
    Inserire il protocollo IP
Descrizione
    E' possibile aggiungere una descrizione opzionale che indichi
    chiaramente la scopo della regola. Per esempio: priorità bassa per il
    servizio ftp.

