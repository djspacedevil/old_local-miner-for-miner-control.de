// JavaScript Document
function show_error(msg)
{
	/*jQuery('div.display-none:eq(1) span').html(msg);
	jQuery('div.display-none:eq(0)').slideUp('fast');
	jQuery('div.display-none:eq(1)').slideDown('fast');*/
	alert(msg);
}

jQuery(document).on('click', 'a[name="refresh"]', function(e)
{
	if (jQuery(this).find('img').css('opacity') == 1)
	{
		var _this = this;
		var _interface = jQuery(this).attr('lang');
		
		jQuery('a[name="refresh"] img').animate({opacity: 0.5}, 300, function(e)
		{
			jQuery(_this).find('img').animate({opacity: 1}, 300).addClass('rotate_icon');
		});
		
		jQuery('div.network_status div.inner-header span').text('Status: '+_interface);
		jQuery('div.network_status div.inner').html('<strong>Das Interface wird neu gestartet...</strong>');
		jQuery('div.network_status').slideDown('fast');
		
		jQuery.post(network_path+'/interface_connect.php', {interface: _interface}, function(data)
		{
			if (data == 'done')
			{
				jQuery('div.network_status div.inner').html('<strong class="green">Das Interface wurde erfolgreich neu gestartet.</strong>');
				jQuery('a[name="refresh"] img').animate({opacity: 1}, 300, function(e)
				{
					jQuery(_this).find('img').removeClass('rotate_icon');
				});
			}
			else
			{
				show_error(data);
				return false;
			}
		});
	}
	else
		alert('Es kann nur ein Interface gleichzeitig neu gestartet werden.');
	
	return false;
});

jQuery(document).on('click', 'a[href=#try_again]', function(e)
{
	jQuery('input:password').prop('disabled', false);
	jQuery('div.display-none:eq(1)').slideUp('fast');
	jQuery('input:submit').parent().slideDown('fast');
	
	return false;
});