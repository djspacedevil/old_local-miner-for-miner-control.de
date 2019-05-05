<?php
$tpl = new RainTPL;

if (file_exists(PLUGINS_PATH) && is_dir(PLUGINS_PATH))
{
	$plugin_available = array();
	
	foreach (getPlugins() as $plugin)
	{
		$plugin_information = NULL;
		$plugin_information = getPluginInfo($plugin);
		
		if (is_array($plugin_information))
			$plugin_available['all'][] = array('name' => $plugin_information['name'], 'id' => $plugin_information['id']);
			if ($plugin_information['status'] == 'enable')
				$plugin_available['status'][] = array('name' => $plugin_information['name'], 'id' => $plugin_information['id']);
	}
	
	if (!isset($plugin_available['status']))
		$plugin_available_string = '<strong class="red">Keine Plugins!</strong>';
	
	if ((getConfigValue('config_last_plugin_update_check')+86400) < time() || (isset($_GET['s']) && $_GET['s'] == 'plugin_search'))
	{
		$update_plugins = checkPluginUpdate(isset($plugin_available['all']) ? $plugin_available['all'] : '');
		if (!is_array($update_plugins))
			setConfigValue('config_last_plugin_update_check', time());
		else
			setConfigValue('config_last_plugin_update_check', time()-86400);
	}
	
	if ((getConfigValue('config_last_update_check')+86400) < time() || (isset($_GET['s'], $_GET['do']) && $_GET['s'] == 'settings' && $_GET['do'] == 'update'))
	{
		$picontrol_update = checkUpdate();
		if (!is_array($picontrol_update))
			setConfigValue('config_last_update_check', time());
		else
			setConfigValue('config_last_update_check', time()-86400);
	}
}
else
	$plugin_available_string = '<strong class="red">Pluginordner nicht gefunden!</strong>';

$tpl->assign('is_mobile', 'false');
$tpl->assign('config_slim_header', getConfigValue('config_slim_header'));
$tpl->assign('javascript_time', time()+date('Z', time()));
$tpl->assign('javascript_req_url', urlencode($_SERVER['REQUEST_URI']));
$tpl->assign('navi_plugin_available', isset($plugin_available['status']) ? array_sort($plugin_available['status'], 'name', SORT_ASC) : $plugin_available_string);
$tpl->assign('navi_plugin_updates', isset($update_plugins) ? $update_plugins : '');
$tpl->assign('update_picontrol', isset($picontrol_update) ? $picontrol_update : '');
$tpl->assign('last_cron_execution', getConfigValue('config_last_cron_execution')+140);

$tpl->draw('html_header');
?>