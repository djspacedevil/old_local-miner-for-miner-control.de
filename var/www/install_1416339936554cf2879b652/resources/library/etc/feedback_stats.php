<?php
(include_once realpath(dirname(__FILE__)).'/../../main_config.php') or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0000');
(include_once LIBRARY_PATH.'/main/functions.php')					or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0001');
(include_once LIBRARY_PATH.'/main/functions_rpi.php')				or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0002');

$stats = array();

// File permission
list (, $errorFiles) = getAllFiles($config['paths']['main']);

$stats['version'] = $config['versions']['versioncode'];
$stats['url'] = urldecode($_GET['url']);
$stats['path'] = $config['paths']['main'];
$stats['php'] = PHP_VERSION;
$stats['webserver'] = $_SERVER['SERVER_SOFTWARE'];
$stats['network'] = (checkInternetConnection()) ? 'connected' : 'disconnected';
$stats['trouble-shooting_file_permission'] = (empty($errorFiles)) ? 'successful' : 'failed';
$stats['trouble-shooting_pi_cron'] = array();
$stats['whoaim'] = exec('whoami');
$stats['last_start'] = time() - rpi_getRuntime();
$stats['server_addr'] = $_SERVER['SERVER_ADDR'];
$stats['server_port'] = $_SERVER['SERVER_PORT'];
$stats['config'] = array();

echo urlencode(base64_encode(json_encode($stats)));
?>