$(document).ready(function(){
	
	$('#add_new_miner').on('click', function() {
		$('#new_miner').slideDown('slow', function() {
			$('#add_new_miner').slideUp('slow');
		});
	});
	
	$('.save_new_miner').on('click', function() {
		var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		var miner_name = $('#new_name').val();
		var miner_transactionHash = $('#new_miner_hash').val();
		var miner_token = $('#new_miner_token').val();
		var miner_ip = $('#new_ip').val();
		var miner_port = $('#new_port').val();
		var miner_configfile = $('#new_configpath').val();
		var miner_customername = $('#new_customername').val();
		var postfile = $('#post_file').val();
		
		if (miner_name == "") {
			for( var i=0; i < 6; i++ ) miner_name += possible.charAt(Math.floor(Math.random() * possible.length));
		}
		
		if (miner_ip 			== "") miner_ip = '127.0.0.1';
		if (miner_port 			== "") miner_port = '4028';
		if (miner_configfile	== "") miner_configfile = '/home/pi/cgminer.conf';
		
		$.ajax({type: "POST", 
					url: postfile,
					async: false,
					data: {Code: "new_miner",
						   name: miner_name,
						   transactionHash: miner_transactionHash,
						   token: miner_token,
						   ip: miner_ip,
						   port: miner_port,
						   configfile: miner_configfile,
						   customername: miner_customername 
						   }
					}).done(function(data) {
				if (data != "") {
					alert(data);
				} else {
					$('#new_miner').slideUp('slow', function() {
						location.reload();
					});
				}
			});
		
	});
	
	$('.delete').on('click', function() {
		var minerID = $(this).attr('minerid');
		var postfile = $('#post_file').val();
		if (minerID != "" && confirm("Are you sure?")) {
			$.ajax({type: "POST", 
					url: postfile,
					async: false,
					data: {Code: "del_miner",
						   miner_id: minerID
						   }
					}).done(function(data) {
				if (data != "") {
					alert(data);
				} else {
					$("#editMiner").fadeOut('slow', function() {
						location.reload();
					});
				}
			});
		}
	});
	
	$('.save_miner').on('click', function() {
		var minerID = $(this).attr('minerid');
		var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		var miner_name = $('#name_'+minerID).val();
		var miner_transactionHash = $('#miner_hash_'+minerID).val();
		var miner_token = $('#miner_token_'+minerID).val();
		var miner_ip = $('#ip_'+minerID).val();
		var miner_port = $('#port_'+minerID).val();
		var miner_configfile = $('#configpath_'+minerID).val();
		var miner_customername = $('#customername_'+minerID).val();
		var postfile = $('#post_file').val();
		
		if (miner_name == "") {
			for( var i=0; i < 6; i++ ) miner_name += possible.charAt(Math.floor(Math.random() * possible.length));
		}
		
		if (miner_ip 			== "") miner_ip = '127.0.0.1';
		if (miner_port 			== "") miner_port = '4028';
		if (miner_configfile	== "") miner_configfile = '/home/pi/cgminer.conf';
		
		$.ajax({type: "POST", 
					url: postfile,
					async: false,
					data: {Code: "update_miner",
						   id: minerID,
						   name: miner_name,
						   transactionHash: miner_transactionHash,
						   token: miner_token,
						   ip: miner_ip,
						   port: miner_port,
						   configfile: miner_configfile,
						   customername: miner_customername 
						   }
					}).done(function(data) {
				if (data != "") {
					alert(data);
				} else {
					$("#editMiner").fadeOut('slow', function() {
						location.reload();
					});
				}
			});
		
	});
	
	$('.abort_new').on('click', function() {
		$('#add_new_miner').slideDown('slow', function() {
			$('#new_miner').slideUp('slow');
		});
	});
	
	$('.edit').on('click', function() {
		var minerID = $(this).attr('minerID');
		$('.miner_'+minerID).fadeIn('slow');
	});
	
	$('.abort').on('click', function() {
		var minerID = $(this).attr('minerID');
		$('.miner_'+minerID).fadeOut('slow');
	});
	
	$(document).mouseup(function (e) {
		var container = $("#editMiner");
		if (!container.is(e.target) && container.has(e.target).length === 0) {
			container.fadeOut('slow');
		}
});
	
});