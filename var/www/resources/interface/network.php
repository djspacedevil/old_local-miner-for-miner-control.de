<?php
(include_once realpath(dirname(__FILE__)).'/../main_config.php') or die(print(json_encode(array('status' => 600))));
(include_once LIBRARY_PATH.'/main/functions.php') or die(print(json_encode(array('status' => 601))));
(include_once LIBRARY_PATH.'/main/functions_rpi.php') or die(print(json_encode(array('status' => 602))));

$data_network = array();
$data_wlan_devices = array();
$data_wlan_network = array();

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
	$data_network[] = array('signal_icon' => (isset($network['option']['signal'])) ? getStringFromSignal($network['option']['signal']) : ($network['ip'] != 0) ? '100' : 'disable',
							'signal' => (isset($network['option']['signal'])) ? $network['option']['signal'] : ($network['ip'] != 0) ? '100' : 'disable',
							'interface' => $network['interface'],
							'ip' => ($network['ip'] != 0) ? $network['ip'] : 'Nicht verbunden',
							'mac' => $network['mac']
						);
}

foreach (scanAccessPoints($networkConnections) as $key => $interface)
{
	$data_wlan_devices[] = $key;
	foreach ($interface as $wlan)
	{
			$data_wlan_network[$key][] = array('signal_icon' => getStringFromSignal($wlan['signal']),
								'signal' => $wlan['signal'],
								'ssid' => $wlan['ssid'],
								'mac' => $wlan['mac'],
								'channel' => $wlan['channel'],
								'encryption' => $wlan['encryption']
							);
		
	}
}

print(json_encode(array('interfaces' => $data_network, 'wlan' => array('devices' => $data_wlan_devices, 'networks' => ((count($data_wlan_network) > 0) ? $data_wlan_network : array("empty" => ""))), 'status' => 200)));
?>