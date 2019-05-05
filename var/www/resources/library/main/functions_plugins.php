<?php
// functions_plugins.php
function getPluginInfo($plugin_id, $special = '')
{
	// Für ältere Pi Conrol-Versionen
	$plugin_config_min_version = 1;
	
	if (!file_exists(PLUGINS_PATH.'/'.$plugin_id.'/plugin_config.php') || !is_file(PLUGINS_PATH.'/'.$plugin_id.'/plugin_config.php') || !include PLUGINS_PATH.'/'.$plugin_id.'/plugin_config.php')
		return false;
	
	if ($plugin_id != $plugin_config_id)
		return false;
	
	if (file_exists(PLUGINS_PATH.'/'.$plugin_id.'/plugin_disabled.php') && is_file(PLUGINS_PATH.'/'.$plugin_id.'/plugin_disabled.php'))
		$plugin_status = 'disable';
	else
		$plugin_status = 'enable';
		
	if (file_exists(PLUGINS_PATH.'/'.$plugin_id.'/settings/settings.php') && is_file(PLUGINS_PATH.'/'.$plugin_id.'/settings/settings.php'))
		$plugin_settings = 'available';
	else
		$plugin_settings = 'unavailable';
	
	if ($plugin_config_name == '' || $plugin_config_id == '' || $plugin_config_version == '' || $plugin_config_versioncode == '' || $plugin_config_min_version == '' || $plugin_config_author == '' || $plugin_config_description == '')
		return false;
	
	if ($special == '')
		return array('name' => $plugin_config_name, 'id' => $plugin_config_id, 'version' => $plugin_config_version, 'versioncode' => $plugin_config_versioncode, 'min_version' => $plugin_config_min_version, 'author' => $plugin_config_author, 'description' => $plugin_config_description, 'status' => $plugin_status, 'settings' => $plugin_settings);
	elseif ($special == 'status')
		return $plugin_status;
	elseif ($special == 'settings')
		return $plugin_settings;
	else
	{
		$special = 'plugin_config_'.$special;
		return $$special;
	}
}

function getPluginCount()
{
	$plugins_installed = 0;
	foreach (getPlugins() as $plugin)
	{
		$plugin_information = '';
		$plugin_information = getPluginInfo($plugin);
		
		if (is_array($plugin_information))
			$plugins_installed += 1;
	}
	
	return $plugins_installed;
}

function ifPluginDisable($plugin_id)
{
	if (is_file(PLUGINS_PATH.'/'.$plugin_id.'/plugin_disabled.php') && file_exists(PLUGINS_PATH.'/'.$plugin_id.'/plugin_disabled.php'))
		return true;
	else
		return false;
}

function getPlugins($check_status = false)
{
	list ($plugins_folder, ) = getDirectory(PLUGINS_PATH);
	
	if ($check_status === true)
	{
		for ($i = 0; $i < count($plugins_folder); $i++)
		{			
			if (ifPluginExists($plugins_folder[$i]) && ifPluginDisable($plugins_folder[$i]))
				unset($plugins_folder[$i]);
		}
	}
	
	return $plugins_folder;
}

function ifPluginExists($plugin_id)
{
	if (is_file(PLUGINS_PATH.'/'.$plugin_id.'/plugin_config.php') && file_exists(PLUGINS_PATH.'/'.$plugin_id.'/plugin_config.php') && include PLUGINS_PATH.'/'.$plugin_id.'/plugin_config.php')
	{
		if ($plugin_config_id == $plugin_id)
			return true;
		else
			return false;
	}
	else
		return false;
}

function checkPluginUpdate($plugins)
{
	global $config;
	
	if (is_array($plugins))
	{
		if (function_exists('fsockopen'))
		{
			if (!$sock = @fsockopen('www.google.com', 80, $num, $error, 5))
				return false; // Raspberry Pi is not connected to internet
			else
			{		
				$xml = simplexml_load_file($config['urls']['pluginUrl']);
				
				if ($xml)
				{
					$plugin_versions = array();
					foreach ($plugins as $plugin)
						$plugin_versions[$plugin['id']] = getPluginInfo($plugin['id'], 'versioncode');
					
					$output = array();
			
					foreach ($xml->plugin as $data)
						if (isset($plugin_versions[(string) $data->id]) && $data->versioncode > $plugin_versions[(string) $data->id])
							$output[(string) $data->id] = array('name' => (string) $data->name, 'version' => (string) $data->version);
					
					if (count($output) > 0)
						return $output;
					else
						return false;
				}
				else
					return false;
			}
		}
		else
			return false; // Function is not enabled
	}
	else
		return false;
}

function disablePlugin($plugin_id)
{
	if (ifPluginExists($plugin_id) && !ifPluginDisable($plugin_id))
	{
		if (copy(TEMP_PATH.'/plugin_disabled.tmp.php', PLUGINS_PATH.'/'.$plugin_id.'/plugin_disabled.php'))
		{
			return 0;
		}
		else
			return 1;
	}
	else
		return 2;
}

function enablePlugin($plugin_id)
{
	if (ifPluginExists($plugin_id) && ifPluginDisable($plugin_id))
	{
		if (unlink(PLUGINS_PATH.'/'.$plugin_id.'/plugin_disabled.php'))
		{
			return 0;
		}
		else
			return 1;
	}
	else
		return 2;
}

function deletePlugin($plugin_id, $referer)
{
	if (ifPluginExists($plugin_id))
	{
		if (file_exists(PLUGINS_PATH.'/'.$plugin_id.'/deinstall.php') && is_file(PLUGINS_PATH.'/'.$plugin_id.'/deinstall.php'))
		{
			if (!headers_sent($filename, $linenum))
				exit(header('Location: resources/plugins/'.$plugin_id.'/deinstall.php?referer='.urlencode($referer)));
			else
			{
				$tpl = new RainTPL;
				$tpl->error('Weiterleitung', 'Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="resources/plugins/'.$plugin_id.'/deinstall.php?referer='.$referer.'">diesen Link</a> an.');
			}
		}
		else
		{
			delete(PLUGINS_PATH.'/'.$plugin_id.'/');
			if (!headers_sent($filename, $linenum))
				exit(header('Location: '.$referer));
			else
			{
				$tpl = new RainTPL;
				$tpl->error('Weiterleitung', 'Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="'.$referer.'">diesen Link</a> an.');
			}
		}
	}
}
?>