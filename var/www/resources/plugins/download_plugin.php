<?php
// download_plugin.php
(include_once realpath(dirname(__FILE__)).'/../main_config.php');

(include_once LIBRARY_PATH.'/main/error_codes.php');
(include_once LIBRARY_PATH.'/main/rain.tpl.nocache.class.php');
(include_once LIBRARY_PATH.'/main/functions.php');

raintpl::$tpl_dir = TEMPLATES_PATH.'/';

$tpl = new RainTPL;
$tpl->assign('html_path_prefix', '../../');
$tpl->assign('box_color', 'info_red');

function searchForID($id, $array)
{
	foreach ($array as $key => $val)
		if ($val['id'] === $id)
			return $key;
	
	return NULL;
}

if (function_exists('fsockopen') && ini_get('allow_url_fopen') !== false)
{
	if (checkInternetConnection() === true)
	{
		if ($plugins_file = json_decode(json_encode(simplexml_load_file($config['urls']['pluginUrl'], NULL , LIBXML_NOCDATA)), true))
		{
			$plugin_file_id = searchForID($_GET['id'], $plugins_file['plugin']);
			
			if (is_numeric($plugin_file_id))
			{
				if (file_put_contents('plugin.zip', file_get_contents($config['urls']['pluginDownloadUrl'].$plugins_file['plugin'][$plugin_file_id]['filename'])))
				{
					if (md5_file('plugin.zip') == $plugins_file['plugin'][$plugin_file_id]['checksum'])
					{
						$plugin_folder = PLUGINS_PATH.'/'.$plugins_file['plugin'][$plugin_file_id]['id'].'/';
						
						if (file_exists($plugin_folder) && is_dir($plugin_folder))
						{
							$tpl->assign('title', 'Download fehlgeschlagen');
							$tpl->assign('msg', 'Der Pluginordner für das Plugin existiert bereits.');
							$tpl->draw('single_box');
							exit();
						}
						else
							mkdir($plugin_folder);
						
						$zip = new ZipArchive;
						if (($zip_error = $zip->open('plugin.zip')) === true)
						{
							$zip->extractTo($plugin_folder);
							$zip->close();
							unlink('plugin.zip');
							
							if (file_exists($plugin_folder.'setup.php') && is_file($plugin_folder.'setup.php'))
							{
								header('Location: '.$plugins_file['plugin'][$plugin_file_id]['id'].'/setup.php');
								exit();
							}
							else
							{
								header('Location: ../../?s=plugin_search&do=information&id='.$plugins_file['plugin'][$plugin_file_id]['id'].'&statusmsg=installed');
								exit();
							}
						}
						else
						{
							$tpl->assign('title', 'Download fehlgeschlagen');
							$tpl->assign('msg', 'Leider ist ein Fehler beim entpacken des Archives aufgetreten! Fehlercode: '.$zip_error);
							$tpl->draw('single_box');
							rmdir($plugin_folder);
							exit();
						}
					}
					else
					{
						$tpl->assign('title', 'Download fehlgeschlagen');
						$tpl->assign('msg', 'Das Pluginarchiv wurde nicht vollständig heruntergeladen. Bitte versuche es erneut. Sollte der Fehler weiterhin auftreten, schreibe mir unter <a href="http://willy-tech.de/kontakt/">Kontakt</a>, sodass ich dir möglichst schnell weiterhelfen kann.');
						$tpl->draw('single_box');
					}
				}
				else
				{
					$tpl->assign('title', 'Download fehlgeschlagen');
					$tpl->assign('msg', 'Konnte das Plugin auf dem Pluginserver nicht finden! Bitte schreibe mir unter <a href="http://willy-tech.de/kontakt/">Kontakt</a>, sodass ich dir möglichst schnell weiter helfen kann.');
					$tpl->draw('single_box');
				}
			}
			else
			{
				$tpl->assign('title', 'Download fehlgeschlagen');
				$tpl->assign('msg', 'Konnte das Plugin in der Konfigurationsdatei auf dem Pluginserver nicht finden! Bitte schreibe mir unter <a href="http://willy-tech.de/kontakt/">Kontakt</a>, sodass ich dir möglichst schnell weiter helfen kann.');
				$tpl->draw('single_box');
			}
		}
		else
		{
			$tpl->assign('title', 'Download fehlgeschlagen');
			$tpl->assign('msg', 'Konnte keine Verbindung zur Konfigurationsdatei auf dem Pluginserver herstellen! Bitte schreibe mir unter <a href="http://willy-tech.de/kontakt/">Kontakt</a>, sodass ich dir möglichst schnell weiter helfen kann.');
			$tpl->draw('single_box');
		}
	}
	else
	{
		$tpl->assign('title', 'Download fehlgeschlagen');
		$tpl->assign('msg', 'Konnte keine Verbindung zum Internet herstellen! Bitte prüfe die Verbindung des Raspberry Pi und deines Routers.');
		$tpl->draw('single_box');
	}
}
else
{
	$tpl->assign('title', 'Download fehlgeschlagen');
	$tpl->assign('msg', 'Kann Download nicht ausführen, da erforderliche Funktion nicht verfügbar. Bitte aktiviere allow_url_fopen().');
	$tpl->draw('single_box');
}
?>