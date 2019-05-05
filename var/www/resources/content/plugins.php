<?php
$tpl = new RainTPL;

$all_plugins = array();
$if_plugin_load = 0;

if (file_exists(PLUGINS_PATH) && is_dir(PLUGINS_PATH))
{
	foreach (getPlugins() as $plugin)
	{
		$plugin_information = '';
		$plugin_information = getPluginInfo($plugin);
		
		if (is_array($plugin_information))
		{
			if ($plugin_information['status'] == 'enable')
			{
				$all_plugins[] = array('id' => $plugin_information['id'], 'name' => $plugin_information['name'], 'description' => $plugin_information['description'], 'version' => $plugin_information['version']);
				
				if (isset($_GET['id']) && $_GET['id'] == $plugin_information['id'])
				{
					if ($plugin_information['min_version'] <= $config['versions']['versioncode'])
					{
						if (isset($_GET['settings']) && $_GET['settings'] == '')
						{
							if ($plugin_information['settings'] == 'available')
							{
								$if_plugin_load = 1;
								$plugin_folder_absolute = PLUGINS_PATH.'/'.$plugin;
								$plugin_folder_html = str_replace(dirname($_SERVER['SCRIPT_FILENAME']).'/', '', PLUGINS_PATH).'/'.$plugin;
								include_once PLUGINS_PATH.'/'.$plugin.'/settings/settings.php';
							}
							else
								$tpl->msg('red', '', 'Das Plugin "'.$plugin_information['name'].'" verfügt über keine Einstellungen! Fehlercode: 1x0006');
						}
						else
						{
							$if_plugin_load = 1;
							$plugin_folder_absolute = PLUGINS_PATH.'/'.$plugin;
							$plugin_folder_html = str_replace(dirname($_SERVER['SCRIPT_FILENAME']).'/', '', PLUGINS_PATH).'/'.$plugin;
							include_once PLUGINS_PATH.'/'.$plugin.'/index.php';
						}
					}
					else
						$tpl->msg('red', '', 'Das Plugin "'.$plugin_information['name'].'" ist nicht mit deinem Pi Control kompatibel. Bitte <a href="?s=settings&do=update">aktualisiere</a> dein Pi Control um das Plugin weiterhin verwenden zu können! Fehlercode: 1x0007');
				}
			}
			elseif (isset($_GET['id']) && $_GET['id'] == $plugin_information['id'])
				$tpl->msg('red', '', 'Das Plugin "'.$plugin_information['name'].'" ist deaktiviert. Bitte aktiviere es, um das Plugin weiterhin verwenden zu können! Fehlercode: 1x0008');
		}
	}
}
else
	$tpl->error('Fehler', $error_code['0x0037']);

if ($if_plugin_load == 0)
{
	$tpl->assign('all_plugins', array_sort($all_plugins, 'name', SORT_ASC));
	$tpl->draw('plugins');
}
?>