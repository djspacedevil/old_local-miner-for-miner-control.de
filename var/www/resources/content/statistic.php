<?php
$tpl = new RainTPL;

$folder = LOG_PATH;
$fileArray = array();
$logArray = array();
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
	if (substr($file_ , 0, -8) == 'coretemp' && array_search('coretemp', $hiddenStatistics) === false)
	{
		$logArray[] = array('log' => 'coretemp',
							'label' => 'CPU-Temperatur',
							'type' => 'coretemp',
							'title' => 'Grad Celsius',
							'unit' => '  °C',
							'columns' => array(1));
	}
	if (substr($file_ , 0, -8) == 'cpuload' && array_search('cpuload', $hiddenStatistics) === false)
	{
		$logArray[] = array('log' => 'cpuload',
							'label' => 'CPU-Auslastung',
							'type' => 'cpuload',
							'title' => 'Auslastung %',
							'unit' => '  %',
							'columns' => array(1));
	}
	if (substr($file_ , 0, -8) == 'ram' && array_search('ram', $hiddenStatistics) === false)
	{
		$logArray[] = array('log' => 'ram',
							'label' => 'RAM-Auslastung',
							'type' => 'ram',
							'title' => 'Auslastung %',
							'unit' => '  %',
							'columns' => array(1));
	}
	elseif (substr($file_ , 0, 8) == 'network_' && array_search(substr($file_ , 0, -8), $hiddenStatistics) === false)
	{
		$logArray[] = array('log' => substr($file_, 0, -8),
							'label' => substr($file_ , 8, -8),
							'type' => 'network',
							'title' => 'Traffic (MB)',
							'unit' => ' MB',
							'columns' => array(1,2));
	}
}

$tpl->assign('statistic_path', str_replace(dirname($_SERVER['SCRIPT_FILENAME']).'/', '', LIBRARY_PATH).'/statistic');
$tpl->assign('logArrayCount', count($fileArray));
$tpl->assign('logArray', $logArray);

$tpl->draw('statistic');
?>