<?php
$tpl = new RainTPL;

$interfaces_config_error = false;

if (!($interface_file = file('/etc/network/interfaces')))
	$interfaces_config_error = true;

include_once LIBRARY_PATH.'/network/network_functions.php';

if (isset($_GET['new']) && $_GET['new'] == '' && $interfaces_config_error != true) // Interface hinzufügen
{
	if (isset($_GET['save']) && $_GET['save'] == '')
	{
		if (isset($_POST['submit'], $_POST['interface'], $_POST['protocol'], $_POST['method'], $_POST['checksum']) && trim($_POST['interface']) != '' && trim($_POST['protocol']) != '' && trim($_POST['method']) != '' && $_POST['checksum'] != '')
		{
			if ($_POST['checksum'] == getInterfaceChecksum($interface_file))
			{
				$new_interface_file = $interface_file;
				
				if ($_POST['method'] == 'static')
				{
					$static_array = array();
					
					if (isset($_POST['address']) && $_POST['address'] != '')
						$static_array[] = "\taddress ".trim($_POST['address'])."\n";
					
					if (isset($_POST['netmask']) && $_POST['netmask'] != '')
						$static_array[] = "\tnetmask ".trim($_POST['netmask'])."\n";
					
					if (isset($_POST['gateway']) && $_POST['gateway'] != '')
						$static_array[] = "\tgateway ".trim($_POST['gateway'])."\n";
					
					$new_interface_file = addInterface($new_interface_file, array("iface ".$_POST['interface']." ".$_POST['protocol']." ".$_POST['method']."\n", implode($static_array)));
				}
				else
					$new_interface_file = addInterface($new_interface_file, array("iface ".$_POST['interface']." ".$_POST['protocol']." ".$_POST['method']."\n"));
				
				include_once LIBRARY_PATH.'/main/ssh_connection.php';
				if (writeToInterface($ssh, $new_interface_file) === 0)
				{
					if (!headers_sent($filename, $linenum))
						exit(header('Location: ?s=network_configuration&edit='.$_POST['interface'].'&save=ready'));
					else
						$tpl->error('Weiterleitung', 'Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="?s=network_configuration&edit='.$_POST['interface'].'&save=ready">diesen Link</a> an.');
				}
			}
			else
				$tpl->msg('red', '', 'Leider wurde die Konfigurationsdatei inzwischen verändert, versuche es deshalb noch einmal.');
		}
		else
			$tpl->msg('red', '', 'Bitte vergebe eine Interfacebezeichnung, ein Protokol und eine Methode!');
	}
	elseif (isset($_GET['save']) && $_GET['save'] == 'ready')
		$tpl->msg('green', '', 'Interface wurde erfolgreich gespeichert. Damit diese Einstellung jedoch wirksam wird, muss das Netzwerk neu gestartet werden.');
}
elseif (isset($_GET['edit']) && $_GET['edit'] != '' && $interfaces_config_error != true) // Interface bearbeiten
{
	if (isset($_GET['save']) && $_GET['save'] == '')
	{
		if (isset($_POST['submit'], $_POST['interface'], $_POST['protocol'], $_POST['method'], $_POST['checksum']) && trim($_POST['interface']) != '' && trim($_POST['protocol']) != '' && trim($_POST['method']) != '' && $_POST['checksum'] != '')
		{
			if ($_POST['checksum'] == getInterfaceChecksum($interface_file))
			{
				$new_interface_file = deleteInterface($interface_file, $_GET['edit']);
				
				if ($_POST['method'] == 'static')
				{
					$static_array = array();
					
					if (isset($_POST['address']) && $_POST['address'] != '')
						$static_array[] = "\taddress ".trim($_POST['address'])."\n";
					
					if (isset($_POST['netmask']) && $_POST['netmask'] != '')
						$static_array[] = "\tnetmask ".trim($_POST['netmask'])."\n";
					
					if (isset($_POST['gateway']) && $_POST['gateway'] != '')
						$static_array[] = "\tgateway ".trim($_POST['gateway'])."\n";
					
					$new_interface_file = addInterface($new_interface_file, array("iface ".$_POST['interface']." ".$_POST['protocol']." ".$_POST['method']."\n", implode($static_array)));
				}
				else
					$new_interface_file = addInterface($new_interface_file, array("iface ".$_POST['interface']." ".$_POST['protocol']." ".$_POST['method']."\n"));
				
				include_once LIBRARY_PATH.'/main/ssh_connection.php';
				if (writeToInterface($ssh, $new_interface_file) === 0)
				{
					if (!headers_sent($filename, $linenum))
						exit(header('Location: ?s=network_configuration&edit='.$_POST['interface'].'&save=ready'));
					else
						$tpl->error('Weiterleitung', 'Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="?s=network_configuration&edit='.$_POST['interface'].'&save=ready">diesen Link</a> an.');
				}
			}
			else
				$tpl->msg('red', '', 'Leider wurde die Konfigurationsdatei inzwischen verändert, versuche es deshalb noch einmal.');
		}
		else
			$tpl->msg('red', '', 'Bitte vergebe eine Interfacebezeichnung, ein Protokol und eine Methode!');
	}
	elseif (isset($_GET['save']) && $_GET['save'] == 'ready')
		$tpl->msg('green', '', 'Interface wurde erfolgreich gespeichert. Damit diese Einstellung jedoch wirksam wird, muss das Netzwerk neu gestartet werden.');
}
elseif (isset($_GET['delete']) && $_GET['delete'] != '' && $interfaces_config_error != true) // Interface löschen
{
	$new_interface_file = deleteInterface($interface_file, $_GET['delete']);
	
	include_once LIBRARY_PATH.'/main/ssh_connection.php';
	if (writeToInterface($ssh, $new_interface_file) === 0)
	{
		if (!headers_sent($filename, $linenum))
			exit(header('Location: ?s=network_configuration&statusmsg=deleted'));
		else
			$tpl->error('Weiterleitung', 'Header bereits gesendet. Redirect nicht m&ouml;glich, klicke daher stattdessen <a href="?s=network_configuration&amp;statusmsg=deleted">diesen Link</a> an.');
	}
}
elseif (isset($_GET['refresh']) && $_GET['refresh'] != '' && $interfaces_config_error != true) // Interface neustarten
{
	//
}

if (isset($_GET['statusmsg']) && $_GET['statusmsg'] != '') // Statusnachricht
{
	switch ($_GET['statusmsg'])
	{
		case 'deleted':
			$tpl->msg('green', '', 'Das Interface wurde erfolgreich gelöscht.');
				break;
	}
}

if (((isset($_GET['edit']) && $_GET['edit'] != '') || (isset($_GET['new']) && $_GET['new'] == '')) && $interfaces_config_error != true) // Interface - Bearbeitung
{
	if (isset($_GET['edit']) && $_GET['edit'] != '')
	{
		$interfaces = getInterface($interface_file, $_GET['edit']);
		
		if (!is_array($interfaces))
		{
			$tpl->assign('msg', 'Leider konnte das Interface nicht gefunden werden!');
			$tpl->draw('network_configuration_error');
		}
	}
	else
	{
		$interfaces = array(array('', (isset($_POST['interface'])) ? $_POST['interface'] : '',
									  (isset($_POST['protocol'])) ? $_POST['protocol'] : '',
									  (isset($_POST['method'])) ? $_POST['method'] : ''),
							array('address' => (isset($_POST['address'])) ? $_POST['address'] : '',
								  'netmask' => (isset($_POST['netmask'])) ? $_POST['netmask'] : '',
								  'gateway' => (isset($_POST['gateway'])) ? $_POST['gateway'] : ''));
	}
	
	if (!(isset($_GET['edit']) && $_GET['edit'] != '' && !is_array($interfaces)))
	{
		$tpl->assign('interface', $interfaces);
		$tpl->assign('interface_checksum', getInterfaceChecksum($interface_file));
		$tpl->draw('network_configuration_edit');
	}
}
/*elseif (isset($_GET['refresh']) && $_GET['refresh'] != '' && $interfaces_config_error != true) // Interface - Neustarten
{
	$tpl->assign('interface', $interfaces);
	$tpl->draw('network_configuration_interface_refresh');

}*/
elseif ($interfaces_config_error != true) // Interface - Alle
{
	// Beta
	$tpl->msg('yellow', '', 'Die Netzwerkkonfiguration ist noch im Beta-Stadium. Sollten Probleme auftreten, schreibe mir bitte unter "Feedback".', false);
	
	$tpl->assign('js_variables', 'var network_path = \''.str_replace(dirname($_SERVER['SCRIPT_FILENAME']).'/', '', LIBRARY_PATH).'/network\';');
	$tpl->assign('interface_loopback', getInterfaceLoopback($interface_file));
	$tpl->assign('interfaces', getInterfaces($interface_file));
	$tpl->draw('network_configuration');
}
else // Interface - Fehler
{
	$tpl->assign('msg', 'Leider konnte die Konfigurationsdatei (/etc/network/interface) nicht erfolgreich geöffnet und gelesen werden! Bitte versuche es noch einmal!');
	$tpl->draw('network_configuration_error');
}
?>