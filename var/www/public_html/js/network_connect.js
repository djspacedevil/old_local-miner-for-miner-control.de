// JavaScript Document
function show_error(msg)
{
	jQuery('div.display-none:eq(1) span').html(msg);
	jQuery('div.display-none:eq(0)').slideUp('fast');
	jQuery('div.display-none:eq(1)').slideDown('fast');
}

jQuery(document).on('click', 'input:submit', function(e)
{
	if (jQuery('input:password').length == 1 && jQuery('input:password').val().length < 8)
	{
		jQuery('input:submit').parent().find('strong.display-none').show();
		return false;
	}
	
	if (jQuery('input:password').length == 1)
		jQuery('input:password').prop('disabled', true);
	
	jQuery('input:submit').parent().slideUp('fast');
	jQuery('div.display-none:eq(0)').slideDown('fast');
	
	var _interface = jQuery('form input[name=interface]').val();
	var _ssid = jQuery('form input[name=ssid]').val();
	
	jQuery.post(wlan_path+'/wlan_connect.php', {interface: _interface, ssid: _ssid, psk: jQuery('form input[name=password]').val()}, function(data)
	{
		if (data == 'done')
			jQuery('div.display-none:eq(0) strong').html('Verbindung wird getrennt...');
		else
		{
			show_error(data);
			return false;
		}
		jQuery.post(wlan_path+'/wlan_connect_down.php', {interface: _interface}, function(data)
		{
			if (data == 'done')
				jQuery('div.display-none:eq(0) strong').html('Verbindung wird wieder hergestellt...');
			else
			{
				show_error(data);
				return false;
			}
			jQuery.post(wlan_path+'/wlan_connect_up.php', {interface: _interface}, function(data)
			{
				if (data == 'done')
					jQuery('div.display-none:eq(0) strong').html('Ermittle IP von Verbindung...');
				else
				{
					show_error(data);
					return false;
				}
				jQuery.post(wlan_path+'/wlan_connect_ip.php', {interface: _interface}, function(data)
				{
					if (data.match(/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/))
					{
						jQuery('div.display-none:eq(0) img').prop('src', 'public_html/img/nm_signal_100.png');
						jQuery('div.display-none:eq(0) strong').html('Verbindung mit "'+_ssid+'" war erfolgreich.').addClass('green');
						jQuery('div.display-none:eq(0)').append('<br /><br /><strong>IP-Adresse:</strong> <a href="http://'+data+'" target="_blank">'+data+'</a>');
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
});

jQuery(document).on('click', 'a[href=#try_again]', function(e)
{
	jQuery('input:password').prop('disabled', false);
	jQuery('div.display-none:eq(1)').slideUp('fast');
	jQuery('input:submit').parent().slideDown('fast');
	
	return false;
});