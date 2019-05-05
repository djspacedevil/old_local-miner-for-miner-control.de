<?php
$tpl = new RainTPL;

function searchForID($id, $array)
{
	foreach ($array as $key => $val)
		if ($val['id'] === $id)
			return $key;
	
	return NULL;
}

$plugins_file = json_decode(json_encode(simplexml_load_file($config['urls']['pluginUrl'], NULL , LIBXML_NOCDATA)), true);

if (isset($_GET['id']))
	$plugin_file_id = searchForID($_GET['id'], $plugins_file['plugin']);

if (isset($_GET['do'], $_GET['id']) && $_GET['do'] == 'information' && $_GET['id'] != '' && is_numeric($plugin_file_id))
{
	$if_plugin_load = 0;
	
	if (isset($_GET['status']) && $_GET['status'] == 'disable') // Plugin deaktivieren
	{
		switch (disablePlugin($_GET['id']))
		{
			case 0:
				if (!headers_sent($filename, $linenum))
					exit(header('Location: ?s=plugin_search&do=information&id='.$_GET['id'].'&statusmsg=disabled'));
				else
					$tpl->error('Weiterleitung', 'Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="?s=plugin_search&do=information&id='.$_GET['id'].'&statusmsg=disabled">diesen Link</a> an.');
					break;
			case 1:
				$tpl->msg('red', '', $error_code['0x0035']);
					break;
			case 2:
				$tpl->msg('green', '', 'Plugin ist bereits deaktiviert.');
					break;
		}
	}
	elseif (isset($_GET['status']) && $_GET['status'] == 'enable') // Plugin aktivieren
	{
		switch (enablePlugin($_GET['id']))
		{
			case 0:
				if (!headers_sent($filename, $linenum))
					exit(header('Location: ?s=plugin_search&do=information&id='.$_GET['id'].'&statusmsg=enabled'));
				else
					$tpl->error('Weiterleitung', 'Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="?s=plugin_search&do=information&id='.$_GET['id'].'&statusmsg=enabled">diesen Link</a> an.');
					break;
			case 1:
				$tpl->msg('red', '', $error_code['0x0036']);
					break;
			case 2:
				$tpl->msg('green', '', 'Plugin ist bereits aktiviert.');
					break;
		}
	}
	elseif (isset($_GET['delete']) && $_GET['delete'] == '') // Plugin löschen
	{
		$if_plugin_load = 1;
		$tpl->assign('plugin', $plugins_file['plugin'][$plugin_file_id]);
		$tpl->assign('server_request_uri', $_SERVER['REQUEST_URI']);
		$tpl->draw('settings/plugin_delete');
	}
	elseif (isset($_GET['delete']) && $_GET['delete'] == 'true') // Plugin löschen abschließen
		deletePlugin($plugins_file['plugin'][$plugin_file_id]['id'], '?s=plugin_search&do=information&id='.$_GET['id'].'&statusmsg=deleted');
	elseif (isset($_GET['install']) && $_GET['install'] == '') // Plugin installieren
	{
		if (!headers_sent($filename, $linenum))
			exit(header('Location: resources/plugins/download_plugin.php?id='.$_GET['id']));
		else
			$tpl->error('Weiterleitung', 'Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="resources/plugins/download_plugin.php?id='.$_GET['id'].'">diesen Link</a> an.');
	}
	elseif (isset($_GET['update']) && $_GET['update'] == '') // Plugin aktualisieren
	{
		if (!headers_sent($filename, $linenum))
			exit(header('Location: resources/plugins/update_plugin.php?id='.$_GET['id']));
		else
			$tpl->error('Weiterleitung', 'Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="resources/plugins/update_plugin.php?id='.$_GET['id'].'">diesen Link</a> an.');
	}
	elseif (isset($_GET['statusmsg']) && $_GET['statusmsg'] != '')
	{
		switch ($_GET['statusmsg'])
		{
			case 'enabled':
				$tpl->msg('green', '', 'Plugin wurde aktiviert.');
					break;
			case 'disabled':
				$tpl->msg('green', '', 'Plugin wurde deaktiviert.');
					break;
			case 'deleted':
				$tpl->msg('green', '', 'Das Plugin wurde gelöscht.');
					break;
			case 'installed':
				$tpl->msg('green', '', 'Das Plugin wurde installiert.');
					break;
			case 'updated':
				$tpl->msg('green', '', 'Das Plugin wurde aktualisiert.');
					break;
		}
				
	}
	
	if ($if_plugin_load == 0)
	{
		$tpl->assign('plugin', $plugins_file['plugin'][$plugin_file_id]);
		$tpl->assign('config_versioncode', $config['versions']['versioncode']);
		$tpl->assign('update_plugins', isset($update_plugins) ? $update_plugins : '');
		$tpl->draw('plugin_search_info');
	}
}
else
{
	if (isset($_GET['do'], $_GET['id']) && $_GET['do'] == 'information' && $_GET['id'] != '' && !is_numeric($plugin_file_id))
		$tpl->msg('red', '', 'Konnte das Plugin nicht finden.');
	
	$tpl->assign('plugins', array_sort($plugins_file['plugin'], 'name', SORT_ASC));
	$tpl->assign('plugins_total', count($plugins_file['plugin']));
	$tpl->assign('plugins_installed', getPluginCount());
	$tpl->assign('update_plugins', isset($update_plugins) ? $update_plugins : '');
	$tpl->draw('plugin_search');
}
?>