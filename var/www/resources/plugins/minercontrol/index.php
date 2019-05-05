<?php
/***************************************************************
	This is a Part of Miner-Control.de
			A: Sven Goessling
		H: http://Miner-Control.de
		 Do not change this file!
****************************************************************/

/* SQLite Connection */

$extensions = get_loaded_extensions();
if (!in_array('ionCube Loader', $extensions)) { 
	echo '<div><div class="box"><center>Please install ionCube Loader first.<br> You get it in this packet.</center></div></div>';
}

include_once(__DIR__ . '/../litesql/sqllite_con_run.php');
$SQLITEdb = __DIR__ . '/../litesql/database';
//use PDO;

$db = db_con($SQLITEdb);
/* SQLite Connection */

$postfile = '/resources/plugins/minercontrol/index.php';

/* Create Table if not exists */
$db->query("CREATE TABLE IF NOT EXISTS 'miner_control_list' ('miner_id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
															 'miner_name' TEXT,
															 'miner_transactionHash' TEXT,
															 'miner_transactionToken' TEXT,
															 'miner_ip' TEXT,
															 'miner_port' NUMERIC,
															 'miner_configfile_path' TEXT,
															 'miner_customername' TEXT,
															 'miner_last_share_time' DATETIME DEFAULT 0);");

/* Check POST */															 
if (isset($_POST)) {
	foreach ($_POST as $key=>$value) {
		$_POST[$key] = addslashes(TRIM($value));
	}
}
/* INSERT MINER */															 
if (isset($_POST['Code']) && $_POST['Code'] == "new_miner" &&
	isset($_POST['name']) && $_POST['name'] != "" &&
	isset($_POST['ip']) && $_POST['ip'] != "" &&
	isset($_POST['port']) && $_POST['port'] != "" &&
	isset($_POST['configfile']) && $_POST['configfile'] != "" &&
	isset($_POST['customername'])) {
	ob_clean();
	if ((isset($_POST['transactionHash']) && $_POST['transactionHash'] == "") ||
		(isset($_POST['token']) && $_POST['token'] == "")) {
		ob_clean();
		echo 'Please fill TransactionHash and Token.';
		exit;
	} else {
		$db->query("INSERT INTO `miner_control_list` (`miner_name`,
													  `miner_transactionHash`,
													  `miner_transactionToken`,
													  `miner_ip`,
													  `miner_port`,
													  `miner_configfile_path`,
													  `miner_customername`
													  ) VALUES (
													  '".$_POST['name']."',
													  '".$_POST['transactionHash']."',
													  '".$_POST['token']."',
													  '".$_POST['ip']."',
													  '".$_POST['port']."',
													  '".$_POST['configfile']."',
													  '".$_POST['customername']."'
													  );") OR die ('Error SQL 1');
		
	}
	exit;
}
/* UPDATE MINER*/
if (isset($_POST['Code']) && $_POST['Code'] == "update_miner" &&
	isset($_POST['id']) && $_POST['id'] != "" &&
	isset($_POST['name']) && $_POST['name'] != "" &&
	isset($_POST['ip']) && $_POST['ip'] != "" &&
	isset($_POST['port']) && $_POST['port'] != "" &&
	isset($_POST['configfile']) && $_POST['configfile'] != "" &&
	isset($_POST['customername'])) {
	ob_clean();
	if ((isset($_POST['transactionHash']) && $_POST['transactionHash'] == "") ||
		(isset($_POST['token']) && $_POST['token'] == "")) {
		ob_clean();
		echo 'Please fill TransactionHash and Token.';
		exit;
	} else {
		$db->query("UPDATE `miner_control_list` SET   `miner_name` = '".$_POST['name']."',
													  `miner_transactionHash` = '".$_POST['transactionHash']."',
													  `miner_transactionToken` = '".$_POST['token']."',
													  `miner_ip` = '".$_POST['ip']."',
													  `miner_port` = '".$_POST['port']."',
													  `miner_configfile_path` = '".$_POST['configfile']."',
													  `miner_customername` = '".$_POST['customername']."'
												WHERE `miner_id` = ".(int)$_POST['id'].";") OR die ('Error SQL 1');
		
	}
	exit;
}
/* DELETE MINER*/
if(isset($_POST['Code']) && $_POST['Code'] == "del_miner") {
	if(isset($_POST['miner_id']) && is_numeric($_POST['miner_id'])) {
	   ob_clean();
	   $db->query("DELETE FROM `miner_control_list` WHERE `miner_id` = ".(int)$_POST['miner_id'].";") or die('Error SQL 3');
	   exit;
   }
}
 
$body = '<style type="text/css">'.file_get_contents(__DIR__ . '/css/miner-control.css').'</style><noscript><style type="text/css">textarea {'.file_get_contents(__DIR__ . '/css/miner-control.css').'}</style></noscript>';
$body .= '<script type="text/javascript">'.file_get_contents(__DIR__ . '/js/miner-control.js').'</script>';

//Neue Miner
$body .= '<div id="head">Add new Miner</div>';
$body .= '	<div id="new_miner">
				<input type="hidden" placeholder="Miner-Name" id="post_file" value="'.$postfile.'">
				<div id="clear"></div>
				<div class="left left20">Miner Name:</div>
				<div class="right new_miner_name right20">
					<input type="text" placeholder="Miner-Name" id="new_name">
				</div>
				<div id="clear"></div>
				<div class="left left20">Miner TransactionHash:</div>
				<div class="right new_miner_transactionhash right20">
					<textarea id="new_miner_hash"></textarea>
				</div>
				<div id="clear"></div>
				<div class="left left20">Miner Token:</div>
				<div class="right new_miner_token right20">
					<textarea id="new_miner_token"></textarea>
				</div>
				<div id="clear"></div>
				<div class="left left20">Miner IP:</div>
				<div class="right new_miner_ip right20">
					<input type="text" placeholder="127.0.0.1" id="new_ip">
				</div>
				<div id="clear"></div>
				<div class="left left20">Miner Port:</div>
				<div class="right new_miner_port right20">
					<input type="text" placeholder="4028" id="new_port">
				</div>
				<div id="clear"></div>
				<div class="left left20">Miner Configfile Path:</div>
				<div class="right new_miner_configfile_path right20">
					<input type="text" placeholder="/home/pi/cgminer.conf" id="new_configpath">
				</div>
				<div id="clear"></div>
				<div class="left left20">Miner Customer Name:</div>
				<div class="right new_miner_customername right20">
					<input type="text" placeholder="" id="new_customername">
				</div>
				<div id="clear"></div>
				<div class="right save_new_miner"> Save new Miner </div>
				<div class="right abort_new">Abort</div>
				<div id="clear"></div>
			</div>
			<div id="add_new_miner">Create new Miner</div>
		  ';

//Bestehende Miner
$body .= '<div id="head">List all Miners</div>';
$allminer = array();
$query = $db->query("SELECT * FROM `miner_control_list`;");
while ($res = $query->fetch(PDO::FETCH_ASSOC)) {
	$allminer[] = $res;
}
$body .= '	<div id="list_miner"><div id="clear"></div>';
	if (count($allminer) > 0) {
		$body .= '<div id="minerlistheader">';
			$body .= '<div>ID</div>';
			$body .= '<div>Name</div>';
			$body .= '<div>Customer</div>';
			$body .= '<div>IP</div>';
			$body .= '<div>Port</div>';
			$body .= '<div>Last Connection</div>';
			$body .= '<div id="last">Options</div>';
		$body .= '</div>';	
		foreach ($allminer as $miner) {
			$body .= '<div id="minerlist">';	
				$body .= '<div>'.$miner['miner_id'].'&nbsp;</div>';
				$body .= '<div>'.$miner['miner_name'].'&nbsp;</div>';
				$body .= '<div>'.$miner['miner_customername'].'&nbsp;</div>';
				$body .= '<div>'.$miner['miner_ip'].'&nbsp;</div>';
				$body .= '<div>'.$miner['miner_port'].'&nbsp;</div>';
				$body .= '<div>'.(($miner['miner_last_share_time'] != '' && $miner['miner_last_share_time'] != 0)?$miner['miner_last_share_time']:'never').'&nbsp;</div>';
				$body .= '<div id="last" class="edit" minerID="'.$miner['miner_id'].'">Edit</div>';
			$body .= '</div>';
			$body .= '<div id="editMiner" class="miner_'.$miner['miner_id'].'">';
				$body .= '<div id="clear"></div>
				<div class="left left20">Miner Name:</div>
				<div class="right miner_name right20">
					<input type="text" placeholder="Miner-Name" id="name_'.$miner['miner_id'].'"  value="'.$miner['miner_name'].'">
				</div>
				<div id="clear"></div>
				<div class="left left20">Miner TransactionHash:</div>
				<div class="right miner_transactionhash right20">
					<textarea id="miner_hash_'.$miner['miner_id'].'">'.$miner['miner_transactionHash'].'</textarea>
				</div>
				<div id="clear"></div>
				<div class="left left20">Miner Token:</div>
				<div class="right miner_token right20">
					<textarea id="miner_token_'.$miner['miner_id'].'">'.$miner['miner_transactionToken'].'</textarea>
				</div>
				<div id="clear"></div>
				<div class="left left20">Miner IP:</div>
				<div class="right miner_ip right20">
					<input type="text" placeholder="127.0.0.1" id="ip_'.$miner['miner_id'].'" value="'.$miner['miner_ip'].'">
				</div>
				<div id="clear"></div>
				<div class="left left20">Miner Port:</div>
				<div class="right miner_port right20">
					<input type="text" placeholder="4028" id="port_'.$miner['miner_id'].'" value="'.$miner['miner_port'].'">
				</div>
				<div id="clear"></div>
				<div class="left left20">Miner Configfile Path:</div>
				<div class="right miner_configfile_path right20">
					<input type="text" placeholder="/home/pi/cgminer.conf" id="configpath_'.$miner['miner_id'].'" value="'.$miner['miner_configfile_path'].'">
				</div>
				<div id="clear"></div>
				<div class="left left20">Miner Customer Name:</div>
				<div class="right miner_customername right20">
					<input type="text" placeholder="" id="customername_'.$miner['miner_id'].'" value="'.$miner['miner_customername'].'">
				</div>
				<div id="clear"></div>
				<div class="right save_miner" minerID="'.$miner['miner_id'].'"> Save Miner </div>
				<div class="right abort" minerID="'.$miner['miner_id'].'">Abort</div>
				<div class="right delete" minerID="'.$miner['miner_id'].'">Delete</div>
				<div id="clear"></div>';	
			$body .= '</div>';
	
		}
		$body .= '<div id="minerlistfooter">';
			$body .= '<div>&nbsp;</div>';
			$body .= '<div>&nbsp;</div>';
			$body .= '<div>&nbsp;</div>';
			$body .= '<div>&nbsp;</div>';
			$body .= '<div>&nbsp;</div>';
			$body .= '<div>&nbsp;</div>';
			$body .= '<div id="last"></div>';
		$body .= '</div>';	
	} else {
		$body .= '<center>We have no miner in the list.<br>Feel free to add a new one.</center>';
	}
$body .= '<div id="clear"></div></div>';


echo '<div><div class="box"><div class="inner-header"><span>Miner-Control - Your Devices List</span></div><div class="inner">';
echo $body;
echo '<div style="width:100%; text-align:right; font-size:11px;">&copy; Sven Goessling from <a href="https://Miner-Control.de/" target="_blank">Miner-Control.de</a></div></div></div></div>';														 
?>