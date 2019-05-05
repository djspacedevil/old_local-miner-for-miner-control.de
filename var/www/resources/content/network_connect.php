<?php
$tpl = new RainTPL;

$networkConnections = getAllNetworkConnections();

$tpl->assign('js_variables', 'var wlan_path = \''.str_replace(dirname($_SERVER['SCRIPT_FILENAME']).'/', '', LIBRARY_PATH).'/network\';');
$tpl->assign('interface', $_GET['interface']);
$tpl->assign('ssid', $_GET['ssid']);

$tpl->draw('network_connect');
?>