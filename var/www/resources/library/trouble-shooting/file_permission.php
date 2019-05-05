<?php
(include_once realpath(dirname(__FILE__)).'/../../main_config.php') or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0000');
(include_once LIBRARY_PATH.'/main/rain.tpl.nocache.class.php')		or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0001');
(include_once LIBRARY_PATH.'/main/functions.php')					or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0002');
(include_once LIBRARY_PATH.'/main/ssh_connection.php')				or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0003');

raintpl::$tpl_dir = TEMPLATES_PATH.'/';

$tpl = new RainTPL;
$tpl->assign('html_path_prefix', '../../../');
$tpl->assign('box_color', '');

$direct_path = $config['paths']['main'];
$whoami = exec('whoami');

if (isset($_GET['command']) && $_GET['command'] == 1)
{
	if ($stream = ssh2_exec($ssh, 'sudo chown -R '.$whoami.':'.$whoami.' '.$direct_path.'/'))
	{
		$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
		stream_set_blocking($errorStream, true);
		$error = stream_get_contents($errorStream);
		
		if ($error == '')
			echo 'done';
		else
			echo $error;
	}
	else
		echo 'Konnte Befehl nicht ausführen!';
	
	if (isset($_GET['ref']) && $_GET['ref'] == 1)
	{
		if (!headers_sent($filename, $linenum))
			exit(header('Location: file_permission.php?command=2&ref=2'));
		else
		{
			$tpl->assign('title', 'Weiterleitung');
			$tpl->assign('msg', '<strong class="red">Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="file_permission.php?command=2&ref=2">diesen Link</a> an.</strong>');
			$tpl->draw('single_box');
		}
	}
}
elseif (isset($_GET['command']) && $_GET['command'] == 2)
{
	if ($stream = ssh2_exec($ssh, 'sudo find '.$direct_path.' -type d -exec chmod 755 {} +'))
	{
		$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
		stream_set_blocking($errorStream, true);
		$error = stream_get_contents($errorStream);
		
		if ($error == '')
			echo 'done';
		else
			echo $error;
	}
	else
		echo 'Konnte Befehl nicht ausführen!';
	
	if (isset($_GET['ref']) && $_GET['ref'] == 2)
	{
		if (!headers_sent($filename, $linenum))
			exit(header('Location: file_permission.php?command=3&ref=3'));
		else
		{
			$tpl->assign('title', 'Weiterleitung');
			$tpl->assign('msg', '<strong class="red">Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="file_permission.php?command=3&ref=3">diesen Link</a> an.</strong>');
			$tpl->draw('single_box');
		}
	}
}
elseif (isset($_GET['command']) && $_GET['command'] == 3)
{
	if ($stream = ssh2_exec($ssh, 'sudo find '.$direct_path.'/ -type f -exec chmod 644 {} +'))
	{
		$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
		stream_set_blocking($errorStream, true);
		$error = stream_get_contents($errorStream);
		
		if ($error == '')
			echo 'done';
		else
			echo $error;
	}
	else
		echo 'Konnte Befehl nicht ausführen!';
	
	if (isset($_GET['ref']) && $_GET['ref'] == 3)
	{
		if (!headers_sent($filename, $linenum))
			exit(header('Location: file_permission.php?command=4&ref=4'));
		else
		{
			$tpl->assign('title', 'Weiterleitung');
			$tpl->assign('msg', '<strong class="red">Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="file_permission.php?command=4&ref=4">diesen Link</a> an.</strong>');
			$tpl->draw('single_box');
		}
	}
}
elseif (isset($_GET['command']) && $_GET['command'] == 4)
{
	list (, $errorFiles) = getAllFiles($direct_path);
	
	if (empty($errorFiles))
		echo 'done';
	else
		echo '';
	
	if (isset($_GET['ref']) && $_GET['ref'] == 4)
	{
		if (!headers_sent($filename, $linenum))
			exit(header('Location: ../../../?s=settings&do=trouble-shooting&statusmsg=file_permission_completed'));
		else
		{
			$tpl->assign('title', 'Weiterleitung');
			$tpl->assign('msg', '<strong class="red">Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="../../../?s=settings&do=trouble-shooting&statusmsg=file_permission_completed">diesen Link</a> an.</strong>');
			$tpl->draw('single_box');
		}
	}
}
?>