<?php
(include_once realpath(dirname(__FILE__)).'/../main_config.php') or die(print(json_encode(array('status' => 600))));
(include_once LIBRARY_PATH.'/main/functions.php') or die(print(json_encode(array('status' => 601))));
(include_once LIBRARY_PATH.'/main/functions_rpi.php') or die(print(json_encode(array('status' => 602))));

$revision = rpi_getRpiRevision();
$memory = rpi_getMemoryInfo();
$ram = rpi_getMemoryUsage();
$data = array();

$data[] = array(
			'runtime' => rpi_getRuntime(),
			'last_start' => time() - rpi_getRuntime(),
			'serial' => rpi_getRpiSerial(),
			'revision_model' => $revision['model'],
			'revision_manufacturer' => $revision['manufacturer'],
			'revision_revision' => $revision['revision'],
			'revision_pcb' => $revision['pcb'],
			'distribution' => rpi_getDistribution(),
			'kernel' => rpi_getKernelVersion(),
			'cpu_typ' => rpi_getCPUType(),
			'cpu_clock' => rpi_getCpuClock(),
			'cpu_load' => rpi_getCPULoad(),
			'cpu_model' => rpi_getCpuModel(),
			'cpu_max_clock' => rpi_getCpuMaxClock(),
			'cpu_temp' => rpi_getCoreTemprature(),
			'ram_total' => $revision['memory'],
			'ram_used' => $ram['used'],
			'memory_used' => $memory[count($memory)-1]['used'],
			'memory_free' => $memory[count($memory)-1]['free'],
			'memory_total' => $memory[count($memory)-1]['total'],
			'ram_percent' => $ram['percent'],
			'memory_percent' => $memory[count($memory)-1]['percent']
		);

print(json_encode(array('detailed_overview' => $data, 'status' => 200)));
?>