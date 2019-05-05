<?php
$tpl = new RainTPL;

if (isset($_POST['submit_main'])) // Einstellungen zum Pi Control
{
	setConfigValue('config_access_public', ((isset($_POST['value']) && $_POST['value'] == 'checked') ? 'true' : 'false'));
	setConfigValue('config_slim_header', ((isset($_POST['slim_header']) && $_POST['slim_header'] == 'checked') ? 'true' : 'false'));
	
	if (isset($_POST['port']) && $_POST['port'] != '')
	{
		if (is_numeric($_POST['port']) && $_POST['port'] >= 0 && $_POST['port'] <= 65535)
		{
			if (($set_config_webserver_port = setConfigValue('config_webserver_port', trim($_POST['port']))) === 0)
				$tpl->msg('green', '', 'Die Einstellungen wurden erfolgreich gespeichert.');
			else
				$tpl->msg('red', '', $error_code['0x0041'].$set_config_webserver_port);
		}
		else
			$tpl->msg('red', '', $error_code['2x0012']);
	}
	else
		$tpl->msg('red', '', $error_code['2x0005']);
	
	if (!headers_sent($filename, $linenum))
		exit(header('Location: ?s=settings&do=pic&statusmsg=saved'));
	else
		$tpl->error('Weiterleitung', 'Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="?s=settings&do=pic&statusmsg=saved">diesen Link</a> an.');
}
elseif (isset($_POST['submit_access_protection'])) // Zugriffsschutz
{
	if (isset($_POST['cb_activate']) && $_POST['cb_activate'] == 'checked')
	{
		if (isset($_POST['query'], $_POST['ht_name'], $_POST['ht_password'], $_POST['ht_password_2']) &&
			($_POST['query'] == 'all' || $_POST['query'] == 'only_extern') && trim($_POST['ht_name']) != '' &&
			$_POST['ht_password'] != '' && $_POST['ht_password_2'] != '')
		{
			if (($set_config_access_protection_option = setConfigValue('config_access_protection_option', '\''.$_POST['query'].'\'')) === 0)
			{
				if (preg_match('/^[A-Za-z0-9_]{2,30}$/', $_POST['ht_name']))
				{
					if (preg_match('/^[A-Za-z0-9_\-\+\*\/\#\.]{2,64}$/', $_POST['ht_password']))
					{
						if ($_POST['ht_password'] == $_POST['ht_password_2'])
						{
							if (($set_config_access_protection = setConfigValue('config_access_protection', 'true')) === 0)
							{
								if (file_exists($config['paths']['main'].'/.htaccess') && is_file($config['paths']['main'].'/.htaccess'))
									unlink($config['paths']['main'].'/.htaccess');
								
								if (file_exists($config['paths']['main'].'/.htpasswd') && is_file($config['paths']['main'].'/.htpasswd'))
									unlink($config['paths']['main'].'/.htpasswd');
								
								if (($htaccess_file = fopen($config['paths']['main'].'/.htaccess', 'w+')) && ($htpasswd_file = fopen($config['paths']['main'].'/.htpasswd', 'w+')))
								{
									if (!fwrite($htaccess_file,
											'AuthType Basic'."\n".
											'AuthName "Dieser Bereich ist nur autorisierten Personen zugaenglich"'."\n".
											'AuthUserFile '.$config['paths']['main'].'/.htpasswd'."\n".
											'require valid-user'."\n".
											''."\n".
											'Order deny,allow'."\n".
											'Deny from all'."\n".
											'Allow from 127.0.0.0/8'."\n".(($_POST['query'] == 'only_extern') ?
											'Allow from 10.0.0.0/8'."\n".
											'Allow from 172.16.0.0/12'."\n".
											'Allow from 192.168.0.0/16'."\n".
											'Allow from 255.255.255.255'."\n".
											'Allow from ::1'."\n" : '').
											'Satisfy any') ||
										!fwrite($htpasswd_file, $_POST['ht_name'].':{SHA}'.base64_encode(sha1($_POST['ht_password'], true))))
									{
										$tpl->msg('red', '', $error_code['0x0013']);
									}
									else
										$tpl->msg('green', '', 'Der Zugriffsschutz wurde erfolgreich erstellt und aktiviert.');
								}
								else
									$tpl->msg('red', '', $error_code['0x0014']);
								
								fclose($htaccess_file);
								fclose($htpasswd_file);
							}
							else
								$tpl->msg('red', '', $error_code['0x0016'].$set_config_access_protection);
						}
						else
							$tpl->msg('red', '', $error_code['2x0002']);
					}
					else
						$tpl->msg('red', '', $error_code['2x0003']);
				}
				else
					$tpl->msg('red', '', $error_code['2x0004']);
			}
			else
				$tpl->msg('red', '', $error_code['0x0017'].$set_config_access_protection_option);
		}
		else
			$tpl->msg('red', '', $error_code['2x0005']);
	}
	else
	{
		if (($set_config_access_protection = setConfigValue('config_access_protection', 'false')) === 0)
		{
			if (file_exists($config['paths']['main'].'/.htaccess') && is_file($config['paths']['main'].'/.htaccess'))
				unlink($config['paths']['main'].'/.htaccess');
			
			if (file_exists($config['paths']['main'].'/.htpasswd') && is_file($config['paths']['main'].'/.htpasswd'))
				unlink($config['paths']['main'].'/.htpasswd');
			
			$tpl->msg('green', '', 'Der Zugriffsschutz wurde deaktiviert.');
		}
		else
			$tpl->msg('red', '', $error_code['0x0018'].$set_config_access_protection);
	}
}
elseif (isset($_POST['submit_temperatur'])) // Temperaturüberwachung
{
	$cron = new Cron;
	$cron->setFile('coretemp_monitoring');
	
	if (isset($_POST['cb_activate']) && $_POST['cb_activate'] == 'checked')
	{
		if (isset($_POST['query']) && $_POST['query'] != '' && ((isset($_POST['cb_mail']) && $_POST['cb_mail'] == 'checked') || (isset($_POST['cb_command']) && $_POST['cb_command'] == 'checked') || (isset($_POST['cb_shutdown']) && $_POST['cb_shutdown'] == 'checked')))
		{
			$if_error = false;
			
			if (isset($_POST['cb_mail']) && $_POST['cb_mail'] == 'checked')
			{
				if (!filter_var(trim($_POST['ip_mail']), FILTER_VALIDATE_EMAIL) || !strlen(trim($_POST['ip_mail'])) >= 6)
				{
					$if_error = true;
					$tpl->msg('red', '', $error_code['2x0006']);
				}
			}
			
			if (isset($_POST['cb_command']) && $_POST['cb_command'] == 'checked' && $if_error === false)
			{
				if (!trim($_POST['ip_command']) != '')
				{
					$if_error = true;
					$tpl->msg('red', '', $error_code['2x0007']);
				}
			}
			
			if ($if_error !== true)
			{				
				if (getConfigValue('config_temp_mail_id') == '')
				{
					$random_1 = rand(1, 1000);
					$random_2 = rand(1, 1000);
					$random_3 = rand(1, 1000);
					
					$random_11 = 'random_'.rand(1, 3);
					$random_12 = 'random_'.rand(1, 3);
					$random_13 = 'random_'.rand(1, 3);
					
					$random = md5($$random_11-$$random_12+$$random_13);
					$microtime = md5(microtime(true));
					
					$temp_mail_id = strtoupper(substr(md5($random.$microtime.uniqid()), 0, 8));
					
					if (($set_config_temp_mail_id = setConfigValue('config_temp_mail_id', '\''.$temp_mail_id.'\'')) !== 0)
						$tpl->msg('red', '', $error_code['0x0019'].$set_config_temp_mail_id);
						
				}
				else
				{
					if ($_POST['cb_mail'] == 'checked')
					{
						if (function_exists('fsockopen'))
						{
							if (!$sock = @fsockopen('www.google.com', 80, $num, $error, 5))
							{
								// Raspberry Pi is not connected to internet
							}
							else
							{
								if ($value = file($config['urls']['tempMonitoringUrl'].'check&id='.getConfigValue('config_temp_mail_id').'&mail='.urlencode(trim($_POST['ip_mail'])).'&code='.getConfigValue('config_temp_mail_code')))
								{								
									if ($value[0] == '0')
									{
										// E-Mail und Code stimmen nicht überein
										$set_pic_temp_mail_code = setConfigValue('config_temp_mail_code', '\'\'');
									}
									elseif ($value[0] == '1')
									{
										// Code stimmt nicht überein
										$set_pic_temp_mail_code = setConfigValue('config_temp_mail_code', '\'\'');
									}
									elseif ($value[0] == '2')
									{
										// E-Mail stimmt nicht überein
										$set_pic_temp_mail_code = setConfigValue('config_temp_mail_code', '\'\'');
									}
									elseif ($value[0] == '3')
									{
										// E-Mail und Code stimmen überein
									}
									else
									{
										//$if_mail_check_error = true;
										//$info_message = array('red', $error_code['1x0034']);
									}
								}
								else
								{
									//$if_mail_check_error = true;
									//$info_message = array('red', $error_code['1x0023']);
								}
							}
						}
						else
						{
							//$if_mail_check_error = true;
							//$info_message = array('red', $error_code['1x0024']);
						}
					}
				}
				
				if (($set_config_temp = setConfigValue('config_temp', 'true')) !== 0)
					$tpl->msg('red', '', $error_code['0x0020'].$set_config_temp);
				
				if (($set_config_temp_celsius = setConfigValue('config_temp_celsius', $_POST['query'])) !== 0)
					$tpl->msg('red', '', $error_code['0x0021'].$set_config_temp_celsius);
				
				if (($set_config_temp_mail = setConfigValue('config_temp_mail', '\''.trim($_POST['ip_mail']).'\'')) !== 0)
					$tpl->msg('red', '', $error_code['0x0022'].$set_config_temp_mail);
				
				if (($set_config_temp_command = setConfigValue('config_temp_command', '\''.trim(base64_encode($_POST['ip_command'])).'\'')) !== 0)
					$tpl->msg('red', '', $error_code['0x0023'].$set_config_temp_command);
				
				if (($set_config_temp_shutdown = setConfigValue('config_temp_shutdown', (($_POST['cb_shutdown'] == 'checked') ? 'true' : 'false'))) !== 0)
					$tpl->msg('red', '', $error_code['0x0024'].$set_config_temp_shutdown);
				
				
				if ($set_config_temp === 0 && $set_config_temp_celsius === 0 && $set_config_temp_mail === 0 && $set_config_temp_command === 0 && $set_config_temp_shutdown === 0)
				{
					if ($cron->ifExist() === false)
					{
						$cron->setInterval(1);
						$cron->setSource(TEMP_PATH.'/coretemp_monitoring.tmp.php');
						if ($cron->save() === true)
							$tpl->msg('green', '', 'Die Temperaturüberwachung wurde aktiviert.');
						else
							$tpl->msg('red', '', $error_code['0x0025']);
					}
					else
						$tpl->msg('green', '', 'Die Temperaturüberwachung ist bereits aktiviert.');
				}
			}
		}
		else
			$tpl->msg('red', '', $error_code['2x0008']);
	}
	else
	{		
		if (($set_config_temp = setConfigValue('config_temp', 'false')) === 0)
		{
			if ($cron->ifExist() === true)
			{
				$cron->readFile();
				$cron->setInterval($cron->getInterval());
				if ($cron->delete() === true)
					$tpl->msg('green', '', 'Die Temperaturüberwachung wurde deaktiviert.');
				else
					$tpl->msg('red', '', $error_code['0x0026']);
			}
			else
				$tpl->msg('green', '', 'Die Temperaturüberwachung ist bereits deaktiviert.');
		}
		else
			$tpl->msg('red', '', $error_code['0x0027'].$set_config_temp);
	}
}

if (isset($_GET['mail_check']) && $_GET['mail_check'] == '')
{
	if (!checkInternetConnection())
		$tpl->msg('red', '', $error_code['1x0001']);
	else
	{
		if ($mail_code = implode('', file($config['urls']['tempMonitoringUrl'].'code&id='.getConfigValue('config_temp_mail_id').'&mail='.urlencode(getConfigValue('config_temp_mail')).'&get_result')))
		{
			if (trim($mail_code) != '' && (trim($mail_code) == 'unconfirmed' || strlen(trim($mail_code)) == 10))
			{
				if (trim($mail_code) != 'unconfirmed')
				{
					if (($set_config_temp_mail_code = setConfigValue('config_temp_mail_code', '\''.trim($mail_code).'\'')) === 0)
						$tpl->msg('green', '', 'Die E-Mailadresse wurde erfolgreich bestätigt.');
					else
						$tpl->msg('red', '', $error_code['0x0028'].$set_config_temp_mail_code);
				}
				else
					$tpl->msg('red', '', $error_code['1x0002']);
			}
			else
				$tpl->msg('red', '', $error_code['1x0003']);
		}
		else
			$tpl->msg('red', '', $error_code['1x0004']);
	}
}

if (isset($_GET['statusmsg']) && $_GET['statusmsg'] != '')
{
	switch ($_GET['statusmsg'])
	{
		case 'saved':
			$tpl->msg('green', '', 'Die Einstellungen wurden erfolgreich gespeichert.');
				break;
	}
}

$temp_option_timeout = getConfigValue('config_temp_option_timeout')-time();

$tpl->assign('config_access_public', getConfigValue('config_access_public'));
$tpl->assign('config_slim_header', getConfigValue('config_slim_header'));
$tpl->assign('config_webserver_port', getConfigValue('config_webserver_port'));
$tpl->assign('urlispublic', urlIsPublic($_SERVER['REMOTE_ADDR']));
$tpl->assign('config_access_protection', getConfigValue('config_access_protection'));
$tpl->assign('config_access_protection_option', getConfigValue('config_access_protection_option'));
$tpl->assign('config_temp', getConfigValue('config_temp'));
$tpl->assign('config_temp_celsius', getConfigValue('config_temp_celsius'));
$tpl->assign('config_temp_option_timeout', $temp_option_timeout);
$tpl->assign('temp_option_timeout_remain', floor($temp_option_timeout/60).':'.((($temp_option_timeout%60) <= 9) ? '0' : '').($temp_option_timeout%60));
$tpl->assign('config_temp_mail_code', getConfigValue('config_temp_mail_code'));
$tpl->assign('config_temp_mail', getConfigValue('config_temp_mail'));
$tpl->assign('config_temp_mail_id', getConfigValue('config_temp_mail_id'));
$tpl->assign('config_temp_command', getConfigValue('config_temp_command'));
$tpl->assign('config_temp_shutdown', getConfigValue('config_temp_shutdown'));
$tpl->assign('temp_monitoring_url', $config['urls']['tempMonitoringUrl']);
$tpl->assign('temp_mail_referer', $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

$tpl->draw('settings/pic');
?>