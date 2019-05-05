<?php
$tpl = new RainTPL;

$tpl->assign('errorHandler', implode('~', $errorHandler));
$tpl->assign('servertime', date('H:i:s', time()-1));
$tpl->assign('version', $config['versions']['version']);
$tpl->assign('help_link', $config['urls']['helpUrl']);

$tpl->draw('html_footer');
?>