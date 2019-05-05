<?php
$tpl = new RainTPL;

if(extension_loaded('ssh2'))
	$cf_ssh = 1;
else
	$cf_ssh = false;

$path_folder_1 = $config['paths']['main'].'/../resources/config/';
$path_folder_2 = TEMP_PATH;
$path_folder_3 = $config['paths']['main'].'/../install/';

// 1
if (file_exists($path_folder_1) && is_dir($path_folder_1))
{
	$perm_folder_1 = substr(sprintf('%o', fileperms($path_folder_1)), -3);
	if ($perm_folder_1 == '755')
	{
		if (is_writeable($path_folder_1))
			$cf_folder_1 = 1;
		else
		{
			$cf_folder_1_uid = posix_getpwuid(fileowner($path_folder_1));
			$cf_folder_1 = array(3, $cf_folder_1_uid['name']);
		}
	}
	else
		$cf_folder_1 = $perm_folder_1;
}
else
	$cf_folder_1 = 2;

// 2
if (file_exists($path_folder_2) && is_dir($path_folder_2))
{
	$perm_folder_2 = substr(sprintf('%o', fileperms($path_folder_2)), -3);
	if ($perm_folder_2 == '755')
	{
		if (is_writeable($path_folder_2))
			$cf_folder_2 = 1;
		else
		{
			$cf_folder_2_uid = posix_getpwuid(fileowner($path_folder_2));
			$cf_folder_2 = array(3, $cf_folder_2_uid['name']);
		}
	}
	else
		$cf_folder_2 = $perm_folder_2;
}
else
	$cf_folder_2 = 2;

// 3
if (file_exists($path_folder_3) && is_dir($path_folder_3))
{
	$perm_folder_3 = substr(sprintf('%o', fileperms($path_folder_3)), -3);
	if ($perm_folder_3 == '755')
	{
		if (is_writeable($path_folder_3))
			$cf_folder_3 = 1;
		else
		{
			$cf_folder_3_uid = posix_getpwuid(fileowner($path_folder_3));
			$cf_folder_3 = array(3, $cf_folder_3_uid['name']);
		}
	}
	else
		$cf_folder_3 = $perm_folder_3;
}
else
	$cf_folder_3 = 2;

$path_file_1 = $config['paths']['main'].'/../resources/config/config.php';
$path_file_2 = $config['paths']['main'].'/../resources/config/config_ssh.php';
$path_file_3 = $config['paths']['main'].'/../resources/config/config_uniqid.php';
$path_file_4 = TEMP_PATH.'/config_datas.php';
$path_file_5 = TEMP_PATH.'/config_settings.php';

// 1
if (file_exists($path_file_1) && is_file($path_file_1))
{
	$perm_file_1 = substr(sprintf('%o', fileperms($path_file_1)), -3);
	if ($perm_file_1 == '644')
	{
		if (is_writeable($path_file_1))
			$cf_file_1 = 1;
		else
		{
			$cf_file_1_uid = posix_getpwuid(fileowner($path_file_1));
			$cf_file_1 = array(3, $cf_file_1_uid['name']);
		}
	}
	else
		$cf_file_1 = $perm_file_1;
}
else
	$cf_file_1 = 2;

// 2
if (file_exists($path_file_2) && is_file($path_file_2))
{
	$perm_file_2 = substr(sprintf('%o', fileperms($path_file_2)), -3);
	if ($perm_file_2 == '644')
	{
		if (is_writeable($path_file_2))
			$cf_file_2 = 1;
		else
		{
			$cf_file_2_uid = posix_getpwuid(fileowner($path_file_2));
			$cf_file_2 = array(3, $cf_file_2_uid['name']);
		}
	}
	else
		$cf_file_2 = $perm_file_2;
}
else
	$cf_file_2 = 2;

// 3
if (file_exists($path_file_3) && is_file($path_file_3))
{
	$perm_file_3 = substr(sprintf('%o', fileperms($path_file_3)), -3);
	if ($perm_file_3 == '644')
	{
		if (is_writeable($path_file_3))
			$cf_file_3 = 1;
		else
		{
			$cf_file_3_uid = posix_getpwuid(fileowner($path_file_3));
			$cf_file_3 = array(3, $cf_file_3_uid['name']);
		}
	}
	else
		$cf_file_3 = $perm_file_3;
}
else
	$cf_file_3 = 2;

// 4
if (file_exists($path_file_4) && is_file($path_file_4))
{
	$perm_file_4 = substr(sprintf('%o', fileperms($path_file_4)), -3);
	if ($perm_file_4 == '644')
	{
		if (is_writeable($path_file_4))
			$cf_file_4 = 1;
		else
		{
			$cf_file_4_uid = posix_getpwuid(fileowner($path_file_4));
			$cf_file_4 = array(3, $cf_file_4_uid['name']);
		}
	}
	else
		$cf_file_4 = $perm_file_4;
}
else
	$cf_file_4 = 2;

// 5
if (file_exists($path_file_5) && is_file($path_file_5))
{
	$perm_file_5 = substr(sprintf('%o', fileperms($path_file_5)), -3);
	if ($perm_file_5 == '644')
	{
		if (is_writeable($path_file_5))
			$cf_file_5 = 1;
		else
		{
			$cf_file_5_uid = posix_getpwuid(fileowner($path_file_5));
			$cf_file_5 = array(3, $cf_file_5_uid['name']);
		}
	}
	else
		$cf_file_5 = $perm_file_5;
}
else
	$cf_file_5 = 2;


if (function_exists('mcrypt_encrypt') !== false)
	$cf_mcrypt = 1;
else
	$cf_mcrypt = false;

if (ini_get('allow_url_fopen') !== false) 
	$cf_auf = 1;
else
	$cf_auf = false;

if (class_exists('ZipArchive') !== false)
	$cf_zipa = 1;
else
	$cf_zipa = false;

if (isset($_GET['next']) && $_GET['next'] == '')
{
	if ($cf_ssh == 1 && $cf_mcrypt == 1 && $cf_folder_1 == 1 && $cf_folder_2 == 1 && $cf_folder_3 == 1 && $cf_file_1 == 1 && $cf_file_2 == 1 && $cf_file_3 == 1 && $cf_file_4 == 1 && $cf_file_5 == 1)
	{
		if (!headers_sent($filename, $linenum))
			exit(header('Location: ?s=install_connection'));
		else
			$tpl->error('Weiterleitung', 'Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="?s=install_connection">diesen Link</a> an.');
	}
	else
		$tpl->msg('red', '', 'Es sind nicht alle Pflicht-Anforderungen erfüllt!');
}

$tpl->assign('cf_ssh', $cf_ssh);
$tpl->assign('path_folder_1', $path_folder_1);
$tpl->assign('path_folder_2', $path_folder_2);
$tpl->assign('path_folder_3', $path_folder_3);
$tpl->assign('cf_folder_1', $cf_folder_1);
$tpl->assign('cf_folder_2', $cf_folder_2);
$tpl->assign('cf_folder_3', $cf_folder_3);
$tpl->assign('path_file_1', $path_file_1);
$tpl->assign('path_file_2', $path_file_2);
$tpl->assign('path_file_3', $path_file_3);
$tpl->assign('path_file_4', $path_file_4);
$tpl->assign('path_file_5', $path_file_5);
$tpl->assign('cf_file_1', $cf_file_1);
$tpl->assign('cf_file_2', $cf_file_2);
$tpl->assign('cf_file_3', $cf_file_3);
$tpl->assign('cf_file_4', $cf_file_4);
$tpl->assign('cf_file_5', $cf_file_5);
$tpl->assign('cf_mcrypt', $cf_mcrypt);
$tpl->assign('cf_auf', $cf_auf);
$tpl->assign('cf_zipa', $cf_zipa);

$tpl->draw('install_check');
?>