# MQTTSyncServer
   Dieses Modul wird dazu benötigt, um auf dem Client System die Instanzen anzulegen, welche die Werte des Master Systems auf dem Slave System widerspiegeln.

   ## Inhaltverzeichnis
   1. [Voraussetzungen](#1-voraussetzungen)
   2. [Konfiguration](#2-konfiguration)
   3. [Spenden](#3-spenden)
   4. [Lizenz](#4-lizenz)
   
## 1. Voraussetzungen

* mindestens IPS Version 5.5

## 2. Konfiguration in IP-Symcon

* Das MQTT Topic gibt an, unter welchem Group Topic die zu synchronisierenden Daten versendet werden.
* In der Liste können die Objekte angegeben werden, welche mit dem Slave System synchronisiert werden sollen.
    * Es wird die ObjektID und das MQTT Topic hinterlegt.
    * Als Objekt können Variablen oder komplette Instanzen hinterlegt werden
