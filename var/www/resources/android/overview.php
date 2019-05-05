<?php
(include_once realpath(dirname(__FILE__)).'/../main_config.php') or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0000');
(include_once LIBRARY_PATH.'/main/functions.php')					or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0001');
(include_once LIBRARY_PATH.'/main/functions_rpi.php')				or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0002');

$memory = rpi_getMemoryInfo();
$ram = rpi_getMemoryUsage();
$data = array();
$data[] = array(
			'runtime' => getDateFormat(rpi_getRuntime()),
			'laststart' => date('d.m.Y H:i', time() - rpi_getRuntime()),
			'cputyp' => rpi_getCPUType(),
			'cpuclock' => rpi_getCpuClock().' MHz',
			'cputemp' => rpi_getCoreTemprature().' °C',
			//'ramtotal' => ($ram['total'] <= 280000000) ? '256 MB' : '512 MB',
			'ramtotal' => sizeUnit($ram['total']),
			'ramused' => sizeUnit($ram['used']),
			'memoryused' => sizeUnit($memory[2]['used']),
			'memoryfree' => sizeUnit($memory[2]['free']),
			'memorytotal' => sizeUnit($memory[2]['total']),
			'ram_percent' => $ram['percent'],
			'memory_percent' => $memory[2]['percent'],
			'cputemp_limit' => getConfigValue('config_temp_celsius')
		);
//print(json_encode(array('overview' => $data)));

$data_devices = array();

foreach (rpi_getUsbDevices() as $device)
{
	$data_devices[] = array('device' => $device);
}

print(json_encode(array('overview' => $data, 'devices' => $data_devices)));
?>