<?php
(include_once realpath(dirname(__FILE__)).'/../main_config.php') or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0000');
(include_once LIBRARY_PATH.'/main/functions.php') or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0001');

$folder = CRON_PATH;
$fileArray = array();

foreach (@scandir($folder) as $file)
{
	if ($file[0] != '.')
	{
		if (is_file($folder.'/'.$file) && $file != 'init.php')
			$fileArray[] = $file;
	}
}

foreach ($fileArray as $file_)
{
	$get_time_of_file = str_replace('-', '', substr($file_, 0, 2));
	$rest = date('i', time()) % $get_time_of_file;
	
	if (is_numeric($rest) && $rest == 0)
	{
		$protocol = 'http://';
		if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) && (getConfigValue('config_webserver_port') == 443))
			$protocol = 'https://';
		
		exec('curl "'.$protocol.'127.0.0.1:'.getConfigValue('config_webserver_port').str_replace(realpath($_SERVER['DOCUMENT_ROOT']), '', CRON_PATH).'/'.$file_.'" -k');
		set_time_limit(30);
	}
}

if (trim(exec('dpkg -s curl | grep Status: ')) != '')
	setConfigValue('config_last_cron_execution', time());
?>