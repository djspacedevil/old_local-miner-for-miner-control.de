<?php
(include_once realpath(dirname(__FILE__)).'/../main_config.php') or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0000');
(include_once LIBRARY_PATH.'/main/functions.php')					or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0001');
(include_once LIBRARY_PATH.'/main/functions_rpi.php')				or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0002');

$core = rpi_getCoreTemprature();

if ($core > getConfigValue('config_temp_celsius') && getConfigValue('config_temp') !== false)
{
	include_once LIBRARY_PATH.'/main/ssh_connection.php';
	
	$if_option = false;
	
	if (getConfigValue('config_temp_option_timeout') <= time())
	{
		if (getConfigValue('config_temp_mail') != '' && getConfigValue('config_temp_mail_id') != '' && getConfigValue('config_temp_mail_code') != '')
		{
			if (function_exists('fsockopen'))
			{
				if (!$sock = @fsockopen('www.google.com', 80, $num, $error, 5))
				{
					// Raspberry Pi is not connected to internet
				}
				else
				{
					if (!$sock = file($config['urls']['tempMonitoringUrl'].'send&id='.getConfigValue('config_temp_mail_id').'&mail='.urlencode(getConfigValue('config_temp_mail')).'&code='.getConfigValue('config_temp_mail_code').'&limit='.getConfigValue('config_temp_celsius').'&temp='.$core))
					{
						// Raspberry Pi is not connected to internet
					}
				}
			}
			$if_option = true;
		}
		
		if (getConfigValue('config_temp_command') != '')
		{
			$stream = ssh2_exec($ssh, base64_decode(getConfigValue('config_temp_command')));
			stream_set_blocking($stream, true);
			$streamOut = stream_get_contents($stream);
			$if_option = true;
		}
	}
	
	if (getConfigValue('config_temp_shutdown') === true)
	{
		sleep(10);
		ssh2_exec($ssh, 'sudo shutdown -h now');
	}
	elseif ($if_option !== false)
	{
		setConfigValue('config_temp_option_timeout', time()+3600);
	}
}
?>