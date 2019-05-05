<?php
$tpl = new RainTPL;

if (isset($_GET['action']) && !empty($_GET['action']))
{
	include_once LIBRARY_PATH.'/main/ssh_connection.php';
	switch ($_GET['action'])
	{
		case 'system_shutdown':
			ssh2_exec($ssh, 'sudo shutdown -h now');
			if (!headers_sent($filename, $linenum))
				exit(header('Location: resources/content/overview_action.php?shutdown'));
			else
				$tpl->error('Weiterleitung', 'Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="resources/content/overview_action.php?shutdown">diesen Link</a> an.');
				break;
		case 'system_restart':
			ssh2_exec($ssh, 'sudo shutdown -r now');
			if (!headers_sent($filename, $linenum))
				exit(header('Location: resources/content/overview_action.php?restart'));
			else
				$tpl->error('Weiterleitung', 'Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="resources/content/overview_action.php?restart">diesen Link</a> an.');
				break;
	}
}

$ram = rpi_getMemoryUsage();
$memory = rpi_getMemoryInfo();

$tpl->assign('js_variables', 'var reload_timeout = '.(getConfigValue('config_overview_reload_time')*1000).'; var overview_path = \''.str_replace(dirname($_SERVER['SCRIPT_FILENAME']).'/', '', LIBRARY_PATH).'/overview\'');
$tpl->assign('show_weather', getConfigValue('config_overview_weather'));
$tpl->assign('weather', (getConfigValue('config_overview_weather') === true) ? getWeather(getConfigValue('config_weather_postcode')) : '');
$tpl->assign('run_time', getDateFormat(rpi_getRuntime()));
$tpl->assign('start_time', date('d.m.Y H:i', time() - rpi_getRuntime()));
$tpl->assign('cpu_clock', rpi_getCpuClock().' MHz');
$tpl->assign('cpu_load', rpi_getCPULoad().'%');
$tpl->assign('cpu_type', rpi_getCPUType());
$tpl->assign('cpu_temp', number_format(rpi_getCoreTemprature(), 2, ',', '').' &deg;C');
$tpl->assign('ram_percentage', $ram['percent'].'%');
$tpl->assign('memory', end($memory));
$tpl->assign('usb_devices', (getConfigValue('config_overview_connected_devices') === true) ? rpi_getUsbDevices() : '');

$tpl->draw('overview');
?>