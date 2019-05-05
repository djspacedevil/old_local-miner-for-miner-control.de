<?php
$tpl = new RainTPL;

$ram = rpi_getMemoryUsage();
$memory = rpi_getMemoryInfo();

$tpl->assign('run_time', getDateFormat(rpi_getRuntime()));
$tpl->assign('start_time', date('d.m.Y H:i', time() - rpi_getRuntime()));
$tpl->assign('serial', rpi_getRpiSerial());
$tpl->assign('revision', rpi_getRpiRevision());
$tpl->assign('distribution', rpi_getDistribution());
$tpl->assign('kernel', rpi_getKernelVersion());
$tpl->assign('cpu_clock', rpi_getCpuClock().' MHz');
$tpl->assign('cpu_max_clock', rpi_getCpuMaxClock().' MHz');
$tpl->assign('cpu_load', rpi_getCPULoad().'%');
$tpl->assign('cpu_type', rpi_getCPUType());
$tpl->assign('cpu_model', rpi_getCpuModel());
$tpl->assign('cpu_temp', number_format(rpi_getCoreTemprature(), 2, ',', '').' &deg;C');
$tpl->assign('ram_percentage', $ram['percent'].'%');
$tpl->assign('memory', $memory);
$tpl->assign('memory_count', count($memory));

$tpl->draw('detailed_overview');
?>