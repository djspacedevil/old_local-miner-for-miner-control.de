<?php
// update_picontrol.php
(include_once realpath(dirname(__FILE__)).'/../main_config.php');

(include_once LIBRARY_PATH.'/main/error_codes.php');
(include_once LIBRARY_PATH.'/main/rain.tpl.nocache.class.php');
(include_once LIBRARY_PATH.'/main/functions.php');

raintpl::$tpl_dir = TEMPLATES_PATH.'/';

$tpl = new RainTPL;
$tpl->assign('html_path_prefix', '../../');
$tpl->assign('box_color', 'info_red');

if (function_exists('fsockopen') && ini_get('allow_url_fopen') !== false)
{
	if (checkInternetConnection() === true)
	{
		$update = checkUpdate();
		if (is_array($update))
		{
			if (file_put_contents('update.zip', file_get_contents($config['urls']['updateDownloadUrl'].urlencode($update['filename']))))
			{
				if (md5_file('update.zip') == $update['checksum'])
				{					
					$zip = new ZipArchive;
					if (($zip_error = $zip->open('update.zip')) === true)
					{
						$zip->extractTo($config['paths']['main']);
						$zip->close();
						unlink('update.zip');
						
						if (file_exists('setup.php') && is_file('setup.php'))
						{
							if (!headers_sent($filename, $linenum))
								exit(header('Location: setup.php'));
							else
							{
								$tpl->assign('title', 'Weiterleitung');
								$tpl->assign('msg', '<strong class="red">Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="setup.php">diesen Link</a> an.</strong>');
								$tpl->draw('single_box');
							}
						}
						else
						{
							if (!headers_sent($filename, $linenum))
								exit(header('Location: ../../?s=settings&do=update&statusmsg=updated'));
							else
							{
								$tpl->assign('title', 'Weiterleitung');
								$tpl->assign('msg', '<strong class="red">Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="../../?s=settings&do=update&statusmsg=updated">diesen Link</a> an.</strong>');
								$tpl->draw('single_box');
							}
						}
					}
					else
					{
						$tpl->assign('title', 'Update fehlgeschlagen');
						$tpl->assign('msg', 'Leider ist ein Fehler beim entpacken des Archives aufgetreten! Fehlercode: '.$zip_error);
						$tpl->draw('single_box');
						exit();
					}
				}
				else
				{
					$tpl->assign('title', 'Update fehlgeschlagen');
					$tpl->assign('msg', 'Das Updatearchiv wurde nicht vollständig heruntergeladen. Bitte versuche es erneut. Sollte der Fehler weiterhin auftreten, schreibe mir unter <a href="http://willy-tech.de/kontakt/">Kontakt</a>, sodass ich dir möglichst schnell weiterhelfen kann.');
					$tpl->draw('single_box');
				}
			}
			else
			{
				$tpl->assign('title', 'Update fehlgeschlagen');
				$tpl->assign('msg', 'Konnte das Update auf dem Updateserver nicht finden! Bitte schreibe mir unter <a href="http://willy-tech.de/kontakt/">Kontakt</a>, sodass ich dir möglichst schnell weiter helfen kann.');
				$tpl->draw('single_box');
			}
		}
		else
		{
			$tpl->assign('title', 'Update fehlgeschlagen');
			$tpl->assign('msg', 'Konnte keine Verbindung zur Konfigurationsdatei auf dem Updateserver herstellen! Bitte schreibe mir unter <a href="http://willy-tech.de/kontakt/">Kontakt</a>, sodass ich dir möglichst schnell weiter helfen kann.');
			$tpl->draw('single_box');
		}
	}
	else
	{
		$tpl->assign('title', 'Update fehlgeschlagen');
		$tpl->assign('msg', 'Konnte keine Verbindung zum Internet herstellen! Bitte prüfe die Verbindung des Raspberry Pi und deines Routers.');
		$tpl->draw('single_box');
	}
}
else
{
	$tpl->assign('title', 'Update fehlgeschlagen');
	$tpl->assign('msg', 'Kann Download nicht ausführen, da erforderliche Funktion nicht verfügbar. Bitte aktiviere allow_url_fopen().');
	$tpl->draw('single_box');
}
?>