<?php
(include_once realpath(dirname(__FILE__)).'/../main_config.php') or die(print(json_encode(array('status' => 600))));
(include_once LIBRARY_PATH.'/main/functions.php') or die(print(json_encode(array('status' => 601))));
(include_once LIBRARY_PATH.'/main/functions_rpi.php') or die(print(json_encode(array('status' => 602))));

$memory = rpi_getMemoryInfo();
$ram = rpi_getMemoryUsage();
$data = array();

$data[] = array(
			'runtime' => rpi_getRuntime(),
			'laststart' => time() - rpi_getRuntime(),
			'cputyp' => rpi_getCPUType(),
			'cpuclock' => rpi_getCpuClock(),
			'cpuload' => rpi_getCPULoad(),
			'cputemp' => rpi_getCoreTemprature(),
			'ramtotal' => $ram['total'],
			'ramused' => $ram['used'],
			'memoryused' => $memory[count($memory)-1]['used'],
			'memoryfree' => $memory[count($memory)-1]['free'],
			'memorytotal' => $memory[count($memory)-1]['total'],
			'ram_percent' => $ram['percent'],
			'memory_percent' => $memory[count($memory)-1]['percent'],
			'cputemp_limit' => getConfigValue('config_temp_celsius')
		);

$data_devices = array();

foreach (rpi_getUsbDevices() as $device)
{
	$data_devices[] = array('device' => $device);
}

print(json_encode(array('overview' => $data, 'devices' => $data_devices, 'status' => 200)));
?>