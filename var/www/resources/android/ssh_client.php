<?php
(include_once realpath(dirname(__FILE__)).'/../main_config.php') or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0000');
include_once LIBRARY_PATH.'/main/ssh_connection.php';

switch ($_GET['command'])
{
	case 'raspi_shutdown':
		if ($stream = ssh2_exec($ssh, 'shutdown -h now'))
		{
			stream_set_blocking($stream, true);
			stream_get_contents($stream);
			print(json_encode(array('client_return' => array(0 => array('error' => '', 'return' => 'true')))));
		}
		else
		{
			print(json_encode(array('client_return' => array(0 => array('error' => 'false', 'return' => 'SSH-Befehl konnte nicht ausgeführt werden.')))));
		}
			break;
	case 'raspi_restart':
		if ($stream = ssh2_exec($ssh, 'shutdown -r now'))
		{
			stream_set_blocking($stream, true);
			stream_get_contents($stream);
			print(json_encode(array('client_return' => array(0 => array('error' => '', 'return' => 'true')))));
		}
		else
		{
			print(json_encode(array('client_return' => array(0 => array('error' => 'false', 'return' => 'SSH-Befehl konnte nicht ausgeführt werden.')))));
		}
			break;
	default:
		print(json_encode(array('client_return' => array(0 => array('error' => 'false', 'return' => 'Kommando ist ungültig.')))));
}
?>