<?php
if (!(isset($_GET['edit']) && $_GET['edit'] != ''))
{
?>
<style type="text/css">
.cron_time {
	width: 30px;
}

textarea {
	display: none;
}

.cron_edit_save {
	display: none;
}
</style>
<noscript>
	<style type="text/css">
		textarea {
			display: block;
		}
		
		.cron_edit_confirm {
			display: none;
		}
		
		.cron_edit_save {
			display: block;
		}
	</style>
</noscript>
<?php
	// Cron löschen
	if (isset($_GET['cron'], $_GET['delete']) && $_GET['cron'] != '' && $_GET['delete'] == '')
	{
		exec('cat /etc/crontab', $lines);
		$new_file = '';
		$line_count = 0;
		
		foreach ($lines as $line)
		{
			if ($line_count != $_GET['cron'])
			{
				$new_file .= $lines[$line_count]."\n";
			}
			
			$line_count += 1;
		}
		
		$file = fopen($plugin_folder_absolute.'/temp/crontab.tmp.php', 'w+');
		fwrite($file, $new_file);
		fclose($file);
		
		include_once LIBRARY_PATH.'/main/ssh_connection.php';
		if (($stream = ssh2_scp_send($ssh, $plugin_folder_absolute.'/temp/crontab.tmp.php', '/etc/crontab')))
		{
			unlink($plugin_folder_absolute.'/temp/crontab.tmp.php');
			header('Location: ?s=plugins&id=cron&delete=ready');
			exit();
		}
		else
		{
			$info_message = array('red', 'Konnte Konfiguration nicht an Raspberry Pi übermitteln!');
		}
	}
	elseif (isset($_GET['delete']) && $_GET['delete'] == 'ready')
	{
		$info_message = array('green', 'Cron wurde erfolgreich gelöscht.');
	}
	
	// Cron hinzufügen
	if (isset($_GET['add']) && $_GET['add'] == '')
	{
		if ($_POST['minute'] != '' && $_POST['hour'] != '' && $_POST['day'] != '' && $_POST['month'] != '' && $_POST['weekday'] != '' && $_POST['user'] != '' && $_POST['command'] != '')
		{
			exec('cat /etc/crontab', $lines);
			$new_file = '';
			$line_count = 0;
			$last_line = count($lines)-1;
			$second_last_line = count($lines)-2;
			$hashtag = 0;
			$hashtag_line = 0;
			
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
						$new_file .= $_POST['minute'].' '.$_POST['hour'].' '.$_POST['day'].' '.$_POST['month'].' '.$_POST['weekday'].' '.$_POST['user'].' '.$_POST['command']."\n";
						$new_file .= '#';
					}
					elseif ($hashtag == 1)
					{
						$new_file .= $_POST['minute'].' '.$_POST['hour'].' '.$_POST['day'].' '.$_POST['month'].' '.$_POST['weekday'].' '.$_POST['user'].' '.$_POST['command']."\n";
					}
					elseif ($hashtag == 2)
					{
						$new_file .= $_POST['minute'].' '.$_POST['hour'].' '.$_POST['day'].' '.$_POST['month'].' '.$_POST['weekday'].' '.$_POST['user'].' '.$_POST['command']."\n";
					}
				}
				
				$new_file .= $lines[$line_count]."\n";
				$line_count += 1;
			}
			
			$file = fopen($plugin_folder_absolute.'/temp/crontab.tmp.php', 'w+');
			fwrite($file, $new_file);
			fclose($file);
	
			include_once LIBRARY_PATH.'/main/ssh_connection.php';
			if (($stream = ssh2_scp_send($ssh, $plugin_folder_absolute.'/temp/crontab.tmp.php', '/etc/crontab')))
			{
				unlink($plugin_folder_absolute.'/temp/crontab.tmp.php');
				header('Location: ?s=plugins&id=cron&add=ready');
				exit();
			}
			else
			{
				$info_message = array('red', 'Konnte Konfiguration nicht an Raspberry Pi übermitteln!');
			}
		}
		else
		{
			$info_message = array('red', 'Bitte fülle alle Felder aus!');
		}
	}
	elseif (isset($_GET['add']) && $_GET['add'] == 'ready')
	{
		$info_message = array('green', 'Cron wurde erfolgreich hinzugefügt.');
	}
	
	// Cron manuell speichern
	if (isset($_GET['manual']) && $_GET['manual'] == 'save')
	{
		if ($_POST['cron_manual'] != '')
		{
			exec('cat /etc/crontab', $lines);
			
			$file = fopen($plugin_folder_absolute.'/temp/crontab.tmp.php', 'w+');
			fwrite($file, $_POST['cron_manual']);
			fclose($file);
	
			include_once LIBRARY_PATH.'/main/ssh_connection.php';
			if (($stream = ssh2_scp_send($ssh, $plugin_folder_absolute.'/temp/crontab.tmp.php', '/etc/crontab')))
			{
				unlink($plugin_folder_absolute.'/temp/crontab.tmp.php');
				header('Location: ?s=plugins&id=cron&manual=ready');
				exit();
			}
			else
			{
				$info_message = array('red', 'Konnte Konfiguration nicht an Raspberry Pi übermitteln!');
			}
		}
		else
		{
			$info_message = array('red', 'Bitte gebe mindestens ein Zeichen ein! Tipp: "#"');
		}
	}
	elseif (isset($_GET['manual']) && $_GET['manual'] == 'ready')
	{
		$info_message = array('green', 'Cron wurde erfolgreich manuell gespeichert.');
	}
	
	// Cron auslesen
	exec('cat /etc/crontab', $lines);
	$output = array();
	for ($i = 0; $i < count($lines); $i += 1)
	{
		$first_sign = substr(trim($lines[$i]), 0, 1);
		if ($first_sign == '#' || trim($lines[$i]) == '' || preg_match('([\d]|\*)', $first_sign) !== 1) // ([\d]|\@|\*)
		{
			continue;
		}
		else
		{
			$result = NULL;
			preg_match('#([\d]{1,2}|\*|\*/[\d]{1,2})\s+([\d]{1,2}|\*|\*/[\d]{1,2})\s+([\d]{1,2}|\*|\*/[\d]{1,2})\s+([\d]{1,2}|\*|\*/[\d]{1,2})\s+([\d]{1,2}|\*|\*/[\d]{1,2})\s+([\w]*)\s+(.*)#', $lines[$i], $result);
			$result[] = $i;
			$output[] = $result;
		}
	}
	
	if (isset($info_message))
	{
?>
<div id="message_box">
	<div class="<?php echo 'info_'.$info_message[0].' '; ?>box">
		<div class="inner">
			<img src="public_html/img/delete.png" style="float: right; margin: -15px -15px 0px 0px; opacity: 0.6; width: 16px;" onClick="document.getElementById('message_box').style.display='none'" />
			<strong><?php echo $info_message[1]; ?></strong>
		</div>
	</div>
</div>
<?php
	}
?>
<div>
	<div class="box">
		<div class="inner-header">
			<span>Cron</span>
		</div>
		<div class="inner">
			<table class="table">
				<tr>
					<th style="width: 5%;" title="Minute">Min.</th>
					<th style="width: 5%;" title="Stunde">Stu.</th>
					<th style="width: 5%;" title="Tag">Tag</th>
					<th style="width: 5%;" title="Monat">Mon.</th>
					<th style="width: 5%;" title="Wochentag (0 - 7; Sonntag ist 0 und 7)">Woc.</th>
					<th style="width: 15%;" title="Benutzer">Benutzer</th>
					<th style="width: 53%;" title="Befehl">Befehl</th>
					<th style="width: 7%;"></th>
				</tr>
<?php
	foreach ($output as $data)
	{
?>
				<tr>
					<td><?php echo $data[1]; ?></td>
					<td><?php echo $data[2]; ?></td>
					<td><?php echo $data[3]; ?></td>
					<td><?php echo $data[4]; ?></td>
					<td><?php echo $data[5]; ?></td>
					<td><?php echo $data[6]; ?></td>
					<td><?php echo $data[7]; ?></td>
					<td style="text-align: center; vertical-align: middle;"><a href="?s=plugins&amp;id=cron&amp;edit=<?php echo $data[8]; ?>" style="margin-right: 8px;"><img src="<?php echo $plugin_folder_html; ?>/images/edit.png" style="width: 16px; vertical-align: middle;" /></a><a href="?s=plugins&amp;id=cron&amp;cron=<?php echo $data[8]; ?>&amp;delete"><img src="<?php echo $plugin_folder_html; ?>/images/delete.png" style="width: 16px; vertical-align: middle;" /></a></td>
				</tr>
<?php
	}
?>
			</table>
		</div>
	</div>
	<div class="box">
		<div class="inner-header">
			<span>Neuen Cron hinzufügen</span>
		</div>
		<form action="?s=plugins&amp;id=cron&amp;add" method="post">
			<div class="inner-bottom">
				<table style="width: 100%; border-spacing: 0px;">
					<tr>
						<td style="width: 20%; font-weight: bold; padding: 5px;" title="Minute">Minute</td>
						<td style="padding: 5px;"><input name="minute" type="text" style="width: 70px;" /></td>
					</tr>
					<tr style="background: #CFE9FC;">
						<td style="width: 20%; font-weight: bold; padding: 5px;" title="Stunde">Stunde</td>
						<td style="padding: 5px;"><input name="hour" type="text" style="width: 70px;" /></td>
					</tr>
					<tr>
						<td style="width: 20%; font-weight: bold; padding: 5px;" title="Tag">Tag</td>
						<td style="padding: 5px;"><input name="day" type="text" style="width: 70px;" /></td>
					</tr>
					<tr style="background: #CFE9FC;">
						<td style="width: 20%; font-weight: bold; padding: 5px;" title="Monat">Monat</td>
						<td style="padding: 5px;"><input name="month" type="text" style="width: 70px;" /></td>
					</tr>
					<tr>
						<td style="width: 20%; font-weight: bold; padding: 5px;" title="Wochentag (0 - 7; Sonntag ist 0 und 7)">Wochentag (0 - 7)</td>
						<td style="padding: 5px;"><input name="weekday" type="text" style="width: 70px;" /></td>
					</tr>
					<tr style="background: #CFE9FC;">
						<td style="width: 20%; font-weight: bold; padding: 5px;" title="Benutzer">Benutzer</td>
						<td style="padding: 5px;"><input name="user" type="text" style="width: 120px;" /></td>
					</tr>
					<tr>
						<td style="width: 20%; font-weight: bold; padding: 5px;" title="Befehl">Befehl</td>
						<td style="padding: 5px;"><input name="command" type="text" style="width: 500px;" /></td>
					</tr>
				</table>
			</div>
			<div class="inner">
				<input type="submit" name="submit" value="Hinzufügen" />
			</div>
		</form>
	</div>
	<div class="box">
		<div class="inner-header">
			<span>Cron manuell bearbeiten</span>
		</div>
		<form action="?s=plugins&amp;id=cron&amp;manual=save" method="post">
			<div class="inner-bottom" style="padding: 2px;">
				<textarea name="cron_manual" style="width: 100%; height: 400px; border: 0px; resize: vertical; padding: 0px; box-shadow: none;"><?php foreach ($lines as $line) { echo $line."\n"; } ?></textarea>
				<div class="cron_edit_confirm" style="padding: 18px;"><a onClick="return ((confirm('Willst du die Cron wirklich selber bearbeiten? Falsche Eingaben können Fehler verursachen!') == false) ? false : jQuery('.cron_edit_save, textarea').show().parent().find('.cron_edit_confirm').hide())">Cron manuell bearbeiten</a></div>
			</div>
			<div class="inner cron_edit_save">
				<input type="submit" value="Speichern" />
			</div>
		</form>
	</div>
</div>
<div class="clear_both"></div>
<?php
}
else // END = Cron // START = Cronedit
{
	// Cron speichern
	if (isset($_GET['save']) && $_GET['save'] == '')
	{
		if ($_POST['minute'] != '' && $_POST['hour'] != '' && $_POST['day'] != '' && $_POST['month'] != '' && $_POST['weekday'] != '' && $_POST['user'] != '' && $_POST['command'] != '')
		{
			exec('cat /etc/crontab', $lines);
			$new_file = '';
			$line_count = 0;
			
			foreach ($lines as $line)
			{
				if ($line_count == $_GET['edit'])
				{
					$new_file .= $_POST['minute'].' '.$_POST['hour'].' '.$_POST['day'].' '.$_POST['month'].' '.$_POST['weekday'].' '.$_POST['user'].' '.$_POST['command']."\n";
				}
				else
				{
					$new_file .= $lines[$line_count]."\n";
				}
				
				$line_count += 1;
			}
			
			$file = fopen($plugin_folder_absolute.'/temp/crontab.tmp.php', 'w+');
			fwrite($file, $new_file);
			fclose($file);
	
			include_once LIBRARY_PATH.'/main/ssh_connection.php';
			if (($stream = ssh2_scp_send($ssh, $plugin_folder_absolute.'/temp/crontab.tmp.php', '/etc/crontab')))
			{
				unlink($plugin_folder_absolute.'/temp/crontab.tmp.php');
				header('Location: ?s=plugins&id=cron&edit='.$_GET['edit'].'&save=ready');
				exit();
			}
			else
			{
				$info_message = array('red', 'Konnte Konfiguration nicht an Raspberry Pi übermitteln!');
			}
		}
		else
		{
			$info_message = array('red', 'Bitte fülle alle Felder aus!');
		}
	}
	elseif (isset($_GET['save']) && $_GET['save'] == 'ready')
	{
		$info_message = array('green', 'Cron wurde erfolgreich gespeichert.');
	}
	
	// Cron auslesen
	exec('cat /etc/crontab', $lines);
	preg_match('#([\d]{1,2}|\*|\*/[\d]{1,2})\s+([\d]{1,2}|\*|\*/[\d]{1,2})\s+([\d]{1,2}|\*|\*/[\d]{1,2})\s+([\d]{1,2}|\*|\*/[\d]{1,2})\s+([\d]{1,2}|\*|\*/[\d]{1,2})\s+([\w]*)\s+(.*)#', $lines[$_GET['edit']], $result);
	
	if (isset($info_message))
	{
?>
<div id="message_box">
	<div class="<?php echo 'info_'.$info_message[0].' '; ?>box">
		<div class="inner">
			<img src="public_html/img/delete.png" style="float: right; margin: -15px -15px 0px 0px; opacity: 0.6; width: 16px;" onClick="document.getElementById('message_box').style.display='none'" />
			<strong><?php echo $info_message[1]; ?></strong>
		</div>
	</div>
</div>
<?php
	}
?>
<div>
<div class="box">
		<div class="inner-header">
			<span>Cron bearbeiten</span>
		</div>
		<form action="?s=plugins&amp;id=cron&amp;edit=<?php echo $_GET['edit']; ?>&amp;save" method="post">
			<div class="inner-bottom">
				<table style="width: 100%; border-spacing: 0px;">
					<tr>
						<td style="width: 20%; font-weight: bold; padding: 5px;" title="Minute">Minute</td>
						<td style="padding: 5px;"><input type="text" name="minute" style="width: 70px;" value="<?php echo $result[1]; ?>" /></td>
					</tr>
					<tr style="background: #CFE9FC;">
						<td style="width: 20%; font-weight: bold; padding: 5px;" title="Stunde">Stunde</td>
						<td style="padding: 5px;"><input type="text" name="hour" style="width: 70px;" value="<?php echo $result[2]; ?>" /></td>
					</tr>
					<tr>
						<td style="width: 20%; font-weight: bold; padding: 5px;" title="Tag">Tag</td>
						<td style="padding: 5px;"><input type="text" name="day" style="width: 70px;" value="<?php echo $result[3]; ?>" /></td>
					</tr>
					<tr style="background: #CFE9FC;">
						<td style="width: 20%; font-weight: bold; padding: 5px;" title="Monat">Monat</td>
						<td style="padding: 5px;"><input type="text" name="month" style="width: 70px;" value="<?php echo $result[4]; ?>" /></td>
					</tr>
					<tr>
						<td style="width: 20%; font-weight: bold; padding: 5px;" title="Wochentag (0 - 7; Sonntag ist 0 und 7)">Wochentag (0 - 7)</td>
						<td style="padding: 5px;"><input type="text" name="weekday" style="width: 70px;" value="<?php echo $result[5]; ?>" /></td>
					</tr>
					<tr style="background: #CFE9FC;">
						<td style="width: 20%; font-weight: bold; padding: 5px;" title="Benutzer">Benutzer</td>
						<td style="padding: 5px;"><input type="text" name="user" style="width: 120px;" value="<?php echo $result[6]; ?>" /></td>
					</tr>
					<tr>
						<td style="width: 20%; font-weight: bold; padding: 5px;" title="Befehl">Befehl</td>
						<td style="padding: 5px;"><input type="text" name="command" style="width: 500px;" value="<?php echo htmlentities($result[7], ENT_QUOTES); ?>" /></td>
					</tr>
				</table>
			</div>
			<div class="inner">
				<input type="submit" name="submit" value="Speichern" />
			</div>
		</form>
	</div>
</div>
<?php
}
?>