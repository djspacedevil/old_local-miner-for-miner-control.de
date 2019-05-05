<?php
$tpl = new RainTPL;

$folder = LOG_PATH;
$fileArray = array();
$logArray = array();
$statistics = array();
$hiddenStatistics = array_filter(explode('~', getConfigValue('config_statistic_hide')));

foreach (@scandir($folder) as $file)
{
	if ($file[0] != '.')
	{
		if (is_file($folder.'/'.$file) && substr($file, -8) == '.log.txt')
			$fileArray[] = $file;
	}
}

foreach ($fileArray as $file_)
{
	if (substr($file_ , 0, -8) == 'coretemp')
		$statistics[] = 'coretemp';
	elseif (substr($file_ , 0, -8) == 'cpuload')
		$statistics[] = 'cpuload';
	elseif (substr($file_ , 0, -8) == 'ram')
		$statistics[] = 'ram';
	elseif (substr($file_ , 0, 8) == 'network_')
		$statistics[] = substr($file_, 0, -8);
}
	
if (!isset($_GET['reset']))
{	
	if (isset($_POST['submit']))
	{
		$hiddenStatistics = array_diff($statistics, (isset($_POST['check'])) ? $_POST['check'] : array());
		
		if (($set_config_statistic_hide = setConfigValue('config_statistic_hide', '\''.implode('~', $hiddenStatistics).'\'')) === 0)
			$tpl->msg('green', '', 'Die Einstellungen wurden erfolgreich gespeichert.');
		else
			$tpl->msg('red', '', $error_code['0x0043'].$set_config_statistic_hide);
	}
	
	foreach ($fileArray as $file_)
	{
		if (substr($file_ , 0, -8) == 'coretemp')
		{
			$logArray[] = array('log' => 'coretemp',
								'label' => 'CPU-Temperatur',
								'display' => (array_search('coretemp', $hiddenStatistics) !== false) ? 0 : 1);
			
			$statistics[] = 'coretemp';
		}
		elseif (substr($file_ , 0, -8) == 'cpuload')
		{
			$logArray[] = array('log' => 'cpuload',
								'label' => 'CPU-Auslastung',
								'display' => (array_search('cpuload', $hiddenStatistics) !== false) ? 0 : 1);
			
			$statistics[] = 'coretemp';
		}
		elseif (substr($file_ , 0, -8) == 'ram')
		{
			$logArray[] = array('log' => 'ram',
								'label' => 'RAM-Auslastung',
								'display' => (array_search('ram', $hiddenStatistics) !== false) ? 0 : 1);
			
			$statistics[] = 'coretemp';
		}
		elseif (substr($file_ , 0, 8) == 'network_')
		{
			$logArray[] = array('log' => substr($file_, 0, -8),
								'label' => substr($file_ , 8, -8),
								'display' => (array_search(substr($file_, 0, -8), $hiddenStatistics) !== false) ? 0 : 1);
			
			$statistics[] = substr($file_, 0, -8);
		}
	}
	
	$tpl->assign('logArray', $logArray);
	
	$tpl->draw('settings/statistic');
}
else
{
	if (array_search(urldecode($_GET['reset']), $statistics) === false)
		$tpl->msg('red', '', $error_code['2x0013'].urldecode($_GET['reset']));
	
	if (isset($_GET['confirm']) && $_GET['confirm'] == '')
	{
		if (array_search(urldecode($_GET['reset']), $statistics) !== false)
		{			
			if (($logFile = fopen(LOG_PATH.'/'.urldecode($_GET['reset']).'.log.txt', 'w')) !== false)
				$tpl->msg('green', '', 'Verlauf wurde erfolgreich zurückgesetzt.');
			else
				$tpl->msg('red', '', 'Verlauf konnte nicht zurückgesetzt werden.');
			
			fclose($logFile);
		}
	}
	
	$label = substr(urldecode($_GET['reset']), 8);
	
	switch (urldecode($_GET['reset']))
	{
		case 'coretemp': $label = 'CPU-Temperatur'; break;
		case 'cpuload': $label = 'CPU-Auslastung'; break;
		case 'ram': $label = 'RAM-Auslastung'; break;
	}
	
	$tpl->assign('log', $_GET['reset']);
	$tpl->assign('label', $label);
	
	$tpl->draw('settings/statistic_reset');
}
?>