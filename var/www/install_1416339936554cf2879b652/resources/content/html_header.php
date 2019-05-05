<?php
$tpl = new RainTPL;

$tpl->assign('javascript_time', time()+date('Z', time()));
$tpl->assign('javascript_req_url', urlencode($_SERVER['REQUEST_URI']));

$tpl->draw('html_header');
?>