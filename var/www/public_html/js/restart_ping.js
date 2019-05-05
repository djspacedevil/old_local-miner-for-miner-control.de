// JavaScript Document

var shutdown = false;

function ping()
{
	var jsonData = $.ajax({
		url: "../interface/main.php",
		dataType:"json",
		async: true,
		timeout: 5000
	}).done(function(data)
	{
		if (shutdown == true)
		{
			jQuery('.inner strong').text('Online. Du wirst sofort weitergeleitet');
			setTimeout("self.location.href='../../'", 2000);
		}
		
		jQuery('.inner strong').addClass('green').removeClass('red');
		setTimeout("ping()", 5000);
	}).error(function(data)
	{
		jQuery('.inner strong').text('Offline');
		shutdown = true;
		
		jQuery('.inner strong').addClass('red').removeClass('green');
		setTimeout("ping()", 5000);
	});
}

jQuery(document).ready(function(e)
{
	jQuery('.inner span').show();
	jQuery('.inner div').hide();
	ping();
});