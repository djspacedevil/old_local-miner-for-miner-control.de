function show_error(msg)
{
	jQuery('.file_permission_status').append(' <span class="red">fehlgeschlagen</span><br /><br />');
	jQuery('.file_permission_status').append('<strong class="red">Fehler beim Ausführen der Befehle! <a href="?s=settings&amp;do=trouble-shooting&amp;file_permission=confirm" onClick="jQuery(\'.file_permission_status\').html(\'\'); file_permission(); return false;">Erneut versuchen.</a></strong>');
	alert(msg);
}

function file_permission()
{
	jQuery('.file_permission_status a').hide();
	
	jQuery('.file_permission_status').append('<i>sudo chown -R ' + whoami + ':' + whoami + ' ' + direct_path + '/</i> wird ausgeführt...');
	jQuery.get(trouble_shooting_path+'/file_permission.php', {command: '1'}, function(data)
	{
		if (data == 'done')
			jQuery('.file_permission_status').append(' <span class="green">erfolgreich</span><br /><br />');
		else
		{
			show_error(data);
			return false;
		}
		
		jQuery('.file_permission_status').append('<i>sudo find ' + direct_path + '/ -type d -exec chmod 755 {} +</i> wird ausgeführt...');
		jQuery.get(trouble_shooting_path+'/file_permission.php', {command: '2'}, function(data)
		{
			if (data == 'done')
				jQuery('.file_permission_status').append(' <span class="green">erfolgreich</span><br /><br />');
			else
			{
				show_error(data);
				return false;
			}
			
			jQuery('.file_permission_status').append('<i>sudo find ' + direct_path + '/ -type f -exec chmod 644 {} +</i> wird ausgeführt...');
			jQuery.get(trouble_shooting_path+'/file_permission.php', {command: '3'}, function(data)
			{
				if (data == 'done')
					jQuery('.file_permission_status').append(' <span class="green">erfolgreich</span><br /><br />');
				else
				{
					show_error(data);
					return false;
				}
			
				jQuery('.file_permission_status').append('Test wird ausgeführt...');
				jQuery.get(trouble_shooting_path+'/file_permission.php', {command: '4'}, function(data)
				{
					if (data == 'done')
					{
						jQuery('.file_permission_status').append(' <span class="green">erfolgreich</span>').addClass('inner-bottom').removeClass('inner');
						jQuery('<div class="inner"><a href="?s=settings&do=trouble-shooting&statusmsg=file_permission_completed"><input type="button" value="Abschließen" /></a></div>').appendTo('.box:last');
					}
					else
					{
						show_error(data);
						return false;
					}
				});
			});
		});
	});
	
	return false;
}