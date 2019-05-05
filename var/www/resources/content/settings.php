<?php
$tpl = new RainTPL;

switch ((isset($_GET['do']) ? $_GET['do'] : ''))
{
	case 'overview':
		include_once CONTENT_PATH.'/settings/overview.php';
			break;
	case 'pic':
		include_once CONTENT_PATH.'/settings/pic.php';
			break;
	case 'update':
		include_once CONTENT_PATH.'/settings/update.php';
			break;
	case 'plugins':
		include_once CONTENT_PATH.'/settings/plugins.php';
			break;
	case 'trouble-shooting':
		include_once CONTENT_PATH.'/settings/trouble-shooting.php';
			break;
	case 'statistic':
		include_once CONTENT_PATH.'/settings/statistic.php';
			break;
	default:
		$tpl->draw('settings');
}
?>