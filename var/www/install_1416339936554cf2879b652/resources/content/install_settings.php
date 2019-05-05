<?php
$tpl = new RainTPL;

if (isset($_GET['send']) && $_GET['send'] == '')
{
	if (isset($_POST['port']) && $_POST['port'] != '')
	{
		if (is_numeric($_POST['port']) && $_POST['port'] >= 0 && $_POST['port'] <= 65535)
		{
			if (!file_exists(TEMP_PATH.'/config_settings.php'))
				fopen(TEMP_PATH.'/config_settings.php', 'w');
			
			if ($config_settings = fopen(TEMP_PATH.'/config_settings.php', 'w+'))
			{
				if (!fwrite($config_settings,
					trim($_POST['port'])))
				{
					$tpl->msg('red', '', 'Leider ist ein Fehler aufgetreten. Bitte versuche es nocheinmal. Fehlercode: 9x0001');
				}
				else
				{
					fclose($config_settings);
					if (!headers_sent($filename, $linenum))
						exit(header('Location: ?s=install_create'));
					else
						$tpl->error('Weiterleitung', 'Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="?s=install_create">diesen Link</a> an.');
				}
			}
			else
				$tpl->msg('red', '', 'Leider ist ein Fehler aufgetreten. Bitte versuche es nocheinmal. Fehlercode: 9x0002');
			
			fclose($config_settings);
			
		}
		else
			$tpl->msg('red', '', 'Ungültiger Port. Der Port muss zwischen 0 und 65535 liegen.');
	}
	else
		$tpl->msg('red', '', 'Bitte alle Felder ausfüllen.');
}

$tpl->assign('port', (isset($_POST['port'])) ? $_POST['port'] : $_SERVER['SERVER_PORT']);

$tpl->draw('install_settings');
?>