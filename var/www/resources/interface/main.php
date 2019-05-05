<?php
(include_once realpath(dirname(__FILE__)).'/../main_config.php') or die(print(json_encode(array('status' => 600))));

$data = array();

$data[] = array(
			'version' => $config['versions']['version'],
			'versioncode' => $config['versions']['versioncode'],
			'android_comp_level' => $config['versions']['android_comp_level'],
			'installed' => (file_exists($config['paths']['install']) && is_dir($config['paths']['install'])) ? 'false' : 'true'
		);

print(json_encode(array('main' => $data, 'status' => 200)));
?>