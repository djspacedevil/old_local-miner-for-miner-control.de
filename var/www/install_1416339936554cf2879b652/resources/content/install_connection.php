<?php
$tpl = new RainTPL;

$ssh_login_check = false;

if (isset($_GET['send']) && $_GET['send'] == '')
{
	if (isset($_POST['port'], $_POST['username'], $_POST['password']) && $_POST['port'] != '' && $_POST['username'] != '' && $_POST['password'] != '')
	{
		if (is_numeric($_POST['port']) && $_POST['port'] >= 0 && $_POST['port'] <= 65535)
		{
			$ssh = ssh2_connect($_SERVER['SERVER_ADDR'], $_POST['port']);
			$ssh_auth = ssh2_auth_password($ssh, trim($_POST['username']), $_POST['password']);
			if ($ssh_auth)
			{
				$uniqid = md5(uniqid(rand(), true));
				$salt_pw = trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $uniqid, $_POST['password'], MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
				
				if (!file_exists(TEMP_PATH.'/config_datas.php'))
					fopen(TEMP_PATH.'/config_datas.php', 'w');
				
				if ($config_datas = fopen(TEMP_PATH.'/config_datas.php', 'w+'))
				{
					if (!fwrite($config_datas,
						trim($_POST['port'])."\r\n".
						trim($_POST['username'])."\r\n".
						$salt_pw."\r\n".
						$uniqid))
					{
						$tpl->msg('red', '', 'Leider ist ein Fehler aufgetreten. Bitte versuche es nocheinmal. Fehlercode: 9x0001 (Siehe Hilfe)');
					}
					else
						$ssh_login_check = true;
				}
				else
					$tpl->msg('red', '', 'Leider ist ein Fehler aufgetreten. Bitte versuche es nocheinmal. Fehlercode: 9x0002');
				
				fclose($config_datas);
			}
			else
			{
				$tpl->msg('red', '', 'Verbindung zum Raspberry Pi war nicht erfolgreich!<br /><br />Bitte überprüfe die eingegebenen Daten.
		Schlägt ein erneuter Versuch mit korrekten Daten fehl, wende dich bitte an <a href="http://willy-tech.de/kontakt/" target="_blank">mich</a>, ich werde dir so schnell wie möglich weiterhelfen.'.((shell_exec('ping -c 1 localhost') == NULL) ? '<br /><br />Info: Ping an localhost ist ebenfalls fehlgeschlagen!' : ''));
			}
		}
		else
			$tpl->msg('red', '', 'Ungültiger Port. Der Port muss zwischen 0 und 65535 liegen.');
	}
	else
		$tpl->msg('red', '', 'Bitte alle Felder ausfüllen.');
}

$tpl->assign('port', (isset($_POST['port'])) ? $_POST['port'] : '22');
$tpl->assign('username', (isset($_POST['username'])) ? $_POST['username'] : 'root');
$tpl->assign('ssh_login_check', $ssh_login_check);

$tpl->draw('install_connection');
?>