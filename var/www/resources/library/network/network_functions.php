<?php
function getInterfaceLoopback($file)
{
	$loopback = 0;
	
	foreach ($file as $row)
	{
		$row = trim($row);
		
		if (substr($row, 0, 1) != '#' && $row != '')
		{
			if ($row == 'auto lo' || $row == 'iface lo inet loopback')
				$loopback += 1;
		}
	}
	
	return $loopback;
}

function getInterfaces($file)
{
	$array = array();
	$i = 0;
	
	foreach ($file as $row)
	{
		$i += 1;
		$row = trim($row);
		
		if (substr($row, 0, 1) != '#' && $row != '')
		{
			if (substr($row, 0, 5) == 'iface')
			{
				if ($row == 'auto lo' || $row == 'iface lo inet loopback')
					continue;
				
				$iface = explode(' ', $row);
				
				$array[$i] = array($iface);
			}
		}
	}
	
	return $array;
}

function getInterface($file, $interface, $return_rows = false)
{
	$output = '';
	$rows = array();
	$i = 0;
	$if_iface = 0;
	
	foreach ($file as $row)
	{
		$i += 1;
		$row = trim($row);
		
		if (substr($row, 0, 1) != '#' && $row != '')
		{
			if (substr($row, 0, 5) == 'iface')
			{				
				$iface = explode(' ', $row);
				
				if ($iface[1] == $interface)
				{
					switch ($iface[3])
					{
						case 'dhcp':
						if (substr($iface[1], 0, 4) == 'wlan')
						{ 
							$if_iface = $i;
							$output = array($iface, array());
						}
						else
						{
							$if_iface = 0;
							$output = array($iface);
						}
						$rows[] = $i;
							break;
						case 'static':
						$if_iface = $i;
						$output = array($iface, array());
						$rows[] = $i;
							break;
						case 'manual':
						$if_iface = 0;
						$output = array($iface);
						$rows[] = $i;
							break;
					}
				}
			}
			elseif ($if_iface != 0)
			{
				switch (substr($row, 0, 7))
				{
					case 'address ':
					$output[1]['address'] = substr($row, 7);
					$rows[] = $i;
						break;
					case 'netmask':
					$output[1]['netmask'] = substr($row, 8);
					$rows[] = $i;
						break;
					case 'gateway':
					$output[1]['gateway'] = substr($row, 8);
					$rows[] = $i;
						break;
				}
				
				if (substr($row, 0, 4) == 'wpa-')
				{
					switch (substr($row, 4, 4))
					{
						case 'ssid':
						$output[1]['wpa-ssid'] = substr($row, 9);
						$rows[] = $i;
							break;
						case 'psk ':
						$output[1]['wpa-psk'] = substr($row, 8);
						$rows[] = $i;
							break;
						default:
						$rows[] = $i;
							break;
					}
				}
			}
		}
	}
	
	if ($return_rows === false)
		return $output;
	else
		return $rows;
}

function addInterface($file, $interface)
{	
	return array_merge($file, $interface);
}

function deleteInterface($file, $interface)
{
	$interface_rows = getInterface($file, $interface, true);
	
	foreach ($interface_rows as $row)
		unset($file[$row-1]);
	
	return array_values($file);
}

function writeToInterface($ssh, $file)
{
	if (!file_exists(TEMP_PATH) || !is_dir(TEMP_PATH))
		return 4;
	
	if (($f_file = fopen(TEMP_PATH.'/interfaces.tmp.php', 'w')))
	{
		if (fwrite($f_file, implode($file)))
		{
			fclose($f_file);
			if (($stream = ssh2_scp_send($ssh, TEMP_PATH.'/interfaces.tmp.php', '/etc/network/interfaces')))
			{
				unlink(TEMP_PATH.'/interfaces.tmp.php');
				return 0;
			}
			else
				return 3;
		}
		else
		{
			fclose($f_file);
			return 1;
		}
	}
	else
		return 2;
}

function getInterfaceChecksum($file)
{
	return md5(implode($file));
}

function editHostname($ssh, $hostname)
{
	if (!file_exists(TEMP_PATH) || !is_dir(TEMP_PATH))
		return 4;
	
	$hosts = shell_exec('cat /etc/hosts');
	if (empty($hosts))
		return 5;
	
	$new = preg_replace('/^(127\.0\.1\.1)(\s)+(.+)/mi', '$1$2'.$hostname, $hosts);
	
	if (($f_file = fopen(TEMP_PATH.'/hosts.tmp.php', 'w')))
	{
		if (fwrite($f_file, $new))
		{
			fclose($f_file);
			if (($stream = ssh2_scp_send($ssh, TEMP_PATH.'/hosts.tmp.php', '/etc/hosts')))
			{
				unlink(TEMP_PATH.'/hosts.tmp.php');
				
				if (!($stream = ssh2_exec($ssh, 'echo "'.$hostname.'" > /etc/hostname')))
					return 6;
					
				return 0;
			}
			else
				return 3;
		}
		else
		{
			fclose($f_file);
			return 1;
		}
	}
	else
		return 2;
}

function formatInterfaceProtocol($string)
{
	switch ($string)
	{
		case 'inet':
		return 'IPv4';
			break;
		case 'inet6':
		return 'IPv6';
			break;
		case 'ipx':
		return 'IPX/SPX';
			break;
	}
}

function formatInterfaceMethod($string)
{
	switch ($string)
	{
		case 'loopback':
		return 'Loopback';
			break;
		case 'dhcp':
		return 'Dynamisch';
			break;
		case 'static':
		return 'Statisch';
			break;
		case 'manual':
		return 'Manuell';
			break;
	}
}
?>