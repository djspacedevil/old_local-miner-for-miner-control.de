<?php
(include_once realpath(dirname(__FILE__)).'/../../main_config.php') or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0000');
(include_once LIBRARY_PATH.'/main/functions.php')					or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0001');
(include_once LIBRARY_PATH.'/main/functions_rpi.php')				or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0001');

switch ($_GET['data'])
{
	case 'runtime':
	echo getDateFormat(rpi_getRuntime());
		break;
	case 'cpuFreq':
	echo rpi_getCpuClock();
		break;
	case 'cpuLoad':
	echo rpi_getCpuLoad();
		break;
	case 'temp':
	echo number_format(rpi_getCoreTemprature(), 2, ',', '');
		break;
	case 'ram':
	$ram = rpi_getMemoryUsage(); echo $ram['percent'];
		break;
	case 'memoryUsed':
	$memory = rpi_getMemoryInfo(); echo sizeUnit($memory[count($memory)-1]['used']);
		break;
	case 'memoryFree':
	$memory = rpi_getMemoryInfo(); echo sizeUnit($memory[count($memory)-1]['free']);
		break;
	case 'memoryTotal':
	$memory = rpi_getMemoryInfo(); echo sizeUnit($memory[count($memory)-1]['total']);
		break;
	case 'memoryPercent':
	$memory = rpi_getMemoryInfo(); echo $memory[count($memory)-1]['percent'];
		break;
	default:
	echo 'Kein Wert';
}
?>