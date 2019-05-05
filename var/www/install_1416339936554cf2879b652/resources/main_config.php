<?php
$config = array(
	'ssh' => array(
		'ssh_ip'				=> 'localhost'
	),
	'versions' => array(
		'version'				=> '1.3',
		'versioncode'			=> 13,
		'android_comp_level'	=> 4
	),
	'urls' => array(
		'baseUrl'				=> 'http://picontrol.willy-tech.de/web/1-0/',
		'updateUrl'				=> 'http://picontrol.willy-tech.de/web/1-0/updates.xml',
		'updateDownloadUrl'		=> 'http://picontrol.willy-tech.de/web/1-0/?s=update&file=',
		'pluginUrl'				=> 'http://picontrol.willy-tech.de/web/1-0/plugins.xml',
		'pluginDownloadUrl'		=> 'http://picontrol.willy-tech.de/web/1-0/plugins/',
		'tempMonitoringUrl'		=> 'http://picontrol.willy-tech.de/web/1-0/?s=temp_mail_',
		'helpUrl'				=> 'http://picontrol.willy-tech.de/web/1-0/?s=help'
	),
	'paths' => array(
		'main'					=> realpath(dirname(__FILE__).'/../'),
		'resources'				=> realpath(dirname(__FILE__)),
		'images'				=> realpath(dirname(__FILE__).'/../public_html/img/'),
		'install'				=> realpath(dirname(__FILE__).'/../install/')
	)
);

defined('LIBRARY_PATH')		or define('LIBRARY_PATH',	realpath($config['paths']['resources'].'/library/'));
defined('CONTENT_PATH')		or define('CONTENT_PATH',	realpath($config['paths']['resources'].'/content/'));
defined('CONFIG_PATH')		or define('CONFIG_PATH',	realpath($config['paths']['main'].'/../resources/config/'));
defined('TEMP_PATH')		or define('TEMP_PATH',		realpath($config['paths']['resources'].'/temp/'));
defined('CRON_PATH')		or define('CRON_PATH',		realpath($config['paths']['main'].'/../resources/cron/'));

if (isset($_GET['debug']))
{
	if ($_GET['debug'] == 'hide')
	{
		setcookie('debug', NULL, -1);
		unset($_COOKIE['debug']);
	}
	else
		setcookie('debug', 'debug_mode', time()+3600);
}

$errorHandler = array();
function myErrorHandler($code, $msg, $file, $line)
{
	global $errorHandler;
	$errorHandler[] = 'Fehler ['.$code.']: '.$msg.' in der Datei '.$file.', Zeile '.$line;
	
	if (isset($_COOKIE['debug']) && $_COOKIE['debug'] == 'debug_mode')
		return false;
	else
		return true;
}

error_reporting(E_ALL ^ E_STRICT);
ini_set('display_errors', 1);
set_error_handler('myErrorHandler');

header('Content-Type: text/html; charset=utf-8');

if (isset($_COOKIE['debug'], $_GET['s']) && $_COOKIE['debug'] == 'debug_mode')
	echo '<!DOCTYPE HTML><div style="background: #FF0000; color: #FFFFFF; padding: 2px;">DEBUG: PHP-Fehlermeldungen werden angezeigt. <a href="'.$_SERVER['REQUEST_URI'].'&debug=hide" style="color: #FFFF00;">Deaktivieren.</a></div>';
?>