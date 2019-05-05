<?php
(include_once realpath(dirname(__FILE__)).'/../main_config.php') or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0000');
(include_once LIBRARY_PATH.'/main/functions.php')					or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0001');
(include_once LIBRARY_PATH.'/main/functions_rpi.php')				or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0002');

$data_network = array();

function getStringFromSignal($signal)
{
	if ($signal <= 10)
		return '00';
	elseif ($signal <= 25)
		return '25';
	elseif ($signal <= 50)
		return '50';
	elseif ($signal <= 75)
		return '75';
	elseif ($signal <= 100)
		return '100';
	else
		return false;
}

$networkConnections = getAllNetworkConnections();

foreach ($networkConnections as $network)
{
	$data_network[] = array('signal' => (isset($network['option']['signal'])) ? getStringFromSignal($network['option']['signal']) : ($network['ip'] != 0) ? '100' : 'disable',
							'interface' => $network['interface'],
							'ip' => ($network['ip'] != 0) ? $network['ip'] : 'Nicht verbunden',
							'mac' => $network['mac']
						);
}

foreach (scanAccessPoints($networkConnections) as $key => $interface)
{
	foreach ($interface as $wlan)
	{
		if ($key == 'wlan0')
		{
			
			$data_wlan_network[] = array('signal' => getStringFromSignal($wlan['signal']),
								'ssid' => $wlan['ssid'],
								'mac' => $wlan['mac'],
								'channel' => $wlan['channel'],
								'encryption' => $wlan['encryption']
							);
		}
		/*
		$data_wlan_networks[$key][] = array('signal' => getStringFromSignal($wlan['signal']),
								'ssid' => $wlan['ssid'],
								'mac' => $wlan['mac'],
								'channel' => $wlan['channel'],
								'encryption' => $wlan['encryption']
							);*/
	}
}

print(json_encode(array('interfaces' => $data_network, 'wlan_network' => $data_wlan_network)));
?>