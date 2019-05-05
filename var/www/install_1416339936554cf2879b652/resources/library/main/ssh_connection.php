<?php
(include_once realpath(dirname(__FILE__)).'../../../../resources/main_config.php')	or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0000');

if (include CONFIG_PATH.'/config_ssh.php')
{
	if (isset($config_ssh_port, $config_ssh_username, $config_ssh_password) && $config_ssh_port != '' && $config_ssh_username != '' && $config_ssh_password != '')
	{
		$index_ssh_port = $config_ssh_port;
		$index_ssh_username = $config_ssh_username;
		$index_ssh_pasword = $config_ssh_password;
	}
}

if (include CONFIG_PATH.'/config_uniqid.php')
{
	if (isset($config_uniqid) && $config_uniqid != '')
	{
		$index_uniqid = $config_uniqid;
	}
}

if (isset($index_ssh_port, $index_ssh_username, $index_ssh_pasword, $index_uniqid))
{
	$ssh = ssh2_connect($config['ssh']['ssh_ip'], $index_ssh_port);
	$ssh_auth = ssh2_auth_password($ssh, $index_ssh_username, trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $index_uniqid, base64_decode($index_ssh_pasword), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
}

unset($index_ssh_port, $index_ssh_username, $index_ssh_pasword, $index_uniqid, $config_ssh_port, $config_ssh_username, $config_ssh_password, $config_uniqid);
?>