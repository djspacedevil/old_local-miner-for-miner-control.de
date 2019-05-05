// JavaScript Document
setInterval(function()
{
	time++;
	var hour = parseInt(time / 60 / 60 % 24);
	hour = (hour < 10) ? '0'+hour : hour;
	hour = (hour == 24) ? '00' : hour;
	var minute = parseInt(time / 60 % 60);
	minute = (minute < 10) ? '0'+minute : minute;
	var second = parseInt(time % 60);
	second = (second < 10) ? '0'+second : second;
	document.getElementById('servertime').innerHTML = hour+':'+minute+':'+second;
}, 1000);