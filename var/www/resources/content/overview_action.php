<?php
(include_once realpath(dirname(__FILE__)).'/../main_config.php') or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0000');
(include_once LIBRARY_PATH.'/main/rain.tpl.nocache.class.php') or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0001');

raintpl::$tpl_dir = TEMPLATES_PATH.'/';

$tpl = new RainTPL;
$tpl->assign('html_path_prefix', '../../');
$tpl->assign('box_color', '');

if (isset($_GET['restart']))
{
	$tpl->assign('title', 'Raspberry Pi wird neugestartet');
	$tpl->assign('msg', '<script type="text/javascript" src="../../public_html/js/restart_ping.js"></script><span style="display: none;">Sobald dein Raspberry Pi wieder erreichbar ist, wirst du automatisch zur Übersicht weitergeleitet.<br />Solltest du nicht weitergeleitet werden, kommst du hier <a href="../../">zurück zur Übersicht.</a><br /><br />Aktueller Status: <strong class="green">Online</strong></span><div>Sollte dein Raspberry Pi wieder herhochgefahren sein, kommst du hier <a href="../../">zurück zur Übersicht.</a></div>');
}

if (isset($_GET['shutdown']))
{
	$tpl->assign('title', 'Raspberry Pi wird heruntergefahren');
	$tpl->assign('msg', 'Sollte dein Raspberry Pi wieder herhochgefahren sein, kommst du hier <a href="../../">zurück zur Übersicht.</a>');
}

$tpl->draw('single_box');
?>