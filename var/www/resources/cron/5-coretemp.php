<?php
(include_once realpath(dirname(__FILE__)).'/../main_config.php') 	or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0000');
(include_once LIBRARY_PATH.'/main/classes.php')						or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0001');
(include_once LIBRARY_PATH.'/main/functions_rpi.php')				or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0002');

$Logging = new Logging();
$Logging->setFile(LOG_PATH.'/coretemp.log.txt');
$Logging->setLimit(2016);
$Logging->add(time().'~'.rpi_getCoreTemprature());
$Logging->close();
?>