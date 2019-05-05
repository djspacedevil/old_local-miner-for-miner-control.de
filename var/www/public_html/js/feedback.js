// JavaScript Document
jQuery(document).on('mousedown', 'a[href="http://willy-tech.de/kontakt/"]', function(e)
{
	if (e.which == 3)
		return false;
	
	window.scrollTo(0, 0);
	
	if (jQuery('#feedback').length == 0)
	{
		jQuery('body').append('<div id="feedback_background" title="Ausblenden"></div>');
		jQuery('body').append('<div id="feedback" class="box"><div class="inner-header"><span>Feedback</span><a href="#" title="Ausblenden">Ausblenden</a></div><div class="inner padding-0" style="background: url(public_html/img/ajaxloader.gif) center no-repeat; min-height: 200px;"></div></div>');
		
		jQuery.get('resources/library/etc/feedback_stats.php', {url: req_url}, function(data)
		{
			var url_param = '';
			
			if (data != '')
				url_param = data;
			else
				alert('Fehler beim Auslesen der Systeminformationen.');
				
			jQuery('#feedback .inner').append('<iframe style="display: none;" name="iframe_feedback" src="http://picontrol.willy-tech.de/web/1-0/?s=feedback">Dein Browser unterstützt keine eingebetteten Frames. Folge einfach diesem Link: <a href="http://willy-tech.de/kontakt/" target="_blank">Kontakt</a></iframe>');
			
			var form = jQuery('<form action="http://picontrol.willy-tech.de/web/1-0/?s=feedback" target="iframe_feedback" method="post"><input type="hidden" name="stats" value="'+url_param+'" /><input type="hidden" name="errorHandler" value="'+errorHandler+'" /></form>');
			jQuery('#feedback .inner').append(form);
			form.submit();
			
			jQuery('#feedback .inner iframe').fadeIn('fast');
		});
	}
	
	jQuery('#feedback_background, #feedback').fadeIn('fast');

	return false;
});

jQuery(document).on('click', 'a[title="Ausblenden"], #feedback_background', function(e)
{
	jQuery('#feedback_background, #feedback').fadeOut('fast');
	
	return false;
});