<?php
(include_once realpath(dirname(__FILE__)).'/../../main_config.php') or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0000');
(include_once LIBRARY_PATH.'/main/functions.php')					or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0001');
(include_once LIBRARY_PATH.'/main/functions_rpi.php')				or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0002');

$stats = array();

// File permission
list (, $errorFiles) = getAllFiles($config['paths']['main']);

// Pi cron
exec('cat /etc/crontab', $crontab_lines);
$cron_htaccess_check = 'successful';

if (file_exists('../../../.htaccess') && is_file('../../../.htaccess'))
{		
	if (trim(shell_exec('curl "'.$protocol.'127.0.0.1:'.getConfigValue('config_webserver_port').'/'.str_replace(realpath($_SERVER['DOCUMENT_ROOT']), '', $config['paths']['main']).'/android/appcheck.php?id=echo"')) != 'successful')
		$cron_htaccess_check = 'failed';
}

$cron_match = preg_match('/^\* \* \* \* \* root curl "http[s]?:\/\/127\.0\.0\.1:'.getConfigValue('config_webserver_port').preg_quote(str_replace(realpath($_SERVER['DOCUMENT_ROOT']), '', CRON_PATH), '/').'\/init\.php"( \-k)?( > \/dev\/null 2>&1)? # By Pi Control/im', implode("\n", $crontab_lines));

$stats['version'] = $config['versions']['versioncode'];
$stats['url'] = urldecode($_GET['url']);
$stats['path'] = $config['paths']['main'];
$stats['php'] = PHP_VERSION;
$stats['webserver'] = $_SERVER['SERVER_SOFTWARE'];
$stats['network'] = (checkInternetConnection()) ? 'connected' : 'disconnected';
$stats['trouble-shooting_file_permission'] = (empty($errorFiles)) ? 'successful' : 'failed';
$stats['trouble-shooting_pi_cron'] = array('match' => $cron_match, 'paket_status' => trim(exec('dpkg -s curl | grep Status: ')), 'htaccess_check' => $cron_htaccess_check);
$stats['whoaim'] = exec('whoami');
$stats['last_start'] = time() - rpi_getRuntime();
$stats['server_addr'] = $_SERVER['SERVER_ADDR'];
$stats['server_port'] = $_SERVER['SERVER_PORT'];
$stats['config'] = array('access_public' => getConfigValue('config_access_public'),
						 'access_protection' => getConfigValue('config_access_protection'),
						 'access_protection_option' => getConfigValue('config_access_protection_option'),
						 'temp' => getConfigValue('config_temp'),
						 'temp_option_timeout' => getConfigValue('config_temp_option_timeout'),
						 'last_cron_execution' => getConfigValue('config_last_cron_execution'),
						 'webserver_port' => getConfigValue('config_webserver_port')
					);

echo urlencode(base64_encode(json_encode($stats)));
?>