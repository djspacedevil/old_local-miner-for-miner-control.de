<?php
(include_once realpath(dirname(__FILE__)).'/../main_config.php') or die(print(json_encode(array('status' => 600))));
(include_once LIBRARY_PATH.'/main/functions.php') or die(print(json_encode(array('status' => 601))));
(include_once LIBRARY_PATH.'/main/ssh_connection.php') or die(print(json_encode(array('status' => 602))));

switch (isset($_GET['command']) ? $_GET['command'] : '')
{
	case 'raspi_shutdown':
		if ($stream = ssh2_exec($ssh, 'sudo shutdown -h now'))
		{
			stream_set_blocking($stream, true);
			stream_get_contents($stream);
			print(json_encode(array('status' => 702)));
		}
		else
			print(json_encode(array('status' => 701)));
			
			break;
	case 'raspi_restart':
		if ($stream = ssh2_exec($ssh, 'sudo shutdown -r now'))
		{
			stream_set_blocking($stream, true);
			stream_get_contents($stream);
			print(json_encode(array('status' => 702)));
		}
		else
			print(json_encode(array('status' => 701)));
			
			break;
	case 'scan_wlan':
		if (scanAccessPoints(getAllNetworkConnections(), isset($ssh) ? $ssh : ''))
			print(json_encode(array('status' => 702)));
		else
			print(json_encode(array('status' => 701)));
			
			break;
	case 'wait':
		sleep(2);
			print(json_encode(array('status' => 702)));
			
			break;
	default:
		print(json_encode(array('status' => 700)));
}
?>