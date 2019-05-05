<?php
$tpl = new RainTPL;

$all_plugins = array();

if (file_exists(PLUGINS_PATH) && is_dir(PLUGINS_PATH))
{
	$if_plugin_load = 0;
	
	foreach (getPlugins() as $plugin)
	{
		$plugin_information = '';
		$plugin_information = getPluginInfo($plugin);
		
		if (is_array($plugin_information))
		{
			$all_plugins[$plugin] = array('id' => $plugin_information['id'], 'name' => $plugin_information['name'], 'description' => $plugin_information['description'], 'version' => $plugin_information['version'], 'min_version' => $plugin_information['min_version'], 'status' => $plugin_information['status'], 'settings' => $plugin_information['settings']);
			
			if (isset($_GET['id']) && $_GET['id'] == $plugin_information['id'])
			{
				if (isset($_GET['status']) && $_GET['status'] == 'disable') // Plugin deaktivieren
				{
					switch (disablePlugin($plugin))
					{
						case 0:
							if (!headers_sent($filename, $linenum))
								exit(header('Location: ?s=settings&do=plugins&id='.$plugin.'&statusmsg=disabled'));
							else
								$tpl->error('Weiterleitung', 'Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="?s=settings&do=plugins&id='.$plugin.'&statusmsg=disabled">diesen Link</a> an.');
								break;
						case 1:
							$tpl->msg('red', '', $error_codes['0x0029']);
								break;
						case 2:
							$tpl->msg('green', '', 'Plugin ist bereits deaktiviert.');
								break;
					}
				}
				elseif (isset($_GET['status']) && $_GET['status'] == 'enable') // Plugin aktivieren
				{
					switch (enablePlugin($plugin))
					{
						case 0:
							if (!headers_sent($filename, $linenum))
								exit(header('Location: ?s=settings&do=plugins&id='.$plugin.'&statusmsg=enabled'));
							else
								$tpl->error('Weiterleitung', 'Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="?s=settings&do=plugins&id='.$plugin.'&statusmsg=enabled">diesen Link</a> an.');
								break;
						case 1:
							$tpl->msg('red', '', $error_codes['0x0030']);
								break;
						case 2:
							$tpl->msg('green', '', 'Plugin ist bereits aktiviert.');
								break;
					}
				}
				elseif (isset($_GET['delete']) && $_GET['delete'] == '') // Plugin löschen
				{
					$if_plugin_load = 1;
					$tpl->assign('plugin', $all_plugins[$plugin]);
					$tpl->assign('server_request_uri', $_SERVER['REQUEST_URI']);
					$tpl->draw('settings/plugin_delete');
				}
				elseif (isset($_GET['delete']) && $_GET['delete'] == 'true') // Plugin löschen abschließen
				{
					deletePlugin($plugin, '?s=settings&do=plugins&id='.$plugin.'&statusmsg=deleted');
				}
				elseif (isset($_GET['statusmsg']) && $_GET['statusmsg'] != '')
				{
					if ($_GET['statusmsg'] == 'enabled')
						$tpl->msg('green', '', 'Plugin wurde aktiviert.');
					elseif ($_GET['statusmsg'] == 'disabled')
						$tpl->msg('green', '', 'Plugin wurde deaktiviert.');
					elseif ($_GET['status'] == 'deleted')
						$tpl->msg('green', '', 'Das Plugin wurde gelöscht.');
				}
			}
		}
	}
}
else
	$tpl->error('Fehler', $error_codes['0x0031']);

if ($if_plugin_load == 0)
{
	$tpl->assign('all_plugins', $all_plugins);
	$tpl->assign('config_versioncode', $config['versions']['versioncode']);
	$tpl->draw('settings/plugins');
}
?>