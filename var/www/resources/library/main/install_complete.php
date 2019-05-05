<?php
(include_once realpath(dirname(__FILE__)).'../../../../resources/main_config.php')	or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0000');
(include_once LIBRARY_PATH.'/main/rain.tpl.nocache.class.php');

raintpl::$tpl_dir = TEMPLATES_PATH.'/';

$tpl = new RainTPL;
$tpl->assign('html_path_prefix', '../../../');
$tpl->assign('box_color', '');

if (rename($config['paths']['main'].'/install', $config['paths']['main'].'/install_'.uniqid(rand())) !== false)
{
	if (!headers_sent($filename, $linenum))
		exit(header('Location: ../../../'));
	else
	{
		$tpl->assign('title', 'Weiterleitung');
		$tpl->assign('msg', '<strong class="red">Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="../../../">diesen Link</a> an.</strong>');
	}
}
else
{
	$tpl->assign('title', 'Fehler');
	$tpl->assign('msg', '<strong class="red">Leider konnte die Installation nicht erfolgreich abgeschlossen werden! Bitte lösche den Ordner "'.$config['paths']['main'].'/install/" oder benenne ihn um. Wenn das erledigt ist, kannst du hier auf <a href="../../../">weiter</a> klicken.</strong>');
}

$tpl->draw('single_box');
?>