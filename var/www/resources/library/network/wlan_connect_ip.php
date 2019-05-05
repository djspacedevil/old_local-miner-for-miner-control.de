<?php
(include_once realpath(dirname(__FILE__)).'/../../main_config.php') or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0000');

$stream = trim(shell_exec('/sbin/ifconfig '.escapeshellarg($_POST['interface']).' | grep "inet addr:" | cut -d: -f2 | awk \'{ print $1}\''));

if (!empty($stream))
{
	echo $stream;
}
else
{
	echo 'Konnte IP-Adresse nicht abrufen!';
}
?>