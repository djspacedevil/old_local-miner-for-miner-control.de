<?php
(include_once realpath(dirname(__FILE__)).'/../../main_config.php') or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0000');
(include_once 'network_functions.php')								or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0001');

if (isset($_POST['interface'], $_POST['ssid'], $_POST['psk']))
{
	include_once LIBRARY_PATH.'/main/ssh_connection.php';
	if ($stream = ssh2_exec($ssh, 'sudo wpa_passphrase "'.escapeshellarg($_POST['ssid']).'" "'.escapeshellarg($_POST['psk']).'" | grep "psk=[[:alnum:]]"'))
	{
		stream_set_blocking($stream, true);
		$stream_ssh = stream_get_contents($stream);
		$psk_string = trim(str_replace('psk=', '', $stream_ssh));
	}
	else
	{
		echo 'Konnte Konfiguration nicht verarbeiten!';
		exit();
	}
	
	if (!($interface_file = file('/etc/network/interfaces')))
	{
		echo 'Konnte Konfiguration nicht laden!';
		exit();
	}
	
	$interface_file = deleteInterface($interface_file, $_POST['interface']);
	
	$wpa_settings = array();
	$wpa_settings[] = '	wpa-ssid '.$_POST['ssid'];
	$wpa_settings[] = '	wpa-psk '.$psk_string;
	
	$new_interface = addInterface($interface_file, array('iface '.$_POST['interface'].' inet dhcp'."\n", implode("\n", $wpa_settings)));
	
	if (($interface_status = writeToInterface($ssh, $new_interface)) != 0)
		echo 'Fehler beim speichern. Fehlercode: '.$interface_status;
	else
		echo 'done';
}
else
{
	echo 'Es wurde entweder keine SSID oder Passwort übergeben!';
}
?>