=========
Multi WAN
=========

Se si dispone di più di una connessione ad Internet,
è necessario configurare i dati relativi alla singole WAN
(provider) e stabilire le politiche d'uso (per esempio favorire una connessione).



Configura
=========

Selezionare la politica di gestione dei provider:

* Balance per utilizzare contemporanemaente tutte le connessioni
* Active-Backup per usare le connessioni in caso di problemi al provider con la priorità maggiore


Crea o Modifica
===============

Modifica la configurazione del provider.

Nome
    Un nome per identificare la connessione (ISP). Max 5 caratteri.

Abilitato/Disablitato
    Abilita o disabilita il provider.

Peso
    Il "peso" della connessione. A peso maggiore verrà proporzionalmente
    instradato più traffico dati, quindi, per esempio,
    un provider con peso 100 riceverà il doppio del traffico di uno con peso 50.
    Se si dispone di due linee con disponibiltà di banda diversa, per esempio una doppia dell'altra, 
    sarà necessario assegnare peso doppio alla più veloce rispetto alla più lenta.
    Nella modalità acrive-backup, il peso determina l'uso della linea. 
    Se il provider 1 ha peso 100 e il 2 50, il traffico verrà sempre inviato al provider 1, passando al 2 solo in caso di problemi all'1.

Descrizione
    Una descrizione opzionale per riconoscere il provider.

IP controllo
    All'IP di controllo viene inviato un pacchetto ping ogni 5 secondi e, 
    in caso di mancata risposta, il sistema disabilita il provider fino a quando non comincerà a ricevere nuovamente risposte.
    Attenzione: l'IP di controllo preferito è un indirizzo del provider: 
    il sistema lo determina e suggerisce automaticamente, consigliamo di non modificare l'IP pre-impostato. 
    In caso di problemi di connettività, l'IP di controllo non è raggiungibile.

