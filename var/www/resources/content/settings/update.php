<?php
$tpl = new RainTPL;

list (, $errorFiles) = getAllFiles($config['paths']['main']);

if (isset($_GET['update']) && $_GET['update'] == '')
{
	if (empty($errorFiles))
	{
		if (!headers_sent($filename, $linenum))
			exit(header('Location: resources/update/update_picontrol.php'));
		else
			$tpl->error('Weiterleitung', 'Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="resources/update/update_picontrol.php">diesen Link</a> an.');
	}
	else
		$tpl->msg('red', '', $error_code['0x0034']);
}

if (isset($_GET['statusmsg']) && $_GET['statusmsg'] == 'updated')
	$tpl->msg('green', '', 'Das Update wurde erfolgreich installiert und ist nun einsatzbereit.<br />Bei Problemen klicke einfach unten auf "Feedback" und schreibe mir. Viel Spaß.');

$tpl->assign('update_file_status_error', (!empty($errorFiles)) ? true : false);
$tpl->assign('update_status', checkUpdate());
$tpl->assign('config_version', $config['versions']['version']);
$tpl->assign('config_mail_url', $config['urls']['baseUrl']);

$tpl->draw('settings/update');
?>