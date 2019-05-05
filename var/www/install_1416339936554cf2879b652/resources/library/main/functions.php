<?php
// functions.php
function setConfigValue($name, $value, $path = CONFIG_PATH)
{
	if ($value != '')
	{
		if (file_exists($path.'/config.php') && is_file($path.'/config.php') && is_writeable($path.'/config.php') && ($file = file($path.'/config.php')))
		{
			$file_string = '';
			$if_variable_set = false;
			
			foreach ($file as $data)
			{
				if ('$'.$name.' ' == substr($data, 0, strlen($name)+2))
				{
					$file_string .= '$'.$name.' = '.$value.';'."\n";
					$if_variable_set = true;
				}
				else
				{
					if ($data == '?>')
						continue;
					
					$file_string .= $data;
				}
			}
			
			if ($if_variable_set !== true)
				$file_string .= '$'.$name.' = '.$value.';'."\n";
			
			$file_string .= '?>';
			
			if (($f_file = fopen($path.'/config.php', 'w')))
			{
				if (fwrite($f_file, $file_string))
				{
					fclose($f_file);
					return 0;
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
		else
			return 3;
	}
	else
		return 4;
}

function checkUpdate($what = '')
{
	global $config;
	
	if (function_exists('fsockopen') && ini_get('allow_url_fopen') !== false)
	{
		if (!$sock = @fsockopen('www.google.com', 80, $num, $error, 5))
			return 3; // Raspberry Pi is not connected to internet
		else
		{
			if ($xml = simplexml_load_file($config['urls']['updateUrl']))
			{
				$output = '';
				$check = '';
				$i = 0;
		
				foreach($xml as $data)
				{
					if ($data->versioncode == $config['versions']['versioncode']+1)
					{
						$check = 'update';
						break;
					}
					else
						$check = 'no update';
					
					$i++;
				}
				
				if ($check == 'update')
				{
					$output = '';
					if ($what == 'log')
						$output = (string) nl2br($xml->update[$i]->log);
					elseif ($what == 'filename')
						$output = (string) $xml->update[$i]->filename;
					elseif ($what == 'filesize')
						$output = (string) $xml->update[$i]->filesize;
					elseif ($what == 'checksum')
						$output = (string) $xml->update[$i]->checksum;
					elseif ($what == 'version')
						$output = (string) $xml->update[$i]->version;
					elseif ($what == 'versioncode')
						$output = (string) $xml->update[$i]->versioncode;
					else
						$output = array('version' => (string) $xml->update[$i]->version,
										'versioncode' => (string) $xml->update[$i]->versioncode,
										'filename' => (string) $xml->update[$i]->filename,
										'filesize' => (string) $xml->update[$i]->filesize,
										'checksum' => (string) $xml->update[$i]->checksum,
										'log' => (string) nl2br($xml->update[$i]->log),
										'date' => (string) $xml->update[$i]->date);
					
					return $output;
				}
				elseif ($check == 'no update')
					return 0;
				else
					return 1;
			}
			else
				return 2;
		}
	}
	else
		return 4; // Function is not enabled
}

function getDateFormat($time)
{
	$day = floor($time / 60 / 60 / 24);
	$day = ($day < 10) ? '0'.$day : $day;
	$day = ($day == 1) ? '01 Tag ' : $day.' Tage ';
	$day = ($day == '00 Tage ') ? '' : $day;
	$hour = floor($time / 60 / 60 % 24);
	$hour = ($hour < 10) ? '0'.$hour : $hour;
	$hour = ($hour == 24) ? '00' : $hour;
	$minute = floor($time / 60 % 60);
	$minute = ($minute < 10) ? '0'.$minute : $minute;
	$second = floor($time % 60);
	$second = ($second < 10) ? '0'.$second : $second;
	
	return $day.$hour.':'.$minute.':'.$second;
}

function getAllFiles($folder_)
{
	$folderArray = array();
	$fileArray = array();
	$folder = array();
	$file = array();
	$errorArray = array();
	
	foreach (@scandir($folder_) as $file_)
		if ($file_[0] != '.')
			if (is_dir($folder_.'/'.$file_))
				$folderArray[] = $file_;
			else
				$fileArray[] = $file_;
	
	if (isset($folderArray))
	{
		foreach ($folderArray as $row)
		{
			list ($file_return, $error_log) = getAllFiles($folder_.'/'.$row);
			$file[$row] = $file_return;
			
			if (is_writeable($folder_.'/'.$row) !== true)
				$errorArray[] = $folder_.'/'.$row.'/';

			$errorArray = array_merge($errorArray, $error_log);
		}
	}
	
	if (isset($fileArray))
	{
		foreach ($fileArray as $row)
		{
			$file[] = $row;
			
			if (is_writeable($folder_.'/'.$row) !== true)
				$errorArray[] = $folder_.'/'.$row;
		}
	}
	
	return array($file, $errorArray);
}

function checkInternetConnection()
{
	if (function_exists('fsockopen') && ini_get('allow_url_fopen') !== false)
	{
		if (!$sock = @fsockopen('www.google.com', 80, $num, $error, 5))
			return false; // Raspberry Pi is not connected to internet
		else
			return true;
	}
	else
		return false;
}

function showHelper($url, $extern = false)
{
	global $config;
	
	if ($extern === false)
		$url = $config['urls']['helpUrl'].'#'.$url;
	
	return '<a href="'.$url.'" title="Klicke für Hilfe" target="_blank" class="helper">&nbsp;</a>';
}

function addCronToCrontab($cron_entry, $ssh)
{
	exec('cat /etc/crontab', $lines);
	$new_file = '';
	$line_count = 0;
	$last_line = count($lines)-1;
	$second_last_line = count($lines)-2;
	$hashtag = 0;
	$hashtag_line = 0;
	
	if (!in_array($cron_entry, $lines))
	{
		if (substr(trim($lines[$last_line]), 0, 1) == '')
		{
			if (substr(trim($lines[$second_last_line]), 0, 1) == '#')
			{
				$hashtag = 1;
				$hashtag_line = $second_last_line;
			}
			else
			{
				$hashtag = 0;
				$hashtag_line = $last_line;
			}
		}
		
		if (substr(trim($lines[$last_line]), 0, 1) == '#')
		{
			$hashtag = 2;
			$hashtag_line = $last_line;
		}
		
		foreach ($lines as $line)
		{
			if ($line_count == $hashtag_line)
			{
				if ($hashtag == 0)
				{
					$new_file .= $cron_entry."\n";
					$new_file .= '#';
				}
				elseif ($hashtag == 1)
					$new_file .= $cron_entry."\n";
				elseif ($hashtag == 2)
					$new_file .= $cron_entry."\n";
			}
			
			$new_file .= $lines[$line_count]."\n";
			$line_count += 1;
		}
		
		if (file_exists(TEMP_PATH.'/crontab') && is_file(TEMP_PATH.'/crontab'))
			unlink(TEMP_PATH.'/crontab');
		
		if (($file = fopen(TEMP_PATH.'/crontab', 'w+')))
		{
			if (!fwrite($file, $new_file))
				return 4;
		}
		else
			return 3;
		
		if (($stream = ssh2_scp_send($ssh, TEMP_PATH.'/crontab', '/etc/crontab')))
		{
			unlink(TEMP_PATH.'/crontab');
			return 0;
		}
		else
			return 1;
	}
	else
		return 2;
}
?>