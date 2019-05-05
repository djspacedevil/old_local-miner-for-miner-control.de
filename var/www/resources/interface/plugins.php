<?php
(include_once realpath(dirname(__FILE__)).'/../main_config.php') or die(print(json_encode(array('status' => 600))));
(include_once LIBRARY_PATH.'/main/functions.php') or die(print(json_encode(array('status' => 601))));
(include_once LIBRARY_PATH.'/main/functions_plugins.php') or die(print(json_encode(array('status' => 602))));

$plugins = array();

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
				$plugins[] = array('id' => $plugin_information['id'], 'name' => $plugin_information['name'], 'description' => $plugin_information['description'], 'version' => $plugin_information['version']);
			}
		}
	}
}
else
	$tpl->error('Fehler', $error_code['0x0037']);

print(json_encode(array('plugins' => $plugins, 'status' => 200)));
?>
