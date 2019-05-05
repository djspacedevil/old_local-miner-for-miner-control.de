<?php
$tpl = new RainTPL;

list (, $errorFiles) = getAllFiles($config['paths']['main']);

$protocol = 'http://';
if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) && (getConfigValue('config_webserver_port') == 443))
	$protocol = 'https://';

$cron_entry = '* * * * * root curl "'.$protocol.'127.0.0.1:'.getConfigValue('config_webserver_port').str_replace(realpath($_SERVER['DOCUMENT_ROOT']), '', CRON_PATH).'/init.php" -k > /dev/null 2>&1 # By Pi Control';

if (isset($_GET['file_permission']) && $_GET['file_permission'] == '' && !empty($errorFiles))
{
	$tpl->assign('js_variables', 'var whoami = \''.exec('whoami').'\'; var direct_path = \''.$config['paths']['main'].'\'; var trouble_shooting_path = \''.str_replace(dirname($_SERVER['SCRIPT_FILENAME']).'/', '', LIBRARY_PATH).'/trouble-shooting\'');
	$tpl->assign('file_permission_whoaim', exec('whoami'));
	$tpl->assign('file_permission_absolute_path', $config['paths']['main']);
	
	$tpl->draw('settings/trouble-shooting_file_permission');
}
elseif (isset($_GET['file_permission']) && $_GET['file_permission'] == 'confirm' && !empty($errorFiles))
{
	if (!headers_sent($filename, $linenum))
		exit(header('Location: '.str_replace(dirname($_SERVER['SCRIPT_FILENAME']).'/', '', LIBRARY_PATH).'/trouble-shooting/file_permission.php?command=1&ref=1'));
	else
		$tpl->error('Weiterleitung', 'Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="'.str_replace(dirname($_SERVER['SCRIPT_FILENAME']).'/', '', LIBRARY_PATH).'/trouble-shooting/file_permission.php?command=1&ref=1">diesen Link</a> an.');
}
elseif (isset($_GET['file_permission']) && empty($errorFiles))
{
	$tpl->msg('red', '', $error_code['2x0009'], false);
}
else
{
	if (isset($_GET['pi_cron']) && $_GET['pi_cron'] == '')
	{
		include_once LIBRARY_PATH.'/main/ssh_connection.php';
		switch (addCronToCrontab($cron_entry, $ssh))
		{
			case 0:
				$tpl->msg('green', '', 'Der Cron wurde erfolgreich angelegt. Sollte der Cron in ca. 5 Minuten immer noch nicht funktionieren, schreib mir unter "Feedback".');
					break;
			case 1:
				$tpl->msg('red', '', $error_code['1x0005']);
					break;
			case 2:
				$tpl->msg('green', '', 'Der Cron ist bereits angelegt.');
					break;
			case 3:
				$tpl->msg('red', '', $error_code['0x0032']);
					break;
			case 4:
				$tpl->msg('red', '', $error_code['0x0033']);
					break;
		}
	}
	
	if (isset($_GET['statusmsg']) && $_GET['statusmsg'] != '')
	{
		switch ($_GET['statusmsg'])
		{
			case 'file_permission_completed':
				$tpl->msg('green', '', 'Die Datei- und Ordnerberechtigungen wurden erfolgreich angepasst.');
					break;
		}
	}
	
	exec('cat /etc/crontab', $crontab_lines);
	$cron_htaccess_check = 'successful';
	
	if (file_exists('../.htaccess') && is_file('../.htaccess'))
	{		
		if (trim(shell_exec('curl "'.$protocol.'127.0.0.1:'.getConfigValue('config_webserver_port').'/'.str_replace(realpath($_SERVER['DOCUMENT_ROOT']), '', $config['paths']['main']).'/android/appcheck.php?id=echo"')) != 'successful')
			$cron_htaccess_check = 'failed';
	}
	
	$cron_match = preg_match('/^\* \* \* \* \* root curl "http[s]?:\/\/127\.0\.0\.1:'.getConfigValue('config_webserver_port').preg_quote(str_replace(realpath($_SERVER['DOCUMENT_ROOT']), '', CRON_PATH), '/').'\/init\.php"( \-k)?( > \/dev\/null 2>&1)? # By Pi Control/im', implode("\n", $crontab_lines));
	
	$tpl->assign('file_permission_whoaim', exec('whoami'));
	$tpl->assign('file_permission_absolute_path', $config['paths']['main']);
	$tpl->assign('file_permission_error_files', $errorFiles);
	$tpl->assign('file_permission_error_files_count', count($errorFiles) - 10);
	$tpl->assign('cron_config_last_cron_execution', getConfigValue('config_last_cron_execution'));
	$tpl->assign('cron_cron_entry', $cron_entry);
	$tpl->assign('cron_match', $cron_match);
	$tpl->assign('cron_paket_status', trim(exec('dpkg -s curl | grep Status: ')));
	$tpl->assign('cron_htaccess_check', $cron_htaccess_check);
}

if (!isset($_GET['file_permission']))
	$tpl->draw('settings/trouble-shooting');
?>