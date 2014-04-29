=========================
Gestión de ancho de banda 
=========================

El gestor de ancho de banda le permite cambiar las prioridades en el tráfico al pasar por el servidor de seguridad (que debe tener al menos dos interfaces de red). 

General
=======

Activa o desactiva la gestión de ancho de banda.


Reglas de interfaz
==================

Para cada interfaz en la que desea administrar es necesaria la prioridad de ancho de banda para especificar la cantidad máxima de ancho de banda disponible en ambas direcciones salientes y entrantes. Si no hay datos se transmiten a una velocidad a la configurada. Es imprescindible utilizar los valores reales medidos preferiblemente con pruebas de velocidad, en particular para la banda de subida (de salida). La tabla muestra los valores configurados en cada interfaz, lo que permite modificar los límites de ancho de banda.

Crear / Modificar
-----------------

Crear una configuración de límites de ancho de banda de la interfaz.

Interfaz
    Seleccione la interfaz a la que se aplica el límite de ancho de banda. En general, el ancho de banda está limitado sólo en las interfaces WAN. 

Ancho de banda entrante (kbps)
    Ajuste la cantidad de ancho de banda entrante (descargar).

Ancho de banda de salida (kbps)
    Ajuste la cantidad de ancho de banda de salida (subir).

Descripción
    Una nota opcional (por ejemplo: ADSL 1280/256).


Reglas de direcciones
=====================

La tabla muestra la lista de direcciones de red (IP o MAC) que tienen personalizada reglas de prioridad. Por ejemplo, usted puede decidir que el tráfico de un equipo específico en la red local tenga una prioridad alta o baja en comparación con otros.


Crear / Modificar
-----------------

Dirección IP o MAC
    Introduzca la dirección IP o la dirección MAC que identifica el ordenador.

Descripción
     Una descripción opcional para identificar claramente el propósito de la regla. Por ejemplo: una alta prioridad para el jefe.

Reglas de puerto
================

La tabla muestra la lista de puertos TCP / UDP que tienen reglas con prioridad personalizada. Por ejemplo, puede especificar que el tráfico en un servicio de red en particular (desde o hacia un puerto en particular) tiene una prioridad baja o alta en comparación con el tráfico normal de la red.


Crear
-----

Puerto
    Especifique el puerto utilizado por el servicio de red.

Protocolo
    Introduzca el protocolo IP.

Descripción 
    Una descripción opcional que establece claramente la finalidad de la norma. Por ejemplo: Fondo para Servicio FTP.
