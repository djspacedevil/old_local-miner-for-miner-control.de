// JavaScript Document
var is_loding = false;

function overview_status_reload_effect(element)
{
	element.css({'transition': 'background-color 0.5s', 'background-color': 'rgba(243, 255, 164, 1)'});
	setTimeout(function(){
		element.css({'background-color': 'transparent'});
	}, 800);
}
function overview_status_reload()
{
	jQuery('.overview_status_reload_bar').animate({width: '100%'}, reload_timeout, 'linear', function(e)
	{
		var runtime	=		jQuery('table._status td:eq(1)');
		var cpuFreq =		jQuery('table._status td:eq(4)');
		var cpuLoad =		jQuery('table._status td:eq(6)');
		var temp =			jQuery('table._status td:eq(10)');
		var ram =			jQuery('table._status td:eq(12)');
		var memoryUsed =	jQuery('table._status td:eq(14)');
		var memoryFree =	jQuery('table._status td:eq(15)');
		var memoryTotal =	jQuery('table._status td:eq(17)');
		var memoryPercent =	jQuery('table._status td:eq(18)');
		
		is_loding = true;
		jQuery('table._status tr img').addClass('rotate_icon');
		jQuery('.overview_status_reload_bar').animate({width: '90%'}, 360, 'linear');
		
		jQuery.get(overview_path+'/overview_status_reload.php', {data: 'runtime'}, function(data)
		{
			if (runtime.html() != data)
			{
				runtime.html(data);
				overview_status_reload_effect(runtime);
			}
			jQuery('.overview_status_reload_bar').animate({width: '80%'}, 360, 'linear');
			
			jQuery.get(overview_path+'/overview_status_reload.php', {data: 'cpuFreq'}, function(data)
			{
				if (cpuFreq.html() != (data+' MHz'))
				{
					cpuFreq.html(data+' MHz');
					overview_status_reload_effect(cpuFreq);
				}
				jQuery('.overview_status_reload_bar').animate({width: '70%'}, 360, 'linear');
					
				jQuery.get(overview_path+'/overview_status_reload.php', {data: 'cpuLoad'}, function(data)
				{
					if (cpuLoad.find('div.progress div:eq(1)').html() != (data+'%'))
					{
						cpuLoad.find('div.progress div:eq(0)').css('width', data+'%');
						cpuLoad.find('div.progress div:eq(1)').html(data+'%');
						overview_status_reload_effect(cpuLoad);
					}
					jQuery('.overview_status_reload_bar').animate({width: '60%'}, 360, 'linear');
			
					jQuery.get(overview_path+'/overview_status_reload.php', {data: 'temp'}, function(data)
					{
						if (temp.html() != (data+' °C'))
						{
							temp.html(data+' &deg;C');
							overview_status_reload_effect(temp);
						}
						jQuery('.overview_status_reload_bar').animate({width: '50%'}, 360, 'linear');
						
						jQuery.get(overview_path+'/overview_status_reload.php', {data: 'ram'}, function(data)
						{
							if (ram.find('div.progress div:eq(1)').html() != (data+'%'))
							{
								ram.find('div.progress div:eq(0)').css('width', data+'%');
								ram.find('div.progress div:eq(1)').html(data+'%');
								overview_status_reload_effect(ram);
							}
							jQuery('.overview_status_reload_bar').animate({width: '40%'}, 360, 'linear');
						
							jQuery.get(overview_path+'/overview_status_reload.php', {data: 'memoryUsed'}, function(data)
							{
								if (memoryUsed.html() != data)
								{
									memoryUsed.html(data);
									overview_status_reload_effect(memoryUsed);
								}
								jQuery('.overview_status_reload_bar').animate({width: '30%'}, 360, 'linear');
								
								jQuery.get(overview_path+'/overview_status_reload.php', {data: 'memoryFree'}, function(data)
								{
									if (memoryFree.html() != data)
									{
										memoryFree.html(data);
										overview_status_reload_effect(memoryFree);
									}
									jQuery('.overview_status_reload_bar').animate({width: '20%'}, 360, 'linear');
								
									jQuery.get(overview_path+'/overview_status_reload.php', {data: 'memoryTotal'}, function(data)
									{
										if (memoryTotal.html() != data)
										{
											memoryTotal.html(data);
											overview_status_reload_effect(memoryTotal);
										}
										jQuery('.overview_status_reload_bar').animate({width: '10%'}, 360, 'linear');
										
										jQuery.get(overview_path+'/overview_status_reload.php', {data: 'memoryPercent'}, function(data)
										{
											if (memoryPercent.find('div.progress div:eq(1)').html() != (data+'%'))
											{
												memoryPercent.find('div.progress div:eq(0)').css('width', data+'%');
												memoryPercent.find('div.progress div:eq(1)').html(data+'%');
												overview_status_reload_effect(memoryPercent);
											}
											jQuery('.overview_status_reload_bar').animate({width: '0%'}, 360, 'linear', function(e)
											{
												is_loding = false;
												jQuery('table._status tr img').removeClass('rotate_icon');
											});
											
											overview_status_reload();
										});
									});
								});
							});
						});
					});
				});
			});
		});
	});
}

jQuery(document).on('click', 'a[href=#reload]', function(e)
{
	if (is_loding == false)
		jQuery('.overview_status_reload_bar').stop(false, true);
	
	return false;
});

setTimeout('overview_status_reload()', 1);