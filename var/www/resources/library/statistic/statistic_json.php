<?php
(include_once realpath(dirname(__FILE__)).'/../../main_config.php') or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0000');
(include_once LIBRARY_PATH.'/main/classes.php')						or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0001');

$log = new Logging();
$log->setFile(LOG_PATH.'/'.$_GET['log'].'.log.txt');

switch ($_GET['type'])
{
	case 'coretemp':
	$arr = array();
	$arr['cols'][] = array('id' => '', 'label' => 'Zeit', 'type' => 'datetime');
	$arr['cols'][] = array('id' => '', 'label' => 'Temperatur', 'type' => 'number');
	foreach ($log->getAll() as $row)
	{
		$arr['rows'][]['c'] = array(
				array('v' => 'Date('.date('Y,'.(date('m', $row[0])-1).',d,H,i', $row[0]).')'),
				array('v' => str_replace("\n", '', $row[1]))
			);
	}
		break;
		
	case 'cpuload':
	$arr = array();
	$arr['cols'][] = array('id' => '', 'label' => 'Zeit', 'type' => 'datetime');
	$arr['cols'][] = array('id' => '', 'label' => 'Auslastung', 'type' => 'number');
	foreach ($log->getAll() as $row)
	{
		$arr['rows'][]['c'] = array(
				array('v' => 'Date('.date('Y,'.(date('m', $row[0])-1).',d,H,i', $row[0]).')'),
				array('v' => str_replace("\n", '', $row[1]))
			);
	}
		break;
		
	case 'ram':
	$arr = array();
	$arr['cols'][] = array('id' => '', 'label' => 'Zeit', 'type' => 'datetime');
	$arr['cols'][] = array('id' => '', 'label' => 'Auslastung', 'type' => 'number');
	foreach ($log->getAll() as $row)
	{
		$arr['rows'][]['c'] = array(
				array('v' => 'Date('.date('Y,'.(date('m', $row[0])-1).',d,H,i', $row[0]).')'),
				array('v' => str_replace("\n", '', $row[1]))
			);
	}
		break;
	
	case 'network':
	$arr = array();
	$arr['cols'][] = array('id' => '', 'label' => 'Zeit', 'type' => 'datetime');
	$arr['cols'][] = array('id' => '', 'label' => 'Gesendet', 'type' => 'number');
	$arr['cols'][] = array('id' => '', 'label' => 'Empfangen', 'type' => 'number');
	foreach ($log->getAll() as $row)
	{
		$arr['rows'][]['c'] = array(
				array('v' => 'Date('.date('Y,'.(date('m', $row[0])-1).',d,H,i', $row[0]).')'),
				array('v' => str_replace("\n", '', round($row[1]/1048576,2))),
				array('v' => str_replace("\n", '', round($row[2]/1048576,2)))
			);
	}
		break;
}

if (file_exists(LOG_PATH.'/'.$_GET['log'].'.log.txt') && is_file(LOG_PATH.'/'.$_GET['log'].'.log.txt') && filesize(LOG_PATH.'/'.$_GET['log'].'.log.txt') == 0)
	header("HTTP/1.0 412");
else
	print(json_encode($arr, JSON_NUMERIC_CHECK));
?>