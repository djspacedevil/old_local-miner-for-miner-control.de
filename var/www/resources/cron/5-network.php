<?php
(include_once realpath(dirname(__FILE__)).'/../main_config.php') 	or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0000');
(include_once LIBRARY_PATH.'/main/classes.php')						or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0001');
(include_once LIBRARY_PATH.'/main/functions.php')					or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0002');
(include_once LIBRARY_PATH.'/main/functions_rpi.php')					or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0003');

$networkConnections = getAllNetworkConnections();
$networkCountsJson = getConfigValue('config_network_count');

$networkCounts = json_decode($networkCountsJson, true);

foreach ($networkConnections as $network)
{
	$Logging = new Logging();
	$Logging->setFile(LOG_PATH.'/network_'.$network['interface'].'.log.txt');
	$Logging->setLimit(2016);
	$last = $Logging->getLast();
	
	$countSent = 0;
	$countReceive = 0;
	
	if (isset($networkCounts[$network['interface']]['sent']))
		$countSent = $networkCounts[$network['interface']]['sent'];
		
	if (isset($networkCounts[$network['interface']]['receive']))
		$countReceive = $networkCounts[$network['interface']]['receive'];
	
	if ((time() - rpi_getRuntime()) < (int) $last[0] && (int) $last[1] > ($network['sent'] + 4294967295 * $countSent))
		$countSent++;
	
	if ((time() - rpi_getRuntime()) < (int) $last[0] && (int) $last[2] > ($network['receive'] + 4294967295 * $countReceive))
		$countReceive++;
	
	if ((time() - rpi_getRuntime()) > (int) $last[0] && (int) $last[0] != 0)
	{
		$countSent = 0;
		$countReceive = 0;
	}
	
	$networkCounts[$network['interface']]['sent'] = $countSent;
	$networkCounts[$network['interface']]['receive'] = $countReceive;
	
	$Logging->add(time().'~'.($last[1] + (4294967295 * $countSent - $last[1]) + $network['sent']).'~'.($last[2] + (4294967295 * $countReceive - $last[2]) + $network['receive']));
	$Logging->close();
}
setConfigValue('config_network_count', '\''.json_encode($networkCounts).'\'');
?>