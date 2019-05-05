<?php
(include_once realpath(dirname(__FILE__)).'/../../main_config.php') or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0000');
(include_once LIBRARY_PATH.'/main/ssh_connection.php')				or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0001');

if ($stream = ssh2_exec($ssh, 'sudo ifdown '.escapeshellarg($_POST['interface'])))
	echo 'done';
else
	echo 'Konnte Verbindung nicht trennen!';
?>