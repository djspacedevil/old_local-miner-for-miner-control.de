<?php
/******************************************************************
*			       Runfile for your Miner					  	  *
*					A: Sven Goessling							  *
*					W: Miner-Control.de							  *
*						V: 1.0.0								  *
******************************************************************/
//Config File
include_once(__DIR__."/config.php");
	if (!isset($TransactionHash) || $TransactionHash == "" || !isset($MinerToken) || $MinerToken == "") {
		echo 'No Config Infos found.'.PHP_EOL;
		exit;
	} else {
		include_once(__DIR__."/class.miner.php");
		$security_decode = 'iqfWvXPa66ytDokyiDCgekOXepxzUKtou0OIN3qVd0SCk46Xwa4qiJdr40pKmQZeDNpW5rPXBDyvOPCWGKQWY9bG1bls2nQCRF4BAr5V6VZtv0gG1m4ANRfwQ5NvgzIbefTpJOZJuXVI4b99J6KBsZIKbwwwx5WemXfse9BVYErxNPuobMZZL0MzlKQcFavxpjcIflrSxFq9337cdfsUSLZkoSJmmp08EkSNQ9bXCES8sKX82h22TgyLAWapmjufS1iCvW8iAzxmoMiCaFF8UtuKH4CGykvSPhWesLvqQbK4';
		//echo '- Collect Miner Infos:';
		$miner = new LocalMiner('127.0.0.1', 4028);
		
		$protocol = 'https://';
		$Miner_Control = 'miner-control.de';
		//$Miner_Control = '192.168.0.16'; //TEST SERVER
		$postminer = '/miner.php';
		
		//Claim Miner Infos
		$send_miner_infos = array();
		$send_miner_infos['TRANSACTIONHASH'] 	= $TransactionHash;
		$send_miner_infos['TOKEN'] 				= $MinerToken;
		$send_miner_infos['CONFIG'] 			= $miner->config();
		$send_miner_infos['VERSION'] 			= $miner->version();
		$send_miner_infos['APIVERSION'] 		= $miner->apiversion();
		$send_miner_infos['UPTIME'] 			= $miner->uptime();
		$send_miner_infos['SUMMERY'] 			= $miner->allinfos();
		$send_miner_infos['COIN']				= $miner->coin();
		$send_miner_infos['POOLS'] 				= $miner->pools();
		for ($i = 0; $i < sizeof($send_miner_infos['POOLS']); $i++) {
			$send_miner_infos['POOLSTATS'][$i] 			= $miner->stats($i);
		}
		$send_miner_infos['HASHRATE'] 			= $miner->hashrate();
		$send_miner_infos['DEVICES'] 			= $miner->devices();
		$send_miner_infos['SHARES'] 			= $miner->shares();
		
		$send = json_encode($send_miner_infos);
		$send = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($security_decode), $send, MCRYPT_MODE_CBC, md5(md5($security_decode))));
		$fields = array(
						'transactioncode' 	=> urlencode($send_miner_infos['TRANSACTIONHASH']),
						'minertoken' 		=> urlencode($send_miner_infos['TOKEN']),
						'configfile'		=> urlencode(((file_exists($ConfigFile))?file_get_contents($ConfigFile):'')),
						'miner_infos' 		=> urlencode($send)
						);
		$fields_string = '';
		foreach($fields as $key=>$value) { 
			$fields_string .= $key.'='.$value.'&'; 
		}
		rtrim($fields_string, '&');
		
		//open connection
		$ch = curl_init();

		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $protocol.$Miner_Control.$postminer);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch,CURLOPT_POST, count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, (($Miner_Control == '192.168.0.16')?0:1));

		//execute post
		$result = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		//close connection
		
		curl_close($ch);
		
		if($httpCode == 200) {
			if (!empty($json)) echo 'Daten erhalten:'.PHP_EOL;
			
			$json = json_decode($result, true);	
			if (is_array($json)) {
				if (isset($json['DONATETIME']) && !empty($json['DONATETIME'])) {
					//Spendenzeit
					echo '- DONATION TIME RUNNING'.PHP_EOL;
					$found_pool = false;
					foreach ($send_miner_infos['POOLS'] as $pool) {
						if ($pool->URL == 'stratum+tcp://'.$json['DONATETIME']['pool'] && $pool->User == $json['DONATETIME']['user']) {
							$found_pool = true;
							if ($pool->Priority != 0 && $pool->{'Stratum Active'} != 1) {
								//Pool ist nicht aktiv 
								$miner->switchpool($pool->POOL);
							}
						}
					}
				
					if (!$found_pool) {
						//Pool wurde nicht gefunden und muss hinzugefügt werden.
						$miner->addpool($json['DONATETIME']['pool'], $json['DONATETIME']['user'], $json['DONATETIME']['password']);
						foreach ($miner->pools() as $pool) {
							if ($pool->URL == 'stratum+tcp://'.$json['DONATETIME']['pool'] && $pool->User == $json['DONATETIME']['user']) {
								$found_pool = true;
								if ($pool->Priority != 0 && $pool->{'Stratum Active'} != 1) {
									//Pool ist nicht aktiv 
									$miner->switchpool($pool->POOL);
								}
							}
						}
					}
				} else {
					//Switch Pool
					if (isset($json['SWITCH POOL']) && $json['SWITCH POOL'] != "") {
						echo '- SWITCH POOL TO '.$json['SWITCH POOL']. PHP_EOL;
						$miner->switchpool((int)$json['SWITCH POOL']);
					}
					//SPEICHER CONFIG
					if (isset($json['NEWCONFIGFILE']) && $json['NEWCONFIGFILE'] != "") { 
						echo '- SAVE CONFIG FILE.'.PHP_EOL;
						file_put_contents($ConfigFile, $json['NEWCONFIGFILE']);
					}
					//DELETE POOLS
					if (isset($json['DELETEPOOLS']) && $json['DELETEPOOLS'] != "") { 
						echo '- DELETE POOLS.'.PHP_EOL;
						$delPools = json_decode($json['DELETEPOOLS']);
						foreach ($delPools as $del) {
							$miner->removepool((int)$del);
						}
					}
					//NEW POOLS
					if (isset($json['NEWPOOLS']) && $json['NEWPOOLS'] != "") {
						echo '- NEW POOLS.'.PHP_EOL;
						$newPools = json_decode($json['NEWPOOLS']);
						foreach ($newPools as $new) {
							$miner->addpool($new->Pool, $new->User, $new->Password);
						}
					}
					//SOFT REBOOT
					if (isset($json['SOFTREBOOT']) && $json['SOFTREBOOT'] != "") {
						echo '- Soft REBOOT'.PHP_EOL;
						$miner->restart();
					}
					//HARD REBOOT
					if (isset($json['HARDREBOOT']) && $json['HARDREBOOT'] != "") {
						echo '- HARD REBOOT';
						exec('sudo reboot');
					}
				}
			}
		} else {
			echo $httpCode.' ERROR. NOT FOUND'.PHP_EOL;
			exit;
		}
			//Commands
			//$miner->restart();
	}
?>