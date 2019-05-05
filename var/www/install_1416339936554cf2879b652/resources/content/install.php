<?php
$tpl = new RainTPL;

$picontrol_update = checkUpdate();
$tpl->assign('update_picontrol', $picontrol_update);

$tpl->draw('install');
?>