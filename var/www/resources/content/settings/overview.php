<?php
$tpl = new RainTPL;

if (isset($_POST['value'], $_POST['submit']))
{
	if (is_numeric($_POST['value']) && ($_POST['value']) != '')
	{
		if (($set_config_overview_reload_time = setConfigValue('config_overview_reload_time', $_POST['value'])) === 0)
			$tpl->msg('green', '', 'Die Einstellungen wurden gespeichert.');
		else
			$tpl->msg('red', '', $error_code['0x0011'].$set_config_overview_reload_time);
		
		if (($set_config_overview_connected_devices = setConfigValue('config_overview_connected_devices', ((isset($_POST['connected_devices']) && $_POST['connected_devices'] == 'checked') ? 'true' : 'false'))) !== 0)
			$tpl->msg('red', '', $error_code['0x0012'].$set_config_overview_connected_devices);
		
		if (($set_config_overview_weather = setConfigValue('config_overview_weather', ((isset($_POST['weather']) && $_POST['weather'] == 'checked') ? 'true' : 'false'))) !== 0)
			$tpl->msg('red', '', $error_code['0x0040'].$set_config_overview_weather);
		
		if (isset($_POST['weather_country']) && ($_POST['weather_country'] == 'germany' || $_POST['weather_country'] == 'austria' || $_POST['weather_country'] == 'swiss'))
		{
			if (($set_config_weather_country = setConfigValue('config_weather_country', '\''.$_POST['weather_country'].'\'')) !== 0)
				$tpl->msg('red', '', $error_code['0x0042'].$set_config_weather_country);
		}
		
		if ((strlen($_POST['weather_postcode']) == 4 || strlen($_POST['weather_postcode']) == 5) && $_POST['weather_postcode'] >= 1 && $_POST['weather_postcode'] <= 99999)
		{
			if (($set_config_weather_postcode = setConfigValue('config_weather_postcode', '\''.$_POST['weather_postcode'].'\'')) !== 0)
				$tpl->msg('red', '', $error_code['0x0038'].$set_config_weather_postcode);
		}
		else
			$tpl->msg('red', '', $error_code['2x0010']);
	}
	else
		$tpl->msg('red', '', $error_code['2x0001']);
}

$tpl->assign('overview_reload_time', getConfigValue('config_overview_reload_time'));
$tpl->assign('overview_connected_devices', getConfigValue('config_overview_connected_devices'));
$tpl->assign('overview_weather', getConfigValue('config_overview_weather'));
$tpl->assign('weather_postcode', getConfigValue('config_weather_postcode'));
$tpl->assign('weather_country', getConfigValue('config_weather_country'));
$tpl->assign('weather_city_status', getWeather(getConfigValue('config_weather_postcode')));

$tpl->draw('settings/overview');
?>